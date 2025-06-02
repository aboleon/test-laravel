<?php

namespace App\Services\Accommodation;

use App\Accessors\EventContactAccessor;
use App\Accessors\EventManager\Availability;
use App\Enum\OrderAmendedType;
use App\Helpers\{CsvHelper, DateHelper};
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Accommodation;
use App\Models\Order;
use App\Models\Order\Cart\AccommodationCart;
use App\Services\Pec\PecParser;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use MetaFramework\Traits\DateManipulator;

class AccommodationPackageSearchService
{
    use DateManipulator;

    private array $noMatchRoomGroups = [];
    private array $noMatchRooms = [];
    private array $pricePerNight = [];
    private array $pecPerNight = [];
    private array $availableRoomDetailsPerAccommodation = [];
    private array $availability = [];
    private bool $isEligible = false;

    private array $control = [];

    private int|float $minimumPrice = 0;
    private ?int $excludedRoom = null;
    private Collection $excludedPecDates;
    private EventContactAccessor $eventContactAccessor;

    public function __construct(
        private readonly Event $event,
        private readonly EventContact $eventContact,
        private string $searchDateStart,
        private string $searchDateEnd,
        private readonly ?string $amend = null,
        private readonly null|Order|AccommodationCart $amendable = null,
    ) {
        $this->eventContactAccessor = (new EventContactAccessor())->setEventContact($this->eventContact);
        $this->pecEligibility();
        $this->setPriceForUpsell();
        $this->setExcludedRoom();
        $this->setExcudedPecDates();
    }

    public function setExcudedPecDates(): self
    {
        $this->excludedPecDates = collect($this->eventContactAccessor->getPecAccommodationDates())->map(fn($item) => $item->toDateString());

        return $this;
    }

    public function execute(): array
    {
        if ($this->amend && $this->isCart()) {
            return $this->amendCartSearch();
        }

        return $this->eventSearch();
    }

    private function isCart(): bool
    {
        return $this->amendable instanceof AccommodationCart;
    }

    private function isOrder(): bool
    {
        return $this->amendable instanceof Order;
    }

    public function amendCartSearch(): array
    {
        $this->searchDateStart = $this->amendable->date->format('Y-m-d');

        if ( ! $this->searchDateStart) {
            return ['error' => "La date n'est pas correctement renseignée."];
        }

        return $this->performSearch($this->searchDateStart);
    }

    public function eventSearch(): array
    {
        if ($error = $this->validateSearchDates()) {
            return ['error' => $error];
        }

        $userDates = $this->getUserDates();


        return $this->performSearch($userDates);
    }

    private function performSearch(string|array $dates): array
    {
        $isSingleDate = ! is_array($dates);

        $accommodations = $this->event->publishedAccommodations();

        // Lock amendable hotel
        if ($this->amend) {
            $hotel_id = $this->amend == OrderAmendedType::CART->value ? $this->amendable->event_hotel_id
                : $this->amendable->accommodation->first()?->event_hotel_id;
            if ($hotel_id) {
                $accommodations->where('id', $hotel_id);
            }
        }

        $accommodations = $accommodations
            ->get()
            ->filter(
                function (Accommodation $accommodation) use (
                    $dates,
                    $isSingleDate,
                ) {
                    if ( ! $this->isParticipationTypeAllowed($accommodation)) {
                        return false;
                    }

                    $availability = new Availability();
                    $availability
                        ->setEventAccommodation($accommodation)
                        ->setParticipationType($this->eventContact->participation_type_id)
                        ->setExcludeRoomsId($this->excludedRoom)
                        ->setMinimumPrice($this->minimumPrice)
                        ->setEventContact($this->eventContact)
                        ->publishedRoomsOnly();

                    if ($isSingleDate) {
                        $availability->setDate($this->searchDateStart);
                    } else {
                        $availability->setDateRange(
                            [$this->searchDateStart, $this->searchDateEnd],
                        );
                    }

                    if ( ! $availability->getStrictAvaiability()) {
                        return false;
                    }

                    $this->availability[$accommodation->id] = [
                        'availability' => $availability->getStrictAvaiability(),
                        'summary'      => $availability->getSummarizedData(),
                    ];

                    $this->availableRoomDetailsPerAccommodation[$accommodation->id] = $this->generatePackagePrices(
                        $availability->getStrictAvaiability(),
                        $availability->getSummarizedData()['room_configs'],
                    );

                    $roomDetails = $this->initializeRoomDetails(
                        $availability->getSummarizedData(),
                    );

                    if ( ! $this->processDates(
                        $isSingleDate ? [$dates] : $this->getUserDates(),
                        $availability->getAvailability(),
                        $availability->getSummarizedData(),
                        $roomDetails,
                        $accommodation,
                    )
                    ) {
                        return false;
                    }

                    return true;
                },
            );

        return $this->getSearchResults($accommodations);
    }

    private function generatePackagePrices(
        array $availability,
        array $room_configs,
    ): array {
        $pecDatesCollection = $this->excludedPecDates;

        // Filter room_configs to only include dates and room types that are present in availability
        $filteredConfigs = collect($room_configs)->filter(
            function ($roomGroups, $date) use ($availability) {
                return isset($availability[$date]);
            },
        )->map(function ($roomGroups, $date) use ($availability, $pecDatesCollection) {
            return collect($roomGroups)->filter(
                function ($rooms, $roomTypeId) use ($availability, $date) {
                    return isset($availability[$date][$roomTypeId]);
                },
            );
        });

        $transformedConfigs = $filteredConfigs->map(function ($roomGroups, $date) use ($pecDatesCollection) {
            return collect($roomGroups)->map(function ($rooms) use ($date, $pecDatesCollection) {
                return collect($rooms)->mapWithKeys(function ($room) use ($date, $pecDatesCollection) {
                    // If date is in pec_dates, set pec to 0

                    $pec = $pecDatesCollection->contains($date) ? 0 : ($room['pec_allocation'] ?? 0);

                    return [
                        $room['id'] => [
                            'price' => $room['price'],
                            'pec'   => $pec,
                        ],
                    ];
                });
            });
        });

        $aggregatedData = [];

        $transformedConfigs->each(
            function ($roomGroups) use (&$aggregatedData) {
                collect($roomGroups)->each(
                    function ($rooms, $roomGroupId) use (&$aggregatedData) {
                        if ( ! isset($aggregatedData[$roomGroupId])) {
                            $aggregatedData[$roomGroupId] = [];
                        }

                        collect($rooms)->each(
                            function ($roomData, $roomId) use (
                                &$aggregatedData,
                                $roomGroupId,
                            ) {
                                if ( ! isset($aggregatedData[$roomGroupId][$roomId])) {
                                    $aggregatedData[$roomGroupId][$roomId] = [
                                        'price_ttc' => 0,
                                        'pec_ttc'   => 0,
                                    ];
                                }

                                $aggregatedData[$roomGroupId][$roomId]['price_ttc'] += $roomData['price'];
                                $aggregatedData[$roomGroupId][$roomId]['pec_ttc']   += $roomData['pec'];
                            },
                        );
                    },
                );
            },
        );

        return array_filter($aggregatedData, function ($rooms) {
            return ! empty($rooms);
        });
    }


    private function getSearchResults(EloquentCollection $accommodations): array
    {
        return [
            'accommodations'  => $accommodations,
            'global'          => $this->availableRoomDetailsPerAccommodation,
            'pricePerNight'   => $this->pricePerNight,
            'pecPerNight'     => $this->pecPerNight,
            'availability'    => $this->availability,
            'start_date'      => $this->toDateFormat('d/m/Y', $this->searchDateStart),
            'end_date'        => $this->toDateFormat('d/m/Y', $this->searchDateEnd),
            'start_date_sql'  => $this->toDateFormat('Y-m-d', $this->searchDateStart),
            'end_date_sql'    => $this->toDateFormat('Y-m-d', $this->searchDateEnd),
            'excudedPecDates' => $this->excludedPecDates,
        ];
    }

    private function validateSearchDates(): ?string
    {
        if ( ! $this->searchDateStart || ! $this->searchDateEnd) {
            return "Veuillez renseigner une date de début et de fin";
        }

        $dateStart = DateHelper::parseFrontDate($this->searchDateStart, false);
        $dateEnd   = DateHelper::parseFrontDate($this->searchDateEnd, false);
        if ($dateStart > $dateEnd) {
            return "La date de début doit être inférieure à la date de fin";
        }

        return null;
    }

    private function getUserDates(): array
    {
        $dateStart = DateHelper::parseFrontDate($this->searchDateStart, false);
        $dateEnd   = DateHelper::parseFrontDate($this->searchDateEnd, false);

        return DateHelper::getDatesFromCarbonPeriod(
            new CarbonPeriod($dateStart, $dateEnd->subDay()),
        );
    }


    private function isParticipationTypeAllowed(Accommodation $accommodation,
    ): bool {
        $allowedParticipationTypeIds = CsvHelper::csvToUniqueArray(
            $accommodation->participation_types,
        );

        return in_array(
            $this->eventContact->participation_type_id,
            $allowedParticipationTypeIds,
        );
    }

    private function initializeRoomDetails(array $summary): array
    {
        $roomDetails = [];
        foreach ($summary['roomgroups'] as $roomGroupId => $roomGroup) {
            foreach ($roomGroup['rooms'] as $roomId => $name) {
                $roomDetails[$roomGroupId][$roomId] = [
                    'price_ttc' => 0,
                    'pec_ttc'   => 0,
                ];
            }
        }

        return $roomDetails;
    }

    private function processDates(
        array $dates,
        array $availability,
        array $summary,
        array &$roomDetails,
        Accommodation $accommodation,
    ): bool {
        foreach ($dates as $date) {
            foreach ($roomDetails as $roomGroupId => $rooms) {
                if ($this->isRoomGroupUnavailable(
                    $availability,
                    $date,
                    $roomGroupId,
                )
                ) {
                    $this->noMatchRoomGroups[] = $roomGroupId;
                    continue;
                }

                $roomConfigs = $summary['room_configs'][$date][$roomGroupId] ??
                    null;
                if ($roomConfigs === null) {
                    $this->noMatchRoomGroups[] = $roomGroupId;
                    continue;
                }

                $this->processRoomDetails(
                    $rooms,
                    $roomGroupId,
                    $roomConfigs,
                    $accommodation,
                    $date,
                );
            }
        }

        return true;
    }

    private function isRoomGroupUnavailable(
        array $availability,
        string $userDate,
        int $roomGroupId,
    ): bool {
        return array_key_exists($userDate, $availability)
            && array_key_exists(
                $roomGroupId,
                $availability[$userDate],
            )
            && $availability[$userDate][$roomGroupId] <= 0;
    }


    private function processRoomDetails(
        array &$rooms,
        int $roomGroupId,
        array $roomConfigs,
        Accommodation $accommodation,
        string $userDate,
    ): void {
        foreach ($rooms as $roomId => $totalPrice) {
            $roomAvailable = false;
            $key           = $accommodation->id."-".$roomId;

            foreach ($roomConfigs as $roomConfig) {
                if ($roomId === $roomConfig['id']) {
                    $roomAvailable                        = true;
                    $rooms[$roomId]['price_ttc']          += $roomConfig['price'];
                    $this->pricePerNight[$key][$userDate] = $roomConfig['price'];

                    if ($this->isEligible && $roomConfig['pec']) {
                        $rooms[$roomId]['pec_ttc']          += $roomConfig['pec_allocation'];
                        $this->pecPerNight[$key][$userDate] = $roomConfig['pec_allocation'];
                    } else {
                        $this->pecPerNight[$key][$userDate] = 0;
                    }
                }
            }

            // Ensure keys exist even if the room is not available
            if ( ! $roomAvailable) {
                $this->pricePerNight[$key][$userDate] = $this->pricePerNight[$key][$userDate] ?? 0;
                $this->pecPerNight[$key][$userDate]   = $this->pecPerNight[$key][$userDate] ?? 0;
            }
        }
    }


    private function filterRoomDetails(array $roomDetails): array
    {
        foreach ($this->noMatchRoomGroups as $roomGroupId) {
            unset($roomDetails[$roomGroupId]);
        }
        foreach ($roomDetails as $roomGroupId => $rooms) {
            foreach ($rooms as $roomId => $totalPrice) {
                if (in_array($roomId, $this->noMatchRooms)) {
                    unset($roomDetails[$roomGroupId][$roomId]);
                }
            }
        }

        return $roomDetails;
    }

    private function isAccommodationMatch(array $availableRoomDetails): bool
    {
        foreach ($availableRoomDetails as $rooms) {
            if (count($rooms) > 0) {
                return true;
            }
        }

        return false;
    }

    private function pecEligibility(): void
    {
        $pec = new PecParser(
            $this->event, collect()->push($this->eventContact),
        );
        $pec->calculate();
        $this->isEligible = $pec->hasGrants($this->eventContact->id);
    }

    private function setPriceForUpsell(): int|float
    {
        return $this->minimumPrice = match ($this->amend) {
            OrderAmendedType::CART->value => $this->amendable->unit_price,
            OrderAmendedType::ORDER->value => $this->amendable->accommodation?->first()->unit_price ?: 0,
            default => 0,
        };
    }

    private function setExcludedRoom(): ?int
    {
        return $this->excludedRoom = match ($this->amend) {
            OrderAmendedType::CART->value => $this->amendable->room_id,
            OrderAmendedType::ORDER->value => $this->amendable->accommodation?->first()->room_id ?: 0,
            default => 0,
        };
    }
}

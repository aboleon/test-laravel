<?php

namespace App\Livewire\Front\Accommodation;

use App\Accessors\EventContactAccessor;
use App\Accessors\OrderAccessor;
use App\Actions\Front\Cart\FrontCartActions;
use App\Enum\OrderAmendedType;
use App\Accessors\Front\FrontCartAccessor;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\EventManager\Accommodation\RoomGroup;
use App\Models\EventManager\Accommodation\Room;
use App\Models\{Event,
    EventContact,
    Order
};
use App\Services\Accommodation\AccommodationPackageSearchService;
use App\Services\Pec\PecParser;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Livewire\Component;

class AccommodationBooker extends Component
{

    public Event $event;
    public EventContact $eventContact;
    public null|Order|AccommodationCart $amendable = null;

    /*
     * Amend booking, null|OrderAmendedType
     */
    public null|string $amend = null;
    public Collection $accommodations;
    public string $searchDateStart = '';
    public string $searchDateEnd = '';
    public string $topError = '';
    public array $searchResultInfo = [];
    public array $userRoomPreferences = [];


    public function mount(
        Event                        $event,
        EventContact                 $eventContact,
                                     $amend = null,
        Order|AccommodationCart|null $amendable = null,
    ): void
    {
        $this->event = $event;
        $this->eventContact = $eventContact;
        $this->accommodations = new Collection();
        $this->amend = $amend;
        $this->amendable = $amendable;
    }

    public function render(): Renderable
    {
        return view('livewire.front.accommodation.accommodation-booker');
    }

    public function getIsAmendableProperty(): bool
    {
        return !is_null($this->amend);
    }

    public function getIsAmendableCartProperty(): bool
    {
        return $this->amend == OrderAmendedType::CART->value;
    }

    public function getIsAmendableOrderProperty(): bool
    {
        return $this->amend == OrderAmendedType::ORDER->value;
    }

    public function getAmendableOrderIdProperty(): int
    {
        return $this->getIsAmendableCartProperty() ? $this->amendable->order_id : $this->amendable->id;
    }

    public function getAmendedOrderAmount(): int|float
    {
        if ($this->getIsAmendableCartProperty()) {
            return $this->amendable->total_net + $this->amendable->total_vat;
        }
        if ($this->getIsAmendableOrderProperty()) {
            $totals = (new OrderAccessor($this->amendable))->accommodationCartTotals();
            return $totals['total_net'] + $totals['total_vat'];
        }
        return 0;
    }

    public function getAmendableDatesTitleProperty(): string
    {
        if ($this->getIsAmendableCartProperty()) {
            return __('front/order.amend_dates_single') . $this->amendable->date->format('d/m/Y');
        }
        if ($this->getIsAmendableOrderProperty()) {
            $start_date = $this->amendable->accommodation->min('date');
            $end_date = $this->amendable->accommodation->max('date');
            if ($start_date->equalTo($end_date)) {
                return __('front/order.amend_dates_single') . $start_date->format('d/m/Y');
            }
            return __('front/order.amend_dates_from') . $start_date->format('d/m') . __('front/order.amend_dates_to') . $end_date->addDay()->format('d/m/Y');
        }
    }

    public function getAmendableDates(): string|array
    {
        return $this->getIsAmendableCartProperty()
            ? $this->amendable->date->format('d/m/Y')
            : [$this->amendable->accommodation->min('date')->format('d/m/Y'), $this->amendable->accommodation->max('date')->addDay()->format('d/m/Y')];
    }

    public function search(): void
    {
        if ($this->amend) {
            // Limit to original booking dates
            $dates = $this->getAmendableDates();
            $this->searchDateStart = is_array($dates) ? $dates[0] : $dates;
            $this->searchDateEnd = is_array($dates) ? $dates[1] : $dates;

        }

        $searchService = new AccommodationPackageSearchService(
            $this->event,
            $this->eventContact,
            $this->searchDateStart,
            $this->searchDateEnd,
            $this->amend,
            $this->amendable
        );
        $result = $searchService->execute();

        if (isset($result['error'])) {
            $this->addTopError($result['error']);
            return;
        }

        //de($result['global']);

        $keysWithAvailabilityLessThanOne = collect($result['availability'])->filter(function ($item) {
            return collect($item['availability'])
                ->every(fn($dateAvailability) => collect($dateAvailability)->every(fn($value) => $value < 1));
        })->keys();

        $result['accommodations'] = $result['accommodations']->reject(fn ($item) => $keysWithAvailabilityLessThanOne->contains($item['id']));

        $this->accommodations = $result['accommodations'];
        unset($result['accommodations']); //de($result);
        $this->searchResultInfo = $result;

    }

    public function bookStay(RoomGroup $roomGroup, Room $room): void
    {

        $eventContactAccessor = (new EventContactAccessor())->setEventContact($this->eventContact);
        $frontCartActions = new FrontCartActions();
        $hasPecBooking = $eventContactAccessor->hasAnyPecAccommodation() or $frontCartActions->hasPecStayLines();

        $priceDetails = $this->searchResultInfo['global'][$roomGroup->event_accommodation_id][$roomGroup->id][$room->id] ?? null;
        if (!$priceDetails) {
            goto bookStayPriceError;
        }
        $totalTtc = $priceDetails['price_ttc'] ?? null;
        if (!$totalTtc) {
            bookStayPriceError:
            $this->addModalError(__('front/accommodation.cant_obtain_price'));
            $this->addModalError('no total ttc');
            return;
        }


        $processingFee = $roomGroup->accommodation->processing_fee;
        $processingFeeVatId = $roomGroup->accommodation->processing_fee_vat_id;


        $totalTtc += $processingFee;


        $key = $roomGroup->event_accommodation_id . "-" . $room->id;
        $pricePerNight = $this->searchResultInfo['pricePerNight'][$key] ?? null;
        $pecPerNight = $this->searchResultInfo['pecPerNight'][$key] ?? null;
        if (!$pricePerNight) {
            $this->addModalError(__('front/accommodation.cant_obtain_price'));
           // d($key);
           // de($this->searchResultInfo);
            return;
        }

        $totalPec = 0;
        $grantId = null;



        // PEC Calculate
        if (!$hasPecBooking && $eventContactAccessor->isPecAuthorized()) {

            $pec = new PecParser($this->event, collect()->push($this->eventContact));
            $pec->calculate();

            if ($pec->hasGrants($this->eventContact->id)) {
                $grantId = $pec->getPreferedGrantFor($this->eventContact->id)->id;
                $totalPec = $priceDetails['pec_ttc'];
                if (array_key_exists("pec_ttc", $priceDetails) && $priceDetails["pec_ttc"] > 0) {
                    $totalPec += $processingFee;
                }
                if ($totalPec > $totalTtc) {
                    $totalPec = $totalTtc;
                }
            }
        }

        $nbPerson = $this->userRoomPreferences[$room->id]['capacity'] ?? 1;
        $accompanyingDetails = $this->userRoomPreferences[$room->id]['accompanying_details'] ?? "";
        $comment = $this->userRoomPreferences[$room->id]['comment'] ?? "";

        $res = (new FrontCartActions())->addStay([
            'event_contact_id' => $this->eventContact->id,
            'date_start' => $this->searchResultInfo['start_date'],
            'date_end' => $this->searchResultInfo['end_date'],
            'room_group' => $roomGroup,
            'room' => $room,
            'nb_person' => $nbPerson,
            'total_ttc' => $totalTtc,
            'total_pec' => $totalPec,
            'grant_id' => $grantId,
            'price_per_night' => $pricePerNight,
            'pec_per_night' => $pecPerNight,
            'accompanying_details' => $accompanyingDetails,
            'comment' => $comment,
            'processing_fee' => $processingFee,
            'processing_fee_vat_id' => $processingFeeVatId,
            'amendable' => $this->amend,
            'amendable_order_id' => $this->amend ? $this->getAmendableOrderIdProperty() : null,
            'amendable_id' => $this->amend ? $this->amendable->id : null,
            'amendable_amount' => $this->getAmendedOrderAmount()
        ]);


        if (is_array($res)) {
            $this->addModalError($res[0]);
        }
        $this->dispatch("AccommodationBooker:onBookRoomSuccess", $roomGroup, $room);
    }


    private function addTopError(string $errorMsg): void
    {
        $this->topError = $errorMsg;
    }

    private function addModalError(string $errorMsg): void
    {
        $this->dispatch("AccommodationBooker:onBookRoomError", $errorMsg);
    }
}

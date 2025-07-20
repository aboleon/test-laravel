<?php

declare(strict_types=1);

namespace App\Exports\EventContact\Abstract;

use App\Accessors\Accounts;
use App\Accessors\EventContactAccessor;
use App\Enum\ParticipantType;
use App\Exports\Traits\ExportTrait;
use App\Helpers\CsvHelper;
use App\Models\AccountAddress;
use App\Models\EventContact;
use App\Models\ParticipationType;
use App\Traits\Models\EventModelTrait;
use DateTime;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use MetaFramework\Traits\Ajax;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Throwable;

abstract class AccountExportAbstract
{
    use Ajax;
    use ExportTrait;
    use EventModelTrait;

    protected array $userIds = [];
    protected array $fields = [];
    protected array $accommodationData = [];
    protected array $serviceData = [];
    protected ?AccountAddress $address = null;
    protected string $format = 'xlsx';
    protected string $group = 'all';

    protected string $mode = 'all';
    protected ?EventContactAccessor $eventContactAccessor = null;
    protected ?Accounts $accountAccessor = null;

    // New property to store all event services
    protected array $eventServices = [];

    public function __construct()
    {
        $this->setEvent((int)request('event_id'));
        if (in_array((string)request('mode'), ['all', 'selection'])) {
            $this->mode = (string)request('mode');
        }

        if (in_array((string)request('group'), $this->groupTypes())) {
            $this->group = (string)request('group');
        }
    }

    protected function groupTypes(): array
    {
        return array_merge(
            [$this->group],
            ParticipantType::values(),
        );
    }

    /**
     * Get fields mapping - must be implemented by child classes
     *
     * @return array<string, array{type: string, name: string}>
     */
    abstract public static function getFieldsMapping(): array;

    /**
     * Get relations to load - can be overridden by child classes
     *
     * @return array<string>
     */
    protected function getRelations(): array
    {
        return [
            'event',
            'account',
            'account.address',
            'account.phones',
            'account.mails',
            'eventGroup',
            'profile',
            'profile.establishment',
            'grantDeposit',
            'participationType',
            'transport',
        ];
    }

    /**
     * Build data row
     *
     * @return array<string, mixed>
     */
    abstract protected function buildDataRow(EventContact $row): array;

    /**
     * Export data based on format
     */
    public function export(): string
    {
        return match ($this->format) {
            'csv' => $this->exportToCsv(),
            default => $this->exportToXlsx(),
        };
    }

    /**
     * Get all sellable services for the event ordered by family position
     */
    protected function loadEventServices(): void
    {
        $event = \App\Models\Event::find($this->getEventId());
        if ( ! $event) {
            $this->eventServices = [];

            return;
        }

        // Get all sellable services for this event
        $sellableServices = $event
            ->sellableService()
            ->with(['group'])
            ->get();

        // Get event services to get the family positions
        $eventServices = $event
            ->services()
            ->get()
            ->keyBy('service_id');

        // Sort sellable services by their family position
        $sortedServices = $sellableServices->sort(function ($a, $b) use ($eventServices) {
            $aGroupId = $a->service_group;
            $bGroupId = $b->service_group;

            $aPosition = $aGroupId && isset($eventServices[$aGroupId])
                ? $eventServices[$aGroupId]->fo_family_position
                : PHP_INT_MAX;

            $bPosition = $bGroupId && isset($eventServices[$bGroupId])
                ? $eventServices[$bGroupId]->fo_family_position
                : PHP_INT_MAX;

            return $aPosition <=> $bPosition;
        });

        // Store as simple arrays with only needed data
        $this->eventServices = $sortedServices->map(function ($service) {
            return [
                'id'    => $service->id,
                'title' => $service->title,
            ];
        })->values()->toArray();
    }
    protected function prepareSheet($sheet): void
    {
        // Load all event services before processing
        $this->loadEventServices();

        // Prepare fields including service columns
        $allFields = $this->prepareFieldsWithServices();

        //--------------------------------------------
        // header
        //--------------------------------------------
        $columnIndex = 'A';
        foreach ($allFields as $field => $label) {
            $sheet->setCellValue($columnIndex.'1', $label);
            $sheet->getColumnDimension($columnIndex)->setAutoSize(true);
            $sheet->getStyle($columnIndex.'1')->getFont()->setBold(true);
            $columnIndex++;
        }

        // background color (only for Excel)
        if ($this->format === 'xlsx') {
            $lastHeaderColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($allFields));
            $sheet
                ->getStyle('A1:'.$lastHeaderColumn.'1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('D3D3D3');
        }

        //--------------------------------------------
        // body - fetch event contacts data
        //--------------------------------------------
        $data = $this->composeData();

        $rowIndex = 2;
        foreach ($data as $row) {
            $columnIndex = 'A';
            foreach ($allFields as $field => $label) {
                $value = $row[$field] ?? null;

                // Handle multi-line content in cells
                if (is_string($value) && str_contains($value, "\n")) {
                    // Enable text wrapping for cells with multi-line content
                    $sheet->getStyle($columnIndex.$rowIndex)->getAlignment()->setWrapText(true);
                }

                // IMPORTANT: Force RPPS to be treated as text
                if (in_array($field, ['phone', 'phone_2','rpps']) && $value !== null) {
                    // Use setCellValueExplicit to force string type
                    $sheet->setCellValueExplicit(
                        $columnIndex.$rowIndex,
                        $value,
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );
                } else {
                    // For all other fields, use normal setCellValue
                    $sheet->setCellValue($columnIndex.$rowIndex, $value);
                }

                $sheet
                    ->getStyle($columnIndex.$rowIndex)->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $columnIndex++;
            }
            $rowIndex++;
        }
    }

    /**
     * Prepare fields including service columns
     */
    protected function prepareFieldsWithServices(): array
    {
        $allFields = [];

        // Add fields in order, inserting service columns where 'services' field would be
        foreach ($this->fields as $field) {
            if ($field === 'services') {
                // Insert all service columns here instead of 'services'
                foreach ($this->eventServices as $service) {
                    $fieldKey             = 'service_'.$service['id'];
                    $allFields[$fieldKey] = $service['title'] ?? 'Service '.$service['id'];
                }
            } else {
                // Add all other fields normally
                $fieldLabel        = static::getFieldsMapping()[$field]['name'] ?? $field;
                $allFields[$field] = $fieldLabel;
            }
        }

        return $allFields;
    }

    /**
     * Fetch and prepare event contacts data
     *
     * @return array<int, array<string, mixed>>
     */
    protected function composeData(): array
    {
        // Build the query - always filter by event_id
        $query = EventContact::whereIn('user_id', $this->userIds)
            ->where('event_id', $this->getEventId())
            ->with($this->getRelations());

        $eventContacts = $query->get();

        $data = [];

        LazyCollection::make($eventContacts)->each(function (EventContact $row) use (&$data): void {
            if ( ! $row->account || ! $row->profile) {
                return;
            }

            $this->eventContactAccessor = new EventContactAccessor()->setEvent($this->event)->setAccount($row->account);
            $this->eventContactAccessor->setEventContact($row);

            $this->accountAccessor = new Accounts($row->account);
            $this->address         = $this->accountAccessor->billingAddress();

            // Process accommodation data
            $this->accommodationData = $this->processAccommodationData();

            // Process services data - this now includes individual columns AND caution_prestation_titles
            $this->serviceData = $this->processServiceDataWithColumns();

            // Build the data row using child class implementation
            $baseData = $this->buildDataRow($row);

            // Add service columns to the data row
            foreach ($this->eventServices as $service) {
                $fieldKey            = 'service_'.$service['id'];
                $baseData[$fieldKey] = $this->serviceData[$fieldKey] ?? 'Non';
            }

            $data[] = $baseData;
        });

        return $data;
    }

    /**
     * Process accommodation data
     *
     * @return array
     */
    /**
     * Process accommodation data - Fixed version to consolidate date ranges
     *
     * @return array
     */
    protected function processAccommodationData(): array
    {
        if (!$this->eventContactAccessor) {
            return [];
        }

        $accommodations = $this->eventContactAccessor->getAccommodationCheckIns();
        $hotelNames = [];
        $checkIns = [];
        $checkOuts = [];
        $accompagnants = [];
        $nbreAccompagnants = [];

        if ($accommodations->isNotEmpty()) {
            // Group accommodations by hotel to consolidate date ranges
            $groupedByHotel = $accommodations->groupBy('hotel_name');

            foreach ($groupedByHotel as $hotelName => $hotelAccommodations) {
                // Sort by check-in date
                $sorted = $hotelAccommodations->sortBy('check_in_formatted');

                // Group consecutive dates
                $ranges = [];
                $currentRange = null;

                foreach ($sorted as $accommodation) {
                    $checkInDate = \Carbon\Carbon::createFromFormat('d/m/Y', $accommodation['check_in_formatted']);
                    $checkOutDate = \Carbon\Carbon::createFromFormat('d/m/Y', $accommodation['check_out_formatted']);

                    if (!$currentRange) {
                        // Start first range
                        $currentRange = [
                            'hotel_name' => $accommodation['hotel_name'],
                            'check_in' => $checkInDate,
                            'check_out' => $checkOutDate,
                            'accompagnants' => collect([$accommodation['accompagnant'] ?? '']),
                            'nbre_accompagnants' => collect([$accommodation['nbre_accompagnant'] ?? 0]),
                        ];
                    } else {
                        // Check if this accommodation is consecutive (check-in equals previous check-out)
                        if ($checkInDate->equalTo($currentRange['check_out'])) {
                            // Extend current range
                            $currentRange['check_out'] = $checkOutDate;
                            $currentRange['accompagnants']->push($accommodation['accompagnant'] ?? '');
                            $currentRange['nbre_accompagnants']->push($accommodation['nbre_accompagnant'] ?? 0);
                        } else {
                            // Save current range and start new one
                            $ranges[] = $currentRange;
                            $currentRange = [
                                'hotel_name' => $accommodation['hotel_name'],
                                'check_in' => $checkInDate,
                                'check_out' => $checkOutDate,
                                'accompagnants' => collect([$accommodation['accompagnant'] ?? '']),
                                'nbre_accompagnants' => collect([$accommodation['nbre_accompagnant'] ?? 0]),
                            ];
                        }
                    }
                }

                // Don't forget the last range
                if ($currentRange) {
                    $ranges[] = $currentRange;
                }

                // Add consolidated ranges to arrays
                foreach ($ranges as $range) {
                    $hotelNames[] = $range['hotel_name'];
                    $checkIns[] = $range['check_in']->format('d/m/Y');
                    $checkOuts[] = $range['check_out']->format('d/m/Y');

                    // Get unique accompagnants (remove empty strings and duplicates)
                    $uniqueAccompagnants = $range['accompagnants']
                        ->filter(fn($a) => !empty($a))
                        ->unique()
                        ->implode(', ');
                    $accompagnants[] = $uniqueAccompagnants;

                    // Sum total accompanying persons for this range
                    $totalAccompagnants = $range['nbre_accompagnants']
                        ->filter(fn($n) => is_numeric($n))
                        ->sum();
                    $nbreAccompagnants[] = $totalAccompagnants;
                }
            }
        }

        return [
            'hotel_names' => $hotelNames,
            'check_ins' => $checkIns,
            'check_outs' => $checkOuts,
            'accompagnants' => $accompagnants,
            'nbre_accompagnants' => $nbreAccompagnants,
        ];
    }

    /**
     * Process service data with individual columns
     *
     * @return array
     */
    protected function processServiceDataWithColumns(): array
    {
        if ( ! $this->eventContactAccessor) {
            return [];
        }

        $services                = $this->eventContactAccessor->getServiceItems();
        $serviceData             = [];
        $cautionPrestationTitles = [];

        // Build a lookup of services the EventContact has by title
        $contactServicesByTitle = [];

        foreach ($services as $service) {
            $title = $service['title'] ?? '';
            if ($title) {
                $contactServicesByTitle[$title] = true;
            }

            // Collect caution_prestation titles (services where deposit_paid is not 0)
            if (isset($service['deposit_paid']) && $service['deposit_paid'] != 0 && isset($service['title'])) {
                $cautionPrestationTitles[] = $service['title'];
            }
        }

        // Create columns for each event service
        foreach ($this->eventServices as $service) {
            $fieldKey     = 'service_'.$service['id'];
            $serviceTitle = $service['title'] ?? '';

            // Check if the EventContact has this service by matching the title
            $serviceData[$fieldKey] = isset($contactServicesByTitle[$serviceTitle]) ? 'Oui' : 'Non';
        }

        // Add the caution_prestation data
        $serviceData['caution_prestation_titles'] = $cautionPrestationTitles;

        return $serviceData;
    }

    /**
     * Process service data (legacy method - kept for backward compatibility)
     *
     * @return array
     */
    protected function processServiceData(): array
    {
        if ( ! $this->eventContactAccessor) {
            return [];
        }

        $services                = $this->eventContactAccessor->getServiceItems();
        $serviceTitles           = [];
        $cautionPrestationTitles = [];

        if ($services->isNotEmpty()) {
            foreach ($services as $service) {
                if (isset($service['title'])) {
                    $serviceTitles[] = $service['title'];
                }
                // For caution_prestation, get services where deposit_paid is not 0
                if (isset($service['deposit_paid']) && $service['deposit_paid'] != 0 && isset($service['title'])) {
                    $cautionPrestationTitles[] = $service['title'];
                }
            }
        }

        return [
            'service_titles'            => $serviceTitles,
            'caution_prestation_titles' => $cautionPrestationTitles,
        ];
    }

    /**
     * Alternative entry point for exportAccountProfiles
     */
    public function run(): array
    {
        if ( ! $this->getEventId()) {
            $this->responseError('L\'identifiant de l\'événement est requis pour l\'export.');

            return $this->fetchResponse();
        }

        if ($this->mode === 'all') {
            // Get all user IDs from event contacts for this specific event
            $query = EventContact::where('event_id', $this->getEventId());

            if ($this->group != 'all') {
                $query->whereIn('participation_type_id', ParticipationType::where('group', $this->group)->pluck('id'));
            }

            $userIds = $query->pluck('user_id')
                ->unique()
                ->toArray();
        } else {
            $userIds = $this->getSaneIds();
            if (empty($userIds)) {
                $this->responseError('Veuillez sélectionner au moins un contact à exporter.');

                return $this->fetchResponse();
            }
        }

        return $this->exportAccountProfilesByUserIds($userIds);
    }

    /**
     * Common export logic
     */
    private function exportAccountProfilesByUserIds(array $userIds): array
    {
        try {
            $fields = request('exportFields');
            $format = request('exportFormat', 'xlsx'); // Default to xlsx if not specified

            // Set instance variables
            $this->userIds = $userIds;
            $this->fields  = $fields;
            $this->format  = $format;

            // Export based on format
            $content = $this->export();

            $this->responseElement('file', base64_encode($content));

            // Generate filename with appropriate extension
            $extension = $format === 'csv' ? 'csv' : 'xlsx';
            $className = Str::snake((new \ReflectionClass($this))->getShortName());
            $filename  = $className.'_event_'.$this->getEventId().'_'.(new DateTime())->format('Y-m-d_H-i-s').'.'.$extension;

            $this->responseElement('filename', $filename);
            $this->responseElement('callback', 'handleExportDownload');

            return $this->fetchResponse();
        } catch (Throwable $e) {
            $this->responseException($e);

            return $this->fetchResponse();
        }
    }

    /**
     * Get sane IDs from request
     */
    private function getSaneIds(): array
    {
        return CsvHelper::csvToUniqueArray(request('ids'));
    }
}

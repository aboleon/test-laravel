<?php

declare(strict_types=1);

namespace App\Actions\EventManager\Program;

use App\Accessors\Accounts;
use App\Exports\Traits\ExportTrait;
use App\Models\EventManager\Program\EventProgramSession;
use DateTime;
use Illuminate\Support\LazyCollection;
use MetaFramework\Accessors\Countries;
use MetaFramework\Traits\Ajax;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Throwable;

class ExportProgramInterventionsAction
{
    use Ajax;
    use ExportTrait;
    private int $eventId = 0;
    private string $locale = 'fr';
    private string $format = 'xlsx';

    public static array $fieldsMapping = [
        'date' => 'Date',
        'salle' => 'Salle',
        'session' => 'Session',
        'session_starts' => 'Horaire début session',
        'session_ends' => 'Horaire fin session',
        'session_duration' => 'Durée',
        'session_type' => 'Type de session',
        'intervention_name' => 'Intitulé d\'intervention',
        'intervention_starts' => 'Horaire de début Intervention',
        'intervention_ends' => 'Horaire de fin intervention',
        'intervention_duration' => 'Durée',
        'orators' => 'Intervenants',
        'intervention_comment' => 'Commentaires intervention',
        'intervention_sponsor' => 'Sponsor intervention',
    ];

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

    protected function prepareSheet($sheet): void
    {
        //--------------------------------------------
        // header
        //--------------------------------------------
        $columnIndex = 'A';
        $fields = array_keys(self::$fieldsMapping);

        foreach ($fields as $field) {
            $fieldLabel = self::$fieldsMapping[$field];
            $sheet->setCellValue($columnIndex . '1', $fieldLabel);

            $sheet->getColumnDimension($columnIndex)->setAutoSize(true);
            $sheet->getStyle($columnIndex . '1')->getFont()->setBold(true);
            $columnIndex++;
        }

        // background color (only for Excel)
        if ($this->format === 'xlsx') {
            $lastHeaderColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($fields));
            $sheet->getStyle('A1:' . $lastHeaderColumn . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('D3D3D3');
        }

        //--------------------------------------------
        // body - fetch interventions data
        //--------------------------------------------
        $data = $this->fetchInterventionsData();

        $rowIndex = 2;
        foreach ($data as $row) {
            $columnIndex = 'A';
            foreach ($fields as $field) {
                $value = $row[$field] ?? null;

                // Special handling for orators field
                if ($field === 'orators' && is_array($value)) {
                    $oratorStrings = [];
                    foreach ($value as $orator) {
                        $oratorStrings[] = sprintf(
                            "%s %s (%s) - %s, %s - %s",
                            $orator['last_name'],
                            $orator['first_name'],
                            $orator['email'],
                            $orator['locality'] ?? '',
                            $orator['country'] ?? '',
                            $orator['participation_type']
                        );
                    }
                    $value = implode("\n", $oratorStrings);
                }

                // Handle multi-line content in cells
                if (is_string($value) && str_contains($value, "\n")) {
                    $sheet->getStyle($columnIndex . $rowIndex)->getAlignment()->setWrapText(true);
                }

                $sheet->setCellValue($columnIndex . $rowIndex, $value);
                $sheet->getStyle($columnIndex . $rowIndex)->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $columnIndex++;
            }
            $rowIndex++;
        }
    }

    /**
     * Fetch and prepare interventions data
     * @return array<int, array<string, mixed>>
     */
    protected function fetchInterventionsData(): array
    {
        $locale = $this->locale;
        $event_id = $this->eventId;

        $ec = EventProgramSession::query()
            ->whereHas('programDay', function ($query) use ($event_id) {
                $query->where('event_id', $event_id);
            })
            ->with([
                'programDay',
                'interventions',
                'room.place',
                'moderators',
                'sponsor',
                'type',
                'interventions.sponsor',
                'interventions.specificity',
                'interventions.orators.participationType',
                'interventions.orators.account',
                'interventions.orators.account.phones',
            ])
            ->get();

        $sessionTimes = $ec->mapWithKeys(function ($session) {
            $interventions = $session->interventions;

            if ($interventions->isEmpty()) {
                return [
                    $session->id => [
                        'start' => null,
                        'end'   => null,
                    ],
                ];
            }

            $sessionStart = $interventions->min('start');
            $sessionEnd   = $interventions->max('end');

            return [
                $session->id => [
                    'start' => \Carbon\Carbon::parse($sessionStart),
                    'end'   => \Carbon\Carbon::parse($sessionEnd),
                ],
            ];
        });

        $interventions = $ec->flatMap(function ($session) use ($locale, $sessionTimes) {
            return $session->interventions->map(function ($intervention) use ($session, $locale, $sessionTimes) {
                $sessionTime = $sessionTimes[$session->id];

                $intervention->session_date           = $session->programDay->getRawOriginal('datetime_start');
                $intervention->session_date_formatted = $session->programDay->datetime_start->format('d/m/Y');
                $intervention->session_starts         = $sessionTime['start'] ? $sessionTime['start']->format('H:i') : '';
                $intervention->session_ends           = $sessionTime['end'] ? $sessionTime['end']->format('H:i') : '';
                $intervention->session_duration       = $sessionTime['start'] && $sessionTime['end']
                    ? $sessionTime['start']->diffInMinutes($sessionTime['end'])
                    : 0;
                $intervention->translated_name        = $intervention->translation('name', $locale);
                $intervention->session_name           = $session->translation('name', $locale);
                $intervention->session_position       = $session->position;
                $intervention->room                   = $session->room->place->name.' > '.$session->room->translation('name', $locale);
                $intervention->intervention_position  = $intervention->position;
                $intervention->intervention_sponsor   = $intervention->sponsor?->translation('name', $locale);
                $intervention->session_type           = $session->type->translation('name', $locale);

                return $intervention;
            });
        })->sortBy('start')->values();

        $oratorsData = [];

        $interventions->each(function ($intervention) use (&$oratorsData) {
            $oratorsData[$intervention->id] = $intervention->orators->map(function ($orator) {
                $accountAccessor = new Accounts($orator->account);
                $address         = $accountAccessor->billingAddress();

                return [
                    'last_name'          => $orator->account->last_name ?? '',
                    'first_name'         => $orator->account->first_name ?? '',
                    'email'              => $orator->account->email,
                    'phone'              => $accountAccessor->defaultPhone('phone'),
                    'locality'           => $address?->locality,
                    'country'            => Countries::getCountryNameByCodeAndLocale($address->country_code) ?? '',
                    'participation_type' => $orator->participationType?->name ?? '',
                ];
            })->toArray();
        });

        $data = [];

        LazyCollection::make($interventions)->each(function ($row) use (&$data, $oratorsData) {
            $data[] = [
                'date'                  => $row->session_date_formatted,
                'salle'                 => $row->room,
                'session'               => $row->session_name,
                'session_starts'        => $row->session_starts,
                'session_ends'          => $row->session_ends,
                'session_duration'      => $row->session_duration,
                'session_type'          => $row->session_type,
                'intervention_name'     => $row->translated_name,
                'intervention_starts'   => $row->start->format('H:i'),
                'intervention_ends'     => $row->end->format('H:i'),
                'intervention_duration' => $row->start->diffInMinutes($row->end),
                'orators'               => $oratorsData[$row->id],
                'intervention_comment'  => $row->internal_comment,
                'intervention_sponsor'  => $row->intervention_sponsor,
            ];
        });

        return $data;
    }

    /**
     * Ajax entry point for export
     */
    public function run(): array
    {
        try {
            $eventId = (int) request('event_id');
            $locale = request('locale', 'fr');
            $format = request('export_format', 'xlsx');

            if (!$eventId) {
                $this->responseError('L\'identifiant de l\'événement est requis.');
                return $this->fetchResponse();
            }

            // Set instance variables
            $this->eventId = $eventId;
            $this->locale = $locale;
            $this->format = $format;

            // Export based on format
            $content = $this->export();

            $this->responseElement('file', base64_encode($content));

            // Generate filename with appropriate extension
            $extension = $format === 'csv' ? 'csv' : 'xlsx';
            $filename = 'export_interventions_' . (new DateTime())->format('Y-m-d_H-i-s') . '.' . $extension;

            $this->responseElement('filename', $filename);

            return $this->fetchResponse();

        } catch (Throwable $e) {
            $this->responseException($e);
            return $this->fetchResponse();
        }
    }
}

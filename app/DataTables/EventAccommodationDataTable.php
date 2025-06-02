<?php

namespace App\DataTables;

use App\Accessors\EventManager\Availability;
use App\Accessors\EventManager\Availability\AvailabilityRecap;
use App\DataTables\View\EventManagerAccommodationView;
use App\Models\Event;
use App\Models\EventManager\Accommodation;
use App\Traits\DataTables\Common;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventAccommodationDataTable extends DataTable
{
    use Common;

    public function __construct(public Event $event)
    {
        parent::__construct();
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check"><input type="checkbox" class="form-check-input row-checkbox" value="' . $row->id . '"></div>';
            })
            // À voir la pertinence de cette approche
            ->addColumn('stock', function ($event) {
                try {
                    $totalReserved = 0;
                    $totalBlocked = 0;
                    $totalContingent = 0;
                    $accommodation = Accommodation::find($event->id);
                    $availability = (new Availability())->setEventAccommodation($accommodation);

                    $getAvailability = $availability->getAvailability();
                    $availability_recap = new AvailabilityRecap($availability);

                    $_REQUEST['GLOBALS']['contingent_dates'] = $availability->get('contingent')
                        ? collect([array_key_first($availability->get('contingent')), array_key_last($availability->get('contingent'))])->map(fn($item) => Carbon::createFromFormat('Y-m-d', $item)->format('d/m/Y'))
                        : collect();

                    foreach ($availability->get('contingent', []) as $date => $contingent) {

                        $totalContingentForDate = array_sum($contingent);
                        $cumultatedAvailable = array_sum($getAvailability[$date]);
                        $totalContingent += $totalContingentForDate;
                        foreach ($contingent as $roomgroup => $total) {
                            $recap = $availability_recap->get($date, $roomgroup);
/*                            dump($roomgroup, $recap);*/

                            // Chambres réservées (confirmées - annulées - modifiées)
                            $reserved = $recap['confirmed']['total'] - $recap['cancelled']['total'] - $recap['amended']['total'];
                            // Chambres bloquées
                            $blocked = $recap['blocked']['total'];

                            $totalReserved += $reserved;
                            $totalBlocked += $blocked;
                        }
                    }

                    $totalOccupied = $totalReserved + $totalBlocked;
                    return $totalOccupied . '/' . $totalContingent;

                } catch (Exception $e) {
                    /*d($e->getMessage());*/
                    return 'N/A';
                }
            })
            ->addColumn('action', function ($data) {
                return view('events.manager.accommodation.datatable.action')->with([
                    'event' => $this->event,
                    'data' => $data,
                ])->render();
            })
            ->rawColumns(['action', 'checkbox', 'published']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventManagerAccommodationView $model): QueryBuilder
    {
        return $model->newQuery()->where('event_id', $this->event->id);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->setHtml('eventmanager-accommodation');
    }

    /**
     * Get the dataTable columns definition
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')->title('<div class="form-check"><input type="checkbox" class="form-check-input" id="datatable-select-all"/></div>')->orderable(false)->searchable(false)->width('50'),
            Column::make('name')->title('Hôtel'),
            Column::make('locality')->title('Ville'),
            Column::make('published')->title('En ligne'),
            Column::make('pec')->title('PEC'),
            Column::computed('stock')->title('Stock'),
            Column::computed('action')->addClass('text-end')->title('Actions')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventAccommodation_' . date('YmdHis');
    }
}

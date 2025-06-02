@php
    $error = $errors->any();
    $logs = \Spatie\Activitylog\Models\Activity::where('subject_type', 'App\Models\User')
            ->where('log_name', 'default')
            ->where('subject_id', $eventContact->user->id)
            ->get();
@endphp
<div class="tab-pane fade pt-4"
     id="historyc-tabpane"
     role="tabpanel"
     aria-labelledby="history-tabpane-tab">

        <header class="wg-card mfw-line-separator mb-3">
                <span class="mfw-badge mfw-bg-red float-end"
                      style="padding: 5px 10px 3px 11px;">{{ $logs->count() }}</span>
            <h4>Historique</h4>
        </header>
        @if ($logs->count() > 0)
            <table class="table table-compact">
                <thead>
                <tr>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Exécuté par</th>
                </tr>
                </thead>
                @foreach($logs as $log)
                    <tr>
                        <td>
                            {{ $log->description }}
                        </td>
                        <td>
                            {{ $log->created_at->format(config('app.date_display_format') . " H:i:s") }}
                        </td>
                        <td>
                            {{ optional($log->causer)->names() }}
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            <p>Aucun historique</p>
        @endif
    </div>

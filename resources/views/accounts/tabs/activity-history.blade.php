@php
    $error = $errors->any();
@endphp
<div class="tab-pane fade pt-4"
     id="activity-history"
     role="tabpanel"
     aria-labelledby="activity-history-tab">

    <header class="wg-card mfw-line-separator mb-3">
                <span class="mfw-badge mfw-bg-red float-end"
                      style="padding: 5px 10px 3px 11px;">{{ $activity_histories->count() }}</span>
        <h4>Historique</h4>
    </header>
    @if ($activity_histories->count() > 0)
        <table class="table table-compact">
            <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Par</th>
                <th>Action</th>
            </tr>
            </thead>
            @foreach($activity_histories as $activity_history)
                <tr>
                    <td>
                        {{ $activity_history->created_at->format(config('app.date_display_format')) }}
                    </td>
                    <td>
                        {{ $activity_history->created_at->format(" H:i:s") }}
                    </td>
                    <td>
                        {!! \App\Printers\ActivityPrinter::printCauser($activity_history) !!}
                    </td>
                    <td>
                        {!! $activity_history->description !!}
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p>Aucun historique</p>
    @endif
</div>

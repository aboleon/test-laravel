<div class="wg-card">
    <header class="mb-3">
            <span class="mfw-badge mfw-bg-red float-end"
                  style="padding: 5px 10px 3px 11px;">{{$deposits->count() ?? 0}}</span>
        <h4>Cautions</h4>
    </header>
    @if($deposits->count())
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Statut</th>
                <th>Total TTC</th>
                <th>Total HT</th>
                <th>Désignation</th>
            </tr>
        </thead>
        <tbody>
        @foreach($deposits as $deposit)
            <tr>
                <td>{{$deposit->date_fr}}</td>
                <td>{!! \App\Printers\Event\Deposit::printStatus($deposit) !!}</td>
                <td>{{\App\Printers\Event\Deposit::printTotalTtc($deposit)}}</td>
                <td>{{\App\Printers\Event\Deposit::printTotalNet($deposit)}}</td>
                <td>{{$deposit->shoppable_label}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <p>Pas de caution pour ce participant</p>
    @endif

    <div class="mfw-line-separator my-3"></div>

</div>

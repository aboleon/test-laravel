<!-- TODO: Implement the view for the accounting invoices export - Exemple below -->
{{-- <table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th>Contact</th>
            <th>Désignation</th>
            <th>Date</th>
            <th>N° Commande</th>
            <th>Total HT</th>
            <th>Total TTC</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{$row['beneficiary_name'] ?? 'N/A'}}</td>
                <td>{{$row['shoppable_label']}}</td>
                <td>{{$row['date_fr']}}</td>
                <td>{{$row['order_id']}}</td>
                <td>{{App\Printers\Event\Deposit::printTotalNet($row)}}</td>
                <td>{{App\Printers\Event\Deposit::printTotalTtc($row)}}</td>
                <td>{!! App\Printers\Event\Deposit::printStatus($row)!!}</td>
            </tr>
        @endforeach

    </tbody>
</table> --}}

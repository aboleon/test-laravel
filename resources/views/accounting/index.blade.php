<x-backend-layout>
    <x-slot name="header">
        <h2>
            Comptabilité
        </h2>
    
        <x-back.topbar.separator route-prefix="panel.accounting" />
    </x-slot>
    

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="shadow p-4 bg-body-tertiary rounded">

                    <!-- responses message -->
                    <x-mfw::response-messages />
                                        
                
                    <div class="card-body">
                        <!-- Date Filter -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Date de début</label>
                                    <input type="date" class="form-control" id="start_date" value="{{ $startDate->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Date de fin</label>
                                    <input type="date" class="form-control" id="end_date" value="{{ $endDate->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Invoices Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Factures</h4>
                                <div class="btn-group">
                                    <form action="{{ route('panel.accounting.export.invoices.pdf') }}" method="GET" class="d-inline">
                                        <input type="hidden" name="start_date" id="invoices_pdf_start_date" value="{{ $startDate->format('Y-m-d') }}">
                                        <input type="hidden" name="end_date" id="invoices_pdf_end_date" value="{{ $endDate->format('Y-m-d') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-file-pdf"></i> Export global tous événements PDF
                                        </button>
                                    </form>
                                    <form action="{{ route('panel.accounting.export.invoices.csv') }}" method="GET" class="d-inline">
                                        <input type="hidden" name="start_date" id="invoices_csv_start_date" value="{{ $startDate->format('Y-m-d') }}">
                                        <input type="hidden" name="end_date" id="invoices_csv_end_date" value="{{ $endDate->format('Y-m-d') }}">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-file-csv"></i> Export global tous événements CSV
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Credits Section -->
                        <div class="row">
                            <div class="col-12">
                                <h4>Avoirs</h4>
                                <div class="btn-group">
                                    <form action="{{ route('panel.accounting.export.credits.pdf') }}" method="GET" class="d-inline">
                                        <input type="hidden" name="start_date" id="credits_pdf_start_date" value="{{ $startDate->format('Y-m-d') }}">
                                        <input type="hidden" name="end_date" id="credits_pdf_end_date" value="{{ $endDate->format('Y-m-d') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-file-pdf"></i> Export global tous événements PDF
                                        </button>
                                    </form>
                                    <form action="{{ route('panel.accounting.export.credits.csv') }}" method="GET" class="d-inline">
                                        <input type="hidden" name="start_date" id="credits_csv_start_date" value="{{ $startDate->format('Y-m-d') }}">
                                        <input type="hidden" name="end_date" id="credits_csv_end_date" value="{{ $endDate->format('Y-m-d') }}">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-file-csv"></i> Export global tous événements CSV
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend-layout>



    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            function updateHiddenFields() {
                const startValue = startDate.value;
                const endValue = endDate.value;

                console.log(startValue, endValue);

                // Update invoices PDF form
                document.getElementById('invoices_pdf_start_date').value = startValue;
                document.getElementById('invoices_pdf_end_date').value = endValue;

                // Update invoices CSV form
                document.getElementById('invoices_csv_start_date').value = startValue;
                document.getElementById('invoices_csv_end_date').value = endValue;

                // Update credits PDF form
                document.getElementById('credits_pdf_start_date').value = startValue;
                document.getElementById('credits_pdf_end_date').value = endValue;

                // Update credits CSV form
                document.getElementById('credits_csv_start_date').value = startValue;
                document.getElementById('credits_csv_end_date').value = endValue;
            }

            // Initial update
            updateHiddenFields();

            startDate.addEventListener('change', updateHiddenFields);
            endDate.addEventListener('change', updateHiddenFields);
        });
    </script>
    @endpush
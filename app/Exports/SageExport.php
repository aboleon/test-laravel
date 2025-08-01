<?php

namespace App\Exports;

use App\Accessors\Dictionnaries;
use App\Accessors\OrderAccessor;
use App\DataTables\View\OrderPaymentView;
use App\Enum\OrderClientType;
use App\Models\EventManager\Accommodation;
use App\Models\EventManager\Accommodation\Contingent;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\Order\Cart\TaxRoomCart;
use App\Traits\Models\EventModelTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use MetaFramework\Accessors\Prices;
use MetaFramework\Accessors\VatAccessor;
use MetaFramework\Traits\Ajax;
use ZipArchive;

class SageExport
{
    use Ajax;
    use EventModelTrait;

    protected array $sellablesData = [];
    protected array $invoicesData = [];
    protected array $paymentsData = [];
    protected array $exportFiles = [];

    public string $dateStart = '';
    public string $dateEnd = '';

    private array $rooms = [];
    private array $contingents = [];
    private array $contingentConfigs = [];
    private array $accommodationLineSageCode = [];
    private array $roomTypes = [];

    private string $sageAnalyticsCode = '';
    private string $sageYear = '';
    private string $sageEventAcronym = '';


    public function run(): self
    {
        $this->setupDataQuery();

        if ($this->hasErrors()) {
            return $this;
        }

        return $this
            ->generateData()
            ->exportAsTxt()
            ->asArchive();
    }

    /**
     * Generate all export data
     */
    protected function generateData(): self
    {
        $this->exportSellables();
        $this->exportInvoices();
        $this->exportPayments();

        return $this;
    }

    /**
     * Export sellables data
     */
    protected function exportSellables(): void
    {
        $this->sellablesData = [];

        $compteComptables = [];
        $compteVAT        = [];

        // Export regular sellable services
        foreach ($this->event->sellableService as $article) {
            if ( ! array_key_exists($article->service_group, $compteComptables)) {
                $compteComptables[$article->service_group] = $article->getSageAccountCode();
            }
            if ( ! array_key_exists($article->service_group, $compteVAT)) {
                $compteVAT[$article->service_group] = $article->getSageVatAccount();
            }

            $this->sellablesData[] = [
                'nom_article'      => $article->title,
                'code_article'     => $this->getEventSageCode().$article->getSageDatabaseId().$article->getSageReferenceValue(),
                'code_analytique'  => $this->sageAnalyticsCode,
                'compte_comptable' => $compteComptables[$article->service_group],
                'compte_tva'       => $compteVAT[$article->service_group],
            ];
        }

        // Export accommodation items (ContingentConfig for each room)
        foreach ($this->event->accommodation as $accommodation) {
            // Load contingents with configs
            $contingents = $accommodation->contingent;

            $accommodatioSageAccount = $accommodation->getSageReferenceValue(Accommodation::SAGEACCOUNT);
            $accommodationVatAccount = $accommodation->getSageReferenceValue(Accommodation::SAGEVAT);
            $hotelName               = $accommodation->hotel->name ?? '';

            foreach ($contingents as $contingent) {
                foreach ($contingent->configs as $config) {
                    if ($config->rooms) {
                        $roomName              = $this->getRoom($config->rooms->room_id);
                        $this->sellablesData[] = [
                            'nom_article'      => "Hébergement - {$hotelName} - {$roomName} x {$config->rooms->capacity}",
                            'code_article'     => $this->getEventSageCode().$config->getSageDatabaseId().$config->getSageReferenceValue(),
                            'code_analytique'  => $this->sageAnalyticsCode,
                            'compte_comptable' => $accommodatioSageAccount,
                            'compte_tva'       => $accommodationVatAccount,
                        ];
                    }
                }
            }

            $this->sellablesData[] = [
                'nom_article'      => "Frais de dossier - {$hotelName}",
                'code_article'     => $this->getEventSageCode().$accommodation->getSageDatabaseId().$accommodation->getSageReferenceValue(Accommodation::SAGETAXROOM),
                'code_analytique'  => $this->sageAnalyticsCode,
                'compte_comptable' => $accommodatioSageAccount,
                'compte_tva'       => $accommodationVatAccount,
            ];
        }
    }

    /**
     * Export invoices data
     */
    protected function exportInvoices(): void
    {
        $this->invoicesData = [];

        // Load invoices with necessary relations
        $invoices = $this->event->invoices();
        if ($this->dateStart) {
            $invoices->whereBetween('order_invoices.created_at', [$this->dateStart, $this->dateEnd]);
        }

        $invoices = $invoices->with(
            'order',
            'order.accommodation',
            'order.accommodation.room',
            'order.taxRoom',
            'order.taxRoom.room',
            'order.services',
            'order.invoiceable',
            'order.invoiceable.account',
            'order.suborders',
            'order.suborders.invoiceable',
            'order.suborders.accommodation',
            'order.suborders.accommodation.room',
            'order.suborders.services',
            'order.suborders.taxRoom',
            'order.suborders.taxRoom.room',
            'order.suborders.invoiceable.account',
        )->get();

        // Load services and accommodations
        $services = $this->event->sellableService;
        if (!$services->first()?->relationLoaded('sageData')) {
            $services->load('sageData', 'group');
        }
        $hotels = $this->event->accommodation->mapWithKeys(fn($item) => [
            $item->id => $item->hotel->name.' '.($item->hotel->stars ? $item->hotel->stars.'*' : '').$item->title,
        ])->toArray();

        foreach ($invoices as $invoice) {
            $orderAccessor = new OrderAccessor($invoice->order);

            // Add invoice header line (Type E)
            $this->invoicesData[] = [
                'Type_de_Ligne'   => 'E',
                'Type_pièce'      => 'Facture',
                'Numéro'          => $invoice->created_at->toDateString().'-'.$invoice->id,
                'Date'            => $invoice->created_at->format('d/m/Y'),
                'Code_cli'        => '', // TODO: Add customer code if needed
                'Nom_Cli'         => $invoice->order->invoiceable->account->names(),
                'Code_article'    => '',
                'Libelle_Article' => '',
                'Quantité'        => '',
                'PU_HT'           => '',
                'Taux_TVA'        => '',
            ];

            // Handle different order types
            if (in_array($invoice->order->client_type, [OrderClientType::CONTACT->value, OrderClientType::ORATOR->value])) {
                // Individual orders - Services
                if ($orderAccessor->serviceCart()) {
                    foreach ($orderAccessor->serviceCart() as $shoppable) {
                        $sellable = $services->where('id', $shoppable->service_id)->first();
                        $this->addServiceLine($sellable, $shoppable, $orderAccessor);
                    }
                }

                // Accommodations
                if ($orderAccessor->accommodationCart()) {
                    foreach ($orderAccessor->accommodationCart() as $shoppable) {
                        $this->addAccommodationLine($shoppable, $hotels, $orderAccessor);
                    }
                }

                // Tax room (processing fees)
                if ($orderAccessor->taxRoomCart()) {
                    foreach ($orderAccessor->taxRoomCart() as $shoppable) {
                        $this->addTaxRoomLine($shoppable, $hotels, $orderAccessor);
                    }
                }
            } elseif (OrderClientType::GROUP->value === $invoice->order->client_type) {
                // Group orders - check if it's a front group order or back office group order
                if ($orderAccessor->isNotFrontGroupOrder()) {
                    // Back office group order - process carts directly

                    // Services
                    foreach ($orderAccessor->serviceCart() as $shoppable) {
                        $sellable = $services->where('id', $shoppable->service_id)->first();
                        $this->addServiceLine($sellable, $shoppable, $orderAccessor);
                    }

                    // Accommodations
                    foreach ($orderAccessor->accommodationCart() as $shoppable) {
                        $this->addAccommodationLine($shoppable, $hotels, $orderAccessor);
                    }

                    // Tax room
                    if ($orderAccessor->taxRoomCart()) {
                        foreach ($orderAccessor->taxRoomCart() as $shoppable) {
                            $this->addTaxRoomLine($shoppable, $hotels, $orderAccessor);
                        }
                    }
                } else {
                    // Front group order - process suborders
                    $suborders = $invoice->order->suborders->load('services', 'accommodation', 'account', 'taxRoom');

                    // Process services from suborders
                    foreach ($suborders as $suborder) {
                        foreach ($suborder->services as $cart) {
                            $sellable = $services->where('id', $cart->service_id)->first();
                            $this->addServiceLine($sellable, $cart, $orderAccessor);
                        }
                    }

                    // Process accommodations from suborders
                    foreach ($suborders as $suborder) {
                        foreach ($suborder->accommodation as $cart) {
                            $this->addAccommodationLine($cart, $hotels, $orderAccessor);
                        }
                    }

                    // Process tax room from suborders
                    foreach ($suborders as $suborder) {
                        foreach ($suborder->taxRoom as $cart) {
                            $this->addTaxRoomLine($cart, $hotels, $orderAccessor);
                        }
                    }
                }
            }
        }
    }

    /**
     * Add service line to invoice data
     */
    private function addServiceLine($sellable, $cart, $orderAccessor): void
    {
        $this->invoicesData[] = [
            'Type_de_Ligne'   => 'L',
            'Type_pièce'      => '',
            'Numéro'          => '',
            'Date'            => '',
            'Code_cli'        => '',
            'Nom_Cli'         => '',
            'Code_article'    => $sellable ? $this->getEventSageCode().$sellable->getSageDatabaseId().$sellable->getSageReferenceValue() : '',
            'Libelle_Article' => $sellable?->title ?? 'NC',
            'Quantité'        => $cart->quantity,
            'PU_HT'           => $orderAccessor->isOrator() ?
                '0.00'
                : number_format(VatAccessor::netPriceFromVatPrice($cart->unit_price, $cart->vat_id), 2, '.', ''),
            'Taux_TVA'        => Prices::readableFormat(VatAccessor::fetchVatRate($cart->vat_id ?? $sellable->vat_id), currency: '', decimal_separator: '.'),
        ];
    }

    private function getRoom(int $id): string
    {
        if (array_key_exists($id, $this->rooms)) {
            return $this->rooms[$id];
        }

        $this->rooms[$id] = Dictionnaries::entry('type_chambres', $id)->name ?: 'Chambre';

        return $this->rooms[$id];
    }

    /**
     * Add accommodation line to invoice data
     */
    private function addAccommodationLine(AccommodationCart $cart, $hotels, $orderAccessor): void
    {
        $hotelName = $hotels[$cart->event_hotel_id] ?? '';
        $roomType  = $this->getRoom($cart->room->room_id);

        // Fetch the ContingentConfig for the Sage code
        if (!array_key_exists($cart->room_id, $this->accommodationLineSageCode)) {
            $this->accommodationLineSageCode[$cart->room_id] = $this->getAccommodationSageCode($cart);
        }

        $this->invoicesData[] = [
            'Type_de_Ligne'   => 'L',
            'Type_pièce'      => '',
            'Numéro'          => '',
            'Date'            => '',
            'Code_cli'        => '',
            'Nom_Cli'         => '',
            'Code_article'    => $this->accommodationLineSageCode[$cart->room_id],
            'Libelle_Article' => "Hébergement - {$cart->date->format('d/m/Y')} - {$hotelName} - {$roomType}",
            'Quantité'        => $cart->quantity,
            'PU_HT'           => $orderAccessor->isOrator() ?
                '0.00'
                : number_format($cart->total_net, 2, '.', ''),
            'Taux_TVA'        => Prices::readableFormat(VatAccessor::fetchVatRate($cart->vat_id), currency: '', decimal_separator: '.'),
        ];
    }

    /**
     * Get Sage code for accommodation through the relationship chain
     * cart->room_id -> ContingentConfig via contingent_id -> Contingent via event_accommodation_id
     */
    private function getAccommodationSageCode($cart): string
    {
        if ( ! $cart->room_id || ! $cart->event_hotel_id) {
            return '';
        }

        // Find the contingent config through the relationship chain
        // First, find all contingents for this accommodation
        if ( ! array_key_exists($cart->event_hotel_id, $this->contingents)) {
            $this->contingents[$cart->event_hotel_id.'_'.$cart->date] = Contingent::where('event_accommodation_id', $cart->event_hotel_id)
                ->where('date', $cart->date)
                ->get();
        }


        // Then find the ContingentConfig that matches our room_id
        foreach ($this->contingents[$cart->event_hotel_id.'_'.$cart->date] as $contingent) {

            if (!array_key_exists($cart->event_hotel_id.'_'.$cart->date.'_'.$cart->room_id, $this->contingentConfigs)) {
                $this->contingentConfigs[$cart->event_hotel_id.'_'.$cart->date.'_'.$cart->room_id] = $contingent
                    ->configs()
                    ->where('room_id', $cart->room_id)
                    ->first();
            }

            $config = $this->contingentConfigs[$cart->event_hotel_id.'_'.$cart->date.'_'.$cart->room_id];

            if ($config) {
                return $this->getEventSageCode().$config->getSageDatabaseId().$config->getSageReferenceValue();
            }
        }

        return '';
    }

    /**
     * Add tax room line (processing fees) to invoice data
     */
    private function addTaxRoomLine(TaxRoomCart $cart, $hotels): void
    {
        $hotelName = $hotels[$cart->event_hotel_id] ?? '';
        $roomType  = $this->getRoom($cart->room->room_id);

        $this->invoicesData[] = [
            'Type_de_Ligne'   => 'L',
            'Type_pièce'      => '',
            'Numéro'          => '',
            'Date'            => '',
            'Code_cli'        => '',
            'Nom_Cli'         => '',
            'Code_article'    => '', // TODO: Add tax room Sage code if needed
            'Libelle_Article' => "Frais de dossier Hébergement - {$hotelName} - {$roomType}",
            'Quantité'        => $cart->quantity ?? 1,
            'PU_HT'           => number_format($cart->amount_net, 2, '.', ''),
            'Taux_TVA'        => Prices::readableFormat(VatAccessor::fetchVatRate($cart->vat_id), currency: '', decimal_separator: '.'),
        ];
    }

    /**
     * Export payments data
     */
    protected function exportPayments(): void
    {
        $payments = OrderPaymentView::where('event_id', $this->event->id);
        if ($this->dateStart) {
            $payments->whereBetween('date', [$this->dateStart, $this->dateEnd]);
        }
        $payments = $payments->orderBy('id')->get();

        $bankAccount = $this->event->bankAccount->load('sageData');

        $sageAccount        = $bankAccount ? $bankAccount->getSageReferenceValue($bankAccount::SAGEACCOUNT) : $bankAccount::SAGEUNKNOWN;
        $this->paymentsData = [];

        foreach ($payments as $payment) {
            $this->paymentsData[] = [
                'date_reglement'   => $payment->date_formatted,
                'numero_cb'        => $payment->card_number,
                'compte_comptable' => $sageAccount,
                'montant_ttc'      => $payment->amount,
            ];
        }
    }

    /**
     * Export data as tab-separated text files
     */
    protected function exportAsTxt(): self
    {
        $timestamp = date('Ymd_His');
        $eventCode = $this->event->texts->subname ?? 'event_'.$this->event->id;

        // Export sellables if data exists
        if ( ! empty($this->sellablesData)) {
            $filename = "sage_articles_{$eventCode}_{$timestamp}.txt";
            $content  = $this->generateTxtContent($this->sellablesData);
            $this->saveFile($filename, $content);
            $this->exportFiles['articles'] = $filename;
        }

        // Export invoices if data exists
        if ( ! empty($this->invoicesData)) {
            $filename = "sage_factures_{$eventCode}_{$timestamp}.txt";
            $content  = $this->generateTxtContent($this->invoicesData);
            $this->saveFile($filename, $content);
            $this->exportFiles['factures'] = $filename;
        }

        // Export payments if data exists
        if ( ! empty($this->paymentsData)) {
            $filename = "sage_reglements_{$eventCode}_{$timestamp}.txt";
            $content  = $this->generateTxtContent($this->paymentsData);
            $this->saveFile($filename, $content);
            $this->exportFiles['reglements'] = $filename;
        }

        return $this;
    }

    /**
     * Generate tab-separated content from data array
     */
    protected function generateTxtContent(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        // Get headers from first row
        $headers = array_keys($data[0]);
        $content = implode("\t", $headers)."\n";

        // Add data rows
        foreach ($data as $row) {
            $values = array_map(function ($value) {
                // Clean value and replace tabs with spaces
                return str_replace("\t", " ", $value ?? '');
            }, array_values($row));

            $content .= implode("\t", $values)."\n";
        }

        return $content;
    }

    /**
     * Save file to storage
     */
    protected function saveFile(string $filename, string $content): void
    {
        $path = 'exports/sage/'.$filename;
        Storage::disk('local')->put($path, $content);
    }

    /**
     * Create archive and prepare for download
     */
    protected function asArchive(): self
    {
        if (empty($this->exportFiles)) {
            $this->responseWarning("Aucune donnée à exporter.");

            return $this;
        }

        $timestamp   = date('Ymd_His');
        $eventCode   = $this->event->texts->subname ?? 'event_'.$this->event->id;
        $zipFilename = "sage_export_{$eventCode}_{$timestamp}.zip";
        $zipPath     = storage_path('app/exports/sage/'.$zipFilename);

        // Ensure directory exists
        if ( ! file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        // Create ZIP archive
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($this->exportFiles as $type => $filename) {
                $filePath = storage_path('app/exports/sage/'.$filename);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $filename);
                }
            }
            $zip->close();
        } else {
            $this->responseError("Impossible de créer l'archive ZIP.");

            return $this;
        }

        // Clean up individual files after zipping
        foreach ($this->exportFiles as $filename) {
            $filePath = storage_path('app/exports/sage/'.$filename);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Prepare download response for AJAX
        $this->responseSuccess("Export Sage généré avec succès. Téléchargement en cours...");

        // Set up auto-download
        $downloadUrl = route('panel.download.file', [
            'path'     => 'exports/sage/'.$zipFilename,
            'filename' => $zipFilename,
        ]);

        // These elements will trigger the download in your AJAX handler
        $this->responseElement('download_url', $downloadUrl);
        $this->responseElement('auto_download', true);

        return $this;
    }

    private function setupDataQuery(): self
    {
        if ( ! request()->filled('sage.event')) {
            $this->responseWarning("Vous n'avez pas sélectionné un évènement.");

            return $this;
        }

        $request_event_id = (int)request('sage.event');
        $this->setEvent($request_event_id);

        if ( ! $this->event) {
            $this->responseWarning("Impossible de récupérer un évènement avec l'ID ".$request_event_id);

            return $this;
        }

        // Dates

        if (request()->filled('sage.start')) {
            $this->dateStart = Carbon::createFromFormat('d/m/Y', request('sage.start'))->toDateString();
        }

        if (request()->filled('sage.end')) {
            $this->dateEnd = Carbon::createFromFormat('d/m/Y', request('sage.end'))->toDateString();
        }

        if ($this->dateStart && ! $this->dateEnd or $this->dateEnd && ! $this->dateStart) {
            $this->responseWarning("Les deux dates doivent être renseignées.");
        }

        $this->event->load(
            'sellableService.sageData',
            'sellableService.group.sageData',
            'accommodation.hotel',
            'accommodation.sageData',
            'accommodation.contingent.configs.sageData',
            'accommodation.contingent.configs.rooms'
        );

        $this->sageAnalyticsCode = $this->event->getSageAnalyticsCode();
        $this->sageYear          = $this->event->getSageYear();
        $this->sageEventAcronym  = $this->event->getSageEventAcronym();

        return $this;
    }

    private function getEventSageCode(): string
    {
        return $this->sageEventAcronym.$this->sageYear;
    }

    /* TEST METHODS*/
    public function getTestExportData(): array
    {
        //$this->setupDataQuery();

       // $this->exportInvoices();
        // return $this->paymentsData;
    }
}

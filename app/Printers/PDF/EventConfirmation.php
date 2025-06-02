<?php

namespace App\Printers\PDF;


use App\Accessors\Accounts;
use App\Accessors\EventContactAccessor;
use App\Accessors\Order\Orders;
use App\Accessors\OrderAccessor;
use App\Models\EventContact;
use App\Models\Order;
use App\Traits\EventCommons;
use App\Traits\EventSignatures;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use MetaFramework\Traits\DomPdf;

class EventConfirmation
{
    use DomPdf;
    use EventCommons;
    use EventSignatures;

    private bool $isReceipt = false;
    private array $data = [];

    private ?Collection $orders = null;
    private Collection $paid_services;
    private Collection $unpaid_services;

    private Collection $paid_hotels;
    private Collection $unpaid_hotels;
    private array $hotels = [];

    private ?Order $order = null;

    private ?EventContact $eventContact = null;

    public function __construct(public string $identifier)
    {
        $this->paid_services   = collect();
        $this->unpaid_services = collect();
        $this->paid_hotels     = collect();
        $this->unpaid_hotels   = collect();
        $this->setData();
        $this->pdf = Pdf::loadView('pdf.event-confirmation', $this->data);
    }

    public function setData(): void
    {
        $this->eventContact = EventContact::where('uuid', '=', $this->identifier)->with('account')->first();

        if ( ! $this->eventContact) {
            abort(404, "Event not found with uuid ".$this->identifier);
        }

        $accountAccessor = new Accounts($this->eventContact->account);
        App::setLocale($accountAccessor->getLocale());
        $this->event          = $this->eventContact?->event->load('adminSubs');
        $this->orders         = Orders::getEventDashboardOrders($this->event, $this->eventContact)->groupBy('status');
        $eventContactAccessor = (new EventContactAccessor())
            ->setEventContact($this->eventContact)
            ->setEvent($this->event);

        $order_paid   = $this->orders['paid'] ?? collect();
        $order_unpaid = $this->orders['unpaid'] ?? collect();

        foreach ($order_paid as $paid) {
            $accessor = new OrderAccessor($paid);
            $services = $accessor->serviceCartLeft();
            $hotels   = $accessor->accommodationCartLeft();

            if ($services->count()) {
                $this->paid_services[] = $services;
            }
            if ($hotels->count()) {
                $this->paid_hotels[] = $hotels;
            }
        }

        foreach ($order_unpaid as $unpaid) {
            $accessor = new OrderAccessor($unpaid);
            $services = $accessor->serviceCartLeft();
            $hotels   = $accessor->accommodationCartLeft();

            if ($services?->count()) {
                $this->unpaid_services[] = $services;
            }
            if ($hotels->count()) {
                $this->unpaid_hotels[] = $hotels;
            }
        }

        $this->order = new Order();
        $attributed  = $eventContactAccessor->getAttributionSummary()->groupBy('paid_by');

        $this->data = [
            'order'           => $this->order,
            'banner'          => $this->getBanner($this->event,'thumbnail'),
            'event'           => $this->event,
            'event_name'      => $this->event->texts->name,
            'eventContact'    => $this->eventContact,
            'names'           => $this->eventContact->account->names(),
            'address'         => '',
            'services'        => $this->event->sellableService->load('event.services'),
            'hotels'          => $this->event->accommodation->load('hotel')->mapWithKeys(fn($item) => [$item->id => $item->hotel->name.' '.($item->hotel->stars ? $item->hotel->stars.'*' : '').$item->title])->toArray(),
            'paid_services'   => $this->paid_services->flatten(),
            'unpaid_services' => $this->unpaid_services->flatten(),
            'paid_hotels'     => $this->paid_hotels->flatten(),
            'unpaid_hotels'   => $this->unpaid_hotels->flatten(),
            'attributed'      => $attributed,
        ];

        $this->data['letter'] = $this->letter()[$accountAccessor->getLocale()];
    }

    public function __invoke(): Response
    {
        if (request()->has('download')) {
            return $this->download("Confirmation.pdf");
        }

        return $this->stream();
    }

    public function letter(): array
    {
        return [
            'fr' => [
                'title'              => 'Confirmation',
                'body'              => "Bonjour ".$this->data['names'].",<br /><br /><p>Nous vous remercions pour votre/(vos) inscription(s) au ".$this->data['event_name'].".</p>
<p>Veuillez trouver ci-dessous les prestations que vous avez sélectionnées.</p>
<p>Nous vous prions de conserver ce document dans vos mails, il pourra vous être demandé par l’hôtesse d’accueil afin de récupérer votre badge.</p>
<p>Avant le congrès, vous recevrez une newsletter avec les horaires de l'accueil et toutes les informations pratiques.</p>
<p>Nous vous remercions de bien vouloir vérifier vos informations et revenir vers nous pour toute correction à apporter.</p>
<p>Au plaisir de vous accueillir, vous et/ou vos invités.</p><br>
Bien cordialement,<br /><br>
Responsable inscription<br>".$this->adminSignature()."<br><br>
Responsable grants<br>".$this->adminGrantSignature(),
                'product_name'       => 'Désignation',
                'paid_title'         => '<strong>Prestation(s) soldée(s)</strong>',
                'unpaid_title'       => '<strong>Prestation(s) non soldée(s) et non garantie(s)</strong>',
                'title_front'        => 'Confirmation d\'inscription',
                'order_by'           => 'Commandé par ',
                'attributed_content' => 'Contenu',
            ],
            'en' => [
                'title'              => 'Confirmation',
                'body'              => "Hello ".$this->data['names'].",<br /><br /><p>Thank you for registering for ".$this->data['event_name'].".</p>
<p>Please find below the services you have selected.</p>
<p>Please keep this document in your emails, as it may be requested by the hostess to collect your badge.</p>
<p>Prior to the congress, you will receive a newsletter with opening hours and practical information.</p>
<p>Please check your information and come back to us if you need to make any corrections.</p>
<p>We look forward to welcoming you and/or your guests.</p><br>
Best regards,<br /><br>
Registration coordinator<br>".$this->adminSignature()."<br><br>
Grant coordinator<br>".$this->adminGrantSignature(),
                'product_name' => 'Designation',
                'paid_title' => '<strong>Service(s) paid</strong>',
                'unpaid_title' => '<strong>Unpaid and unguaranteed service(s).</strong><br />Availability subject to full payment prior to the conference',
                'order_by' => 'Ordered by ',
                'attributed_content' => 'Content',
                'title_front'        => 'Register confirmation',
            ],
        ];
    }
}

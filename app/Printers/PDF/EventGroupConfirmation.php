<?php

namespace App\Printers\PDF;


use App\Accessors\Accounts;
use App\Accessors\EventManager\EventGroups;
use App\Accessors\GroupAccessor;
use App\Models\EventManager\EventGroup;
use App\Models\Order;
use App\Printers\Groups;
use App\Traits\EventCommons;
use App\Traits\EventSignatures;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use MetaFramework\Traits\DomPdf;

class EventGroupConfirmation
{
    use DomPdf;
    use EventCommons;
    use EventSignatures;

    private bool $isReceipt = false;
    private array $data = [];

    private ?Collection $orders = null;

    private ?Collection $eventContacts = null;
    private array $hotels = [];

    private ?Order $order = null;
    private ?EventGroup $eventGroup = null;

    public function __construct(public string $identifier)
    {
        $this->setData();
        $this->pdf = Pdf::loadView('pdf.event-group-confirmation', $this->data);
    }

    public function setData(): void
    {
        $id = decrypt($this->identifier);
        $documentTtitle = __('front/event.confirmation.title');
        $this->eventGroup = EventGroup::where('id', $id)->with(['group', 'event', 'mainContact'])->first();

        if (!$this->eventGroup) {
            abort(404, "EventGroup not found with id " . $id);
        }

        $this->event = $this->eventGroup->event->load(['adminSubs', 'texts']);
        $eventGroupAccessor = (new EventGroups)->setEventGroup($this->eventGroup)->setEvent($this->event);
        $mainAddress = (new GroupAccessor($this->eventGroup->group))->billingAddress();
        //implode('<br>', $this->orderAccessor->invoiceableAddress())

        $address = isset($mainAddress) ? Groups::generateTextAddress($mainAddress) : '';

        $attributedEventContacts = $eventGroupAccessor->getEventContacts()
            ->load(['attributions', 'account', 'accommodationAttributionsRelation.order.amendedOrder', 'serviceAttributions.service', 'accommodationAttributionsRelation.room.group.accommodation.hotel'])
            ->filter(fn($contact) => $contact->attributions->isNotEmpty())
            ->sortBy(fn($contact) => $contact->account->last_name ?? '');

        if(request('contacts')){
            $contactIds = explode(',', request('contacts'));
            $attributedEventContacts = $attributedEventContacts->whereIn('id', $contactIds);
        }

        $mainContact = $this->eventGroup->mainContact;
        $accountAccessor = new Accounts($mainContact->account);
        App::setLocale($accountAccessor->getLocale());

        $this->data = [
            'documentTitle' => $documentTtitle,
            'banner' => $this->getBanner($this->event,'thumbnail'),
            'event' => $this->event,
            'group' => $this->eventGroup->group,
            'address' => str_replace(',',  '<br />', $address),
            'event_name' => $this->event->texts->name,
            'attributedEventContacts' => $attributedEventContacts,
            'services' => $this->event->sellableService->load('event.services'),
            'hotels' => $this->event->accommodation->load('hotel')->mapWithKeys(fn($item) => [$item->id => $item->hotel->name . ' ' . ($item->hotel->stars ? $item->hotel->stars . '*' : '') . $item->title])->toArray(),
        ];

        $this->data['letter'] = $this->letter()[app()->getLocale()];
    }

    public function __invoke(): Response
    {
        if (request()->has('download')) {
            return $this->download("confirmation_groupe.pdf");
        }

        return $this->stream();
    }

    public function letter(): array
    {
        return [
            'fr' => [
                'body'              => "Bonjour,<br /><br /><p>Nous vous remercions pour votre/(vos) inscription(s) au ".$this->data['event_name'].".</p>
<p>Veuillez trouver ci-dessous les prestations que vous avez sélectionnées.</p>
<p>Nous vous prions de conserver ce document dans vos mails, il pourra vous être demandé par l’hôtesse d’accueil afin de récupérer votre badge.</p>
<p>Avant le congrès, vous recevrez une newsletter avec les horaires de l'accueil et toutes les informations pratiques.</p>
<p>Nous vous remercions de bien vouloir vérifier vos informations et revenir vers nous pour toute correction à apporter.</p>
<p>Au plaisir de vous accueillir, vous et/ou vos invités.</p><br>
Bien cordialement,<br /><br>
Responsable inscription<br>".$this->adminSignature()."<br><br>
Responsable grants<br>".$this->adminGrantSignature(),
                'product_name'       => 'Désignation',
            ],
            'en' => [
                'body'              => "Hello,<br /><br /><p>Thank you for registering for ".$this->data['event_name'].".</p>
<p>Please find below the services you have selected.</p>
<p>Please keep this document in your emails, as it may be requested by the hostess to collect your badge.</p>
<p>Prior to the congress, you will receive a newsletter with opening hours and practical information.</p>
<p>Please check your information and come back to us if you need to make any corrections.</p>
<p>We look forward to welcoming you and/or your guests.</p><br>
Best regards,<br /><br>
Registration coordinator<br>".$this->adminSignature()."<br><br>
Grant coordinator<br>".$this->adminGrantSignature(),
                'product_name' => 'Designation',
            ],
        ];
    }
}

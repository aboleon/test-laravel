<?php

namespace App\Actions\Account;

use App\Accessors\Accounts;
use App\Accessors\EventContactAccessor;
use App\Accessors\ParticipationTypes;
use App\Enum\ParticipantType;
use App\Events\EventContactCreated;
use App\Helpers\CsvHelper;
use App\Http\Controllers\MailController;
use App\Http\Requests\DynamicMailTemplateRequest;
use App\Http\Requests\ModalTemplateRequest;
use App\MailTemplates\Models\MailTemplate;
use App\MailTemplates\PdfPrinter;
use App\MailTemplates\Templates\Courrier;
use App\MailTemplates\Templates\ModalTemplate;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\ParticipationType;
use App\Traits\Models\AccountModelTrait;
use App\Traits\Models\EventContactModelTrait;
use App\Traits\Models\EventModelTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use MetaFramework\Services\Validation\ValidationTrait;
use MetaFramework\Traits\Ajax;
use Throwable;

class EventContactActions
{
    use Ajax;
    use AccountModelTrait;
    use EventModelTrait;
    use EventContactModelTrait;
    use ValidationTrait;

    private ?string $participation_type_id = null;

    // Setter for participation_type_id
    public function setParticipationTypeId(?int $id): self
    {
        $this->participation_type_id = $id;

        return $this;
    }

    public function associate(): self
    {
        try {
            $this->validateModelProperties();
            $this->setEventContactFromEventAccount($this->event, $this->account);

            $extraText = $this->getExtraText(ParticipationTypes::getById($this->participation_type_id));

            if ($this->eventContact) {
                $this->responseError("Le contact est déjà associé à l'évènement ".$this->event->texts->name.$extraText);

                return $this;
            }

            $this->eventContact                        = $this->eventContact ?? new EventContact();
            $this->eventContact->user_id               = $this->account->id;
            $this->eventContact->event_id              = $this->event->id;
            $this->eventContact->registration_type     = null;
            $this->eventContact->participation_type_id = $this->participation_type_id;

            $this->eventContact->save();

            $this->responseSuccess($this->account->names()." a été associé.e à l'évènement ".$this->event->texts->name.$extraText);
            event(new EventContactCreated($this->eventContact));
        } catch (Throwable $e) {
            $this->responseException($e, "Une erreur est survenue à l'association du contact à l'évènement.");
        }

        return $this;
    }

    private function getExtraText(?ParticipationType $participationType): string
    {
        return $participationType
            ? " avec la famille de participation ".ParticipantType::translated($participationType->group)
            : " sans famille de participation";
    }

    public function dissociate(): self
    {
        try {
            $this->setEventContact(
                EventContact::where([
                    'user_id'  => $this->account->id,
                    'event_id' => $this->event->id,
                ])->first()
            );
            if ($this->eventContact) {

                $eventContactAccessor = (new EventContactAccessor())->setEventContact($this->eventContact);

                if ($eventContactAccessor->hasSomething()) {
                    $this->responseError("Le contact ne peut pas été dissocié de l'évènement car il a déjà fait des commandes ou d'autres actions associées.");
                    return $this;
                }

                $this->eventContact->delete();

                $this->responseSuccess("Le contact a été dissocié de l'évènement.");

            } else {
                $this->responseError("Le contact n'a pas pu être identifié'.");
            }
        } catch (Throwable $e) {
            $this->responseException($e, "Une erreur est survenue à la dissociation du contact de l'évènement.");
        }

        return $this;
    }

    public function updateProfile(array $userIds, string $key, null|int|string $value): self
    {
        $labels = [
            'participation_type_id' => 'type de participation',
            'is_attending'          => 'Présence',
        ];

        $label    = $labels[$key] ?? $key;
        $mode     = request('mode');
        $event_id = (int)request('event_id');

        if ( ! in_array($key, (new EventContact())->getFillable())) {
            $this->responseError('Le champ <strong>'.$label.'</strong> est inconnu dans le Profil du contact');

            return $this;
        }

        if ($key == 'participation_type_id') {
            if ( ! $value) {
                $this->responseWarning("Vous n'avez sélectionné aucun type de participation");

                return $this;
            }

            if ( ! $event_id) {
                $this->responseWarning("Le type de participation peut être changé uniquement en association avec un évènement");

                return $this;
            }
        }

        //null boolean
        if ($key == 'is_attending' && ! $value) {
            $value = null;
        }

        DB::beginTransaction();

        try {
            $query = EventContact::query()->where(function ($where) use ($event_id, $userIds, $mode) {
                $where->where('event_id', $event_id);
                if ($mode != 'all') {
                    $where->whereIn('user_id', $userIds);
                }
            });

            $query->update([$key => $value]);

            $this->responseSuccess('Le champ <strong>'.$label.'</strong> a été mis à jour pour les entrées sélectionnées.');
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        DB::commit();

        return $this;
    }

    /**
     * @throws Exception
     */
    private function validateModelProperties(): void
    {
        $this->throwException();
        $this->validateModelProperty(property: 'account', message: __('errors.account_not_found'));
        $this->validateModelProperty(property: 'event', message: __('errors.event_not_found'));
    }

    function sendMultipleConfirmation(): self
    {
        $userIds = CsvHelper::csvToUniqueArray(request('ids', ''));

        if(!count($userIds) && request('contacts')){
            $userIds = request('contacts');
        }

        if(!count($userIds)){
            $this->responseError('Aucun utilisateur selectionné');
            return $this;
        }

        $success_send = [];
        $failed_send = [];
        foreach($userIds as $user_id){
            try{
                $event_contact = EventContact::find($user_id)->load(['account', 'event']);
                $accessor = new EventContactAccessor($event_contact->event, $event_contact->account);

                if(!$accessor->hasOrdersOrGroupOrders()){
                    $failed_send[] = $event_contact->account->names();
                    continue;
                }

                $response = (new MailController())->distribute('sendEventContactConfirmation', $event_contact->uuid)->fetchResponse();
                //order count check
                if(array_key_exists('success', $response['messages'][0])) {
                    $success_send[] = $event_contact->account->names();
                }
            } catch (Throwable $e) {
                $this->responseError('Une erreur est survenue dans l\'envoir du mail du contact #'. $user_id);
            }
        }

        if(count($success_send)){
            $this->responseSuccess('Email envoyé avec success pour les contacts :' . implode(', ', $success_send));
        }

        if(count($failed_send)){
            $this->responseError('Email non envoyé pour les contacts :' . implode(', ', $failed_send));
        }

        return $this;
    }

    function sendMultipleMailTemplate(): self
    {
        $userIds = CsvHelper::csvToUniqueArray(request('contact_ids', ''));

        if(!count($userIds)){
            $this->responseError('Aucun utilisateur selectionné');
            return $this;
        }

        $event = Event::find(request('event_id'));
        $mailtemplate = MailTemplate::find(request('mailtemplate_id'));
        $success_send = [];
        $failed_send = [];

        foreach($userIds as $user_id){
            $event_contact = EventContact::find($user_id)->load(['account', 'event']);
            try{
                $accountAccessor = new Accounts($event_contact->account);

                App::setLocale($accountAccessor->getLocale());

                $parsed = (new Courrier($event, $mailtemplate, $event_contact))->serve();

                if(!empty(request('object_'.App::getLocale()))){
                    $parsed->subject = request('object_'.App::getLocale());
                }

                Mail::to($accountAccessor->getEmail())->send(new \App\MailTemplates\Mail\MailTemplate($parsed));
                $success_send[] = $event_contact->account->names();
            } catch (Throwable $e) {
                $failed_send[] = $event_contact?->account->names();
                $this->responseError('Une erreur est survenue dans l\'envoir du mail du contact #'. $user_id);
            }
        }

        if(count($success_send)){
            $this->responseSuccess('Email envoyé avec success pour les contacts :' . implode(', ', $success_send));
        }

        if(count($failed_send)){
            $this->responseError('Email non envoyé pour les contacts :' . implode(', ', $failed_send));
        }

        return $this;
    }

    function sendPdf(): self
    {
        $userIds = CsvHelper::csvToUniqueArray(request('contact_ids', ''));

        if(!count($userIds)){
            $this->responseError('Aucun utilisateur selectionné');
            return $this;
        }

        $event = Event::find(request('event_id'));

        $validation = new ModalTemplateRequest(request()->all());
        $this->validation_rules = $validation->rules();
        $this->validation_messages = $validation->messages();
        $this->validation();

        $pdfTemplate = MailTemplate::find(request('mailtemplate_id'));

        $subject_fr = $this->validated_data['object_fr'];
        $content_fr = $this->validated_data['content_fr'];

        $subject_en = $this->validated_data['object_en'];
        $content_en = $this->validated_data['content_en'];

        $success_send = [];
        $failed_send = [];

        foreach($userIds as $user_id){
            $event_contact = EventContact::find($user_id)->load(['account', 'event']);
            try{
                $accountAccessor = new Accounts($event_contact->account);

                $locale = $accountAccessor->getLocale();
                App::setLocale($locale);

                $subject = $locale === 'fr' ? $subject_fr : $subject_en;
                $content = $locale === 'fr' ? $content_fr : $content_en;

                $pdfParsed = (new Courrier($event, $pdfTemplate, $event_contact))->serve();
                $attachment = (new PdfPrinter($pdfParsed))->output();

                $template = (new ModalTemplate($event));
                $template->subject = $subject;
                $template->content = $content;

                Mail::to($accountAccessor->getEmail())->send((new \App\MailTemplates\Mail\MailTemplate($template))->attachData($attachment, 'attachment.pdf', ['mime' => 'application/pdf']));
                $success_send[] = $event_contact->account->names();
            } catch (Throwable $e) {
                $failed_send[] = $event_contact?->account->names();
                $this->responseError('Une erreur est survenue dans l\'envoir du mail du contact #'. $user_id);
            }
        }

        if(count($success_send)){
            $this->responseSuccess('Email envoyé avec success pour les contacts :' . implode(', ', $success_send));
        }

        if(count($failed_send)){
            $this->responseError('Email non envoyé pour les contacts :' . implode(', ', $failed_send));
        }

        return $this;
    }
}

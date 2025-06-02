<?php
namespace App\MailTemplates\Traits\Variables;
use App\Actions\Front\AutoConnectHelper;
use Illuminate\Support\Carbon;
use MetaFramework\Accessors\Countries;

Trait EventVariables {

    public function EVENEMENT_Type(): string
    {
        return $this->event?->type->name ?? '';
    }

    public function EVENEMENT_Adresse(): string
    {
        return $this->event->place?->address?->text_address ?? '';
    }

    public function EVENEMENT_Ville(): string
    {
        return $this->event->place?->address?->locality ?? '';
    }

    public function EVENEMENT_Pays(): string
    {
        return isset($this->event->place->address->country_code) ? Countries::getCountryNameByCode($this->event->place?->address?->country_code) : '';
    }

    public function EVENEMENT_Date_Debut(): string
    {
        return $this->event->starts ?? '';
    }

    public function EVENEMENT_Date_Fin(): string
    {
        return $this->event->ends ?? '';
    }

    public function EVENEMENT_Nom(): string
    {
        return $this->event->texts?->name ?? '';
    }

    public function EVENEMENT_Acronyme(): string
    {
        return $this->event->texts?->subname ?? '';
    }
    public function EVENEMENT_Lieu(): string
    {
        return $this->event->place ? ($this->event->place?->name . ', ' .  $this->event->place?->address?->locality . ', ' . Countries::getCountryNameByCode($this->event->place?->address?->country_code)) : '';
    }

    public function EVENEMENT_PrenomRespInscription(): string
    {
        return $this->event->adminSubs?->first_name ?? '';
    }

    public function EVENEMENT_NomRespInscription(): string
    {
        return $this->event->adminSubs?->last_name ?? '';
    }

    public function EVENEMENT_EmailRespInscription(): string
    {
        return $this->event->adminSubs?->email ?? '';
    }

    public function EVENEMENT_TelRespInscription(): string
    {
        return $this->event->adminSubs?->profile?->mobile ?? '';
    }

    public function EVENEMENT_PrenomGrant(): string
    {
        return $this->event->pec?->grantAdmin?->first_name ?? '';
    }

    public function EVENEMENT_NomGrant(): string
    {
        return $this->event->pec?->grantAdmin?->last_name ?? '';
    }

    public function EVENEMENT_MobileGrant(): string
    {
        return $this->event->pec?->grantAdmin?->profile?->mobile ?? '';
    }

    public function EVENEMENT_Photo():string
    {
        return '!! A DEFINIR !!!';
    }

    public function EVENEMENT_Url(): string
    {
        if(!$this->eventContact || !str_contains($this->template->content, '{EVENEMENT_Url}')) {
            return '';
        }

        $token = AutoConnectHelper::generateAutoConnectUrlForEventContact($this->eventContact);
        return '<a href="' . $token . '">' . __('ui.auto_connect_link') . '</a>';
    }
}

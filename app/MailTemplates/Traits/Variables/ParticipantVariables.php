<?php

namespace App\MailTemplates\Traits\Variables;

use App\Accessors\Accounts;
use App\Actions\Front\AutoConnectHelper;
use App\Printers\Account;
use MetaFramework\Accessors\Countries;

trait ParticipantVariables
{
    public function PARTICIPANT_DateDeNaissance(): string
    {
        return $this->eventContact?->profile?->birth?->format('d/m/Y') ?? '';
    }

    public function PARTICIPANT_lieuDeNaissance(): string
    {
        //existe pas ?
        return '';
    }

    public function PARTICIPANT_PassportExpiration(): string
    {
        return $this->eventContact?->passports?->first()?->expires_at?->format('d/m/Y');
    }

    public function PARTICIPANT_NumDocument(): string
    {
        return $this->eventContact?->passports?->first()?->serial;
    }

    public function PARTICIPANT_AdresseFacturation(): string
    {
        if (!$this->eventContactAddress) {
            return '';
        }

        return Account::address($this->eventContactAddress);
    }

    public function PARTICIPANT_CodePostal(): string
    {
        return $this->eventContactAddress?->postal_code ?? '';
    }

    public function PARTICIPANT_VilleAdresseFacturation(): string
    {
        return $this->eventContactAddress?->locality ?? '';
    }

    public function PARTICIPANT_Email(): string
    {
        return $this->eventContact?->account?->email ?? '';
    }

    public function PARTICIPANT_Fonction(): string
    {
        return $this->eventContact?->profile?->function ?? '';
    }

    public function PARTICIPANT_Nom(): string
    {
        return $this->eventContact?->account?->last_name ?? '';
    }

    public function PARTICIPANT_Participation(): string
    {
        return '';
    }

    public function PARTICIPANT_Pays(): string
    {
        if (!$this->eventContactAddress) {
            return '';
        }

        return Countries::getCountryNameByCode($this->eventContactAddress->country_code);
    }

    public function PARTICIPANT_Prenom(): string
    {
        return $this->eventContact?->account?->first_name ?? '';
    }

    public function PARTICIPANT_Societe(): string
    {
        if (!$this->accountAccessor?->isCompany()) {
            return '';
        }

        return $this->eventContact?->profile?->company_name ?? '';
    }

    public function PARTICIPANT_Rpps(): string
    {
        return $this->eventContact?->profile?->rpps ?? '';
    }

    public function PARTICIPANT_Interventions(): string
    {
        return '';
    }

    public function PARTICIPANT_Orders(): string
    {
        return '';
    }

    public function PARTICIPANT_Hebergement(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportAllerDateDepart(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportAllerHeureDepart(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportAllerHeureArrivee(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportAllerVilleDepart(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportAllerVilleArrivee(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportAllerTypeTransport(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportRetourDateDepart(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportRetourHeureDepart(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportRetourHeureArrivee(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportRetourVilleDepart(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportRetourVilleArrivee(): string
    {
        return '';
    }

    public function PARTICIPANT_TransportRetourTypeTransport(): string
    {
        return '';
    }

    public function PARTICIPANT_Cautions(): string
    {
        return '';
    }

    public function PARTICIPANT_Prestations(): string
    {
        return '';
    }

    public function PARTICIPANT_Labos(): string
    {
        return '';
    }

    public function PARTICIPANT_Telephone(): string
    {
        return '';
    }

    public function PARTICIPANT_Titre(): string
    {
        return $this->eventContact?->profile?->title?->name ?? '';
    }

    public function PARTICIPANT_UrlConnect(): string
    {
        //pareil que event ?
        if (!$this->eventContact || !str_contains($this->template->content, '{PARTICIPANT_UrlConnect}')) {
            return '';
        }

        $token = AutoConnectHelper::generateAutoConnectUrlForEventContact($this->eventContact);
        return '<a href="' . $token . '">' . __('ui.auto_connect_link') . '</a>';
    }

}

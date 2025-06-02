<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Participant implements GroupVariables
{

    public static function variables(): array
    {
        return [
            'Adresse de facturation' => 'PARTICIPANT_AdresseFacturation',
            'Cautions' => 'PARTICIPANT_Cautions',
            'Code postal adresse de facturation' => 'PARTICIPANT_CodePostal',
            'Commandes' => 'PARTICIPANT_Commandes',
            'Date de naissance' => 'PARTICIPANT_DateDeNaissance',
            'Date d\'expiration du premier document uploadé' => 'PARTICIPANT_DateExpiration',
            'e-mail' => 'PARTICIPANT_Email',
            'Fax' => 'PARTICIPANT_Fax',
            'Fonction' => 'PARTICIPANT_Fonction',
            'Hébergement' => 'PARTICIPANT_Hebergement',
            'Interventions' => 'PARTICIPANT_Interventions',
            'Labos' => 'PARTICIPANT_Labos',
            'Lieu de naissance' => 'PARTICIPANT_lieuDeNaissance',
            'N° du premier document uploadé' => 'PARTICIPANT_NumDocument',
            'Nom' => 'PARTICIPANT_Nom',
            'Num.téléphone' => 'PARTICIPANT_Telephone',
            'Participation' => 'PARTICIPANT_Participation',
            'Pays' => 'PARTICIPANT_Pays',
            'Prénom' => 'PARTICIPANT_Prenom',
            'Prestations' => 'PARTICIPANT_Prestations',
            'RPPS' => 'PARTICIPANT_Rpps',
            'Société' => 'PARTICIPANT_Societe',
            'Titre' => 'PARTICIPANT_Titre',
            'Transport aller date départ' => 'PARTICIPANT_TransportAllerDateDepart',
            'Transport aller heure d\'arrivée' => 'PARTICIPANT_TransportAllerHeureArrivee',
            'Transport aller heure départ' => 'PARTICIPANT_TransportAllerHeureDepart',
            'Transport aller type de transport' => 'PARTICIPANT_TransportAllerTypeTransport',
            'Transport aller ville arrivée' => 'PARTICIPANT_TransportAllerVilleArrivee',
            'Transport aller ville départ' => 'PARTICIPANT_TransportAllerVilleDepart',
            'Transport retour date départ' => 'PARTICIPANT_TransportRetourDateDepart',
            'Transport retour heure d\'arrivée' => 'PARTICIPANT_TransportRetourHeureArrivee',
            'Transport retour heure départ' => 'PARTICIPANT_TransportRetourHeureDepart',
            'Transport retour type de transport' => 'PARTICIPANT_TransportRetourTypeTransport',
            'Transport retour ville arrivée' => 'PARTICIPANT_TransportRetourVilleArrivee',
            'Transport retour ville départ' => 'PARTICIPANT_TransportRetourVilleDepart',
            'Url connexion' => 'PARTICIPANT_UrlConnect',
            'Ville adresse de facturation' => 'PARTICIPANT_VilleAdresseFacturation',
        ];
    }

    public static function title(): string
    {
        return 'Participant';
    }

    public static function icon(): string
    {
        return 'accessibility-check';
    }

}

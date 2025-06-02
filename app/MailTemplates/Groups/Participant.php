<?php

namespace App\MailTemplates\Groups;

use App\MailTemplates\Contracts\GroupVariables;

class Participant implements GroupVariables
{
    public static string $prefix = 'PARTICIPANT_';

    public static function variables(): array
    {
        return [
            self::$prefix.'AdresseFacturation' => 'Adresse de facturation',
            self::$prefix.'Cautions' => 'Cautions',
            self::$prefix.'CodePostal' => 'Code postal adresse de facturation',
            self::$prefix.'Commandes' => 'Commandes',
            self::$prefix.'DateDeNaissance' => 'Date de naissance',
            self::$prefix.'DateExpiration' => 'Date d\'expiration du premier document uploadé',
            self::$prefix.'Email' => 'e-mail',
            self::$prefix.'Fax' => 'Fax',
            self::$prefix.'Fonction' => 'Fonction',
            self::$prefix.'Hebergement' => 'Hébergement',
            self::$prefix.'Interventions' => 'Interventions',
            self::$prefix.'Labos' => 'Labos',
            self::$prefix.'lieuDeNaissance' => 'Lieu de naissance',
            self::$prefix.'NumDocument' => 'N° du premier document uploadé',
            self::$prefix.'Nom' => 'Nom',
            self::$prefix.'Telephone' => 'Num.téléphone',
            self::$prefix.'Participation' => 'Participation',
            self::$prefix.'Pays' => 'Pays',
            self::$prefix.'Prenom' => 'Prénom',
            self::$prefix.'Prestations' => 'Prestations',
            self::$prefix.'Rpps' => 'RPPS',
            self::$prefix.'Societe' => 'Société',
            self::$prefix.'Titre' => 'Titre',
            self::$prefix.'TransportAllerDateDepart' => 'Transport aller date départ',
            self::$prefix.'TransportAllerHeureArrivee' => 'Transport aller heure d\'arrivée',
            self::$prefix.'TransportAllerHeureDepart' => 'Transport aller heure départ',
            self::$prefix.'TransportAllerTypeTransport' => 'Transport aller type de transport',
            self::$prefix.'TransportAllerVilleArrivee' => 'Transport aller ville arrivée',
            self::$prefix.'TransportAllerVilleDepart' => 'Transport aller ville départ',
            self::$prefix.'TransportRetourDateDepart' => 'Transport retour date départ',
            self::$prefix.'TransportRetourHeureArrivee' => 'Transport retour heure d\'arrivée',
            self::$prefix.'TransportRetourHeureDepart' => 'Transport retour heure départ',
            self::$prefix.'TransportRetourTypeTransport' => 'Transport retour type de transport',
            self::$prefix.'TransportRetourVilleArrivee' => 'Transport retour ville arrivée',
            self::$prefix.'TransportRetourVilleDepart' => 'Transport retour ville départ',
            self::$prefix.'UrlConnect' => 'Url connexion',
            self::$prefix.'VilleAdresseFacturation' => 'Ville adresse de facturation',
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

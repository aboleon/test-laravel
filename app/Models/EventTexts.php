<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFramework\Polyglote\Traits\Translation;

class EventTexts extends Model
{
    use HasFactory;
    use Translation;

    protected $table = 'events_texts';
    protected $guarded = [];


    public array $fillables = [];
    public $timestamps = false;
    public array $structured_fillables = [
        'general' => [
            'name' => [
                'label' => 'Nom',
                'required' => true,
            ],
            'subname' => [
                'label' => 'Acronyme',
            ],
        ],
        'event' => [
            'description' => [
                'type' => 'textarea',
                'label' => 'Description',
                'class' => 'simplified',
            ],

            'cancelation' => [
                'type' => 'textarea',
                'label' => 'Conditions d\'annulation',
                'required' => true,
            ],
        ],
        'accommodation' => [
            'external_accommodation' => [
                'type' => 'textarea',
                'label' => 'Hébergement géré en externe',
            ],
        ],
        'contact' => [
            'contact_title' => [
                'label' => 'Titre de la page Contact',
            ],
            'contact_text' => [
                'type' => 'textarea',
                'label' => 'Texte de la page Contact',
            ],
        ],
        'privacy_policy' => [
            'privacy_policy_title' => [
                'label' => 'Titre de la page Politique de confidentialité',
            ],
            'privacy_policy_text' => [
                'type' => 'textarea',
                'label' => 'Texte de la page Politique de confidentialité',
            ],
        ],
        'transport' => [
            'transport_admin' => [
                'type' => 'textarea',
                'label' => "Texte pour le site - gestion Divine ID",
            ],
            'transport_unnecessary' => [
                'type' => 'textarea',
                'label' => "Texte si Pas de transport nécessaire",
            ],
            'transport_user' => [
                'type' => 'textarea',
                'label' => "Texte pour le site - gestion internaute",
            ],
            'max_price_text' => [
                'type' => 'textarea',
                'label' => "Phrase montant max",
            ],
        ],
        'shop' => [
            'cancellation_shop' => [
                'type' => 'textarea',
                'label' => 'Conditions d\'annulation boutique exposant',
            ],
        ],
        'fo' => [
            'fo_login_participant' => [
                'type' => 'textarea',
                'label' => 'Texte page création compte Participant',
            ],
            'fo_login_speaker' => [
                'type' => 'textarea',
                'label' => 'Texte page création compte intervenant',
            ],
            'fo_login_industry' => [
                'type' => 'textarea',
                'label' => 'Texte page création compte Industriel',
            ],
            'fo_group' => [
                'type' => 'textarea',
                'label' => 'Texte page Groupe',
            ],
            'fo_exhibitor' => [
                'type' => 'textarea',
                'label' => 'Texte page Exposant',
            ],
        ],
        'second_fo' => [
            'second_home_subtitle' => [
                'type' => 'text',
                'label' => 'Sous titre page Accueil générale',
            ],
            'second_fo_home' => [
                'type' => 'textarea',
                'label' => 'Texte page Accueil générale',
                'class' => 'simplified',
            ],
            'second_fo_particpant_subtitle' => [
                'type' => 'text',
                'label' => 'Sous titre Page login participant',
            ],
            'second_fo_login_participant' => [
                'type' => 'textarea',
                'label' => 'Texte Page login participant',
                'class' => 'simplified',
            ],
            'second_fo_speaker_subtitle' => [
                'type' => 'text',
                'label' => 'Sous titre Page login orateur',
            ],
            'second_fo_login_speaker' => [
                'type' => 'textarea',
                'label' => 'Texte Page login orateur',
                'class' => 'simplified',
            ],
            'second_fo_industry_subtitle' => [
                'type' => 'text',
                'label' => 'Sous titre Page login industriel',
            ],
            'second_fo_login_industry' => [
                'type' => 'textarea',
                'label' => 'Texte Page login industriel',
                'class' => 'simplified',
            ],
            'second_fo_exhibitor_subtitle' => [
                'type' => 'text',
                'label' => 'Sous titre Page login groupe',
            ],
            'second_fo_exhibitor' => [
                'type' => 'textarea',
                'label' => 'Texte Page login groupe',
                'class' => 'simplified',
            ],
        ],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillables = collect($this->structured_fillables)->flatMap(fn($item)=>$item)->all();
        $this->defineTranslatables();
    }
}

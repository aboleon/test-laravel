<?php

namespace App\Validation\Event;

use MetaFramework\Services\Validation\ValidationAbstract;

class EventConfigValidation extends ValidationAbstract
{

    private string $prefix = 'event.config.';

    /**
     * @return array<string, array<int, \Illuminate\Validation\Rules\Enum|string>|string>>
     */
    public function rules(): array
    {
        $eventId = request('event_id');

        return [
            $this->prefix.'flags'                         => 'required',
            $this->prefix.'starts'                        => 'date_format:d/m/Y',
            $this->prefix.'ends'                          => 'date_format:d/m/Y|after_or_equal:'.$this->prefix.'starts',
            $this->prefix.'subs_ends'                     => 'date_format:d/m/Y|before_or_equal:'.$this->prefix.'starts',
            $this->prefix.'event_main_id'                 => 'nullable',
            $this->prefix.'event_type_id'                 => 'required',
            $this->prefix.'event_type_id'                 => 'required',
            $this->prefix.'place_id'                      => 'required',
            $this->prefix.'bank_account_id'               => 'required',
            $this->prefix.'code'                          => 'required | unique:events,code,'.$eventId,
            $this->prefix.'admin_id'                      => 'required',
            $this->prefix.'admin_subs_id'                 => 'required',
            $this->prefix.'bank_card_code'                => 'nullable',
            $this->prefix.'reminder_unpaid_accommodation' => 'nullable | integer',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            $this->prefix.'texts.name.'.app()->getLocale().'.required'  => __('validation.required', ['attribute' => "Le nom de l'èvenement"]),
            $this->prefix.'texts.subname.'.app()->getLocale().'.string' => __('validation.string', ['attribute' => "Le sous - titre de l'èvenement"]),
            $this->prefix.'flags.required'                              => __('validation.required', ['attribute' => "L'indication d'affichage des drapeaux"]),
            $this->prefix.'starts.date_format'                          => __('validation.date_format', ['attribute' => "La date de début", 'format' => 'JJ / MM / AAAA']),
            $this->prefix.'ends.date_format'                            => __('validation.date_format', ['attribute' => "La date de fin", 'format' => 'JJ / MM / AAAA']),
            $this->prefix.'ends.after_or_equal'                         => "La date de fin doit être postérieure ou égale à la date de début",
            $this->prefix.'subs_ends.date_format'                       => __('validation.date_format', ['attribute' => "La date limite des inscriptions", 'format' => 'JJ / MM / AAAA']),
            $this->prefix.'subs_ends.before_or_equal'                   => "La date limite des inscriptions doit être antérieure ou égale à la date de début",
            $this->prefix.'event_main_id.required'                      => __('validation.required', ['attribute' => "La famille de l'èvenement"]),
            $this->prefix.'event_type_id.required'                      => __('validation.required', ['attribute' => "Le type de l'èvenement"]),
            $this->prefix.'place_id.required'                           => __('validation.required', ['attribute' => "Le lieu de l'èvenement"]),
            $this->prefix.'bank_account_id.required'                    => __('validation.required', ['attribute' => "Le compte bancaire"]),
            $this->prefix.'code.required'                               => __('validation.required', ['attribute' => "Le code de l'évènement"]),
            $this->prefix.'code.unique'                                 => __('validation.unique', ['attribute' => "Le code de l'évènement"]),
            $this->prefix.'admin_id.required'                           => __('validation.required', ['attribute' => "L'administrateur de l'évènemment"]),
            $this->prefix.'admin_subs_id.required'                      => __('validation.required', ['attribute' => "L'administrateur des inscriptions"]),
            //$this->prefix.'bank_card_code.required' => __('validation.integer', ['attribute' => "Le code CB"]),
            $this->prefix.'reminder_unpaid_accommodation.integer'       => __('validation.integer', ['attribute' => "Le nombre de jours de relance pour hébergement non soldé"]),
        ];
    }
}

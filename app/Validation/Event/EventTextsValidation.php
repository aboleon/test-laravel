<?php

namespace App\Validation\Event;

use MetaFramework\Services\Validation\ValidationAbstract;

class EventTextsValidation extends ValidationAbstract
{
    private string $prefix = 'event.texts.';

    /**
     * @return array<string, array<int, \Illuminate\Validation\Rules\Enum|string>|string>>
     */
    public function rules(): array
    {
        $rules = [];

        if (request()->has('event.config.flags')) {
            foreach (request('event.config.flags') as $flag) {
                $rules[$this->prefix . 'name.' . $flag] = 'required';
                $rules[$this->prefix . 'subname.' . $flag] = 'nullable|string';
                $rules[$this->prefix . 'cancelation.' . $flag] = 'required';
            }
        }
        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        $messages = [];

        if (request()->has('event.config.flags')) {
            foreach (request('event.config.flags') as $flag) {
                $messages[$this->prefix . 'name.' . $flag . '.required'] = __('validation.required', ['attribute' => "Le nom de l'èvenement en " . __('lang.' . $flag . '.label')]);
                $messages[$this->prefix . 'subname.' . $flag . '.string'] = __('validation.string', ['attribute' => "Le sous-titre de l'èvenement en " . __('lang.' . $flag . '.label')]);
                $messages[$this->prefix . 'cancelation.' . $flag . '.required'] = __('validation.required', ['attribute' => "La saisie des conditions d'annulation en " . __('lang.' . $flag . '.label')]);
            }
        }

        return $messages;
    }
}

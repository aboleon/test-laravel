<?php

namespace App\Http\Requests\Front\Transport;

use Illuminate\Foundation\Http\FormRequest;

class TransportDepartureStepFormRequest extends FormRequest
{

    public function rules()
    {
        return [
            'departure_start_date'          => ['nullable'],
            'departure_start_time'          => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            'departure_end_time'            => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            'departure_start_location'      => ['nullable'],
            'departure_end_location'        => ['nullable'],
            'departure_transport_type'      => ['nullable'],
            'departure_participant_comment' => ['nullable']
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'departure_start_time.regex' => "L'heure de départ doit être au format HH:mm (de 00:00 à 23:59)",
            'departure_end_time.regex'   => "L'heure d'arrivée doit être au format HH:mm (de 00:00 à 23:59)"
        ];
    }
}

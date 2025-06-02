<?php

namespace App\Http\Requests\Front\Transport;

use Illuminate\Foundation\Http\FormRequest;

class TransportReturnStepFormRequest extends FormRequest
{

    public function rules()
    {
        return [
            'return_start_date' => ['nullable'],
            'return_start_time' => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            'return_end_time' => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            'return_start_location' => ['nullable'],
            'return_end_location' => ['nullable'],
            'return_transport_type' => ['nullable'],
            'return_participant_comment' => ['nullable']
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'return_start_time.regex' => "L'heure de départ doit être au format HH:mm (de 00:00 à 23:59)",
            'return_end_time.regex' => "L'heure d'arrivée doit être au format HH:mm (de 00:00 à 23:59)",
        ];
    }
}

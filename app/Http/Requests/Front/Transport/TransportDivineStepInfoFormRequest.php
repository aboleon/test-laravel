<?php

namespace App\Http\Requests\Front\Transport;

use Illuminate\Foundation\Http\FormRequest;

class TransportDivineStepInfoFormRequest extends FormRequest
{

    public function rules()
    {
        return [
            'passport_first_name' => ['nullable'],
            'passport_last_name' => ['nullable'],
            'birth' => ['nullable'],
            'travel_preferences' => ['nullable'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
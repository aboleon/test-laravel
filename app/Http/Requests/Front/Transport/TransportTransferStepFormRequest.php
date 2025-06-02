<?php

namespace App\Http\Requests\Front\Transport;

use Illuminate\Foundation\Http\FormRequest;

class TransportTransferStepFormRequest extends FormRequest
{

    public function rules()
    {
        return [
            'transfer_requested' => ['required'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
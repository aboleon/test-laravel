<?php

namespace App\Http\Requests\EventManager\EventContact;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class EventContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array{
        return [
            'participation_type_id' => 'nullable|integer',
            'order_cancellation' => 'nullable|bool',
            'is_attending' => 'nullable|bool',
            'comment' => 'nullable|string',
            'pec_enabled' => 'nullable|bool',
        ];
    }
}

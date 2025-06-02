<?php

namespace App\Http\Requests\EventManager\EventGroup;

use Illuminate\Foundation\Http\FormRequest;

class EventGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array{
        return [
            'is_exhibitor' => 'nullable|bool',
            'password' => 'nullable|string',
            'nb_free_badges' => 'nullable|integer',
            'comment' => 'nullable|string',
            'event_comment' => 'nullable|string',
            'free_text_1' => 'nullable|string',
            'free_text_2' => 'nullable|string',
            'free_text_3' => 'nullable|string',
            'free_text_4' => 'nullable|string',
        ];
    }
}
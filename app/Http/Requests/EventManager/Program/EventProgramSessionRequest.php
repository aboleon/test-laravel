<?php

namespace App\Http\Requests\EventManager\Program;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class EventProgramSessionRequest extends FormRequest
{

    use Locale;
    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array{
        return [
            'session_texts.name.' . $this->defaultLocale() => 'required',
            'session.main.session_type_id' => 'required|integer',
            'session.main.event_program_day_room_id' => 'required|integer',
            'session.main.place_room_id' => 'nullable|integer',
        ];
    }

    public function messages(): array{
        return [
            'session_texts.name.' . $this->defaultLocale() . '.required' => __('validation.required', ['attribute' => __('programs.the_session_name')]),
            'session.main.session_type_id' => __('validation.required', ['attribute' => __('programs.the_session_type')]),
            'session.main.event_program_day_room_id' => __('validation.required', ['attribute' => "Le conteneur"]),
        ];
    }

}
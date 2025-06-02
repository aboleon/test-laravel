<?php

namespace App\Http\Requests\EventManager\Program;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class EventProgramInterventionRequest extends FormRequest
{

    use Locale;
    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array{
        return [
            'intervention_texts.name.' . $this->defaultLocale() => 'required',
            'intervention.main.event_program_session_id' => 'required|integer',
            'intervention.main.specificity_id' => 'nullable|integer',
            'intervention.main.duration' => 'required|integer',
        ];
    }

    public function messages(): array{
        return [
            'intervention_texts.name.' . $this->defaultLocale() . '.required' => __('validation.required', ['attribute' => __('programs.the_intervention_name')]),
            'intervention.main.event_program_session_id' => __('validation.required', ['attribute' => __('programs.the_session')]),
            'intervention.main.duration' => __('validation.required', ['attribute' => __('programs.the_duration')]),
        ];
    }

}

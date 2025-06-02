<?php

namespace App\Http\Requests;

use App\Enum\ParticipantType;
use Illuminate\Foundation\Http\FormRequest;

class ParticipationTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $locales = [];
        foreach (config('mfw.translatable.active_locales') as $locale) {
            $locales['name.' . $locale] = 'required';
        }
        return array_merge(
            $locales,
            [
                'group' => 'required|in:' . (implode(',', ParticipantType::keys())),
                'default' => 'integer',
            ]
        );
    }

    public function messages(): array
    {

        $locales = [];
        foreach (config('mfw.translatable.active_locales') as $locale) {
            $locales['name.' . $locale . '.required'] = __('validation.required', ['attribute' => __('mfw.title') . ' en ' . __('lang.' . $locale . '.label')]);
        }

        return array_merge(
            $locales,
            [
                'group.required' => __('validation.required', ['attribute' => "Le groupe"]),
                'group.in' => "Le groupe doit être un parmi ces choix : " . (implode(', ', ParticipantType::translations())),
                'default.integer' => "Le type de participation par défaut n'est pas correctement transmis",
            ]
        );
    }
}

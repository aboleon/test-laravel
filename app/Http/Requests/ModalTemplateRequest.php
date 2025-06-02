<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModalTemplateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mailtemplate_id' => 'required',
            'object_fr' => 'required|string',
            'object_en' => 'required|string',
            'content_fr' => 'required|string',
            'content_en' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'mailtemplate_id.required' => __('validation.required', ['attribute' => __('ui.modal_email.mailtemplate_id')]),
            'object_fr.required' => __('validation.required', ['attribute' => __('ui.modal_email.object_fr')]),
            'object_en.required' => __('validation.required', ['attribute' => __('ui.modal_email.object_en')]),
            'content_fr.required' => __('validation.required', ['attribute' => __('ui.modal_email.content_fr')]),
            'content_en.required' => __('validation.required', ['attribute' => __('ui.modal_email.content_en')]),
        ];
    }
}

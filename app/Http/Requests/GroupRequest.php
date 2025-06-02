<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'group.name' => 'required',
            'group.company' => 'required',
            'group.billing_comment' => 'nullable',
            'group.siret' => 'nullable',
            'group.vat_id' => 'nullable',
            'group.country_code' => ['nullable', 'required_with:' . 'group.phone'],
            'group.phone' => ['nullable', 'phone:group.country_code'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'group.name.required' => __('validation.required', ['attribute' => strval(__('ui.title'))]),
            'group.company.required' => __('validation.required', ['attribute' => "La raison sociale"]),
            'group.phone.required' => __('validation.required', ['attribute' => __('account.phone')]),
             'group.phone.phone' => __('validation.phone', ['attribute' => strval(__('account.phone'))]),
            'group.country_code.required_with' => __('validation.required_with', [
                'attribute' => __('Country code'),
                'values' => __('Phone')
            ]),
        ];
    }
}

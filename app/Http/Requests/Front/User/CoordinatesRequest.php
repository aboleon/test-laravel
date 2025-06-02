<?php

namespace App\Http\Requests\Front\User;

use Illuminate\Foundation\Http\FormRequest;

class CoordinatesRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone'  => ['required', 'string'],
            'email'  => ['required', 'email', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => __('front/account.validation.phone'),
            'email.required' => __('front/account.validation.email'),
            'email.email' => __('front/account.validation.email_format'),
        ];
    }

}

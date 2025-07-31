<?php

namespace App\Http\Requests\Front\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
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
            'participation_type' => ['nullable', 'integer'],
            'domain_id' => ['required', 'integer'],
            'title_id' => ['nullable', 'integer'],
            'civ' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'birth' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'lang' => ['required', 'string', Rule::in(config('mfw.translatable.locales'))],
            'profession_id' => ['required', 'integer'],
            'savant_society_id' => ['nullable', 'integer'],
            'establishment_id' => ['nullable', 'integer'],
            'function' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'domain_id.required' => __('front/account.validation.domain'),
            'civ.required' => __('front/account.validation.genre'),
            'birth.required' => __('front/account.validation.birth'),
            'first_name.required' => __('front/account.validation.first_name'),
            'last_name.required' => __('front/account.validation.last_name'),
            'lang.required' => __('front/account.validation.lang'),
            'profession_id.required' => __('front/account.validation.profession_id'),
            'function.required' => __('front/account.validation.function'),
        ];
    }

}

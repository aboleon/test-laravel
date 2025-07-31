<?php

namespace App\Http\Requests\Front\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
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
            'participation_type' => ['required', 'integer'],
            'domain'             => ['required', 'integer'],
            'function'           => ['required', 'string'],
            'profession_id'      => ['required', 'integer'],
            'title'              => ['nullable', 'integer'],
            'genre'              => ['required'],
            'first_name'         => ['required', 'string', 'max:255'],
            'last_name'          => ['required', 'string', 'max:255'],
            'lang'               => ['required', 'string', Rule::in(config('mfw.translatable.locales'))],
            'email'              => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'pass'               => ['required', 'string', 'confirmed'],
            'address'            => ['required', 'string'],
            'zipcode'            => ['required', 'string'],
            'city'               => ['required', 'string'],
            'country_code'       => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'participation_type.required' => __('front/register.validation.participation_type_required'),
            'domain.required'             => __('front/register.validation.domain_required'),
            'function.required'           => __('front/register.validation.function_required'),
            'profession_id.required'      => __('front/register.validation.profession_required'),
            'genre.required'              => __('front/register.validation.genre_required'),
            'first_name.required'         => __('front/register.validation.first_name_required'),
            'last_name.required'          => __('front/register.validation.last_name_required'),
            'lang.required'               => __('front/register.validation.lang_required'),
            'email.required'              => __('front/register.validation.email_required'),
            'email.email'                 => __('front/register.validation.email_format'),
            'email.confirmed'             => __('front/register.validation.email_confirmed'),
            'email.unique'                => __('front/register.validation.email_already_exists'),
            'pass.required'               => __('front/register.validation.password_required'),
            'pass.confirmed'              => __('front/register.validation.password_confirmed'),
            'address.required'            => __('front/register.validation.address_required'),
            'zipcode.required'            => __('front/register.validation.zipcode_required'),
            'city.required'               => __('front/register.validation.city_required'),
            'country_code.required'       => __('front/register.validation.country_code_required'),
        ];
    }

}

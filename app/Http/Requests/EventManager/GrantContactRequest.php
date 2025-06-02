<?php

namespace App\Http\Requests\EventManager;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class GrantContactRequest extends FormRequest
{
    use Locale;

    private string $prefix;

    public function __construct()
    {
        parent::__construct();
        $this->prefix = 'grant_contact.';
    }


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
            $this->prefix . 'first_name' => 'required|string',
            $this->prefix . 'last_name' => 'required|string',
            $this->prefix . 'email' => 'required|email',
            //$this->prefix . 'phone' => 'nullable|phone',
            $this->prefix . 'phone' => 'required',
            $this->prefix . 'fonction' => 'nullable|string',
            $this->prefix . 'service' => 'nullable|string',
        ];
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return [
            $this->prefix . 'first_name.required' => __('validation.required', ['attribute' => "Le prénom"]),
            $this->prefix . 'first_name.string' => __('validation.string', ['attribute' => "Le prénom"]),
            $this->prefix . 'last_name.string' => __('validation.string', ['attribute' => "Le nom"]),
            $this->prefix . 'last_name.required' => __('validation.required', ['attribute' => "Le nom"]),
            $this->prefix . 'email.required' => __('validation.required', ['attribute' => "L'adresse e-mail"]),
            $this->prefix . 'email.email' => __('validation.email', ['attribute' => "L'adresse e-mail"]),
            $this->prefix . 'phone.required' => __('validation.required', ['attribute' => "Le numéro de téléphone"]),
            $this->prefix . 'phone.phone' => "Le numéro de téléphone ne semble pas correct",
            $this->prefix . 'fonction.string' => __('validation.string', ['attribute' => "La fonction"]),
            $this->prefix . 'service.string' => __('validation.string', ['attribute' => "Le service"]),
        ];
    }
}

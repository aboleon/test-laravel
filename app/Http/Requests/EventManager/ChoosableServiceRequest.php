<?php

namespace App\Http\Requests\EventManager;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class ChoosableServiceRequest extends FormRequest
{
    use Locale;

    /**
     * @var array<array<string,mixed>>
     */

    public function __construct()
    {
        parent::__construct();
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
            'service_texts.title.' . $this->defaultLocale() => 'required',
        ];
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return [
            'service_texts.title.' . $this->defaultLocale() . '.required' => __('validation.required', ['attribute' => "L'intitulé"]),
        ];
    }
}

<?php

namespace App\Http\Requests\EventManager;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class AccommodationServiceRequest extends FormRequest
{
    use Locale;

    private string $prefix;

    /**
     * @var array<array<string,mixed>>
     */
    private array $address_validation;

    public function __construct()
    {
        parent::__construct();
        $this->setPrefix('service');
    }

    public function setPrefix(string $prefix): static
    {
        $this->prefix = $prefix . '.';
        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
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
        $lang = (string)request('event_lang') ?: $this->defaultLocale();

        $rules = [
            $this->prefix . 'name.' . $this->defaultLocale() => 'nullable|string'
        ];

        if (request()->filled($this->prefix . 'name.'.$lang)) {
            $rules[$this->prefix . 'price'] = 'numeric';
        }

        $rules[$this->prefix . 'vat_id'] = 'required_with:' . $this->prefix . 'price|numeric';

        return $rules;
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return [
            $this->prefix . 'vat_id.numeric' => __('validation.string', ['attribute' => 'TVA']),
            $this->prefix . 'price.numeric' => __('validation.string', ['attribute' => 'Montant de la prestation']),
            $this->prefix . 'price.required_with' => __('validation.required_with', ['attribute' => 'vous saisissez une prestation']),
            $this->prefix . 'vat_id.required_with' => __('validation.required_with', ['attribute' => 'vous saisissez un prix']),
            $this->prefix . 'name.' . $this->defaultLocale() . '.string' => __('validation.string', ['attribute' => __('ui.access')])
        ];
    }
}

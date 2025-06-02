<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use MetaFramework\Services\Validation\GoogleAddressValidation;

class GroupAddressRequest extends FormRequest
{

    private string $prefix = '';

    /**
     * @var array<array<string,mixed>>
     */
    private array $address_validation;

    public function __construct()
    {
        parent::__construct();
        $this->setPrefix('wa_geo');
        $this->address_validation = (new GoogleAddressValidation())->setPrefix('wa_geo')->logic();

    }


    public function setPrefix(string $prefix): static
    {
        $this->prefix = $prefix . '.';
        return $this;
    }
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(
            [
                $this->prefix . 'name' => ['nullable', 'string'],
                $this->prefix . 'billing' => ['nullable', 'boolean'],
            ],
            $this->address_validation['rules'],
        );

    }

    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return array_merge(
            [
                $this->prefix . 'name.string' => __('validation.string', ['attribute' => strval(__('ui.title'))]),
                $this->prefix . 'billing.boolean' => __('validation.boolean', ['attribute' => "Le choix d'adresse de facturation par défaut"])
            ],
            $this->address_validation['messages']
        );
    }
}

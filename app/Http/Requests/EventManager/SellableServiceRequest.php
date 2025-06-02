<?php

namespace App\Http\Requests\EventManager;

use App\Rules\SellableStockRule;
use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class SellableServiceRequest extends FormRequest
{
    use Locale;

    private string $prefix;

    /**
     * @var array<array<string,mixed>>
     */

    public function __construct()
    {
        parent::__construct();
        $this->setPrefix('service');
        $this->service_prices = new SellableServicePricesRequest();
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

        return array_merge([
            $this->prefix . 'published' => 'nullable|integer',
            $this->prefix . 'is_invitation' => 'nullable|integer',
            $this->prefix . 'invitation_quantity_enabled' => 'nullable|integer',
            $this->prefix . 'pec' => 'nullable|integer',
            $this->prefix . 'pec_max_pax' => 'required_if:' . $this->prefix . 'pec,1',
            $this->prefix . 'service_group' => 'required|integer',
            $this->prefix . 'service_group_combined' => 'nullable|integer',
            $this->prefix . 'service_date' => 'nullable|date_format:d/m/Y|required_with:' . $this->prefix . 'service_starts,' . $this->prefix . 'service_ends',
            $this->prefix . 'service_starts' => 'nullable|date_format:H:i',
            $this->prefix . 'service_ends' => 'nullable|date_format:H:i',
            $this->prefix . 'place_id' => 'nullable|integer',
            $this->prefix . 'room_id' => 'nullable|integer',
            $this->prefix . 'vat_id' => 'integer',
            $this->prefix . 'choosable' => 'nullable|integer',
            $this->prefix . 'stock_showable' => 'nullable|integer',
            $this->prefix . 'stock_unlimited' => 'nullable|integer',
            $this->prefix . 'stock' => ['integer', new SellableStockRule()],
            'sellable_deposit' => 'required_if:sellable_has_deposit,1',
            'sellable_deposit_vat_id' => 'required_if:sellable_has_deposit,1|integer',
            'service_texts.title.' . $this->defaultLocale() => 'required',
        ],
            $this->service_prices->rules()
        );
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return array_merge([
            $this->prefix . 'service_group.required' => __('validation.required', ['attribute' => 'La famille de la prestation']),
            $this->prefix . 'service_group_combined.integer' => __('validation.service_group_combined'),
            $this->prefix . 'service_date.required_with' => __('validation.required_with', ['attribute' => 'La date', 'values' => 'heure de début ou de fin']),
            $this->prefix . 'service_date.date_format' => __('validation.date_format', ['attribute' => 'La date', 'format' => 'JJ/MM/AAAA']),
            $this->prefix . 'service_starts.date_format' => __('validation.date_format', ['attribute' => 'L\'heure du début', 'format' => 'HH:MM']),
            $this->prefix . 'service_ends.date_format' => __('validation.date_format', ['attribute' => 'L\'heure de fin', 'format' => 'HH:MM']),
            $this->prefix . 'place_id.integer' => "Le lieu n'est pas correctement indiqué",
            $this->prefix . 'room_id.integer' => "La salle n'est pas correctement indiquée",
            $this->prefix . 'vat_id.numeric' => __('validation.string', ['attribute' => 'TVA']),
            $this->prefix . 'price.numeric' => __('validation.string', ['attribute' => 'Montant des annulations']),
            'service_texts.title.' . $this->defaultLocale() . '.required' => __('validation.required', ['attribute' => "L'intitulé"]),
            'service_texts.title.' . $this->defaultLocale() . '.string' => __('validation.string', ['attribute' => "L'intitulé"]),
            'sellable_deposit.required_if' => "Le montant de la caution est obligatoire lorsque la prestation nécessite une caution",
            'sellable_deposit_vat_id.numeric' => __('validation.string', ['attribute' => 'TVA']),
            $this->prefix . 'pec_max_pax.required_if' => __('validation.required', ['attribute' => "Le nombre max PEC"])
        ],
            $this->service_prices->messages()
        );
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'service' => array_merge($this->service,
                [
                    'stock' => (int) $this->service['stock'] ?? 0,
                    'stock_unlimited' => $this->service['stock_unlimited'] ?? NULL,
                ])
        ]);
    }
}

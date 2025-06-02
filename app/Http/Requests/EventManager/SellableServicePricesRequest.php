<?php

namespace App\Http\Requests\EventManager;

use Illuminate\Foundation\Http\FormRequest;
use MetaFramework\Services\Validation\ValidationPrefix;

class SellableServicePricesRequest extends FormRequest
{

    use ValidationPrefix;

    public function __construct()
    {
        parent::__construct();
        $this->setPrefix('service_price');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            $this->prefix . 'price.*' => 'nullable|numeric|min:0',
            $this->prefix . 'ends.*' =>'required_with:'.$this->prefix .'price.*|date_format:"d/m/Y"',
        ];
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return [
            $this->prefix . 'price.*.numeric' => "Les montants d'acompte doivent être tous des chiffres",
            $this->prefix . 'price.*.min' => "Les montants d'acompte doivent être au minimum de 1",
            $this->prefix . 'ends.required_with' => "Les dates d'acompte doivent être toutes renseignées.",
            $this->prefix . 'ends.*.date_format' => "Les dates d'acompte doivent être au format dd/mm/aaaa.",
        ];
    }
}

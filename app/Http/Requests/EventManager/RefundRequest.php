<?php

namespace App\Http\Requests\EventManager;

use Illuminate\Foundation\Http\FormRequest;
use MetaFramework\Services\Validation\ValidationPrefix;

class RefundRequest extends FormRequest
{
    use ValidationPrefix;

    public function __construct()
    {
        parent::__construct();
        $this->setPrefix('order_refund');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            $this->prefix . 'object.*' => 'required|string',
            $this->prefix . 'amount.*' => 'gt:0',
            $this->prefix . 'vat_id.*' => 'required',
            $this->prefix . 'date.*' =>'date_format:"d/m/Y"',
        ];
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return [
            $this->prefix . 'amount.*.gt' => "Le montant de l'avoir doit être plus de zéro :attribute",
            $this->prefix . 'object.*.required' => "L'objet de l'avoir :attribute n'est pas renseigné.",
            $this->prefix . 'vat_id.*.required' => "Le taux de TVA de l'avoir :attribute n'est pas renseigné.",
            $this->prefix . 'object.*.string' => "L'objet de l'avoir :attribute doit être un texte.",
            $this->prefix . 'date.*.date_format' => "La dates de l'avoir :attribute doit être au format dd/mm/aaaa.",
        ];
    }
    public function attributes(): array
    {
        $attributes = [];
        foreach (request($this->prefix.'object') as $key => $value) {
            $attributes[$this->prefix .'object.'.$key] = "sur la ligne " . ($key + 1);
        }
        foreach (request($this->prefix.'amount') as $key => $value) {
            $attributes[$this->prefix .'amount.'.$key] = "sur la ligne " . ($key + 1);
        }
        foreach (request($this->prefix.'date') as $key => $value) {
            $attributes[$this->prefix .'date.'.$key] = "sur la ligne " . ($key + 1);
        }
        foreach (request($this->prefix.'vat_id') as $key => $value) {
            $attributes[$this->prefix .'vat_id.'.$key] = "sur la ligne " . ($key + 1);
        }

        return $attributes;
    }
}

<?php

namespace App\Http\Requests\EventManager\Transport;

use Illuminate\Foundation\Http\FormRequest;
use MetaFramework\Services\Validation\ValidationPrefix;

class EventTransportRequest extends FormRequest
{

    use ValidationPrefix;

    private string $prefix_account;

    public function __construct()
    {
        parent::__construct();
        $this->setPrefix('item.main');
        $this->prefix_account = 'item.account.';
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //
            $this->prefix_account.'passport_first_name' => 'nullable|string|max:255',
            $this->prefix_account.'passport_last_name'  => 'nullable|string|max:255',
            $this->prefix_account.'birth_date'          => 'nullable|date_format:d/m/Y',
            //
            $this->prefix.'desired_management'          => 'required',
            $this->prefix.'departure_start_time'        => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            $this->prefix.'departure_end_time'          => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            $this->prefix.'return_start_time'           => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            $this->prefix.'return_end_time'             => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            $this->prefix.'transfer_shuttle_time'       => ['nullable', 'regex:/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/'],
            $this->prefix.'price_before_tax'            => ['nullable', 'numeric', 'required_with:'.$this->prefix.'price_after_tax'],
            $this->prefix.'price_after_tax'             => ['nullable', 'numeric', 'required_with:'.$this->prefix.'price_before_tax'],
        ];
    }

    public function messages(): array
    {
        return [
            $this->prefix.'desired_management.required'    => 'La gestion du transport est obligatoire',
            $this->prefix.'departure_step'                 => "L'étape de transport du départ est obligatoire",
            $this->prefix_account.'birth_date'             => __('validation.date', ['attribute' => strval(__('account.birth'))]),
            $this->prefix.'departure_start_time.regex'     => "L'heure de départ doit être au format HH:mm (de 00:00 à 23:59)",
            $this->prefix.'departure_end_time.regex'       => "L'heure d'arrivée doit être au format HH:mm (de 00:00 à 23:59)",
            $this->prefix.'return_start_time.regex'        => "L'heure de départ doit être au format HH:mm (de 00:00 à 23:59)",
            $this->prefix.'return_end_time.regex'          => "L'heure d'arrivée doit être au format HH:mm (de 00:00 à 23:59)",
            $this->prefix.'transfer_shuttle_time.regex'    => "L'heure doit être au format HH:mm (de 00:00 à 23:59)",
            $this->prefix.'price_before_tax.required_with' => 'Si Montant TTC est rempli, Montant HT doit également être renseigné.',
            $this->prefix.'price_after_tax.required_with'  => 'Si Montant HT est rempli, Montant TTC doit également être renseigné.',
            $this->prefix.'price_before_tax.numeric'       => 'Le champ Montant HT doit être un chiffre valide.',
            $this->prefix.'price_after_tax.numeric'        => 'Le champ Montant TTC doit être un chiffre valide.',
        ];
    }
}

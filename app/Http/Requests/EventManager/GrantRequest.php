<?php

namespace App\Http\Requests\EventManager;

use App\Enum\AmountType;
use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GrantRequest extends FormRequest
{
    use Locale;

    private string $prefix;

    private GrantContactRequest $contact;

    public function __construct()
    {
        parent::__construct();
        $this->prefix  = 'grant.';
        $this->contact = new GrantContactRequest();
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
        $rules = [
            $this->prefix.'active'                            => 'nullable',
            $this->prefix.'title.'.app()->getFallbackLocale() => 'required',
            $this->prefix.'amount'                            => 'required|numeric|gt:0',
            $this->prefix.'amount_type'                       => Rule::in(AmountType::values()),
            $this->prefix.'pax_min'                           => 'nullable|integer',
            $this->prefix.'pax_max'                           => 'nullable|integer',
            $this->prefix.'pax_avg'                           => 'nullable|integer',
            $this->prefix.'pec_fee'                           => 'required|numeric',
            $this->prefix.'deposit_fee'                       => 'required|numeric',
            $this->prefix.'manage_transport_upfront'          => 'nullable',
            $this->prefix.'manage_transfert_upfront'          => 'nullable',
            $this->prefix.'age_eligible_min'                  => 'nullable|numeric',
            $this->prefix.'age_eligible_max'                  => 'nullable|numeric',
            $this->prefix.'refund_transport'                  => 'nullable',
            $this->prefix.'refund_transport_amount'           => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (request()->filled($this->prefix.'refund_transport')) {
                        if ((int)$value < 1) {
                            $fail(__('validation.gt.numeric', ['attribute' => "Le montant du remboursement transport", 'value' => 0]));
                        }
                    }
                },
            ],
            $this->prefix.'comment'                           => 'nullable',
            $this->prefix.'prenotification_date'              => 'required|date_format:d/m/Y',
        ];

        foreach (config('translatable.locales') as $locale) {
            $rules[$this->prefix.'refund_transport_text.'.$locale] = 'nullable';

            if ($locale === config('translatable.fallback_locale')) {
                $rules[$this->prefix.'refund_transport_text.'.$locale] = 'required_if:'.$this->prefix.'refund_transport,1';
            }
        }

        return array_merge(
            $rules,
            $this->contact->rules(),
        );
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return array_merge(
            [
                $this->prefix.'title.'.app()->getFallbackLocale().'.required'                    => __('validation.required', ['attribute' => "L'intitulé"]),
                $this->prefix.'amount.required'                                                  => __('validation.required', ['attribute' => "Le montant HT"]),
                $this->prefix.'amount.numeric'                                                   => __('validation.numeric', ['attribute' => "Le montant HT"]),
                $this->prefix.'amount.gt'                                                        => __('validation.gt.numeric', ['attribute' => "Le montant du grant"]),
                $this->prefix.'amount.in'                                                        => __('validation.in', ['attribute' => "Le type de montant", 'values' => "HT, TTC"]),
                /* $this->prefix . 'pax_min.required' => __('validation.required', ['attribute' => "L'indication du nombre min de pers."]),
                $this->prefix . 'pax_min.integer' => __('validation.integer', ['attribute' => "L'indication du nombre min de pers."]),
                $this->prefix . 'pax_max.required' => __('validation.required', ['attribute' => "L'indication du nombre max de pers."]),
                $this->prefix . 'pax_max.integer' => __('validation.integer', ['attribute' => "L'indication du nombre max de pers."]),
                $this->prefix . 'pax_avg.required' => __('validation.required', ['attribute' => "L'indication du nombre moyen de pers."]),
                $this->prefix . 'pax_avg.integer' => __('validation.integer', ['attribute' => "L'indication du nombre moyen de pers."]),*/
                $this->prefix.'pec_fee.required'                                                 => __('validation.required', ['attribute' => "L'indication des frais de dossier"]),
                $this->prefix.'pec_fee.numeric'                                                  => __('validation.numeric', ['attribute' => "L'indication des frais de dossier"]),
                $this->prefix.'deposit_fee.required'                                             => __('validation.required', ['attribute' => "Le montant de la caution"]),
                $this->prefix.'deposit_fee.numeric'                                              => __('validation.numeric', ['attribute' => "Le montant de la caution"]),
                $this->prefix.'refund_transport_amount.required_if'                              => __('validation.required', ['attribute' => "Le montant du remboursement transport"]),
                $this->prefix.'refund_transport_amount.gt'                                       => __('validation.gt.numeric', ['attribute' => "Le montant du remboursement transport"]),
                $this->prefix.'refund_transport_text.'.app()->getFallbackLocale().'.required_if' => __('validation.required', ['attribute' => "Le texte concernant le remboursement transport"]),
                $this->prefix.'comment'                                                          => 'nullable',
                $this->prefix.'prenotification_date.required'                                    => __('validation.required', ['attribute' => "La date de l'envoi pour la liste prélimanaire"]),
                $this->prefix.'prenotification_date.date_format'                                 => __('validation.date_format', ['attribute' => "La date de l'envoi pour la liste prélimanaire", 'format' => 'd/m/Y']),
            ],
            $this->contact->messages(),
        );
    }
}

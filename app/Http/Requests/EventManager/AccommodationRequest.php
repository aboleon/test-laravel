<?php

namespace App\Http\Requests\EventManager;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class AccommodationRequest extends FormRequest
{
    use Locale;

    private string $prefix;

    private AccommodationServiceRequest $service_validation;
    private AccommodationDepositRequest $deposit_validation;

    public function __construct()
    {
        parent::__construct();
        $this->setPrefix('accommodation');
        $this->service_validation = new AccommodationServiceRequest();
        $this->deposit_validation = new AccommodationDepositRequest();
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
            $this->prefix . 'processing_fee' => 'nullable|numeric',
            $this->prefix . 'processing_fee_vat_id' => 'required',
            $this->prefix . 'total_cancellation' => 'nullable|numeric',
            $this->prefix . 'turnover' => 'nullable|numeric',
            $this->prefix . 'title.' . $this->defaultLocale() => 'nullable|string',
            $this->prefix . 'description.' . $this->defaultLocale() => 'nullable|string',
            $this->prefix . 'cancellation.' . $this->defaultLocale() => 'nullable|string',
            $this->prefix . 'comission' => 'numeric',
            $this->prefix . 'comission_room' => 'nullable|numeric',
            $this->prefix . 'comission_breakfast' => 'nullable|numeric',
        ],
            $this->service_validation->rules(),
            $this->deposit_validation->rules(),
        );
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return array_merge([
            $this->prefix . 'processing_fee.numeric' => __('validation.string', ['attribute' => 'Frais de dossier']),
            $this->prefix . 'processing_fee_vat_id.required' => __('validation.required', ['attribute' => 'TVA du frais de dossier']),
            $this->prefix . 'total_cancellation.numeric' => __('validation.string', ['attribute' => 'Montant des annulations']),
            $this->prefix . 'total_commission.numeric' => __('validation.string', ['attribute' => 'Total commission']),
            $this->prefix . 'turnover.numeric' => __('validation.string', ['attribute' => 'CA Total']),
            $this->prefix . 'title.' . $this->defaultLocale() . '.string' => __('validation.string', ['attribute' => __('ui.access')]),
            $this->prefix . 'description.' . $this->defaultLocale() . '.string' => __('validation.string', ['attribute' => 'Intitulé libre titre']),
            $this->prefix . 'cancellation.' . $this->defaultLocale() . '.string' => __('validation.string', ['attribute' => 'Intitulé libre contenu']),
            $this->prefix . 'comission.numeric' => __('validation.required', ['attribute' => "La comission attribuéе par l'hôtel"]),
            $this->prefix . 'comission_room.numeric' => __('validation.required', ['attribute' => "La comission chambre attribuéе par l'hôtel"]),
            $this->prefix . 'comission_breakfast.numeric' => __('validation.required', ['attribute' => "La comission PDJ attribuéе par l'hôtel"]),
        ],
            $this->service_validation->messages(),
            $this->deposit_validation->messages(),
        );
    }
}

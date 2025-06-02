<?php

namespace App\Http\Requests\EventManager;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class AccommodationDepositRequest extends FormRequest
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
        $this->setPrefix('deposit');
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
        return [
            $this->prefix . 'amount.*' => 'nullable|numeric|min:1',
            $this->prefix . 'paid_at.*' =>'required_with:'.$this->prefix .'amount.*|date_format:"d/m/Y"',
        ];
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return [
            $this->prefix . 'amount.*.numeric' => "Les montants d'acompte doivent être tous des chiffres",
            $this->prefix . 'amount.*.min' => "Les montants d'acompte doivent être au minimum de 1",
            $this->prefix . 'paid_at.required_with' => "Les dates d'acompte doivent être toutes renseignées.",
            $this->prefix . 'paid_at.*.date_format' => "Les dates d'acompte doivent être au format dd/mm/aaaa.",
        ];
    }
}

<?php

namespace App\Http\Requests\EventManager;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;

class GrantDepositRequest extends FormRequest
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
            'amount' => 'required|numeric|min:1',
        ];
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return [
            'amount.required' => __('validation.required', ['attribute' => 'Le montant de la caution']),
            'amount.numeric' => __('validation.numeric', ['attribute' => 'Le montant de la caution']),
            'amount.min' => 'Le montant de la caution ne peut pas être zéro',
        ];
    }
}

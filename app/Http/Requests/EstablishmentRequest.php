<?php

namespace App\Http\Requests;

use App\Enum\EstablishmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class EstablishmentRequest extends FormRequest
{

    private string $prefix = 'establishment';

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
            $this->prefix . '.name' => 'required|string',
            $this->prefix . '.type' => ['required', new Enum(EstablishmentType::class)],
            $this->prefix . '.street_number' => 'nullable|string',
            $this->prefix . '.route' => 'nullable|string',
            $this->prefix . '.postal_code' => 'nullable|numeric',
            $this->prefix . '.locality' => 'required|string',
            $this->prefix . '.country_code' => 'required|string',
            $this->prefix . '.administrative_area_level_1' => 'nullable|string',
            $this->prefix . '.administrative_area_level_2' => 'nullable|string',
            $this->prefix . '.text_address' => 'required|string',
            $this->prefix . '.lat' => 'nullable|numeric',
            $this->prefix . '.lon' => 'nullable|numeric',
            $this->prefix . '.prefix' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            $this->prefix . '.name.required' => __('validation.required', ['attribute' => __('account.last_name')]),
            $this->prefix . '.type.required' => __('validation.required', ['attribute' => __('ui.establishments.type')]),
            $this->prefix . '.locality.required' => __('validation.required', ['attribute' => __('mfw.geo.locality')]),
            $this->prefix . '.country_code.required' => __('validation.required', ['attribute' => __('mfw.geo.country')]),
            $this->prefix . '.text_address.required' => __('validation.required', ['attribute' => __('ui.hotels.address')]),
            $this->prefix . '.prefix.string' => __('validation.string', ['attribute' => "Complément d'adresse"]),
        ];
    }
}

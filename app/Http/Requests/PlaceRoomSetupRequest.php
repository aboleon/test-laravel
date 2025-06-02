<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceRoomSetupRequest extends FormRequest
{

    private string $prefix;

    public function __construct()
    {
        parent::__construct();
        $this->setPrefix('place_room_setup');
    }

    public function setPrefix(string $prefix): static
    {
        $this->prefix = $prefix . '.';
        return $this;
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
            $this->prefix . 'name.*' => 'required',
            $this->prefix . 'capacity.*' => 'nullable|numeric|min:1',
            $this->prefix . 'description.*' => 'nullable|string',
        ];
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return [
            $this->prefix . 'name.*.required' => "Les titres des mises en places sont requis",
            $this->prefix . 'capacity.*.numeric' => "Les montants d'acompte doivent être tous des chiffres",
        ];
    }
}

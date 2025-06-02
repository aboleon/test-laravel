<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Validation\Event\EventConfigValidation;
use App\Validation\Event\EventTextsValidation;
use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{

    private EventConfigValidation $config_validation;
    private EventTextsValidation $texts_validation;

    public function __construct()
    {
        parent::__construct();
        $this->config_validation = new EventConfigValidation();
        $this->texts_validation = new EventTextsValidation();
    }

    /**
     * Determine if the user is authorized to make this request.
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
        return array_merge(
            $this->config_validation->rules(),
            $this->texts_validation->rules(),
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return array_merge(
            $this->config_validation->messages(),
            $this->texts_validation->messages(),
        );
    }
}

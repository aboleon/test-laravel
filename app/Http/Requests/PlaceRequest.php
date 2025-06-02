<?php

namespace App\Http\Requests;

use App\Traits\Locale;
use Illuminate\Foundation\Http\FormRequest;
use MetaFramework\Services\Validation\GoogleAddressValidation;

class PlaceRequest extends FormRequest
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
        $this->setPrefix('place');
        $this->address_validation = (new GoogleAddressValidation())->setPrefix('wa_geo')->logic();
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
        return array_merge(
            [
                $this->prefix . 'name' => 'bail|required|string',
                $this->prefix . 'place_type_id' => 'nullable|int',
                $this->prefix . 'email' => 'nullable|email',
                $this->prefix . 'website' => 'nullable|string',
                $this->prefix . 'phone' => 'nullable|numeric',
                $this->prefix . 'description.' . $this->defaultLocale() => 'nullable|string',
                $this->prefix . 'access.' . $this->defaultLocale() => 'nullable|string',
                $this->prefix . 'more_title.' . $this->defaultLocale() => 'nullable|string',
                $this->prefix . 'more_description.' . $this->defaultLocale() => 'nullable|string',
            ],
            $this->address_validation['rules'],
        );
    }


    /**
     * @return array<string,mixed>
     */
    public function messages(): array
    {
        return array_merge(
            [
                $this->prefix . 'name.required' => __('validation.required', ['attribute' => strval(__('mfw.name'))]),
                $this->prefix . 'name.string' => __('validation.string', ['attribute' => strval(__('mfw.name'))]),
                $this->prefix . 'email.email' => __('validation.required', ['attribute' => strval(__('ui.email_address'))]),
                $this->prefix . 'website.string' => __('validation.string', ['attribute' => __('forms.website')]),
                $this->prefix . 'phone.numeric' => __('validation.phone', ['attribute' => strval(__('account.phone'))]),
                $this->prefix . 'description.' . $this->defaultLocale() . 'string' => __('validation.string', ['attribute' => __('mfw.description')]),
                $this->prefix . 'access.' . $this->defaultLocale() . '.string' => __('validation.string', ['attribute' => __('ui.access')]),
                $this->prefix . 'more_title.' . $this->defaultLocale() . '.string' => __('validation.string', ['attribute' => 'Intitulé libre titre']),
                $this->prefix . 'more_description.' . $this->defaultLocale() . '.string' => __('validation.string', ['attribute' => 'Intitulé libre contenu']),
            ],
            $this->address_validation['messages']
        );
    }
}

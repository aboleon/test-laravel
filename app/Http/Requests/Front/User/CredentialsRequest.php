<?php

namespace App\Http\Requests\Front\User;

use Illuminate\Foundation\Http\FormRequest;
use MetaFramework\Services\Passwords\PasswordValidationSet;

class CredentialsRequest extends FormRequest
{
    private array $password_validation;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $editPassword = (bool)request('edit_password', true);

        if (
            !$editPassword
        ) {
            $this->password_validation = [
                'rules' => [],
                'messages' => [],
            ];
            return;
        }
        $this->password_validation = (new PasswordValidationSet(request()))->logic();
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return $this->password_validation['rules'];
    }

    public function messages()
    {
        return $this->password_validation['messages'];
    }

}

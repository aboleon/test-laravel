<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsObject implements Rule
{

    public function passes($attribute, $value): bool
    {
        return class_exists($value);
    }

    public function message(): string
    {
        return "L'objet ".request('object')." n'existe pas.";
    }
}

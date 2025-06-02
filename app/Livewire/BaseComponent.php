<?php

namespace App\Livewire;

use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

class BaseComponent extends Component
{
    protected function getDisplayMessage(Throwable $e): string
    {
        $displayMessage = $e->getMessage();
        if ($e instanceof ValidationException) {
            $displayMessage = "";
            foreach ($e->errors() as $k => $v) {
                $displayMessage .= implode("\n", $v);
                $displayMessage .= "\n";
            }
        }
        return $displayMessage;
    }
}
<?php

namespace App\Traits\Livewire;

trait LivewireModalTrait
{


    protected function modalError(string $message, ?string $title = null)
    {
        $this->dispatch("LivewireModal.show", "danger", $message, $title);
    }

    protected function modalInfo(string $message, ?string $title = null)
    {
        $this->dispatch("LivewireModal.show", "info", $message, $title);
    }

    protected function modalWarning(string $message, ?string $title = null)
    {
        $this->dispatch("LivewireModal.show", "warning", $message, $title);
    }

    protected function modalSuccess(string $message, ?string $title = null)
    {
        $this->dispatch("LivewireModal.show", "success", $message, $title);
    }
}
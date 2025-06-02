<?php

namespace App\Livewire\Traits;

trait WithAlertNotifications
{


    public string $alertMessage;
    public string $alertMessageType = 'info';


    //--------------------------------------------
    //
    //--------------------------------------------
    private function success(string $message)
    {
        $this->alertMessage = $message;
        $this->alertMessageType = 'success';
    }

    private function info(string $message)
    {
        $this->alertMessage = $message;
        $this->alertMessageType = 'info';
    }

    private function warning(string $message)
    {
        $this->alertMessage = $message;
        $this->alertMessageType = 'warning';
    }

    private function error(string $message)
    {
        $this->alertMessage = $message;
        $this->alertMessageType = 'danger';
    }
}

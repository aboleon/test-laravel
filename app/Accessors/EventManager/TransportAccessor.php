<?php

namespace App\Accessors\EventManager;

use App\Enum\DesiredTransportManagement;
use App\Models\EventManager\Transport\EventTransport;
use App\Traits\Models\EventTransportModelTrait;

class TransportAccessor
{
    use EventTransportModelTrait;

    public function isDivine(): bool
    {
        return $this->eventTransport?->desired_management == DesiredTransportManagement::DIVINE->value;
    }

    public static function transportDepartureStepIsOk(EventTransport $transport): bool
    {
        if ($transport->departureStep) {
            return str_ends_with(strtolower($transport->departureStep->name), 'ok');
        }
        return false;
    }

    public static function transportReturnStepIsOk(EventTransport $transport): bool
    {
        if ($transport->returnStep) {
            return str_ends_with(strtolower($transport->returnStep->name), 'ok');
        }
        return false;
    }

    public function managementHasChanged(): bool
    {
        if (!$this->eventTransport) {
            return false;
        }

        return !is_null($this->eventTransport->management_history) && ($this->eventTransport->desired_management != $this->eventTransport->management_history);
    }

    public function managementChangeWasNotified(): bool
    {
        return !is_null($this->eventTransport->management_mail);
    }
}

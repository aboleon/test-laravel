<?php

namespace App\Services\Availability\Exceptions;

class AvailabilityException extends \Exception
{
    public static function invalidDateRange(): self
    {
        return new self('Invalid date range provided');
    }

    public static function hotelNotFound(int $id): self
    {
        return new self("Hotel with ID {$id} not found");
    }
}

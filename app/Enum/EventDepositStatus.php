<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum EventDepositStatus: string implements BackedEnumInteface
{

    case PAID = 'paid';
    case REFUNDED = 'refunded';
    case BILLED = 'billed';
    case UNPAID = 'unpaid';
    case TEMP = 'temp';

    use BackedEnum;

    public static function default(): string
    {
        return self::UNPAID->value;
    }

    public static function paid(): array
    {
        return [
            self::PAID->value,
            self::BILLED->value,
        ];
    }

    public static function deletableStates(): array
    {
        return [
          self::UNPAID->value,
          self::TEMP->value,
        ];
    }

}

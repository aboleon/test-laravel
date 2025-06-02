<?php

namespace App\Enum;


use MetaFramework\Interfaces\BackedEnumInteface;
use MetaFramework\Traits\BackedEnum;

enum DesiredTransportManagement: string implements BackedEnumInteface
{

    case PARTICIPANT = 'participant';
    case DIVINE = 'divine';
    case UNNECESSARY = 'unnecessary';

    use BackedEnum;

    public static function default(): string
    {
        return self::UNNECESSARY->value;
    }

    public static function mapping(): array
    {
        return [
            1 => self::PARTICIPANT->value,
            2 => self::DIVINE->value,
            3 => self::UNNECESSARY->value,
        ];
    }

    public static function mapByKey(int $key): string
    {
        return self::translated(self::mapping()[$key] ?? 'NA');
    }


    public static function mapByKeyword(?string $key): ?string
    {
        return array_flip(self::mapping())[$key] ?? null;
    }

}

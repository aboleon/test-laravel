<?php

namespace Database\Seeders\Devs\Ling;

class SeederHelper
{

    private static $id2Names = [
        10 => ['Abdel', 'Jeanmi'],
        11 => ['Irvine', 'Hanskelli'],
        12 => ['Nuella', 'Komerani'],
        13 => ['Peter', 'Kamskinov'],
        14 => ['Amélie', 'Bourg'],
        15 => ['Karnel', 'Hindrash'],
        16 => ['Sopoyo', 'Kilitanman'],
        17 => ['Vérane', 'Bergeron'],
        18 => ['Avenir', 'Poullard'],
        19 => ['Elena', 'Martinez'],
        20 => ['Lucas', 'Gonzalez'],
        21 => ['Sophia', 'Roberts'],
        22 => ['Max', 'Turner'],
    ];

    public static function getNameInfoById(int $id): array
    {
        return self::$id2Names[$id];
    }
}
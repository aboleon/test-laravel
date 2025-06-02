<?php

namespace App\Helpers\Front;

use App\Helpers\Price\DivinePrice;
use App\Models\FrontCartLine;

class DivinePriceHelper
{

    public static function getDivinePrice(FrontCartLine $cartLine): DivinePrice
    {
        return new DivinePrice([
            'net' => $cartLine->total_net,
            'vat' => $cartLine->total_ttc - $cartLine->total_net,
        ]);
    }
}
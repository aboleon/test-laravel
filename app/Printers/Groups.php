<?php

namespace App\Printers;

use App\Models\GroupAddress;

class Groups
{
    public static function address(GroupAddress $data): string
    {
        $output = '<address>' .
            '<h5 class="card-title text-dark">' . ($data->name ?: 'Sans titre') . '</h5>' .
            trim($data->street_number . ' ' . $data->route) . '<br>' .
            trim($data->postal_code . ' ' . $data->locality) . '<br>' .
            $data->country_code . '<br>';

        if ($data->billing) {
            $output .= '<h6 class="mt-2"><span class="badge bg-warning text-dark">Adresse de facturation</span></h6>';
        }
        $output .= '</address>';


        return $output;
    }

    public static function generateTextAddress(?GroupAddress $data) : string
    {
        $address = trim($data->street_number . ' ' . $data->route) . ',' . trim($data->postal_code . ' ' . $data->locality) . ',' . \MetaFramework\Accessors\Countries::getCountryNameByCode($data->country_code);
        return $address ?? '';
    }

}

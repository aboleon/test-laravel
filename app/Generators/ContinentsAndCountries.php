<?php

namespace App\Generators;

use App\Accessors\Countries;
use App\Models\Continent;
use App\Models\Country;

class ContinentsAndCountries
{

    public static function feedContinentsTable()
    {
        $continents = [
            "Africa",
            "Antarctica",
            "Asia",
            "Europe",
            "North America",
            "Oceania",
            "South America",
        ];
        foreach ($continents as $continent) {
            Continent::firstOrCreate(['name' => $continent]);
        }
    }


    public static function addContinentsToCountryTable()
    {
        $continents = Continent::all()->pluck('name', 'id')->toArray();
        $countryId2Code = Country::pluck('code', "id")->toArray();

        $continent2CountryCodes = [
            "Africa" => [
                "DZ", "AO", "BJ", "BW", "BF", "BI", "CV", "CM", "CF", "TD", "KM", "CG", "CD", "CI", "DJ", "EG", "GQ", "ER", "ET", "GA", "GM", "GH", "GN", "GW", "KE", "LS", "LR", "LY", "MG", "MW", "ML", "MR", "MU", "YT", "MA", "MZ", "NA", "NE", "NG", "RE", "RW", "SH", "ST", "SN", "SC", "SL", "SO", "ZA", "SS", "SD", "SZ", "TZ", "TG", "TN", "UG", "EH", "ZM", "ZW",
            ],
            "Antarctica" => [
                "AQ", "BV", "TF", "HM", "GS",
            ],
            "Asia" => [
                "AF", "AM", "AZ", "BH", "BD", "BT", "IO", "BN", "KH", "CN", "CX", "CC", "CY", "GE", "HK", "IN", "ID", "IR", "IQ", "IL", "JP", "JO", "KZ", "KP", "KR", "KW", "KG", "LA", "LB", "MO", "MY", "MV", "MN", "MM", "NP", "OM", "PK", "PS", "PH", "QA", "SA", "SG", "LK", "SY", "TW", "TJ", "TH", "TL", "TM", "TR", "AE", "UZ", "VN", "YE",
            ],
            "Europe" => [
                "AL", "AD", "AT", "AX", "BY", "BE", "BA", "BG", "HR", "CS", "CZ", "DK", "EE", "FO", "FI", "FR", "DE", "GI", "GR", "GL", "GG", "VA", "HU", "IS", "IE", "IM", "IT", "JE", "LV", "LI", "LT", "LU", "MK", "MT", "MD", "MC", "ME", "NL", "NO", "PL", "PT", "RO", "RU", "SM", "RS", "SK", "SI", "ES", "SJ", "SE", "CH", "UA", "GB",
            ],
            "North America" => [
                "AI", "AG", "AW", "BS", "BB", "BZ", "BM", "CA", "KY", "CR", "CU", "DM", "DO", "SV", "GL", "GD", "GP", "GT", "HT", "HN", "JM", "MQ", "MX", "MS", "AN", "NI", "PA", "PR", "BL", "KN", "LC", "MF", "PM", "VC", "TT", "TC", "US", "VG", "VI",
            ],
            "Oceania" => [
                "AS", "AU", "CK", "FJ", "PF", "GU", "KI", "MH", "FM", "NR", "NC", "NZ", "NU", "NF", "MP", "PW", "PG", "PN", "WS", "SB", "TK", "TO", "TV", "UM", "VU", "WF",
            ],
            "South America" => [
                "AR", "BO", "BR", "CL", "CO", "EC", "FK", "GF", "GY", "PY", "PE", "SR", "UY", "VE",
            ],
        ];
        foreach ($continent2CountryCodes as $continentName => $countryCodes) {
            $continentId = array_search($continentName, $continents);

            foreach ($countryCodes as $code) {
                $countryId = array_search($code, $countryId2Code);

                if ($countryId !== false && $continentId !== false) {
                    Country::find($countryId)->update(['continent_id' => $continentId]);
                }
            }
        }
    }

}
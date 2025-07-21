<?php

namespace Database\Seeders\Account;


use App\Models\AccountAddress;
use Illuminate\Database\Seeder;

class AccountAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 10; $i <= 22; $i++) {

            AccountAddress::updateOrCreate(['id' => $i], [
                'user_id' => $i,
                'billing' => "0",
                'street_number' => "9",
                'route' => "rue fleurie",
                'locality' => "Paris",
                'postal_code' => "75009",
                'country_code' => "FR",
                'text_address' => "9 rue fleurie 75009 Paris",
                'name' => "Maison",
            ]);
        }
    }
}

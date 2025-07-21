<?php

namespace Database\Seeders\Account;


use App\Models\AccountPhone;
use App\Models\AccountProfile;
use Database\Seeders\Devs\Ling\SeederHelper;
use Illuminate\Database\Seeder;

class AccountPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 10; $i <= 22; $i++) {
            AccountPhone::updateOrCreate(['id' => $i], [
                'user_id' => $i,
                'country_code' => "FR",
                'default' => "1",
                'phone' => "+33653154267",
            ]);
        }
    }
}

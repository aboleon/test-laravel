<?php

namespace Database\Seeders\Account;


use App\Models\AccountProfile;
use Database\Seeders\Devs\Ling\SeederHelper;
use Illuminate\Database\Seeder;

class AccountProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 10; $i <= 22; $i++) {

            list($firstName, $lastName) = SeederHelper::getNameInfoById($i);


            AccountProfile::updateOrCreate(['id' => $i], [
                'user_id' => $i,
                'account_type' => "company",
                'base_id' => "9",
                'domain_id' => "11",
                'title_id' => "12",
                'profession_id' => "2",
                'civ' => "M",
                'created_by' => "1",
                'passport_first_name' => $firstName,
                'passport_last_name' => $lastName,
            ]);
        }
    }
}

<?php

namespace Database\Seeders\Account;


use App\Models\AccountCard;
use Illuminate\Database\Seeder;

class AccountCardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 1; $i <= 12; $i++) {
            $userId = 9 + $i;
            AccountCard::updateOrCreate(['id' => $i], [
                'user_id' => $userId,
                'name' => "Carte fidélité " . $userId,
                'serial' => "9870ze54r40z8",
                'expires_at' => "2026-01-01",
            ]);
        }

        for ($i = 1; $i <= 12; $i++) {
            $id = 12 + $i;
            $userId = 9 + $i;
            AccountCard::updateOrCreate(['id' => $id], [
                'user_id' => $userId,
                'name' => "Carte fidélité " . $userId . " (bis)",
                'serial' => "zrze05e9881322",
                'expires_at' => "2027-01-01",
            ]);
        }
    }
}

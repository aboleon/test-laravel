<?php

namespace Database\Seeders\Account;


use App\Models\AccountCard;
use App\Models\AccountDocument;
use Illuminate\Database\Seeder;

class AccountDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 1; $i <= 12; $i++) {
            $userId = 9 + $i;
            AccountDocument::updateOrCreate(['id' => $i], [
                'user_id' => $userId,
                'name' => "Carte identité " . $userId,
                'serial' => "998-7754-204-EL",
                'emitted_at' => "2026-01-01",
                'expires_at' => "2036-01-01",
            ]);
        }

        for ($i = 1; $i <= 12; $i++) {
            $id = 12 + $i;
            $userId = 9 + $i;
            AccountDocument::updateOrCreate(['id' => $id], [
                'user_id' => $userId,
                'name' => "Carte identité " . $userId . " (bis)",
                'serial' => "998-7754-204-ELS",
                'emitted_at' => "2027-01-01",
                'expires_at' => "2037-01-01",
            ]);
        }
    }
}

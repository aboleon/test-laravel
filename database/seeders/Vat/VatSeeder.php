<?php

namespace Database\Seeders\Vat;

use App\Models\Vat;
use Illuminate\Database\Seeder;

class VatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vat::updateOrCreate(['id' => 1], ['rate' => 2000, 'default' => '1']);
    }
}

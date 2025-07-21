<?php

namespace Database\Seeders;

use MetaFramework\Models\Vat;
use Illuminate\Database\Seeder;

class VatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vat::insert([
                [
                    'rate' => 2000,
                    'default' => 1
                ],
                [
                    'rate' => 1000,
                    'default' => null
                ],
                [
                    'rate' => 550,
                    'default' => null
                ],
                [
                    'rate' => 210,
                    'default' => null
                ],
                [
                    'rate' => 0,
                    'default' => null
                ],
            ]
        );
    }
}

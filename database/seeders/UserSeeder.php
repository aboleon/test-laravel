<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::updateOrCreate(
            ['email' => 'dev@wagaia.com'],
            [
                'first_name' => 'Dev',
                'last_name' => 'Wagaia',
                'password' => bcrypt('wagadmin'),
            ]);

        $user->roles()->save(new UserRole(['role_id' => 1]));
    }
}

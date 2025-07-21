<?php

namespace Database\Seeders\User;

use App\Enum\UserType;
use App\Models\User;
use Database\Seeders\Devs\Ling\SeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        $users = [
            [
                'id' => 10,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(10)[0],
                'last_name' => SeederHelper::getNameInfoById(10)[1],
                'email' => 'abdeljeanmi@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 11,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(11)[0],
                'last_name' => SeederHelper::getNameInfoById(11)[1],
                'email' => 'irvinehanskelli@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 12,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(12)[0],
                'last_name' => SeederHelper::getNameInfoById(12)[1],
                'email' => 'nuellakomerani@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 13,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(13)[0],
                'last_name' => SeederHelper::getNameInfoById(13)[1],
                'email' => 'peterkamskinov@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 14,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(14)[0],
                'last_name' => SeederHelper::getNameInfoById(14)[1],
                'email' => 'ameliebourg@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 15,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(15)[0],
                'last_name' => SeederHelper::getNameInfoById(15)[1],
                'email' => 'karnelhindrash@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 16,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(16)[0],
                'last_name' => SeederHelper::getNameInfoById(16)[1],
                'email' => 'sopoyokilitanman@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 17,
                'type' => UserType::SYSTEM,
                'first_name' => SeederHelper::getNameInfoById(17)[0],
                'last_name' => SeederHelper::getNameInfoById(17)[1],
                'email' => 'veranebergeron@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 18,
                'type' => UserType::SYSTEM,
                'first_name' => SeederHelper::getNameInfoById(18)[0],
                'last_name' => SeederHelper::getNameInfoById(18)[1],
                'email' => 'avenirpoullard@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 19,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(19)[0],
                'last_name' => SeederHelper::getNameInfoById(19)[1],
                'email' => 'elenamartinez@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 20,
                'type' => UserType::ACCOUNT,
                'first_name' => SeederHelper::getNameInfoById(20)[0],
                'last_name' => SeederHelper::getNameInfoById(20)[1],
                'email' => 'lucasgonzalez@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 21,
                'type' => UserType::SYSTEM,
                'first_name' => SeederHelper::getNameInfoById(21)[0],
                'last_name' => SeederHelper::getNameInfoById(21)[1],
                'email' => 'sophiaroberts@gmail.com',
                'password' => Hash::make('pass')
            ],
            [
                'id' => 22,
                'type' => UserType::SYSTEM,
                'first_name' => SeederHelper::getNameInfoById(22)[0],
                'last_name' => SeederHelper::getNameInfoById(22)[1],
                'email' => 'maxturner@gmail.com',
                'password' => Hash::make('pass')
            ],
        ];


        foreach ($users as $user) {
            User::updateOrCreate(
                ['id' => $user['id']],
                $user
            );
        }
    }
}
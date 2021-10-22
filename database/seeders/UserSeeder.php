<?php

namespace Database\Seeders;

use App\Models\User;
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
        $users = [
            [
                'name'     => 'Flashbox Administrator',
                'email'    => 'admin@flashbox.com',
                'password' => 'admin',
                'roles'    => ['admin']
            ],
            [
                'name'     => 'Moein Pakkhesal',
                'email'    => 'moein@flashbox.com',
                'password' => 'moein',
                'roles'    => ['seller']
            ],
            [
                'name'     => 'Vahid Ramezanipour',
                'email'    => 'vahid@flashbox.com',
                'password' => 'vahid',
                'roles'    => ['customer']
            ]
        ];

        $existedUsers = User::whereIn('email', array_map(fn($u) => $u['email'], $users))->pluck('email')->toArray();

        foreach($users as $userData){
            if(!in_array($userData['email'] , $existedUsers)){
                $existedUsers[] = $userData['email'];
                $roles = $userData['roles'];
                unset($userData['roles']);

                $userData['password'] = \Illuminate\Support\Facades\Hash::make($userData['password']);
                $user = \App\Models\User::firstOrCreate($userData);

                $rolesId = \App\Models\Role::whereIn('identity', $roles)->pluck('id')->toArray();
                $user->roles()->sync($rolesId);
            }
        }

    }
}

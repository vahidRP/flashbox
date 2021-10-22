<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'title'          => 'Administrator',
                'identity'       => 'admin',
                'permissions_id' => \App\Models\Permission::where('identity', 'super-admin')
                    ->pluck('id')
                    ->toArray()
            ],
            [
                'title'          => 'Seller',
                'identity'       => 'seller',
                'permissions_id' => \App\Models\Permission::where('identity', '!=', 'super-admin')
                    ->where('identity', 'NOT LIKE', 'roles.%')
                    ->where('identity', 'NOT LIKE', 'permissions.%')
                    ->where(fn($q) => $q
                        ->where('identity', 'LIKE', '%.read')
                        ->orWhere('identity', 'LIKE', 'stores.%')
                        ->orWhere('identity', 'LIKE', 'addresses.%')
                        ->orWhere('identity', 'LIKE', 'receipts.%')
                        ->orWhere('identity', 'LIKE', 'products.%')
                    )
                    ->pluck('id')
                    ->toArray()
            ],
            [
                'title'          => 'Customer',
                'identity'       => 'customer',
                'permissions_id' => \App\Models\Permission::where('identity', '!=', 'super-admin')
                    ->where('identity', 'NOT LIKE', 'roles.%')
                    ->where('identity', 'NOT LIKE', 'permissions.%')
                    ->where(fn($q) => $q
                        ->where('identity', 'LIKE', '%.read')
                        ->orWhere('identity', 'LIKE', 'receipts.%')
                        ->orWhere('identity', 'LIKE', 'addresses.%')
                    )
                    ->pluck('id')
                    ->toArray()
            ]
        ];

        foreach($roles as $roleData){
            $permissionsId = $roleData['permissions_id'];
            unset($roleData['permissions_id']);

            $role = \App\Models\Role::firstOrCreate($roleData);

            if(!empty($permissionsId)){
                $role->permissions()->sync($permissionsId);
            }
        }

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            'manage catalog',
            'manage orders',
            'manage users',
            'access admin',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        // Super admin gets all permissions via Gate::before in AuthServiceProvider or similar, 
        // but let's assign them explicitly for now as well.
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(['manage catalog', 'manage orders', 'access admin']);

        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->syncPermissions(['manage catalog', 'access admin']);

        $customer = Role::firstOrCreate(['name' => 'Customer']);
        // Customers usually have no special permissions in the admin panel
    }
}

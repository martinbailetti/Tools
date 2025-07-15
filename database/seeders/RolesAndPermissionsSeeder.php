<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cache of roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define the permissions
        $permissions = [
            'edit users',
            'view users',
            'edit roles',
            'view roles',
            'edit permissions',
            'view permissions'
        ];

        // Create the permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create the 'super' role if it doesn't exist
        $roleSuper = Role::firstOrCreate(['name' => 'super', 'home_path' => '/security/users', 'guard_name' => 'api']);

        // Assign all permissions to the 'super' role
        $roleSuper->syncPermissions($permissions);

        $this->command->info('Permissions created and assigned to the "super" role successfully!');
    }
}

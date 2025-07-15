<?php

namespace Database\Seeders;

use App\Models\Security\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'super@super.com')->first();

        if (!$user) {
            // Create the super user
            $user = User::create([
                'name' => 'Super User',
                'email' => 'super@super.com',
                'password' => Hash::make('mypassword'),
            ]);

            // Assign 'super-admin' role to the user
            $role = Role::firstOrCreate(['name' => 'super']);
            $user->assignRole($role);

            $this->command->info('Super user created and assigned "super" role successfully!');
        } else {
            $this->command->warn('Super user already exists.');
        }
    }
}

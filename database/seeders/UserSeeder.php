<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        if (!User::where('email', 'admin@tiketin.com')->exists()) {
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@tiketin.com',
                'password' => Hash::make('password'), // You should change this later!
                'email_verified_at' => now(),
            ]);

            $superAdmin->assignRole('super_admin');
        }

        // Additional dummy users can be created here using User::factory()
        // User::factory(10)->create();
    }
}

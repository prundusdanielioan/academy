<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        if (!User::where('email', 'superadmin@lms.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@lms.com',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]);

            $this->command->info('Superadmin account created successfully!');
            $this->command->info('Email: superadmin@lms.com');
            $this->command->info('Password: superadmin123');
            $this->command->warn('Please change the password after first login!');
        } else {
            $this->command->info('Superadmin account already exists.');
        }
    }
}

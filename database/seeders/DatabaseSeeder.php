<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'System Admin',
            'phone' => '+10000000000',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);

        User::firstOrCreate([
            'email' => 'hr@example.com',
        ], [
            'name' => 'HR User',
            'phone' => '+10000000001',
            'password' => Hash::make('password'),
            'role' => 'HR',
        ]);
    }
}

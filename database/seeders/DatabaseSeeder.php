<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'username' => 'adminuser',
                'email' => 'admin@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone' => '1234567890',
                'address' => '123 Admin Street',
                'role' => 'admin',
                'remember_token' => Str::random(10), // Gunakan Str::random
                'created_at' => now(),
                'updated_at' => now(), // Add this line
            ],
            [
                'username' => 'customer1',
                'email' => 'customer1@gmail.com',
                'email_verified_at' => Carbon::now(),

                'password' => Hash::make('customer1'),
                'first_name' => 'Customer',
                'last_name' => 'Satu',
                'phone' => '1234567810',
                'address' => '123 Admin Street',
                'role' => 'customer',
                'remember_token' => Str::random(10), // Gunakan Str::random
                'created_at' => now(),
                'updated_at' => now(),
                // Add this line
            ],

            // Tambahkan lebih banyak pengguna jika diperlukan
        ]);
    }
}

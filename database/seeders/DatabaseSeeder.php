<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                'password' => Hash::make('password'),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone' => '1234567890',
                'address' => '123 Admin Street',
                'role' => 'admin',
                'email_verified_at' => Carbon::now(), // Add this line
            ],

            // Tambahkan lebih banyak pengguna jika diperlukan
        ]);
    }
}

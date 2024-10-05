<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'first_name' => 'Super',
            'last_name'=> 'Admin',
            'email' => 'superadmin@gmail.com',
            'password'=>Hash::make('123456789'),
            'user_type'=>'SUPER ADMIN',
            'status'=> 'active',
            'verify_email'=> 1,
            'otp'=> 0,

        ]);
        User::create([
            'first_name' => 'Admin',
            'last_name'=> 'Lamesha',
            'email' => 'lameshadavis@gmail.com',
            'password'=>Hash::make('123456789'),
            'user_type'=>'ADMIN',
            'status'=> 'active',
            'verify_email'=> 1,
            'otp'=> 0,

        ]);
    }
}

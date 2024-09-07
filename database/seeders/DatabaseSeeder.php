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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'first_name' => 'Abdur',
        //     'last_name'=> 'Rahman',
        //     'email' => 'engrabdurrahman4991@gmail.com',
        //     'user_type'=>'ADMIN',
        //     'password'=>bcrypt('123456789'),

        // ]);
        User::create([
            'first_name' => 'Abdur',
            'last_name'=> 'Rahman',
            'email' => 'engrabdurrahman4991@gmail.com',
            'password'=>Hash::make('123456789'),
            'user_type'=>'ADMIN',
            'verify_email'=> Carbon::now(),

        ]);
    }
}

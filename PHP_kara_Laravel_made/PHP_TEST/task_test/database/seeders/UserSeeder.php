<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            [
            'name' => 'あああ',
            'email' => 'test@test.com',
            'password' => Hash::make('password123'),
            ],
            [
                'name' => 'いいい',
                'email' => 'test2@test.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'ううう',
                'email' => 'test3@test.com',
                'password' => Hash::make('password123'),
            ],
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;
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
        \App\User::whereNotNull('id')->delete();

        \App\User::insert([
            'first_name' => 'Olexandr',
            'last_name' => 'Sydorenko',
            'email' => 'email@gmail.com',
            'password' => Hash::make('12345678')
        ]);

        factory(\App\User::class, 5)->create();
    }
}

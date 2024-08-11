<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['name' => 'Админ', 'email' => 'admin@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password'), 'is_admin' => true]);
        User::create(['name' => 'Клиент1', 'email' => 'client1@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password')]);
        User::create(['name' => 'Клиент2', 'email' => 'client2@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password')]);
        User::create(['name' => 'Клиент3', 'email' => 'client3@gmail.com', 'phone' => '+7(999)999-99-99', 'password' => Hash::make('password')]);
    }
}

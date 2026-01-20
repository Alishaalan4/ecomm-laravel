<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Hash; 
use App\Models\User;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
        ['email' => 'shaalanali20@gmail.com'],
        [
            'name' => 'Ali Shaalan',
            'password' => Hash::make('Shaalan1717@'),
            'role' => 'admin'
        ]
    );
    }
}

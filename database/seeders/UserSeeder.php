<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'mohamadpouran@gmail.com',
            ],
            [
                'name' => 'mrmp',
                'password' => Hash::make('789654123'),
            ]
        );
    }
}

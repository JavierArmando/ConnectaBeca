<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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
        // Crear usuario admin
        User::create([
            'name' => 'Admin',
            'lastname' => 'Sistema',
            'username' => 'admin',
            'email' => 'admin@ejemplo.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Crear usuario normal de ejemplo
        User::create([
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'username' => 'juan',
            'email' => 'juan@ejemplo.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
    }
}

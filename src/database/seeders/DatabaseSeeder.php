<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use App\Models\User; // Ya no es necesario si no interactúas con el modelo User aquí

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- COMENTA O ELIMINA ESTAS LÍNEAS RELACIONADAS CON EL USUARIO ---
        // Opción A: Borra usuarios existentes y crea uno nuevo
        // User::truncate();
        // User::create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password'),
        //     'email_verified_at' => now(),
        // ]);

        // Opción B: Crea un usuario de prueba solo si no existe
        // User::firstOrCreate(
        //     ['email' => 'test@example.com'],
        //     [
        //         'name' => 'Test User',
        //         'password' => bcrypt('password'),
        //         'email_verified_at' => now(),
        //     ]
        // );
        // --- FIN DE LÍNEAS RELACIONADAS CON EL USUARIO ---


        // ¡Importante! Asegúrate de que esta línea esté presente y sin comentar
        // Es la que llama a tu seeder de autos
        $this->call(AutoSeeder::class);

        echo "Todos los seeders han sido llamados.\n"; // Mensaje de depuración
    }
}

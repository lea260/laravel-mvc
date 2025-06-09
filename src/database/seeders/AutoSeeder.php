<?php

namespace Database\Seeders;

use App\Models\Auto; // Importa el modelo Auto
use Illuminate\Database\Seeder;

class AutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta múltiples registros de autos
        Auto::create([
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2020,
        ]);

        Auto::create([
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'anio' => 2022,
        ]);

        Auto::create([
            'marca' => 'Ford',
            'modelo' => 'Mustang',
            'anio' => 2023,
        ]);

        Auto::create([
            'marca' => 'Nissan',
            'modelo' => 'Sentra',
            'anio' => 2021,
        ]);

        echo "Se han agregado autos de ejemplo a la base de datos.\n"; // Mensaje de confirmación
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Beca;

class BecaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $becas = [
            [
                'nombre' => 'Beca de Excelencia Académica',
                'tipo' => 'Académica',
                'monto' => 5000,
                'descripcion' => 'Para estudiantes con promedio mayor a 9.0'
            ],
            [
                'nombre' => 'Beca para Estudiantes de Bajos Recursos',
                'tipo' => 'Económica',
                'monto' => 3000,
                'descripcion' => 'Apoyo financiero para estudiantes de escasos recursos'
            ],
            [
                'nombre' => 'Beca de Investigación',
                'tipo' => 'Investigación',
                'monto' => 8000,
                'descripcion' => 'Para proyectos de investigación científica'
            ],
            [
                'nombre' => 'Beca Internacional',
                'tipo' => 'Intercambio',
                'monto' => 10000,
                'descripcion' => 'Para estudiantes que realicen intercambio académico'
            ],
            [
                'nombre' => 'Beca Deportiva',
                'tipo' => 'Deporte',
                'monto' => 4000,
                'descripcion' => 'Para atletas destacados de la institución'
            ],
        ];

        foreach ($becas as $beca) {
            Beca::create($beca);
        }
    }
}

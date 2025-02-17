<?php

namespace App\Console\Commands;

use App\Models\HistorialPuesto;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CorteDeTurno extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:corte-de-turno';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza el corte de turnos al finalizar el día';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fecha_actual = Carbon::now()->format('Y-m-d');

        $puestos=DB::table('historial_puesto')
            ->whereDate('created_at', $fecha_actual)
            ->where('estado', 'activo')
            ->update([
                'estado' => 'inactivo', // Cambia el estado
                'updated_at' => now(), // Actualiza el timestamp
            ]);

        $this->info($puestos);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Beca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FavoriteController extends Controller
{
    /**
     * Asegurar que la tabla existe
     */
    private function ensureTableExists()
    {
        if (!Schema::hasTable('beca_user')) {
            try {
                DB::unprepared('
                    CREATE TABLE IF NOT EXISTS beca_user (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        user_id BIGINT UNSIGNED NOT NULL,
                        beca_id BIGINT UNSIGNED NOT NULL,
                        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        UNIQUE KEY unique_user_beca (user_id, beca_id),
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (beca_id) REFERENCES becas(id) ON DELETE CASCADE
                    )
                ');
            } catch (\Exception $e) {
                // La tabla podría ya existir
            }
        }
    }

    /**
     * Agregar o quitar una beca de favoritos
     */
    public function toggle(Request $request, $becaId)
    {
        $this->ensureTableExists();
        
        $user = Auth::user();
        $beca = Beca::find($becaId);

        if (!$beca) {
            return response()->json(['error' => 'Beca no encontrada'], 404);
        }

        // Verificar si ya está en favoritos
        if ($user->becasFavoritas()->where('beca_id', $becaId)->exists()) {
            // Quitar de favoritos
            $user->becasFavoritas()->detach($becaId);
            $isFavorite = false;
            $message = 'Eliminado de favoritos';
        } else {
            // Agregar a favoritos
            $user->becasFavoritas()->attach($becaId);
            $isFavorite = true;
            $message = 'Agregado a favoritos';
        }

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $message
        ]);
    }

    /**
     * Obtener todos los favoritos del usuario
     */
    public function index()
    {
        $this->ensureTableExists();
        
        $user = Auth::user();
        $becas = $user->becasFavoritas()->get();

        return view('favoritos', compact('becas'));
    }
}

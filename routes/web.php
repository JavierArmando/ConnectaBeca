<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Models\User; 
use App\Http\Controllers\BecaController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. RUTAS DE AUTENTICACIÓN
Auth::routes();

// Ruta explícita para logout
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Vista para acceso restringido
Route::view('auth-required', 'auth-required')->name('auth-required');

// 2. RUTA PRINCIPAL (INDEX)
Route::get('/', function () {
    $becas = \App\Models\Beca::all();
    return view('index', compact('becas'));
})->name('index');

// 3. HOME (Dashboard usuario logueado)
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth', 'admin');

//3.2 Vista de becas


// 4. CONTACTO
Route::view('contacto', 'contact')->name('contacto');
Route::post('guardar-contacto', [ContactController::class, 'store']); // Cualquiera puede enviar
Route::get('/leer-contactos', [ContactController::class, 'index'])->name('contactos')->middleware('auth', 'admin'); // Solo admin ve mensajes
Route::get('/contacto/{id}', [ContactController::class, 'show'])->name('contacto.show')->middleware('auth', 'admin'); // Ver detalle de mensaje

// 4.1 BÚSQUEDA DE BECAS
Route::get('/busqueda', function (\Illuminate\Http\Request $request) {
    $query = $request->input('q');
    $becas = collect();
    
    if ($query) {
        $becas = \App\Models\Beca::where('nombre', 'like', "%$query%")
                 ->orWhere('tipo', 'like', "%$query%")
                 ->get();
    }
    
    return view('busqueda', compact('becas'));
})->name('busqueda')->middleware('auth');

// 4.2 FAVORITOS
Route::get('/favoritos', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('favoritos')->middleware('auth');

// Toggle favorito (AJAX)
Route::post('/favoritos/toggle/{becaId}', [\App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favoritos.toggle')->middleware('auth');

// 4.3 NOTIFICACIONES
Route::get('/notificaciones', [App\Http\Controllers\NotificationController::class, 'index'])->name('notificaciones')->middleware('auth');
Route::post('/notificaciones/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notificaciones.markAsRead')->middleware('auth');
Route::post('/notificaciones/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notificaciones.markAllAsRead')->middleware('auth');
Route::delete('/notificaciones/{id}', [App\Http\Controllers\NotificationController::class, 'delete'])->name('notificaciones.delete')->middleware('auth');
Route::get('/notificaciones/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('notificaciones.show')->middleware('auth');

// 4.4 ACERCA DE
Route::view('acerca-de', 'acerca-de')->name('acerca-de');

// 5. LÓGICA DE VISITANTE
Route::get('/continuar-como-visitante', function (\Illuminate\Http\Request $request) {
    $request->session()->put('guest', true);
    return redirect()->route('index');
})->name('guest.continue');

// 6. LISTA DE USUARIOS
Route::get('/lista-usuarios', function () {
    $users = User::all(); 
    return view('usuarios', compact('users'));
})->name('usuarios.lista')->middleware('auth', 'admin');


// --------------------------------------------------------
// 7. GESTIÓN DE SERVICIOS (BECAS) - CRUD COMPLETO
// --------------------------------------------------------

// A) VER LISTA (Dashboard de Becas)
Route::get('/becas', [BecaController::class, 'index'])->name('becas.index')->middleware('auth', 'admin');

// B) CREAR (Formulario y Guardar)
Route::get('/crear-beca', [BecaController::class, 'create'])->name('becas.create')->middleware('auth', 'admin');
Route::post('/becas', [BecaController::class, 'store'])->name('becas.store')->middleware('auth', 'admin');

// C) EDITAR (Formulario y Actualizar) - ¡Nuevas!
Route::get('/becas/{id}/editar', [BecaController::class, 'edit'])->name('becas.edit')->middleware('auth', 'admin');
Route::put('/becas/{id}', [BecaController::class, 'update'])->name('becas.update')->middleware('auth', 'admin');

// D) ELIMINAR (Borrar de la BD) - ¡Nueva!
Route::delete('/becas/{id}', [BecaController::class, 'destroy'])->name('becas.destroy')->middleware('auth', 'admin');

// Ruta para la vista de detalle de beca usada en las tarjetas del index
Route::get('/detalle-beca', function (\Illuminate\Http\Request $request) {
    $id = $request->query('id');
    if ($id) {
        // Si viene por ID, buscar la beca en BD
        $beca = \App\Models\Beca::find($id);
        if (!$beca) abort(404);
        return view('vistabecas', ['beca' => $beca, 'imagen' => $beca->imagen]);
    }
    // Si viene por parámetro img, mantener compatibilidad con el anterior
    $imagen = $request->query('img');
    return view('vistabecas', ['imagen' => $imagen, 'beca' => null]);
})->name('detalle-beca');

// Ruta para crear la tabla beca_user manualmente
Route::get('/setup-favorites', function() {
    try {
        // Ejecutar la consulta SQL directamente
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
        return "✅ Tabla beca_user creada exitosamente";
    } catch (\Exception $e) {
        return "⚠️ Nota: " . $e->getMessage();
    }
});

// Ruta para agregar columna role a la tabla users
Route::get('/add-role-field', function() {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
            DB::unprepared('ALTER TABLE users ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT "user" AFTER email_verified_at');
            return "✅ Columna 'role' agregada exitosamente a la tabla users";
        } else {
            return "⚠️ La columna 'role' ya existe en la tabla users";
        }
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

// Ruta para hacer un usuario admin (por email)
Route::get('/make-admin/{email}', function($email) {
    try {
        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            $user->role = 'admin';
            $user->save();
            return "✅ Usuario '{$email}' ahora es administrador";
        } else {
            return "❌ Usuario con email '{$email}' no encontrado";
        }
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

// Ruta para listar todos los usuarios y sus emails
Route::get('/list-users', function() {
    $users = \App\Models\User::all();
    $html = "<h2>Usuarios Registrados</h2>";
    $html .= "<table border='1' cellpadding='10'>";
    $html .= "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Role</th><th>Acción</th></tr>";
    
    foreach ($users as $user) {
        $adminBtn = $user->role !== 'admin' 
            ? "<a href='/dev2/public/make-admin/{$user->email}' style='color: green;'>Hacer Admin</a>" 
            : "<span style='color: green;'>✅ Admin</span>";
        
        $html .= "<tr>";
        $html .= "<td>{$user->id}</td>";
        $html .= "<td>{$user->name}</td>";
        $html .= "<td>{$user->email}</td>";
        $html .= "<td>{$user->role}</td>";
        $html .= "<td>{$adminBtn}</td>";
        $html .= "</tr>";
    }
    
    $html .= "</table>";
    return $html;
});
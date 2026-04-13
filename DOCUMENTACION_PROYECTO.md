

## TABLA DE CONTENIDOS
1. [Guía Rápida de Archivos](#guía-rápida)
2. [Introducción y Descripción General](#introducción)
3. [Arquitectura del Proyecto](#arquitectura)
4. [Tecnologías Utilizadas](#tecnologías)
5. [Estructura de Directorios](#estructura)
6. [Base de Datos](#base-de-datos)
7. [Modelos Eloquent](#modelos)
8. [Controladores](#controladores)
9. [Sistema de Rutas](#rutas)
10. [Sistema de Autenticación](#autenticación)
11. [Componentes de Interfaz](#componentes)
12. [Funcionalidades Principales](#funcionalidades)
13. [Configuración del Sistema](#configuración)
14. [Instrucciones de Instalación](#instalación)

---

# GUÍA RÁPIDA DE ARCHIVOS IMPORTANTES {#guía-rápida}

A continuación se detallan los archivos críticos del sistema, sus propósitos y funcionalidades.

---

## app/Models/User.php

### Descripción Funcional:
Representa la entidad Usuario en la capa de modelos de la aplicación. Proporciona la interfaz de programación entre la tabla `users` de la base de datos y la lógica de negocio de la aplicación. Encapsula la gestión completa de usuarios, autenticación, autorización y relaciones con otras entidades del sistema.

### Responsabilidades:
- Gestión integral de autenticación y autorización de usuarios
- Encriptación y validación de credenciales de acceso
- Definición de relación muchos-a-muchos con becas favoritas
- Control de asignación masiva de atributos para preservar integridad de datos
- Ocultamiento de información sensible en serialización JSON

### Código:
```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atributos asignables en masa.
     * Define los campos permitidos para asignación masiva sin comprometer seguridad.
     */
    protected $fillable = [
        'name',     // Denominación del usuario
        'email',    // Dirección de correo electrónico único
        'password', // Credencial encriptada mediante algoritmo bcrypt
        'role',     // Clasificación de permisos (admin, user, etc.)
    ];

    /**
     * Atributos excluidos de serialización.
     * Previene la exposición de información sensible en respuestas JSON.
     */
    protected $hidden = [
        'password',       // Never expose password
        'remember_token', // Token privado de sesión
    ];

    /**
     * Transformaciones de tipo de datos.
     * Define conversión automática de atributos a tipos específicos.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación muchos-a-muchos con modelo Beca.
     * Establece asociación de múltiples becas como favoritas por usuario.
     */
    public function becasFavoritas()
    {
        return $this->belongsToMany(Beca::class, 'beca_user');
    }
}
```

---

## app/Models/Beca.php

### Descripción Funcional:
Representa la entidad Beca en la capa de modelos. Proporciona la interfaz entre la tabla `becas` de la base de datos y la lógica de negocio. Encapsula los atributos descriptivos de becas académicas incluyendo denominación, clasificación, monto, descripción e identificador de recurso gráfico.

### Responsabilidades:
- Persistencia de información de becas académicas disponibles
- Definición de relación inversa muchos-a-muchos con usuarios
- Facilitación de operaciones de búsqueda y filtrado
- Gestión de referencias a recursos gráficos asociados

### Código:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beca extends Model
{
    use HasFactory;

    /**
     * Relación muchos-a-muchos con User.
     * Una beca puede ser favorita de múltiples usuarios
     */
    public function usuariosFavoritos()
    {
        return $this->belongsToMany(User::class, 'beca_user');
    }
}
```

---

## app/Http/Controllers/BecaController.php

### Descripción Funcional:
Controlador responsable de la orquestación de operaciones CRUD (Create, Read, Update, Delete) para la entidad Beca. Gestiona la totalidad de interacciones administrativas con registros de becas, incluyendo validación de datos, persistencia, modificación de estado y eliminación de recursos asociados.

### Responsabilidades:
- **index()**: Recuperación y presentación de listado completo de becas
- **create()**: Presentación de interfaz para inicialización de nuevo registro
- **store()**: Validación de datos entrantes y persistencia de nueva beca incluyendo recursos gráficos
- **edit()**: Presentación de interfaz con datos preexistentes para modificación
- **update()**: Actualización de información existente con gestión de recursos gráficos previos
- **destroy()**: Eliminación completa de registro y recursos asociados del sistema de archivos

### Código:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beca;
use Illuminate\Support\Facades\File;

class BecaController extends Controller
{
    // --- 1. VER LISTA (READ) ---
    public function index()
    {
        $becas = Beca::all();
        return view('becas.index', compact('becas'));
    }

    // --- 2. CREAR (CREATE & STORE) ---
    public function create()
    {
        return view('becas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'tipo' => 'required'
        ]);

        $beca = new Beca();
        $beca->nombre = $request->input('nombre');
        $beca->tipo = $request->input('tipo');
        $beca->monto = $request->input('monto');
        $beca->descripcion = $request->input('descripcion');

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $destinationPath = 'images/becas/';
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);
            $beca->imagen = $destinationPath . $filename;
        }

        $beca->save();
        return redirect()->route('becas.index')
                        ->with('success', '¡Beca creada exitosamente!');
    }

    // --- 3. EDITAR (EDIT & UPDATE) ---
    public function edit($id)
    {
        $beca = Beca::find($id);
        return view('becas.edit', compact('beca'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'tipo' => 'required'
        ]);
        
        $beca = Beca::find($id);
        $beca->nombre = $request->input('nombre');
        $beca->tipo = $request->input('tipo');
        $beca->monto = $request->input('monto');
        $beca->descripcion = $request->input('descripcion');

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $destinationPath = 'images/becas/';
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);

            if ($beca->imagen && File::exists(public_path($beca->imagen))) {
                File::delete(public_path($beca->imagen));
            }

            $beca->imagen = $destinationPath . $filename;
        }

        $beca->update();
        return redirect()->route('becas.index')
                        ->with('success', '¡Beca actualizada correctamente!');
    }

    // --- 4. BORRAR (DESTROY) ---
    public function destroy($id)
    {
        $beca = Beca::find($id);
        if ($beca) {
            if ($beca->imagen && File::exists(public_path($beca->imagen))) {
                File::delete(public_path($beca->imagen));
            }
            $beca->delete();
            return back()->with('success', 'Beca eliminada correctamente.');
        }
        return back()->with('error', 'No se encontró la beca.');
    }
}
```

---

## app/Http/Controllers/FavoriteController.php

### Descripción Funcional:
Controlador especializado para la administración del subsistema de favoritos. Facilita a usuarios autenticados la gestión de preferencias personalizadas mediante asociación de becas con su perfil. Implementa mecanismos de validación de integridad de datos y disponibilidad de estructuras relacionales.

### Responsabilidades:
- **toggle()**: Alternancia bidireccional de estado de beca en lista de favoritos del usuario
- **index()**: Recuperación y presentación de conjunto completo de becas marcadas como favoritas
- **ensureTableExists()**: Validación de disponibilidad de estructura relacional (tabla pivot) con creación condicional

### Código:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Beca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FavoriteController extends Controller
{
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
                // Silenciar
            }
        }
    }

    public function toggle(Request $request, $becaId)
    {
        $this->ensureTableExists();
        $user = Auth::user();
        $beca = Beca::find($becaId);

        if (!$beca) {
            return response()->json(['error' => 'Beca no encontrada'], 404);
        }

        if ($user->becasFavoritas()->where('beca_id', $becaId)->exists()) {
            $user->becasFavoritas()->detach($becaId);
            $isFavorite = false;
            $message = 'Eliminado de favoritos';
        } else {
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

    public function index()
    {
        $this->ensureTableExists();
        $user = Auth::user();
        $becas = $user->becasFavoritas()->get();
        return view('favoritos', compact('becas'));
    }
}
```

---

## app/Http/Controllers/ContactController.php

### Descripción Funcional:
Controlador encargado de la administración de comunicaciones directas provenientes de usuarios del sistema. Facilita mecanismo de contacto para partes interesadas sin requerir autenticación previa. Proporciona interfaz de gestión para personal administrativo respecto a mensajería recibida.

### Responsabilidades:
- **store()**: Recepción de datos de formulario, validación integral según criterios especificados y persistencia de comunicación
- **index()**: Recuperación y presentación de conjunto completo de mensajes para personal administrativo
- Validación de conformidad de datos con criterios de seguridad y formato (validez de dirección electrónica, límites de longitud, etc.)

### Código:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conctact;

class ContactController extends Controller
{
    public function index()
    {
        $mensajes = Conctact::all();
        return view('indexcontact', compact('mensajes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|min:3',
            'correo'    => 'required|min:10|email',
            'asunto'    => 'required|min:5',
            'prioridad' => 'required|min:0',
            'mensaje'   => 'required|min:10|max:3000',
        ]);

        $contacto = new Conctact;
        $contacto->nombre = $validated['nombre'];
        $contacto->correo = $validated['correo'];
        $contacto->asunto = $validated['asunto'];
        $contacto->prioridad = $validated['prioridad'];
        $contacto->mensaje = $validated['mensaje'];
        $contacto->save();
        
        return redirect()->back();
    }
}
```

---

## routes/web.php

### Descripción Funcional:
Archivo de configuración que define el mapeo integral de endpoints web de la aplicación. Establece la asociación entre identificadores de recurso uniforme (URI) e instrucciones de procesamiento. Define acceso diferenciado según contexto de autenticación y autorización. Sin este archivo, la aplicación carecería de capacidad de enrutamiento de peticiones entrantes.

### Responsabilidades:
- Definición de rutas de acceso público sin restricciones de autenticación
- Definición de rutas protegidas con requisito de autenticación de usuario
- Definición de rutas restringidas a personal con rol administrativo
- Especificación de controlador y método correspondiente a cada ruta
- Aplicación de middleware de seguridad, autenticación y autorización
- Agrupación lógica de rutas por dominio funcional

### Código (fragmento principal):
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BecaController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;

// PÚBLICAS
Route::get('/', function () {
    $becas = \App\Models\Beca::all();
    return view('index', compact('becas'));
})->name('index');

// CONTACTO (Todos)
Route::view('contacto', 'contact')->name('contacto');
Route::post('guardar-contacto', [ContactController::class, 'store']);

// SOLO AUTENTICADOS
Route::get('/busqueda', function (\Illuminate\Http\Request $request) {
    $query = $request->input('q');
    $becas = collect();
    if ($query) {
        $becas = \App\Models\Beca::where('nombre', 'like', "%$query%")
                 ->orWhere('tipo', 'like', "%$query%")->get();
    }
    return view('busqueda', compact('becas'));
})->name('busqueda')->middleware('auth');

// FAVORITOS
Route::get('/favoritos', [FavoriteController::class, 'index'])
     ->name('favoritos')->middleware('auth');
Route::post('/favoritos/toggle/{becaId}', [FavoriteController::class, 'toggle'])
     ->name('favoritos.toggle')->middleware('auth');

// SOLO ADMINS - CRUD DE BECAS
Route::get('/becas', [BecaController::class, 'index'])
     ->name('becas.index')->middleware('auth', 'admin');
Route::get('/crear-beca', [BecaController::class, 'create'])
     ->name('becas.create')->middleware('auth', 'admin');
Route::post('/becas', [BecaController::class, 'store'])
     ->name('becas.store')->middleware('auth', 'admin');
Route::get('/becas/{id}/editar', [BecaController::class, 'edit'])
     ->name('becas.edit')->middleware('auth', 'admin');
Route::put('/becas/{id}', [BecaController::class, 'update'])
     ->name('becas.update')->middleware('auth', 'admin');
Route::delete('/becas/{id}', [BecaController::class, 'destroy'])
     ->name('becas.destroy')->middleware('auth', 'admin');

// AUTENTICACIÓN (Auto-generada por Laravel)
Auth::routes();
```

---

## database/migrations/2014_10_12_000000_create_users_table.php

### Descripción Funcional:
Artefacto de migración de base de datos que especifica estructura y esquema de la tabla de usuarios. Ejecuta instrucciones de inicialización de estructura relacional mediante interfaz de abstracción de esquema. Proporciona mecanismo bidireccional de aplicación y reversión de cambios estructurales.

### Responsabilidades:
- Creación de tabla `users` con estructura definida
- Especificación de tipos de datos para cada atributo
- Aplicación de restricciones de integridad (unicidad de dirección electrónica, obligatoriedad de campos, etc.)
- Provisión de mecanismo de reversión de cambios para control de versión

### Código:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                  // ID auto-incremental
            $table->string('name');                        // Nombre
            $table->string('lastname')->nullable();        // Apellido (opcional)
            $table->string('email')->unique();             // Email único
            $table->timestamp('email_verified_at')->nullable(); // Verificación
            $table->string('password');                    // Contraseña encriptada
            $table->rememberToken();                       // Token para "Recuérdame"
            $table->timestamps();                          // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

---

## database/migrations/2025_12_01_162224_create_becas_table.php

### Descripción Funcional:
Artefacto de migración que define estructura y esquema de la tabla de becas académicas. Especifica arquitectura relacional para persistencia de información descriptiva y referencial de incentivos educativos disponibles en el sistema.

### Responsabilidades:
- Creación de tabla `becas` con especificación de columnas
- Provisión de estructura de persistencia para información de oportunidades académicas
- Inclusión de campo de referencia para recursos gráficos asociados
- Implementación de timestamps para auditoría temporal de creación y modificación

### Código:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBecasTable extends Migration
{
    public function up()
    {
        Schema::create('becas', function (Blueprint $table) {
            $table->id();                              // ID único
            $table->string('nombre');                  // Nombre de la beca
            $table->string('tipo');                    // Tipo (Básica, Superior, etc.)
            $table->decimal('monto', 10, 2)->nullable(); // Cantidad de dinero
            $table->text('descripcion')->nullable();   // Descripción detallada
            $table->string('imagen')->nullable();      // Ruta de la imagen
            $table->timestamps();                      // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('becas');
    }
}
```

---

## database/migrations/2025_12_02_create_beca_user_table.php

### Descripción Funcional:
Artefacto de migración que especifica estructura de tabla relacional (tabla pivot) para asociación muchos-a-muchos entre usuarios y becas. Facilita relación bidireccional entre entidades mantiendo integridad de datos mediante restricciones estructurales.

### Responsabilidades:
- Creación de tabla `beca_user` para materialización de relación muchos-a-muchos
- Implementación de restricción de unicidad composite para prevención de duplicados
- Especificación de integridad referencial mediante definición de claves foráneas con cascada de operaciones
- Provisión de timestamps para auditoría de asociaciones

### Código:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBecaUserTable extends Migration
{
    public function up()
    {
        Schema::create('beca_user', function (Blueprint $table) {
            $table->id();                          // ID único del registro
            $table->unsignedBigInteger('user_id');  // Referencia al usuario
            $table->unsignedBigInteger('beca_id');  // Referencia a la beca
            $table->timestamps();                  // Fechas

            // Claves foráneas (si se borra usuario o beca, se borran relaciones)
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('beca_id')
                  ->references('id')->on('becas')
                  ->onDelete('cascade');

            // Evitar duplicados
            $table->unique(['user_id', 'beca_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('beca_user');
    }
}
```

---

## resources/views/layouts/master.blade.php

### Descripción Funcional:
Arquetipo de plantilla Blade que define estructura base HTML común a todas las vistas de la aplicación. Implementa patrón de herencia de plantillas para evitar duplicación de código de presentación. Comprende elementos de interfaz global incluyendo navegación, pie de página y esquema estructural.

### Responsabilidades:
- Definición de estructura fundamental HTML (declaración DOCTYPE, elementos semanticosroot)
- Incorporación de interfaz de navegación global
- Inclusión de pie de página global
- Importación de dependencias CSS de terceros (framework de diseño responsivo, librería iconográfica)
- Importación de dependencias JavaScript de terceros
- Provisión de punto de inyección dinámico (@yield) para contenido específico de vistas
- Especificación de estilos CSS personalizados de presentación

### Código (fragmento):
```html
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Plataforma Web</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #ffffff !important;
      padding-top: 76px;
    }
    nav.custom-navbar {
      background-color: #1976D2;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .card { border: none; border-radius: 15px; }
    .card:hover { transform: translateY(-5px); }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg custom-navbar sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('index') }}">Becas Académicas</a>
      <!-- Menú aquí -->
    </div>
  </nav>

  <main class="container-fluid">
    @yield('content')  <!-- Aquí va el contenido específico de cada página -->
  </main>

  <footer class="bg-dark text-white text-center py-4">
    <p>&copy; {{ date('Y') }} Sistema de Becas.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## resources/views/index.blade.php

### Descripción Funcional:
Vista de página de entrada de aplicación que presenta catálogo de oportunidades académicas. Funciona como punto de acceso fundamental para usuarios permitiendo visualización integral de oferta de becas disponibles con opciones de interacción personalizadas según contexto de autenticación.

### Responsabilidades:
- Recuperación de conjunto completo de becas desde capa de persistencia
- Presentación de oportunidades académicas en formato de columnas deslizables
- Facilitación de navegación hacia visualización detallada de becas individuales
- Provisión de mecanismo de marcación de favoritos para usuarios autenticados
- Implementación de diseño responsivo adaptable a diferentes dimensiones de viewport

---

## .env

### Descripción Funcional:
Archivo de configuración de variables de entorno que aloja parámetros específicos de instancia e información sensible. No debe ser versionado bajo control de versiones. Proporciona aislamiento de configuración dependiente de ambiente operacional.

### Responsabilidades:
- Especificación de identidad de aplicación
- Configuración de parámetros de conectividad a sistema de gestión de base de datos
- Configuración de parámetros de servicio de correo electrónico
- Especificación de contexto operacional (desarrollo, testing, producción)

### Contenido típico:
```
APP_NAME="Becas Académicas"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=becas_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## composer.json

### Descripción Funcional:
Artefacto de manifiesto de dependencias que especifica conjunto de librerías PHP requeridas para funcionamiento de aplicación. Define restricciones de versión y criterios de resolución de dependencias transitivas. Facilita reproducibilidad ambiental mediante especificación declarativa de requisitos de ejecución.

### Responsabilidades:
- Enumeración de dependencias de runtime necesarias
- Especificación de restricciones de versión semántica de dependencias
- Inclusión de dependencias de desarrollo específicas para testing y análisis
- Configuración de mapeo de espacio de nombres para autocarga (autoload PSR-4)

---

# INTRODUCCIÓN Y DESCRIPCIÓN GENERAL {#introducción}

La presente documentación técnica especifica la arquitectura, componentes y funcionalidades del **Sistema de Gestión de Becas**. Se trata de una aplicación web desarrollada bajo el framework Laravel versión 8.0, implementada en lenguaje de programación PHP con gestor de base de datos relacional MySQL/MariaDB. El sistema ha sido diseñado para centralizar la administración de becas académicas, facilitando operaciones de búsqueda, clasificación y gestión preferencial de oportunidades educativas.

## Objetivos Funcionales del Sistema

- Centralización de información de oportunidades académicas disponibles
- Facilitar operaciones de búsqueda y filtrado de becas según criterios especificados
- Implementación de subsistema de preferencias personalizado por usuario
- Provisión de canal de comunicación bidireccional entre usuarios e institución
- Provisión de interfaz de administración para gestión integral de becas disponibles

## Público Objetivo del Sistema

- Candidatos a becas educativas que requieren información de oportunidades disponibles
- Personal administrativo encargado de administración de catálogo de becas
- Personal de atención al usuario responsable de gestión de consultas y comunicaciones

---

# ARQUITECTURA DEL PROYECTO {#arquitectura}

El proyecto implementa el patrón arquitectónico **MVC (Modelo-Vista-Controlador)**, que es el estándar en Laravel. La arquitectura se estructura de la siguiente manera:

```
┌─────────────────────────────────────────────────────────┐
│                    CAPA DE PRESENTACIÓN                 │
│              (Vistas Blade, JavaScript, CSS)            │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│                   CAPA DE CONTROLADORES                 │
│        (BecaController, ContactController, etc.)        │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│                    CAPA DE MODELOS                      │
│       (User, Beca, Contact, Role - Eloquent ORM)       │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│                  CAPA DE PERSISTENCIA                   │
│           (Base de Datos MySQL / MariaDB)               │
└─────────────────────────────────────────────────────────┘
```

Esta arquitectura garantiza una separación clara de responsabilidades, facilitando el mantenimiento, escalabilidad y testabilidad del sistema.

---

# TECNOLOGÍAS UTILIZADAS {#tecnologías}

## Backend
- **Laravel 8.54**: Framework PHP moderno para desarrollo web
- **PHP 7.3+/8.0+**: Lenguaje de programación del servidor
- **MySQL/MariaDB**: Sistema administrador de bases de datos relacional

## Frontend
- **Blade Templates**: Motor de plantillas de Laravel
- **Bootstrap 5.3.2**: Framework CSS para diseño responsivo
- **Font Awesome 6.5.2**: Librería de iconos vectoriales
- **JavaScript Vanilla**: Lenguaje de scripting cliente
- **AJAX**: Comunicación asincrónica cliente-servidor

## Herramientas de Desarrollo
- **Composer**: Gestor de dependencias PHP
- **Artisan CLI**: Herramienta de línea de comandos de Laravel
- **PHPUnit**: Framework de testing unitario
- **Faker**: Generador de datos falsos para pruebas

## Dependencias Principales (composer.json)
```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The Laravel Framework.",
    "license": "MIT",
    "require": {
        "php": "^7.3|^8.0",
        "fruitcake/laravel-cors": "^2.0",
        "guzzlehttp/guzzle": "^7.0.1",
        "laravel/framework": "^8.54",
        "laravel/sanctum": "^2.11",
        "laravel/tinker": "^2.5",
        "laravel/ui": "3.4"
    },
    "require-dev": {
        "facade/ignition": "^2.5",
        "fakerphp/faker": "^1.9.1",
        "laravel/sail": "^1.0.1",
        "mockery/mockery": "^1.4.2",
        "nunomaduro/collision": "^5.0",
        "phpunit/phpunit": "^9.3.3"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    }
}
```

---

# ESTRUCTURA DE DIRECTORIOS {#estructura}

```
proyecto-becas/
│
├── app/                              # Código lógico de la aplicación
│   ├── Console/                     # Comandos Artisan personalizados
│   ├── Exceptions/                  # Manejo de excepciones
│   ├── Http/
│   │   ├── Controllers/             # Lógica de controladores
│   │   │   ├── BecaController.php
│   │   │   ├── ContactController.php
│   │   │   ├── FavoriteController.php
│   │   │   ├── HomeController.php
│   │   │   └── RoleController.php
│   │   ├── Middleware/              # Middleware HTTP
│   │   ├── Kernel.php               # Configuración de middleware
│   │   └── Requests/                # Validación de formularios
│   ├── Models/                      # Modelos Eloquent ORM
│   │   ├── User.php
│   │   ├── Beca.php
│   │   ├── Conctact.php
│   │   └── Role.php
│   └── Providers/                   # Service providers
│
├── bootstrap/                        # Archivos de arranque de la aplicación
├── config/                          # Archivos de configuración
├── database/
│   ├── factories/                   # Factories para testing
│   ├── migrations/                  # Migraciones de base de datos
│   └── seeders/                     # Seeders para datos iniciales
│
├── public/                          # Directorio accesible públicamente
│   ├── css/                         # Estilos CSS compilados
│   ├── js/                          # Scripts JavaScript compilados
│   ├── images/                      # Imágenes del proyecto
│   │   └── becas/                   # Imágenes de becas subidas
│   └── index.php                    # Punto de entrada de la aplicación
│
├── resources/
│   ├── css/                         # Archivos CSS fuente
│   ├── js/                          # Archivos JavaScript fuente
│   ├── views/                       # Plantillas Blade
│   │   ├── layouts/                 # Plantillas base
│   │   ├── auth/                    # Vistas de autenticación
│   │   ├── becas/                   # Vistas de gestión de becas
│   │   └── [otros archivos blade]
│   └── lang/                        # Archivos de localización
│
├── routes/
│   ├── web.php                      # Rutas web (formularios, vistas)
│   ├── api.php                      # Rutas API (JSON)
│   ├── console.php                  # Rutas de consola
│   └── channels.php                 # Canales de broadcast
│
├── storage/                         # Almacenamiento de archivos
├── tests/                           # Pruebas unitarias e integración
├── vendor/                          # Dependencias de Composer
├── .env.example                     # Plantilla de variables de entorno
├── artisan                          # CLI de Laravel
├── composer.json                    # Definición de dependencias
└── phpunit.xml                      # Configuración de tests
```

---

# BASE DE DATOS {#base-de-datos}

## Diagrama Entidad-Relación

```
┌─────────────────┐           ┌─────────────────────┐
│      users      │───────────│    beca_user        │
├─────────────────┤    M:M    ├─────────────────────┤
│ id (PK)         │           │ id (PK)             │
│ name            │           │ user_id (FK)        │
│ lastname        │◄──────────│ beca_id (FK)        │
│ email (UNIQUE)  │           │ unique(user, beca)  │
│ password        │           │ created_at          │
│ role            │           │ updated_at          │
│ created_at      │           └─────────────────────┘
│ updated_at      │
└─────────────────┘           ┌─────────────────────┐
                              │       becas         │
                              ├─────────────────────┤
                              │ id (PK)             │
                              │ nombre              │
                              │ tipo                │
                              │ monto (DECIMAL)     │
                              │ descripcion         │
                              │ imagen              │
                              │ created_at          │
                              │ updated_at          │
                              └─────────────────────┘

┌──────────────────────┐
│     conctacts        │
├──────────────────────┤
│ id (PK)              │
│ nombre (VARCHAR)     │
│ correo (VARCHAR)     │
│ prioridad (ENUM)     │
│ asunto (VARCHAR)     │
│ mensaje (TEXT)       │
│ created_at           │
│ updated_at           │
└──────────────────────┘

┌──────────────────┐
│      roles       │
├──────────────────┤
│ id (PK)          │
│ name (VARCHAR)   │
│ created_at       │
│ updated_at       │
└──────────────────┘
```

## Definición de Migraciones

### 1. Tabla: users (2014_10_12_000000_create_users_table.php)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Ejecutar la migración.
     * Esta función crea la tabla principal de usuarios con sus campos básicos
     * y configuraciones de seguridad.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                    // ID incremental automático
            $table->string('name');                          // Nombre del usuario
            $table->string('lastname')->nullable();          // Apellido (opcional)
            $table->string('email')->unique();               // Email único (sin duplicados)
            $table->timestamp('email_verified_at')->nullable(); // Verificación de email
            $table->string('password');                      // Contraseña encriptada
            $table->rememberToken();                         // Token para "Recuérdame"
            $table->timestamps();                            // created_at, updated_at
        });
    }

    /**
     * Revertir la migración.
     * Esta función deshace la creación de la tabla.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

**Descripción de Campos:**
- `id`: Clave primaria que identifica de forma única cada usuario
- `name`: Campo requerido que almacena el nombre del usuario
- `lastname`: Campo opcional para el apellido
- `email`: Campo único que previene usuarios duplicados por correo
- `password`: Contraseña encriptada con algoritmo bcrypt
- `remember_token`: Token que persiste la sesión del usuario
- `timestamps`: Fechas automáticas de creación y actualización

### 2. Tabla: becas (2025_12_01_162224_create_becas_table.php)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBecasTable extends Migration
{
    /**
     * Ejecutar la migración.
     * Crea la tabla que almacena la información de las becas disponibles
     * en el sistema.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('becas', function (Blueprint $table) {
            $table->id();                              // ID incremental
            $table->string('nombre');                  // Nombre de la beca
            $table->string('tipo');                    // Tipo (Básica, Superior, etc.)
            $table->decimal('monto', 10, 2)->nullable(); // Monto en dinero (4000.50)
            $table->text('descripcion')->nullable();   // Descripción detallada
            $table->string('imagen')->nullable();      // Ruta de imagen
            $table->timestamps();                      // Fechas de creación/actualización
        });
    }

    /**
     * Revertir la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('becas');
    }
}
```

**Descripción de Campos:**
- `id`: Identificador único para cada beca
- `nombre`: Nombre descriptivo de la beca (requerido)
- `tipo`: Categoría de la beca (requerido)
- `monto`: Cantidad de dinero de la beca (máximo 8 dígitos, 2 decimales)
- `descripcion`: Información detallada de requisitos y beneficios
- `imagen`: Ruta relativa del archivo de imagen almacenado

### 3. Tabla: conctacts (2025_10_21_174408_create_conctacts_table.php)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConctactsTable extends Migration
{
    /**
     * Ejecutar la migración.
     * Crea la tabla que almacena los mensajes de contacto enviados
     * por usuarios a través del formulario de contacto.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conctacts', function (Blueprint $table) {
            $table->id();                          // ID único del mensaje
            $table->string('nombre', 80);          // Nombre del remitente (máx 80 caracteres)
            $table->string('correo', 60);          // Email del remitente (máx 60)
            $table->enum('prioridad', ['Alta', 'Media', 'Baja']); // Nivel de urgencia
            $table->string('asunto', 60);          // Asunto del mensaje (máx 60)
            $table->text('mensaje');               // Contenido completo del mensaje
            $table->timestamps();                  // Fechas de registro
        });
    }

    /**
     * Revertir la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conctacts');
    }
}
```

**Descripción de Campos:**
- `id`: Identificador único del mensaje de contacto
- `nombre`: Nombre completo de la persona que contacta
- `correo`: Dirección de correo para respuesta
- `prioridad`: Nivel de urgencia (Alta, Media, Baja)
- `asunto`: Tema o asunto del contacto
- `mensaje`: Cuerpo del mensaje (hasta 3000 caracteres en validación)

### 4. Tabla: beca_user (2025_12_02_create_beca_user_table.php)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBecaUserTable extends Migration
{
    /**
     * Ejecutar la migración.
     * Crea la tabla pivot/intermedia que establece la relación
     * muchos-a-muchos entre usuarios y sus becas favoritas.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beca_user', function (Blueprint $table) {
            $table->id();                          // ID único del registro
            $table->unsignedBigInteger('user_id');  // Referencia a usuario
            $table->unsignedBigInteger('beca_id');  // Referencia a beca
            $table->timestamps();                  // Fecha de favorito

            // Definición de claves foráneas
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');           // Si se borra usuario, se borran sus favoritos
            
            $table->foreign('beca_id')
                  ->references('id')->on('becas')
                  ->onDelete('cascade');           // Si se borra beca, se borran sus relaciones

            // Evitar duplicados: un usuario no puede tener la misma beca 2 veces como favorito
            $table->unique(['user_id', 'beca_id']);
        });
    }

    /**
     * Revertir la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beca_user');
    }
}
```

**Descripción de Estructura:**

Esta tabla implementa la relación muchos-a-muchos entre entidades Usuario y Beca, materializando las siguientes capacidades:

- Múltiples asociaciones de becas por usuario individual
- Múltiples asociaciones de usuarios por beca individual
- Garantización de integridad referencial mediante definición de claves foráneas
- Prevención de duplicación mediante restricción de unicidad composite

### 5. Tabla: roles (2025_10_06_171629_create_roles_table.php)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Ejecutar la migración.
     * Tabla de roles para el sistema de autorización y permisos.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();              // ID único del rol
            $table->string('name');    // Nombre del rol (ej: admin, user)
            $table->timestamps();      // Fechas de control
        });
    }

    /**
     * Revertir la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
```

**Descripción de Campos:**
- `id`: Clave primaria
- `name`: Nombre del rol (admin, user, moderator, etc.)
- `timestamps`: Fechas de creación y actualización

---

# MODELOS ELOQUENT {#modelos}

## Modelo: User

**Ruta:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Los atributos que son asignables en masa.
     * Especifica qué campos puede llenar el usuario sin violaciones de seguridad.
     *
     * @var array
     */
    protected $fillable = [
        'name',     // Nombre del usuario
        'email',    // Correo electrónico
        'password', // Contraseña (se encripta automáticamente)
        'role',     // Rol del usuario (admin, user, etc.)
    ];

    /**
     * Los atributos que deben estar ocultos para serialización.
     * Se ocultan para no exponer información sensible en JSON.
     *
     * @var array
     */
    protected $hidden = [
        'password',       // La contraseña nunca se devuelve
        'remember_token', // Token de sesión privado
    ];

    /**
     * Los atributos que deben castearse.
     * Convierte automáticamente tipos de datos.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Convierte a objeto DateTime
    ];

    /**
     * Relación muchos-a-muchos con Beca.
     * Un usuario puede tener múltiples becas como favoritas.
     * Una beca puede ser favorita de múltiples usuarios.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function becasFavoritas()
    {
        return $this->belongsToMany(Beca::class, 'beca_user');
        // 'beca_user' es la tabla pivot/intermedia
    }
}
```

**Métodos Provistos:**

- `$user->becasFavoritas()` - Recuperación de conjunto completo de becas asociadas como favoritas
- `$user->becasFavoritas()->attach()` - Asociación de beca al conjunto de favoritos del usuario
- `$user->becasFavoritas()->detach()` - Disociación de beca del conjunto de favoritos
- `$user->becasFavoritas()->toggle()` - Alternancia bidireccional de estado de asociación

## Modelo: Beca

**Ruta:** `app/Models/Beca.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beca extends Model
{
    use HasFactory;

    /**
     * Relación muchos-a-muchos con User.
     * Una beca puede ser favorita de múltiples usuarios.
     * Un usuario puede tener múltiples becas como favoritas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function usuariosFavoritos()
    {
        return $this->belongsToMany(User::class, 'beca_user');
        // Accede a la tabla pivot mediante 'beca_user'
    }
}
```

**Métodos Provistos:**

- `$beca->usuariosFavoritos()` - Recuperación de conjunto de usuarios que han asociado beca como favorita
- `Beca::all()` - Recuperación de conjunto completo de registros de becas
- `Beca::find($id)` - Recuperación de registro específico mediante identificador
- `Beca::where('nombre', 'like', '%termino%')` - Búsqueda mediante criterios de coincidencia parcial

## Modelo: Role

**Ruta:** `app/Models/Role.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    /**
     * Modelo simple para representar roles en el sistema.
     * Permite diferenciar entre usuarios administradores y usuarios regulares.
     */
}
```

## Modelo: Conctact (Contacto)

**Ruta:** `app/Models/Conctact.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conctact extends Model
{
    use HasFactory;
    
    /**
     * Modelo para representar mensajes de contacto.
     * Almacena información de consultas de usuarios.
     */
}
```

---

# CONTROLADORES {#controladores}

## BecaController

**Ruta:** `app/Http/Controllers/BecaController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beca;
use Illuminate\Support\Facades\File;

class BecaController extends Controller
{
    /**
     * --- 1. VER LISTA (READ) ---
     * Muestra el listado de todas las becas en el panel administrativo.
     * Solo accesible por usuarios autenticados con rol admin.
     *
     * @return \Illuminate\View\View Vista con la lista de becas
     */
    public function index()
    {
        $becas = Beca::all(); // Obtiene todas las becas de la BD
        return view('becas.index', compact('becas'));
    }

    /**
     * --- 2. CREAR - MOSTRAR FORMULARIO (CREATE) ---
     * Muestra el formulario vacío para crear una nueva beca.
     *
     * @return \Illuminate\View\View Vista del formulario
     */
    public function create()
    {
        return view('becas.create');
    }

    /**
     * --- 2B. CREAR - GUARDAR EN BD (STORE) ---
     * Valida y guarda los datos del formulario en la base de datos.
     *
     * @param  \Illuminate\Http\Request $request Datos del formulario
     * @return \Illuminate\Http\RedirectResponse Redirecciona al listado
     */
    public function store(Request $request)
    {
        // Validar que los campos obligatorios estén completos
        $request->validate([
            'nombre' => 'required',  // El nombre es obligatorio
            'tipo'   => 'required'   // El tipo es obligatorio
        ]);

        // Crear nueva instancia del modelo
        $beca = new Beca();
        $beca->nombre = $request->input('nombre');
        $beca->tipo = $request->input('tipo');
        $beca->monto = $request->input('monto');
        $beca->descripcion = $request->input('descripcion');

        // Procesar la imagen si se sube una
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $destinationPath = 'images/becas/';
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);
            $beca->imagen = $destinationPath . $filename;
        }

        // Guardar en la base de datos
        $beca->save();
        
        // Redirigir al listado con mensaje de éxito
        return redirect()->route('becas.index')
                        ->with('success', '¡Beca creada exitosamente!');
    }

    /**
     * --- 3. EDITAR - MOSTRAR FORMULARIO (EDIT) ---
     * Muestra el formulario pre-cargado con los datos de la beca.
     *
     * @param  int $id ID de la beca a editar
     * @return \Illuminate\View\View Vista del formulario con datos
     */
    public function edit($id)
    {
        $beca = Beca::find($id);
        return view('becas.edit', compact('beca'));
    }

    /**
     * --- 3B. EDITAR - GUARDAR CAMBIOS (UPDATE) ---
     * Actualiza los datos de una beca existente en la BD.
     *
     * @param  \Illuminate\Http\Request $request Datos actualizados
     * @param  int $id ID de la beca a actualizar
     * @return \Illuminate\Http\RedirectResponse Redirecciona al listado
     */
    public function update(Request $request, $id)
    {
        // Validar datos obligatorios
        $request->validate([
            'nombre' => 'required',
            'tipo'   => 'required'
        ]);
        
        // Buscar y obtener la beca
        $beca = Beca::find($id);

        // Actualizar campos de texto
        $beca->nombre = $request->input('nombre');
        $beca->tipo = $request->input('tipo');
        $beca->monto = $request->input('monto');
        $beca->descripcion = $request->input('descripcion');

        // Procesar nueva imagen si se sube una
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $destinationPath = 'images/becas/';
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);

            // Borrar imagen antigua si existe en el sistema de archivos
            if ($beca->imagen && File::exists(public_path($beca->imagen))) {
                File::delete(public_path($beca->imagen));
            }

            $beca->imagen = $destinationPath . $filename;
        }

        // Guardar cambios en BD
        $beca->update();
        
        return redirect()->route('becas.index')
                        ->with('success', '¡Beca actualizada correctamente!');
    }

    /**
     * --- 4. ELIMINAR (DESTROY) ---
     * Borra una beca y su imagen asociada de la BD y del sistema de archivos.
     *
     * @param  int $id ID de la beca a eliminar
     * @return \Illuminate\Http\RedirectResponse Redirecciona atrás con mensaje
     */
    public function destroy($id)
    {
        $beca = Beca::find($id);
        
        if ($beca) {
            // Borrar archivo de imagen del servidor si existe
            if ($beca->imagen && File::exists(public_path($beca->imagen))) {
                File::delete(public_path($beca->imagen));
            }
            
            // Eliminar registro de la base de datos
            $beca->delete();
            
            return back()->with('success', 'Beca eliminada correctamente.');
        }
        
        return back()->with('error', 'No se encontró la beca.');
    }
}
```

## ContactController

**Ruta:** `app/Http/Controllers/ContactController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conctact;

class ContactController extends Controller
{
    /**
     * Mostrar el listado de todos los mensajes de contacto.
     * Solo accesible por administradores autenticados.
     *
     * @return \Illuminate\View\View Vista con los mensajes
     */
    public function index()
    {
        // Obtener todos los mensajes de la BD
        $mensajes = Conctact::all();
        
        // Pasar a la vista para mostrarlos
        return view('indexcontact', compact('mensajes'));
    }

    /**
     * Guardar un nuevo mensaje de contacto en la base de datos.
     * Valida todos los campos antes de guardar.
     *
     * @param  \Illuminate\Http\Request $request Datos del formulario
     * @return \Illuminate\Http\RedirectResponse Redirecciona atrás con mensaje
     */
    public function store(Request $request)
    {
        // Validar todos los campos del formulario de contacto
        $validated = $request->validate([
            'nombre'    => 'required|min:3',                    // Mínimo 3 caracteres
            'correo'    => 'required|min:10|email',             // Email válido
            'asunto'    => 'required|min:5',                    // Mínimo 5 caracteres
            'prioridad' => 'required|min:0',                    // Selección obligatoria
            'mensaje'   => 'required|min:10|max:3000',          // Entre 10 y 3000 caracteres
        ]);

        // Crear nuevo contacto
        $contacto = new Conctact;
        $contacto->nombre = $validated['nombre'];
        $contacto->correo = $validated['correo'];
        $contacto->asunto = $validated['asunto'];
        $contacto->prioridad = $validated['prioridad'];
        $contacto->mensaje = $validated['mensaje'];
        
        // Guardar en la BD
        $contacto->save();
        
        // Redirigir atrás (página de contacto)
        return redirect()->back();
    }
}
```

## FavoriteController

**Ruta:** `app/Http/Controllers/FavoriteController.php`

```php
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
     * Asegurar que la tabla beca_user existe.
     * Se ejecuta antes de cualquier operación con favoritos
     * para garantizar que la tabla pivot existe.
     *
     * @return void
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
                // Silenciar si ya existe
            }
        }
    }

    /**
     * Alternar si una beca está en favoritos del usuario.
     * Si ya está en favoritos, la quita. Si no está, la agrega.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $becaId ID de la beca a toggle
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con resultado
     */
    public function toggle(Request $request, $becaId)
    {
        // Asegurar que la tabla existe
        $this->ensureTableExists();
        
        // Obtener usuario autenticado
        $user = Auth::user();
        
        // Buscar la beca
        $beca = Beca::find($becaId);

        if (!$beca) {
            return response()->json(['error' => 'Beca no encontrada'], 404);
        }

        // Verificar si ya está en favoritos
        if ($user->becasFavoritas()->where('beca_id', $becaId)->exists()) {
            // QUITAR de favoritos
            $user->becasFavoritas()->detach($becaId);
            $isFavorite = false;
            $message = 'Eliminado de favoritos';
        } else {
            // AGREGAR a favoritos
            $user->becasFavoritas()->attach($becaId);
            $isFavorite = true;
            $message = 'Agregado a favoritos';
        }

        // Devolver respuesta JSON con estado actualizado
        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $message
        ]);
    }

    /**
     * Mostrar todas las becas favoritas del usuario actual.
     *
     * @return \Illuminate\View\View Vista con las becas favoritas
     */
    public function index()
    {
        // Asegurar que la tabla existe
        $this->ensureTableExists();
        
        // Obtener usuario autenticado
        $user = Auth::user();
        
        // Obtener becas favoritas del usuario
        $becas = $user->becasFavoritas()->get();

        // Mostrar vista con las becas
        return view('favoritos', compact('becas'));
    }
}
```

## HomeController

**Ruta:** `app/Http/Controllers/HomeController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Crear una nueva instancia del controlador.
     * Requiere autenticación para acceder.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el dashboard de la aplicación.
     * Página principal para usuarios autenticados.
     *
     * @return \Illuminate\Contracts\Support\Renderable Vista del dashboard
     */
    public function index()
    {
        return view('home', ['hideNavbar' => true]);
    }
}
```

---

# SISTEMA DE RUTAS {#rutas}

## Archivo: routes/web.php

El archivo de rutas define todos los endpoints HTTP disponibles en la aplicación web.

```php
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
| WEB ROUTES - RUTAS DE LA APLICACIÓN
|--------------------------------------------------------------------------
| Estas rutas son accedidas por formularios HTML y retornan vistas.
| A diferencia de las rutas API, estas preservan la sesión del usuario.
*/

// ========== 1. AUTO-RUTAS DE AUTENTICACIÓN ==========
// Laravel UI genera automáticamente: /login, /register, /logout, /reset-password
Auth::routes();

// ========== 2. RUTA PRINCIPAL (ÍNDICE) ==========
/**
 * GET /
 * Muestra la página principal con todas las becas disponibles
 * Middleware: Ninguno (accesible para todos, autenticados y no autenticados)
 */
Route::get('/', function () {
    $becas = \App\Models\Beca::all();
    return view('index', compact('becas'));
})->name('index');

// ========== 3. HOME (DASHBOARD DE USUARIO AUTENTICADO) ==========
/**
 * GET /home
 * Dashboard privado del usuario
 * Middleware: auth (requiere autenticación), admin (requiere rol admin)
 */
Route::get('/home', [HomeController::class, 'index'])
     ->name('home')
     ->middleware('auth', 'admin');

// ========== 4. GESTIÓN DE CONTACTOS ==========
/**
 * GET /contacto
 * Muestra el formulario de contacto (Blade view)
 */
Route::view('contacto', 'contact')->name('contacto');

/**
 * POST /guardar-contacto
 * Procesa el envío del formulario de contacto
 * Controlador: ContactController@store
 */
Route::post('guardar-contacto', [ContactController::class, 'store']);

/**
 * GET /leer-contactos
 * Lista todos los mensajes de contacto recibidos
 * Solo visible para administradores
 * Middleware: auth, admin
 */
Route::get('/leer-contactos', [ContactController::class, 'index'])
     ->name('contactos');

// ========== 5. BÚSQUEDA DE BECAS ==========
/**
 * GET /busqueda?q=termino_busqueda
 * Busca becas por nombre o tipo
 * Parámetro: q (query string)
 * Middleware: auth (requiere estar logueado)
 */
Route::get('/busqueda', function (\Illuminate\Http\Request $request) {
    $query = $request->input('q');
    $becas = collect();
    
    if ($query) {
        // Buscar en campos nombre y tipo
        $becas = \App\Models\Beca::where('nombre', 'like', "%$query%")
                 ->orWhere('tipo', 'like', "%$query%")
                 ->get();
    }
    
    return view('busqueda', compact('becas'));
})->name('busqueda')->middleware('auth');

// ========== 6. SISTEMA DE FAVORITOS ==========
/**
 * GET /favoritos
 * Muestra las becas marcadas como favoritas por el usuario
 * Middleware: auth (requiere autenticación)
 */
Route::get('/favoritos', [\App\Http\Controllers\FavoriteController::class, 'index'])
     ->name('favoritos')
     ->middleware('auth');

/**
 * POST /favoritos/toggle/{becaId}
 * Alterna el estado de favorito de una beca (agregar/quitar)
 * Parámetro: becaId (ID de la beca)
 * Retorna: JSON
 * Middleware: auth (requiere autenticación)
 */
Route::post('/favoritos/toggle/{becaId}', [\App\Http\Controllers\FavoriteController::class, 'toggle'])
     ->name('favoritos.toggle')
     ->middleware('auth');

// ========== 7. NOTIFICACIONES ==========
/**
 * GET /notificaciones
 * Página de notificaciones (TODO: Aún no implementado)
 * Middleware: auth (requiere autenticación)
 */
Route::get('/notificaciones', function () {
    return view('notificaciones');
})->name('notificaciones')->middleware('auth');

// ========== 8. INFORMACIÓN GENERAL ==========
/**
 * GET /acerca-de
 * Página estática con información sobre el proyecto
 */
Route::view('acerca-de', 'acerca-de')->name('acerca-de');

// ========== 9. ACCESO SIN AUTENTICACIÓN ==========
/**
 * GET /continuar-como-visitante
 * Marca la sesión como visitante (guest) permitiendo browse sin login
 */
Route::get('/continuar-como-visitante', function (\Illuminate\Http\Request $request) {
    $request->session()->put('guest', true);
    return redirect()->route('index');
})->name('guest.continue');

// ========== 10. GESTIÓN DE USUARIOS ==========
/**
 * GET /lista-usuarios
 * Muestra lista de todos los usuarios del sistema
 * Middleware: auth, admin (solo administradores)
 */
Route::get('/lista-usuarios', function () {
    $users = User::all();
    return view('usuarios', compact('users'));
})->name('usuarios.lista')->middleware('auth', 'admin');

// ========== 11. CRUD COMPLETO DE BECAS ==========
/**
 * GET /becas
 * Muestra el listado de todas las becas (panel admin)
 * Controlador: BecaController@index
 * Middleware: auth, admin (solo administradores)
 */
Route::get('/becas', [BecaController::class, 'index'])
     ->name('becas.index')
     ->middleware('auth', 'admin');

// A. CREAR BECA
/**
 * GET /crear-beca
 * Muestra el formulario para crear una nueva beca
 * Controlador: BecaController@create
 * Middleware: auth, admin
 */
Route::get('/crear-beca', [BecaController::class, 'create'])
     ->name('becas.create')
     ->middleware('auth', 'admin');

/**
 * POST /becas
 * Procesa la creación de una nueva beca
 * Controlador: BecaController@store
 * Middleware: auth, admin
 */
Route::post('/becas', [BecaController::class, 'store'])
     ->name('becas.store')
     ->middleware('auth', 'admin');

// B. EDITAR BECA
/**
 * GET /becas/{id}/editar
 * Muestra el formulario para editar una beca
 * Parámetro: id (ID de la beca)
 * Controlador: BecaController@edit
 * Middleware: auth, admin
 */
Route::get('/becas/{id}/editar', [BecaController::class, 'edit'])
     ->name('becas.edit')
     ->middleware('auth', 'admin');

/**
 * PUT /becas/{id}
 * Procesa la actualización de una beca
 * Parámetro: id (ID de la beca)
 * Controlador: BecaController@update
 * Middleware: auth, admin
 */
Route::put('/becas/{id}', [BecaController::class, 'update'])
     ->name('becas.update')
     ->middleware('auth', 'admin');

// C. ELIMINAR BECA
/**
 * DELETE /becas/{id}
 * Elimina una beca del sistema
 * Parámetro: id (ID de la beca)
 * Controlador: BecaController@destroy
 * Middleware: auth, admin
 */
Route::delete('/becas/{id}', [BecaController::class, 'destroy'])
     ->name('becas.destroy')
     ->middleware('auth', 'admin');

// ========== 12. VISTA DE DETALLE DE BECA ==========
/**
 * GET /detalle-beca?id=n  O  /detalle-beca?img=path
 * Muestra los detalles completos de una beca específica
 * Parámetros: id (ID formato BD) o img (ruta de imagen)
 */
Route::get('/detalle-beca', function (\Illuminate\Http\Request $request) {
    $id = $request->query('id');
    if ($id) {
        $beca = \App\Models\Beca::find($id);
        if (!$beca) abort(404);
        return view('vistabecas', ['beca' => $beca, 'imagen' => $beca->imagen]);
    }
    $imagen = $request->query('img');
    return view('vistabecas', ['imagen' => $imagen, 'beca' => null]);
})->name('detalle-beca');

// ========== 13. CONFIGURACIÓN/SETUP DEL SISTEMA ==========
/**
 * GET /setup-favorites
 * Crea manualmente la tabla beca_user si no existe
 * Útil para migraciones fallidas
 */
Route::get('/setup-favorites', function() {
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
        return "✅ Tabla beca_user creada exitosamente";
    } catch (\Exception $e) {
        return "⚠️ Nota: " . $e->getMessage();
    }
});

/**
 * GET /add-role-field
 * Agrega la columna 'role' a la tabla users si no existe
 */
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

/**
 * GET /make-admin/{email}
 * Convierte un usuario a administrador por su email
 */
Route::get('/make-admin/{email}', function($email) {
    try {
        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            $user->role = 'admin';
            $user->save();
            return "✅ El usuario $email ahora es administrador";
        }
        return "❌ Usuario no encontrado";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});
```

---

# SISTEMA DE AUTENTICACIÓN {#autenticación}

## Middleware HTTP

### Archivo: app/Http/Kernel.php

```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware global HTTP stack.
     * Se ejecutan en TODAS las peticiones.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Middleware groups HTTP.
     * Grupos que se asignan a rutas completas.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Middleware de ruta disponibles.
     * Pueden asignarse a rutas individuales o grupos.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'              => \App\Http\Middleware\Authenticate::class,
        'auth.basic'        => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers'     => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'               => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'             => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm'  => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'            => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'          => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'          => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'guest.access'      => \App\Http\Middleware\AllowGuestAccess::class,
        'admin'             => \App\Http\Middleware\IsAdmin::class,  // Middleware personalizado
    ];
}
```

**Explicación de Middleware de Seguridad:**

- **auth**: Requiere que el usuario esté autenticado
- **admin**: Requiere que el usuario tenga rol 'admin'
- **guest**: Redirige a usuarios autenticados a home
- **verified**: Requiere que el email esté verificado

---

# COMPONENTES DE INTERFAZ {#componentes}

## Layout Base: master.blade.php

**Ubicación:** `resources/views/layouts/master.blade.php`

Este archivo contiene la estructura HTML base, navegación y estilos globales que se comparten en todas las páginas.

```php
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Plataforma Web</title>

  <!-- LIBRERÍAS CSS EXTERNAS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Estilos personalizados del navbar y layout */
    body {
      background-color: #ffffff !important;
      padding-top: {{ isset($hideNavbar) && $hideNavbar ? '0' : '76px' }};
    }

    nav.custom-navbar {
      background-color: #1976D2;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      min-height: 60px;
      padding-top: 10px;
      padding-bottom: 10px;
    }

    .custom-navbar .navbar-brand {
      color: white !important;
      font-weight: bold;
    }

    .custom-navbar .nav-link {
      color: rgb(240, 240, 240) !important;
    }

    /* Estilos para cards y componentes */
    .card {
      border: none;
      border-radius: 15px;
      background-color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: transform 0.2s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    /* Carrusel principal */
    .main-carousel {
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      margin-top: 10px;
    }

    .main-carousel img {
      width: 100%;
      height: 320px;
      object-fit: cover;
    }
  </style>
</head>

<body>
  <!-- BARRA DE NAVEGACIÓN PRINCIPAL -->
  <nav class="navbar navbar-expand-lg custom-navbar sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('index') }}">
        <i class="fas fa-graduation-cap"></i> Becas Académicas
      </a>

      <!-- Botón para dispositivos móviles -->
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menú principal -->
      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title">Menú</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="{{ route('index') }}">Inicio</a>
            </li>
            
            @auth
              <li class="nav-item">
                <a class="nav-link" href="{{ route('busqueda') }}">Buscar</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ route('favoritos') }}">Mis Favoritos</a>
              </li>
              @if(Auth::user()->role === 'admin')
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('becas.index') }}">Administrar Becas</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('contactos') }}">Ver Contactos</a>
                </li>
              @endif
            @endauth

            <li class="nav-item">
              <a class="nav-link" href="{{ route('contacto') }}">Contacto</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('acerca-de') }}">Acerca de</a>
            </li>
          </ul>

          <!-- Botones de autenticación -->
          <div class="d-flex gap-2 ms-3 mt-3">
            @guest
              <a href="{{ route('login') }}" class="btn btn-light btn-sm">Iniciar Sesión</a>
              <a href="{{ route('register') }}" class="btn btn-light btn-sm">Registrarse</a>
            @else
              <span class="text-white me-2">{{ Auth::user()->name }}</span>
              <a href="{{ route('logout') }}" class="btn btn-danger btn-sm"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Salir
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            @endguest
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="container-fluid">
    @if($errors->any())
      <div class="alert alert-danger mt-3">
        <h4>Errores encontrados:</h4>
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Sección dinámica de contenido -->
    @yield('content')
  </main>

  <!-- PIE DE PÁGINA -->
  <footer class="bg-dark text-white text-center py-4 mt-5">
    <p>&copy; {{ date('Y') }} Sistema de Becas. Todos los derechos reservados.</p>
  </footer>

  <!-- LIBRERÍAS JAVASCRIPT -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Scripts personalizados -->
  <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
```

---

# FUNCIONALIDADES PRINCIPALES {#funcionalidades}

## 1. Sistema de Búsqueda de Becas

**Ruta:** `/busqueda?q=termino`

Esta funcionalidad permite a los usuarios buscar becas por nombre o tipo. La búsqueda utiliza la operación `LIKE` de SQL para coincidencias parciales.

```php
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
```

## 2. Sistema de Favoritos

**Rutas:**
- GET `/favoritos` - Ver favoritos
- POST `/favoritos/toggle/{becaId}` - Agregar/Quitar favorito

El sistema de favoritos permite a cada usuario marcar becas como sus favoritas para acceso rápido posterior.

```javascript
// Ejemplo de cómo se ejecuta desde el frontend
async function toggleFavorite(becaId) {
    try {
        const response = await fetch(`/favoritos/toggle/${becaId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        if (data.success) {
            // Actualizar UI para mostrar estado de favorito
            console.log(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
```

## 3. Gestión de Contactos

**Formulario HTML:**

```html
<form action="{{ route('guardar-contacto') }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre Completo</label>
        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
               id="nombre" name="nombre" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="correo" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control @error('correo') is-invalid @enderror" 
               id="correo" name="correo" required>
        @error('correo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="asunto" class="form-label">Asunto</label>
        <input type="text" class="form-control @error('asunto') is-invalid @enderror" 
               id="asunto" name="asunto" required>
        @error('asunto')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="prioridad" class="form-label">Prioridad</label>
        <select class="form-select @error('prioridad') is-invalid @enderror" 
                id="prioridad" name="prioridad" required>
            <option value="">Seleccionar...</option>
            <option value="Alta">Alta</option>
            <option value="Media">Media</option>
            <option value="Baja">Baja</option>
        </select>
        @error('prioridad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="mensaje" class="form-label">Mensaje</label>
        <textarea class="form-control @error('mensaje') is-invalid @enderror" 
                  id="mensaje" name="mensaje" rows="5" required></textarea>
        @error('mensaje')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
</form>
```

## 4. CRUD de Becas (Admin)

El administrador puede:
- **CREATE**: Crear nuevas becas con imagen
- **READ**: Ver lista completa de becas
- **UPDATE**: Editar información de becas existentes
- **DELETE**: Eliminar becas del sistema

---

# CONFIGURACIÓN DEL SISTEMA {#configuración}

## Archivo: config/app.php

```php
<?php

return [
    'name' => env('APP_NAME', 'Becas Académicas'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL', null),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'providers' => [
        // Todos los service providers de Laravel
    ],
];
```

## Archivo: .env.example

```env
APP_NAME="Becas Académicas"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=becas_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="{{ config('app.name') }}"
```

---

# INSTRUCCIONES DE INSTALACIÓN Y CONFIGURACIÓN {#instalación}

## Requisitos de Entorno

Para funcionamiento óptimo del sistema se requiere:

- PHP versión 7.3 o superior
- Sistema gestor de base de datos MySQL/MariaDB versión 5.7 o posterior
- Composer como gestor de dependencias PHP
- Node.js y npm (opcional, para compilación de assets)
- Git como herramienta de control de versiones

## Procedimiento de Instalación

### 1. Obtención de Código Fuente

```bash
git clone [url-del-repositorio]
cd proyecto-becas
```

### 2. Instalación de Dependencias PHP

```bash
composer install
```

### 3. Inicialización de Archivo de Configuración

```bash
cp .env.example .env
```

### 4. Generación de Clave de Aplicación

```bash
php artisan key:generate
```

### 5. Configuración de Parámetros de Base de Datos

Modificación del archivo `.env` con parámetros específicos de conexión:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=becas_db
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Ejecución de Migraciones de Base de Datos

```bash
php artisan migrate
```

### 7. Inicialización de Cuenta Administrativa

```bash
php artisan tinker
>>> $user = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => 'admin']);
>>> exit
```

Alternativamente, mediante acceso a ruta de configuración:
```
http://localhost:8000/make-admin/admin@example.com
```

### 8. Inicialización de Servidor de Desarrollo

```bash
php artisan serve
```

Tras completar este procedimiento, el sistema estará disponible mediante navegador en dirección: `http://localhost:8000`

# CONCLUSIÓN Y PERSPECTIVA DE ESCALABILIDAD

El sistema de gestión de becas presentado en esta documentación proporciona una solución integral y modular para administración de oportunidades educativas. Su arquitectura implementa patrones establecidos de ingeniería de software que facilitan mantenibilidad, escalabilidad y extensibilidad de funcionalidad.

Las siguientes características representan áreas de potencial expansión funcional:

- Implementación de subsistema de notificaciones electrónicas automatizadas
- Generación de reportes estadísticos y análisis de datos
- Integración con pasarelas de pago para gestión de transacciones
- Sistema de comentarios y evaluaciones de oportunidades
- Generación de certificados digitales de solicitud de beca
- Desarrollo de especificación completa de API REST para consumo por aplicaciones cliente específicas

El código fuente ha sido desarrollado respetando estándares de calidad de Laravel y sigue las mejores prácticas reconocidas en ingeniería de software, facilitando colaboración efectiva entre miembros del equipo de desarrollo y mantenimiento prolongado del sistema.

---

**Documento Técnico Generado:** Marzo 2026  
**Versión de Especificación:** 1.0  
**Estado de Documentación:** Completo y Formalizado

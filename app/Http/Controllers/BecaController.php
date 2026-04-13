<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beca; 
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\File; // Importante para poder borrar imágenes viejas

class BecaController extends Controller
{
    // --- 1. VER LISTA (READ) ---
    public function index()
    {
        $becas = Beca::all(); // Trae todo de la BD
        return view('becas.index', compact('becas'));
    }

    // --- 2. CREAR (CREATE & STORE) ---
    public function create()
    {
        return view('becas.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required', 'tipo' => 'required']);

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

        // Crear notificaciones para todos los usuarios normales
        $usersNormales = User::where('role', 'user')->get();
        foreach ($usersNormales as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'beca_nueva',
                'title' => 'Nueva beca disponible',
                'message' => "Se ha añadido una nueva beca: {$beca->nombre} ({$beca->tipo})",
                'url' => route('index'),
            ]);
        }

        // Al guardar, nos manda a la lista (index)
        return redirect()->route('becas.index')->with('success', '¡Beca creada exitosamente!');
    }

    // --- 3. EDITAR (EDIT & UPDATE) ---
    // Muestra el formulario con los datos cargados
    public function edit($id)
    {
        $beca = Beca::find($id);
        return view('becas.edit', compact('beca'));
    }

    // Guarda los cambios en la BD
    public function update(Request $request, $id)
    {
        $request->validate(['nombre' => 'required', 'tipo' => 'required']);
        
        $beca = Beca::find($id); // Buscamos la beca a editar

        $beca->nombre = $request->input('nombre');
        $beca->tipo = $request->input('tipo');
        $beca->monto = $request->input('monto');
        $beca->descripcion = $request->input('descripcion');

        // Si suben NUEVA imagen, borramos la vieja y guardamos la nueva
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $destinationPath = 'images/becas/';
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $filename);

            // Borrar imagen anterior si existe
            if ($beca->imagen && File::exists(public_path($beca->imagen))) {
                File::delete(public_path($beca->imagen));
            }

            $beca->imagen = $destinationPath . $filename;
        }

        $beca->update(); // Guardamos cambios

        // Crear notificaciones para todos los usuarios normales
        $usersNormales = User::where('role', 'user')->get();
        foreach ($usersNormales as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'beca_actualizada',
                'title' => 'Beca actualizada',
                'message' => "La beca {$beca->nombre} ha sido actualizada con nueva información",
                'url' => route('index'),
            ]);
        }

        return redirect()->route('becas.index')->with('success', '¡Beca actualizada correctamente!');
    }

    // --- 4. BORRAR (DESTROY) ---
    public function destroy($id)
    {
        $beca = Beca::find($id);
        if ($beca) {
            // Opcional: Borrar la imagen de la carpeta al eliminar la beca
            if ($beca->imagen && File::exists(public_path($beca->imagen))) {
                File::delete(public_path($beca->imagen));
            }
            
            $beca->delete();
            return back()->with('success', 'Beca eliminada correctamente.');
        }
        return back()->with('error', 'No se encontró la beca.');
    }
}
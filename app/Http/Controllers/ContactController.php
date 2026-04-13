<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conctact;
use App\Models\User;
use App\Models\Notification;

class ContactController extends Controller
{
    public function index()
    {
        $mensajes = Conctact::all();
        return view('indexcontact', compact('mensajes'));
        
    }

    public function show($id)
    {
        $mensaje = Conctact::findOrFail($id);
        return view('detalle-contacto', compact('mensaje'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|min:3|max:255',
            'correo' => 'required|email|max:255',
            'asunto' => 'required|string|min:5|max:255',
            'prioridad' => 'required|in:alta,media,baja',
            'mensaje' => 'required|string|min:10|max:3000',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe ser válido.',
            'asunto.required' => 'El asunto es obligatorio.',
            'asunto.min' => 'El asunto debe tener al menos 5 caracteres.',
            'prioridad.required' => 'La prioridad es obligatoria.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'mensaje.max' => 'El mensaje no puede exceder 3000 caracteres.',
        ]);

        $contacto = new Conctact;
        $contacto->nombre = $validated['nombre'];
        $contacto->correo = $validated['correo'];
        $contacto->asunto = $validated['asunto'];
        $contacto->prioridad = $validated['prioridad'];
        $contacto->mensaje = $validated['mensaje'];
        $contacto->save();

        // Crear notificación para el admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'mensaje',
                'title' => 'Nuevo mensaje de contacto',
                'message' => "Nuevo mensaje de {$validated['nombre']} con asunto: {$validated['asunto']}",
                'url' => route('contacto.show', $contacto->id),
            ]);
        }
        
        return redirect()->back()->with('success', '✓ Mensaje enviado correctamente. Nos pondremos en contacto pronto.');
    }
}

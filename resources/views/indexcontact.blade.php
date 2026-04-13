@extends('layouts.master')

@section('content')

<style>
    /* Estilos específicos para esta vista */
    .table-container {
        border-radius: 20px;
        overflow: hidden; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background-color: white;
    }

    .custom-table thead {
        background-color: #1ee3d6; 
        color: white;
    }
    
    .custom-table th {
        border: none;
        padding: 15px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .custom-table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        color: #555;
    }

    .custom-table tr:hover {
        background-color: #f9fbfb;
    }

    .badge-prioridad {
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: bold;
        background-color: #eef2f3;
        color: #333;
    }

    .page-header {
        border-left: 5px solid #1ee3d6;
        padding-left: 15px;
        color: #2c3e50;
        margin-bottom: 30px;
    }
</style>

<div class="container my-5">

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-header fw-bold">Buzón de Mensajes</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table custom-table mb-0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th class="text-center">Prioridad</th>
                                <th>Asunto</th>
                                <th>Mensaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mensajes as $mensaje)
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('contacto.show', $mensaje->id) }}'">
                                <td class="fw-bold">{{ $mensaje->nombre }}</td>
                                
                                <td>
                                    <a href="mailto:{{ $mensaje->correo }}" class="text-decoration-none text-info" onclick="event.stopPropagation();">
                                        {{ $mensaje->correo }}
                                    </a>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge-prioridad">
                                        {{ $mensaje->prioridad }}
                                    </span>
                                </td>
                                
                                <td>{{Str::limit($mensaje->asunto, 30) }}</td>
                                
                                <td class="text-muted">
                                    {{ Str::limit($mensaje->mensaje, 50) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary"></i>
                                        <p class="h5">No hay mensajes nuevos</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
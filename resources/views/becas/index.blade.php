@extends('layouts.master')

@section('content')

<style>
    /* Estilos de tu Dashboard */
    .table-container {
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background-color: white;
        overflow: hidden;
        margin-top: 30px;
    }
    .page-header {
        border-left: 5px solid #1ee3d6;
        padding-left: 15px;
        color: #2c3e50;
        margin-top: 40px;
    }
    .user-table thead {
        background-color: #2c3e50; 
        color: white;
    }
    .user-table th {
        border: none; padding: 15px; font-weight: 600; text-transform: uppercase; font-size: 0.8rem;
    }
    .user-table td {
        padding: 15px; vertical-align: middle; border-bottom: 1px solid #f0f0f0;
    }
    .avatar-img {
        width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px; border: 2px solid #1ee3d6;
    }
    .avatar-placeholder {
        width: 40px; height: 40px; border-radius: 50%; background-color: #1ee3d6; color: white;
        display: flex; align-items: center; justify-content: center; margin-right: 10px; font-weight: bold;
    }
</style>

<div class="container mb-5">

    {{-- Alerta de éxito --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="page-header fw-bold">Panel de Administración de Becas</h2>
        </div>
        <div class="col-md-6 text-end">
            {{-- Botón para ir a crear nueva --}}
            <a href="{{ route('becas.create') }}" class="btn btn-primary mt-4 text-white fw-bold" style="background-color: #1ee3d6; border:none;">
                <i class="fa-solid fa-plus me-1"></i> Agregar Nueva Beca
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table user-table mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Monto</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($becas as $beca)
                            <tr>
                                <td class="text-muted">#{{ $beca->id }}</td>
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($beca->imagen)
                                            <img src="{{ asset($beca->imagen) }}" class="avatar-img" alt="img">
                                        @else
                                            <div class="avatar-placeholder">{{ substr($beca->nombre, 0, 1) }}</div>
                                        @endif
                                        <div class="fw-bold text-dark">{{ $beca->nombre }}</div>
                                    </div>
                                </td>
                                
                                <td><span class="badge bg-light text-dark border">{{ $beca->tipo }}</span></td>
                                
                                <td class="fw-bold text-success">${{ number_format($beca->monto, 2) }}</td>
                                
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        
                                        {{-- BOTÓN SUBIR (Publica en la página pública) --}}
                                        <a href="{{ route('detalle-beca', ['id' => $beca->id]) }}" target="_blank" class="btn btn-sm btn-light text-success border" title="Ver/Subir">
                                            <i class="fa-solid fa-upload"></i>
                                        </a>

                                        {{-- BOTÓN EDITAR (Lleva a la ruta edit que definimos) --}}
                                        <a href="{{ route('becas.edit', $beca->id) }}" class="btn btn-sm btn-light text-primary border" title="Editar">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- BOTÓN ELIMINAR --}}
                                        <form action="{{ route('becas.destroy', $beca->id) }}" method="POST" onsubmit="return confirm('¿Borrar esta beca?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger border" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">No hay becas registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
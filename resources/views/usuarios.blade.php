@extends('layouts.master')

@section('content')

<style>
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

    /* Estilos tabla usuarios */
    .user-table thead {
        background-color: #2c3e50; /* Un tono más oscuro para diferenciar de mensajes */
        color: white;
    }
    .user-table th {
        border: none;
        padding: 15px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
    }
    .user-table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }
    
    /* Avatar circular */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #1ee3d6;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
    }

    .badge-role {
        background-color: #e3f2fd;
        color: #0d47a1;
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: bold;
    }
</style>

<div class="container mb-5">

    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="page-header fw-bold">Usuarios Registrados</h2>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-outline-secondary btn-sm mt-4">
                <i class="fa-solid fa-download me-1"></i> Exportar Lista
            </button>
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
                                <th>Usuario</th>
                                <th>Correo Electrónico</th>
                                <th>Fecha de Registro</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- AQUÍ RECORREMOS LOS USUARIOS QUE MANDES DESDE EL CONTROLADOR --}}
                            @forelse($users as $user)
                            <tr>
                                <td class="text-muted">#{{ $user->id }}</td>
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="small text-muted">Estudiante</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>{{ $user->email }}</td>
                                
                                <td>
                                    {{ $user->created_at->format('d/m/Y') }} 
                                    <span class="small text-muted">({{ $user->created_at->diffForHumans() }})</span>
                                </td>
                                
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light text-primary" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <p class="text-muted">No hay usuarios registrados en el sistema.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{-- {{ $users->links() }} --}}
            </div>
        </div>
    </div>

</div>

@endsection
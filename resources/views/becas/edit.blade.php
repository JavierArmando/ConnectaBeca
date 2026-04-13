@extends('layouts.master')

@section('content')

<style>
    .form-card {
        border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden;
    }
    .form-header {
        background-color: #ffc107; /* Amarillo para indicar Edición */
        color: #333; padding: 20px; text-align: center;
    }
    .form-control:focus, .form-select:focus {
        border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    }
    .current-img {
        width: 100px; height: 100px; object-fit: cover; border-radius: 10px; border: 2px solid #ddd;
    }
</style>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <a href="{{ route('becas.index') }}" class="btn btn-outline-secondary mb-3 border-0">
                <i class="fa-solid fa-arrow-left"></i> Cancelar y Regresar
            </a>

            <div class="card form-card">
                <div class="form-header">
                    <h3 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Editar Beca</h3>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    {{-- FORMULARIO DE EDICIÓN --}}
                    {{-- IMPORTANTE: La ruta apunta a UPDATE y pasamos el ID --}}
                    <form action="{{ route('becas.update', $beca->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf 
                        @method('PUT') {{-- ESTO ES CLAVE: Convierte el formulario en modo "Actualizar" --}}

                        <div class="row g-3">
                            
                            {{-- Nombre (Con value="{{ ... }}" para que salga lo que ya estaba) --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Nombre de la Beca</label>
                                <input type="text" class="form-control" name="nombre" value="{{ $beca->nombre }}" required>
                            </div>

                            {{-- Tipo y Monto --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Categoría</label>
                                <select class="form-select" name="tipo">
                                    <option value="Básica" {{ $beca->tipo == 'Básica' ? 'selected' : '' }}>Educación Básica</option>
                                    <option value="Media Superior" {{ $beca->tipo == 'Media Superior' ? 'selected' : '' }}>Media Superior</option>
                                    <option value="Superior" {{ $beca->tipo == 'Superior' ? 'selected' : '' }}>Universidad</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Monto ($)</label>
                                <input type="number" class="form-control" name="monto" value="{{ $beca->monto }}">
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="4">{{ $beca->descripcion }}</textarea>
                            </div>

                            {{-- Imagen Actual y Nueva --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Imagen</label>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    @if($beca->imagen)
                                        <div>
                                            <small class="d-block text-muted mb-1">Actual:</small>
                                            <img src="{{ asset($beca->imagen) }}" class="current-img">
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <small class="d-block text-muted mb-1">Cambiar imagen (opcional):</small>
                                        <input type="file" class="form-control" name="imagen">
                                    </div>
                                </div>
                            </div>

                            {{-- Botón Guardar Cambios --}}
                            <div class="col-12 mt-4 d-grid">
                                <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark">
                                    Actualizar Beca
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
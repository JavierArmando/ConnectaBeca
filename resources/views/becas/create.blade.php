@extends('layouts.master')

@section('content')

<style>
    .form-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .form-header {
        background-color: #1ee3d6; 
        color: white;
        padding: 20px;
        text-align: center;
    }

    .form-control:focus, .form-select:focus {
        border-color: #1ee3d6;
        box-shadow: 0 0 0 0.25rem rgba(30, 227, 214, 0.25);
    }
</style>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            {{-- BOTÓN REGRESAR--}}
            <a href="#" class="btn btn-outline-secondary mb-3 border-0">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </a>

            <div class="card form-card">
                <div class="form-header">
                    <h3 class="mb-0 fw-bold">Registrar Nueva Beca</h3>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    {{-- FORMULARIO --}}
                    <form action="{{ route('becas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf 

                        <div class="row g-3">
                            
                            {{-- Nombre --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Nombre de la Beca</label>
                                <input type="text" class="form-control" name="nombre" placeholder="Ej. Beca Jóvenes..." required>
                            </div>

                            {{-- Tipo y Monto --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Categoría</label>
                                <select class="form-select" name="tipo">
                                    <option selected disabled>Selecciona...</option>
                                    <option value="Primaria">Primaria</option>
                                    <option value="Secundaria">Secundaria</option>
                                    <option value="Media Superior">Media Superior</option>
                                    <option value="Universidad">Universidad</option>
                                    <option value="Posgrado">Posgrado</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Monto ($)</label>
                                <input type="number" class="form-control" name="monto" placeholder="0.00">
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="4"></textarea>
                            </div>

                            {{-- Imagen --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Imagen</label>
                                <input type="file" class="form-control" name="imagen">
                            </div>

                            {{-- Guardar --}}
                            <div class="col-12 mt-4 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg text-white fw-bold" style="background-color: #1ee3d6; border: none;">
                                    Guardar Beca
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
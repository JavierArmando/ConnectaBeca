@extends('layouts.master')
@section('content')
    <section class="section">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-exclamation-circle me-2"></i>
                    <strong>Por favor, corrige los siguientes errores:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="post" action="{{ url('guardar-contacto') }}" class="mt-4">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-lg-4">
                        <label for="correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" id="correo" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}" required>
                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="prioridad" class="form-label">Prioridad <span class="text-danger">*</span></label>
                        <select name="prioridad" id="prioridad" class="form-control @error('prioridad') is-invalid @enderror" required>
                            <option value="">Selecciona una prioridad</option>
                            <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="media" {{ old('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                        @error('prioridad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <label for="asunto" class="form-label">Asunto <span class="text-danger">*</span></label>
                        <input type="text" id="asunto" name="asunto" class="form-control @error('asunto') is-invalid @enderror" value="{{ old('asunto') }}" required>
                        @error('asunto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <label for="mensaje" class="form-label">Mensaje <span class="text-danger">*</span></label>
                        <textarea name="mensaje" id="mensaje" class="form-control @error('mensaje') is-invalid @enderror" rows="10" required>{{ old('mensaje') }}</textarea>
                        @error('mensaje')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-4">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                </button>
            </form>
        </div>
    </section>
@endsection
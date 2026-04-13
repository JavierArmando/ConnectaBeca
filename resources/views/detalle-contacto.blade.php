@extends('layouts.master')

@section('content')

<style>
    .detalle-header {
        background: linear-gradient(135deg, #1ee3d6 0%, #18a8a0 100%);
        color: white;
        padding: 30px;
        border-radius: 15px 15px 0 0;
        margin-bottom: 30px;
    }

    .detalle-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .detalle-body {
        padding: 40px;
    }

    .info-row {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-label {
        font-weight: 700;
        color: #2c3e50;
        min-width: 150px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #555;
        flex: 1;
    }

    .info-value a {
        color: #1ee3d6;
        text-decoration: none;
        font-weight: 500;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    .badge-prioridad {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: bold;
        background-color: #e8f5f3;
        color: #00695c;
    }

    .mensaje-contenido {
        background-color: #f9fbfb;
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid #1ee3d6;
        line-height: 1.6;
        color: #555;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .btn-regresar {
        display: inline-block;
        margin-top: 20px;
    }

    .fecha-mensaje {
        font-size: 0.85rem;
        color: #aaa;
    }
</style>

<div class="container my-5">
    
    <div class="detalle-card">
        <div class="detalle-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-2 fw-bold">{{ $mensaje->nombre }}</h3>
                    <p class="mb-0 fecha-mensaje">
                        <i class="fa-regular fa-calendar me-2"></i>
                        {{ $mensaje->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <span class="badge-prioridad">
                    <i class="fa-solid fa-flag me-2"></i>
                    @if($mensaje->prioridad == 1)
                        Baja
                    @elseif($mensaje->prioridad == 2)
                        Media
                    @elseif($mensaje->prioridad == 3)
                        Alta
                    @else
                        {{ $mensaje->prioridad }}
                    @endif
                </span>
            </div>
        </div>

        <div class="detalle-body">
            
            {{-- Correo --}}
            <div class="info-row">
                <div class="info-label">
                    <i class="fa-solid fa-envelope me-2"></i>Correo
                </div>
                <div class="info-value">
                    <a href="mailto:{{ $mensaje->correo }}">{{ $mensaje->correo }}</a>
                </div>
            </div>

            {{-- Asunto --}}
            <div class="info-row">
                <div class="info-label">
                    <i class="fa-solid fa-heading me-2"></i>Asunto
                </div>
                <div class="info-value">
                    <strong>{{ $mensaje->asunto }}</strong>
                </div>
            </div>

            {{-- Mensaje Completo --}}
            <div class="mb-30">
                <div class="info-label mb-3">
                    <i class="fa-solid fa-message me-2"></i>Mensaje
                </div>
                <div class="mensaje-contenido">
                    {{ $mensaje->mensaje }}
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="mt-5">
                <a href="{{ route('contactos') }}" class="btn btn-secondary btn-regresar">
                    <i class="fa-solid fa-arrow-left me-2"></i>Volver al Buzón
                </a>
            </div>

        </div>
    </div>

</div>

@endsection

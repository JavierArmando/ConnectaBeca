@extends('layouts.master')

@section('content')

<style>
    body {
        background: #f5f5f5;
        padding-top: 0 !important;
    }

    .logo-header {
        background: linear-gradient(135deg, #1976D2 0%, #0d47a1 100%);
        width: 100%;
        padding: 40px 20px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);
        margin-bottom: 60px;
    }

    .logo-box {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-text {
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
        letter-spacing: 30px;
    }

    .home-container {
        min-height: 70vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .page-title {
        color: #2c3e50;
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .user-card {
        background: white;
        border-radius: 15px;
        padding: 25px 35px;
        display: flex;
        align-items: center;
        gap: 25px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 50px;
        max-width: 400px;
        border: 1px solid #f0f0f0;
    }

    .user-avatar {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #1976D2 0%, #0d47a1 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        font-weight: bold;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        font-size: 1.05rem;
    }

    .user-email {
        font-size: 0.8rem;
        color: #aaa;
        margin: 5px 0 0 0;
    }

    .btn-logout {
        background: linear-gradient(135deg, #ef5350 0%, #d32f2f 100%);
        color: white;
        border: none;
        padding: 10px 10px;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .btn-logout:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 15px rgba(211, 47, 47, 0.35);
    }

    .dashboards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        width: 100%;
        max-width: 800px;
    }

    .dashboard-card {
        background: linear-gradient(135deg, #1976D2 0%, #0d47a1 100%);
        color: white;
        padding: 30px 20px;
        border-radius: 15px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(25, 118, 210, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 120px;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(25, 118, 210, 0.4);
        text-decoration: none;
        color: white;
    }

    @media (max-width: 768px) {
        .home-container {
            padding: 20px;
        }

        .dashboards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .user-card {
            margin-bottom: 30px;
            padding: 20px 25px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .page-title {
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        .logo-header {
            padding: 30px 20px;
            margin-bottom: 40px;
        }

        .logo-text {
            font-size: 1.8rem;
        }

        .dashboard-card {
            min-height: 100px;
            font-size: 0.95rem;
            padding: 20px 15px;
        }
    }
</style>

{{-- HEADER EXCLUSIVO CON LOGO --}}
<div class="logo-header">
    <div class="logo-box">
        <img src="{{ asset('images/logo.png') }}" alt="Conecta Beca" style="max-height: 100px; max-width: 100%; object-fit: contain;">
    </div>
</div>

<div class="container">
    <div class="home-container">
        
        <div class="page-title">Información de beca</div>

        {{-- Tarjeta de Usuario --}}
        <div class="user-card">
            <div class="user-avatar">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="user-info">
                <p class="user-name">{{ Auth::user()->name }}</p>
                <p class="user-email">{{ Auth::user()->email }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout" title="Cerrar Sesión">
                    <i class="fa-solid fa-power-off"></i>
                </button>
            </form>
        </div>

        {{-- Grid de Dashboards --}}
        <div class="dashboards-grid">
            {{-- Index --}}
            <a href="{{ route('index') }}" class="dashboard-card">
                <div>Index</div>
            </a>

            {{-- Buzón de Mensajes --}}
            <a href="{{ route('contactos') }}" class="dashboard-card">
                <div>Buzón de Mensajes</div>
            </a>

            {{-- Usuarios --}}
            <a href="{{ route('usuarios.lista') }}" class="dashboard-card">
                <div>Usuarios</div>
            </a>

            {{-- Agregar Beca --}}
            <a href="{{ route('becas.create') }}" class="dashboard-card">
                <div>Agregar Beca</div>
            </a>

            {{-- Agregar/Editar Beca (Panel de Admin) --}}
            <a href="{{ route('becas.index') }}" class="dashboard-card">
                <div>Agregar/Editar Beca</div>
            </a>
        </div>

    </div>
</div>

@endsection
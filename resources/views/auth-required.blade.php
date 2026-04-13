@extends('layouts.master')

@section('content')
<style>
    .no-auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 80px);
        padding: 40px 20px;
        background: linear-gradient(135deg, #0BC0DB 0%, #1ee3d6 100%);
    }

    .no-auth-box {
        background-color: white;
        padding: 60px 40px;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        text-align: center;
        max-width: 500px;
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .no-auth-icon {
        font-size: 4rem;
        color: #1976D2;
        margin-bottom: 20px;
    }

    .no-auth-title {
        font-size: 2rem;
        font-weight: 700;
        color: #004d40;
        margin-bottom: 15px;
    }

    .no-auth-message {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 40px;
        line-height: 1.6;
    }

    .btn-login {
        background-color: #1976D2;
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        margin-right: 10px;
    }

    .btn-login:hover {
        background-color: #004d40;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .btn-register {
        background-color: #03dc94;
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-register:hover {
        background-color: #00a86b;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .btn-home {
        background-color: #999;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .btn-home:hover {
        background-color: #666;
    }

    .button-group {
        margin-bottom: 30px;
    }

    @media (max-width: 600px) {
        .no-auth-box {
            padding: 40px 25px;
        }

        .no-auth-title {
            font-size: 1.5rem;
        }

        .no-auth-message {
            font-size: 1rem;
        }

        .btn-login, .btn-register {
            display: block;
            width: 100%;
            margin: 10px 0;
        }
    }
</style>

<div class="no-auth-container">
    <div class="no-auth-box">
        <div class="no-auth-icon">
            <i class="fas fa-lock"></i>
        </div>
        <h1 class="no-auth-title">Acceso Restringido</h1>
        <p class="no-auth-message">
            Necesitas tener una cuenta para acceder a esta característica. 
            Por favor inicia sesión o regístrate para continuar.
        </p>
        <div class="button-group">
            <a href="{{ route('login') }}" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Inicia Sesión
            </a>
            <a href="{{ route('register') }}" class="btn-register">
                <i class="fas fa-user-plus me-2"></i>Regístrate
            </a>
        </div>
        <a href="{{ route('index') }}" class="btn-home">
            <i class="fas fa-home me-2"></i>Volver al Inicio
        </a>
    </div>
</div>
@endsection

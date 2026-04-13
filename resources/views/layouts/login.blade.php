{{-- Definimos la variable para ocultar el navbar --}}
@php $hideNavbar = true; @endphp

@extends('layouts.master')

@section('content')
<style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
        /* COLOR DEL HEADER (Turquesa vibrante) */
        background-color: #0BC0DB; 
    }
    .login-box {
        /* COLOR MENTA MEDIO AZULADO (Suave) */
        background-color: #e0f7fa; 
        padding: 50px 40px;
        border-radius: 20px; 
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); 
        width: 100%;
        max-width: 400px;
        text-align: center;
    }
    .form-title { font-size: 2.5rem; font-weight: 700; margin-bottom: 5px; color: #004d40; /* Texto verde oscuro */ }
    .form-subtitle { font-size: 1.5rem; font-weight: 400; margin-bottom: 40px; color: #00695c; }
    
    .input-group-custom { margin-bottom: 20px; }
    
    .form-control-custom {
        height: 55px; 
        font-size: 1.1rem;
        border: none;
        border-radius: 10px;
        background-color: #ffffff; 
        padding-left: 45px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .input-icon {
        position: absolute; left: 15px; top: 50%; transform: translateY(-50%);
        color: #4db6ac; /* Icono color menta oscuro */
        font-size: 1.2rem; z-index: 10;
    }
    
    .btn-continuar {
        background-color: #03dc94ff; /* Botón verde oscuro para contraste */
        border: none; border-radius: 10px;
        color: white; height: 50px; font-size: 1.1rem; font-weight: 600;
        margin-top: 30px;
        transition: background 0.3s;
    }
    .btn-continuar:hover { background-color: #004d40; }

    .register-link {
        font-size: 0.9rem; color: #00695c; text-decoration: none; margin-top: 20px; display: block;
    }
    .btn-invitado {
        color: #004d40; text-decoration: underline; font-weight: 600; margin-top: 30px; display: block;
    }

    .alert-error {
        background-color: #ffebee;
        border-left: 4px solid #f44336;
        color: #c62828;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .alert-error i {
        margin-right: 10px;
    }

    .invalid-feedback {
        display: block;
        color: #f44336 !important;
        font-size: 0.85rem;
        margin-top: 5px;
    }
</style>

<div class="login-container">
    <div class="login-box">
        <h1 class="form-title">Bienvenido</h1>
        <p class="form-subtitle">Usuario</p>
        
        {{-- Alertas de error general --}}
        @if ($errors->has('login'))
            <div class="alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <strong>{{ $errors->first('login') }}</strong>
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group-custom position-relative">
                <span class="input-icon"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control form-control-custom w-100 @error('login') is-invalid @enderror" name="login" placeholder="Usuario o correo" value="{{ old('login') }}" required autofocus>
                @error('login')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input-group-custom position-relative">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control form-control-custom w-100 @error('password') is-invalid @enderror" name="password" placeholder="Contraseña" required>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div> 

            <button class="btn btn-block w-100 btn-continuar" type="submit">Continuar</button>
        </form>

        <a href="{{ route('register') }}" class="register-link">¿No tienes cuenta? <strong>Regístrate aquí</strong></a>
        
        <a href="{{ route('guest.continue') }}" class="btn-invitado">Continuar como Invitado</a>

    </div>
</div>
@endsection
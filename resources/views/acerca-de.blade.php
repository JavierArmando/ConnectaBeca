@extends('layouts.master')

@section('content')

<style>
    body {
        background: #f5f5f5;
        padding-bottom: 90px;
    }

    .page-header {
        background: linear-gradient(135deg, #1976D2 0%, #0d47a1 100%);
        color: white;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    .page-header h1 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
    }

    .about-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }

    .about-card {
        background: white;
        border-radius: 15px;
        padding: 30px 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .about-card h2 {
        color: #1976D2;
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 1.3rem;
    }

    .about-card p {
        color: #666;
        margin: 0;
    }

    .logo-section {
        text-align: center;
        padding: 20px;
    }

    .app-logo {
        font-size: 2.5rem;
        color: #1976D2;
        margin-bottom: 10px;
    }

    .app-name {
        font-size: 1.3rem;
        font-weight: bold;
        color: #2c3e50;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-info-circle me-2"></i>Acerca de Conecta Beca</h1>
</div>

<div class="about-container">
    <div class="logo-section">
        <div class="app-logo">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="app-name">Conecta Beca</div>
    </div>

    <div class="about-card">
        <h2>¿Quiénes Somos?</h2>
        <p>
            Conecta Beca es una plataforma innovadora diseñada para conectar estudiantes con oportunidades educativas. 
            Nos comprometemos a democratizar el acceso a la educación superior facilitando la búsqueda y gestión de becas.
        </p>
    </div>

    <div class="about-card">
        <h2>Misión</h2>
        <p>
            Facilitar el acceso a oportunidades de becas y financiamiento educativo, empoderando a estudiantes 
            para alcanzar sus metas académicas y profesionales.
        </p>
    </div>

    <div class="about-card">
        <h2>Visión</h2>
        <p>
            Ser la plataforma líder en latinoamérica para la conexión entre estudiantes y oportunidades de becas,
            transformando vidas a través de la educación.
        </p>
    </div>

    <div class="about-card">
        <h2>Contacto</h2>
        <p>
            <a href="mailto:info@conectabeca.com">info@conectabeca.com</a><br>
            <a href="tel:+1234567890">+1 (234) 567-890</a>
        </p>
    </div>
</div>

@endsection

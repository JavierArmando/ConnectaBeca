@extends('layouts.master')

@section("content")

<style>
    /* =========================================
       1. ESTILOS DEL CARRUSEL DE ARRIBA (HERO)
       ========================================= */
    .hero-carousel-container {
        width: 100%;
        height: 350px;
        padding: 0;
        margin: 0;
        border-bottom-left-radius: 20px; 
        border-bottom-right-radius: 20px;
        overflow: hidden; 
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        position: relative;
    }

    .hero-carousel-container .carousel-inner {
        height: 100%;
    }

    .hero-carousel-container .carousel-item {
        height: 100%;
    }

    .hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    /* =========================================
       2. ESTILOS DE LAS IMÁGENES DE BECAS (CARDS)
       ========================================= */
    
    .card-beca-img {
        width: 100%;
        height: 150px;
        
        object-fit: contain !important; 
        background-color: #ffffff; 
        padding: 12px;
        
        border-top-left-radius: 15px; 
        border-top-right-radius: 15px;
        border-bottom: 1px solid rgba(0,0,0,0.04); 
    }

    /* =========================================
       3. ESTILOS GENERALES (SCROLL, CARDS, ETC)
       ========================================= */
    .cards-scroller-wrapper {
        display: flex;
        overflow-x: auto;
        padding: 15px 5px 25px 5px;
        gap: 1rem;
        scrollbar-width: none; 
        -ms-overflow-style: none;
        scroll-behavior: smooth;
    }
    .cards-scroller-wrapper::-webkit-scrollbar { display: none; }

    .card-slide-item {
        min-width: 280px; 
        max-width: 280px;
        flex: 0 0 auto;
        height: auto;
    }

    .cards-scroller-wrapper .card {
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .cards-scroller-wrapper .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
    }

    .card-clickable {
        text-decoration: none !important; 
        color: inherit !important; 
        display: flex !important;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .section-title {
        font-weight: 800;
        color: #2c3e50;
        border-left: 5px solid #0166FC;
        padding-left: 15px;
        font-size: 1.2rem;
        margin-top: 10px; /* Reducido de 15px */
    }

    .cards-scroller-wrapper .card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .badge-new {
        z-index: 10;
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 0.65rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="hero-carousel-container mb-3">
    <img src="{{ asset('images/banner-becas.jpg') }}" class="hero-img" alt="Banner de Becas">
</div>

<div class="container pb-5">

    @php
        // Definir el orden deseado de categorías académicas
        $ordenCategories = ['Primaria', 'Secundaria', 'Media Superior', 'Universidad', 'Posgrado', 'Académica', 'Económica', 'Investigación', 'Intercambio', 'Deporte'];
        
        // Agrupar becas por categoría/tipo
        $becasPorCategoria = $becas->groupBy('tipo');
        
        // Ordenar las categorías según el orden definido
        $becasPorCategoria = $becasPorCategoria->sort(function($a, $b) use ($ordenCategories) {
            $tipoA = $a->first()->tipo ?? '';
            $tipoB = $b->first()->tipo ?? '';
            
            $posA = array_search($tipoA, $ordenCategories);
            $posB = array_search($tipoB, $ordenCategories);
            
            // Si no está en el array, lo pone al final
            $posA = $posA === false ? 999 : $posA;
            $posB = $posB === false ? 999 : $posB;
            
            return $posA <=> $posB;
        });
    @endphp

    @forelse($becasPorCategoria as $categoria => $becasEnCategoria)
        <div class="row mt-4 mb-2">
            <div class="col-12">
                <h5 class="section-title text-start">{{ $categoria }}</h5>
            </div>
        </div>
        
        <div class="cards-scroller-wrapper">
            @foreach($becasEnCategoria as $beca)
            <div class="card-slide-item">
                <a href="{{ route('detalle-beca', ['id' => $beca->id]) }}" class="card card-clickable">
                    @if($beca->imagen)
                        <img src="{{ asset($beca->imagen) }}" class="card-beca-img" alt="{{ $beca->nombre }}">
                    @else
                        <div class="card-beca-img d-flex align-items-center justify-content-center text-secondary fw-bold fs-6" style="background-color: #f8f9fa;">
                            {{ substr($beca->nombre, 0, 1) }}
                        </div>
                    @endif
                    <div class="card-body p-3 text-center">
                        <h6 class="card-title fw-bold mb-2" style="font-size: 0.9rem;">{{ $beca->nombre }}</h6>
                        <p class="text-muted mb-0 small" style="font-size: 0.75rem;">{{ $beca->tipo }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @empty
        <div class="alert alert-info" role="alert">
            <i class="fa-solid fa-info-circle me-2"></i>
            No hay becas disponibles en este momento. Por favor, vuelve más tarde.
        </div>
    @endforelse

</div>

@endsection
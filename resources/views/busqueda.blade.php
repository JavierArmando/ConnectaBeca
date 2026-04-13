@extends('layouts.master')

@section('content')

<style>
    body {
        background: #f5f5f5;
        padding-top: 76px;
        padding-bottom: 80px;
    }

    .search-hero {
        background: linear-gradient(135deg, #1976D2 0%, #0d47a1 100%);
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);
        margin-bottom: 30px;
    }

    .search-container {
        max-width: 500px;
        margin: 0 auto;
    }

    .search-input-wrapper {
        position: relative;
        margin-bottom: 20px;
    }

    .search-input {
        width: 100%;
        padding: 15px 45px 15px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 25px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .search-input:focus {
        outline: none;
        border-color: #1976D2;
        box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
    }

    .search-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: #1976D2;
        color: white;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .search-btn:hover {
        background: #0d47a1;
        transform: translateY(-50%) scale(1.1);
    }

    .results-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .search-results-title {
        color: #2c3e50;
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 30px;
        padding-left: 20px;
        border-left: 5px solid #1976D2;
    }

    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .beca-card {
        background: white;
        border-radius: 15px;
        padding: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        text-align: center;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }

    .beca-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        text-decoration: none;
        color: inherit;
    }

    .beca-card-img {
        width: 100%;
        height: 100px;
        object-fit: contain;
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 8px;
        margin-bottom: 10px;
    }

    .beca-card-title {
        font-weight: 700;
        color: #2c3e50;
        font-size: 0.85rem;
        margin-bottom: 5px;
        line-height: 1.3;
    }

    .beca-card-type {
        font-size: 0.75rem;
        color: #999;
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .no-results-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #ddd;
    }

    .no-results-text {
        font-size: 1.1rem;
        color: #666;
    }

    .no-results-subtext {
        font-size: 0.9rem;
        color: #999;
        margin-top: 10px;
    }

    .search-filters {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-tag {
        background: #e8f4f8;
        color: #1976D2;
        border: 1px solid #1976D2;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .filter-tag:hover {
        background: #1976D2;
        color: white;
    }

    .filter-tag.active {
        background: #1976D2;
        color: white;
    }

    /* Ocultar search-hero en desktop, solo mostrar en móvil */
    @media (min-width: 769px) {
        .search-hero {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .search-hero {
            padding: 20px 15px;
            margin-bottom: 20px;
        }

        .results-grid {
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 15px;
        }

        .beca-card-img {
            height: 80px;
        }

        .beca-card-title {
            font-size: 0.75rem;
        }

        .results-container {
            padding: 15px;
        }

        .search-results-title {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
    }
</style>

<div class="search-hero">
    <div class="search-container">
        <form method="GET" action="{{ route('busqueda') }}" class="search-input-wrapper">
            <input 
                type="text" 
                name="q" 
                class="search-input" 
                placeholder="Buscar becas..." 
                value="{{ request('q', '') }}"
                autofocus
            />
            <button type="submit" class="search-btn" title="Buscar">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<div class="results-container">
    @if(request('q'))
        <div class="search-results-title">
            Resultados para: <strong>"{{ request('q') }}"</strong>
        </div>

        @if($becas && $becas->count() > 0)
            <div class="results-grid">
                @foreach($becas as $beca)
                    <a href="{{ route('detalle-beca', ['id' => $beca->id]) }}" class="beca-card">
                        @if($beca->imagen)
                            <img src="{{ asset($beca->imagen) }}" class="beca-card-img" alt="{{ $beca->nombre }}">
                        @else
                            <div class="beca-card-img d-flex align-items-center justify-content-center text-secondary fw-bold">
                                {{ substr($beca->nombre, 0, 1) }}
                            </div>
                        @endif
                        <div class="beca-card-title">{{ $beca->nombre }}</div>
                        <div class="beca-card-type">{{ $beca->tipo ?? 'Beca' }}</div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="no-results-text">No se encontraron resultados</div>
                <div class="no-results-subtext">Intenta con otras palabras clave</div>
            </div>
        @endif
    @else
        <div class="no-results">
            <div class="no-results-icon">
                <i class="fas fa-search"></i>
            </div>
            <div class="no-results-text">¿Qué beca estás buscando?</div>
            <div class="no-results-subtext">Usa la barra de búsqueda para encontrar becas</div>
        </div>
    @endif
</div>

@endsection

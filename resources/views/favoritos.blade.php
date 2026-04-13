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

    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        padding: 20px;
    }

    .beca-favorite-card {
        background: white;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .beca-favorite-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .beca-favorite-img {
        width: 100%;
        height: 100px;
        object-fit: contain;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 8px;
        margin-bottom: 10px;
    }

    .beca-favorite-title {
        font-weight: 700;
        color: #2c3e50;
        font-size: 0.85rem;
        margin-bottom: 3px;
        line-height: 1.2;
    }

    .beca-favorite-type {
        font-size: 0.7rem;
        color: #999;
        margin-bottom: 3px;
    }

    .beca-favorite-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        color: #e74c3c;
        font-size: 1rem;
        cursor: pointer;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        z-index: 10;
    }

    .beca-favorite-btn:hover {
        transform: scale(1.1);
        background: #e74c3c;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #ddd;
    }

    .empty-state-text {
        font-size: 1.1rem;
        color: #666;
    }

    .empty-state-subtext {
        font-size: 0.9rem;
        color: #999;
        margin-top: 10px;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-heart me-2"></i>Mis Favoritos</h1>
</div>

@if($becas && $becas->count() > 0)
    <div class="favorites-grid">
        @foreach($becas as $beca)
            <div class="beca-favorite-card">
                <button class="beca-favorite-btn remove-favorite" data-beca-id="{{ $beca->id }}" title="Eliminar de favoritos">
                    <i class="fas fa-times"></i>
                </button>
                
                <a href="{{ route('detalle-beca', ['id' => $beca->id]) }}" style="text-decoration: none; color: inherit; flex-grow: 1;">
                    @if($beca->imagen)
                        <img src="{{ asset($beca->imagen) }}" class="beca-favorite-img" alt="{{ $beca->nombre }}">
                    @else
                        <div class="beca-favorite-img d-flex align-items-center justify-content-center text-secondary fw-bold">
                            {{ substr($beca->nombre, 0, 1) }}
                        </div>
                    @endif
                    
                    <h6 class="beca-favorite-title">{{ $beca->nombre }}</h6>
                    <p class="beca-favorite-type">{{ $beca->tipo }}</p>
                </a>
            </div>
        @endforeach
    </div>
@else
    <div class="container" style="max-width: 600px;">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="empty-state-text">No tienes favoritos aún</div>
            <div class="empty-state-subtext">Marca becas como favoritas para verlas aquí</div>
        </div>
    </div>
@endif

<script>
    document.querySelectorAll('.remove-favorite').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const becaId = this.dataset.becaId;
            const card = this.closest('.beca-favorite-card');

            fetch(`/dev2/public/favoritos/toggle/${becaId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => location.reload(), 300);
                }
            });
        });
    });
</script>

@endsection

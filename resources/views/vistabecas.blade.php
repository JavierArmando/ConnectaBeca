@extends('layouts.master')

@section('content')

<div class="container py-4">
	<div class="row">
		<div class="col-12">
			@if(!empty($imagen))
				<img src="{{ asset($imagen) }}" alt="Beca" class="img-fluid rounded mb-3" style="max-height:520px; width:100%; object-fit: cover;">
			@else
				<div class="bg-light rounded mb-3 d-flex align-items-center justify-content-center" style="height:320px;">
					<span class="text-muted">Imagen no disponible</span>
				</div>
			@endif

			@if($beca)
				{{-- DATOS REALES DE LA BECA --}}
				<div class="d-flex align-items-center gap-2 mb-3">
					<h2 class="fw-bold mb-0">{{ $beca->nombre }}</h2>
					<button class="btn btn-outline-danger add-favorite-btn" data-beca-id="{{ $beca->id }}" title="Agregar a favoritos">
						<i class="fas fa-heart"></i>
					</button>
				</div>
				
				<div class="row mt-4 mb-4">
					<div class="col-md-6">
						<p><strong>Categoría:</strong> <span class="badge bg-light text-dark border">{{ $beca->tipo }}</span></p>
						<p><strong>Monto:</strong> <span class="text-success fw-bold">${{ number_format($beca->monto, 2) }}</span></p>
					</div>
					<div class="col-md-6">
						<p><strong>Creada:</strong> {{ $beca->created_at->format('d/m/Y H:i') }}</p>
						<p><strong>Última actualización:</strong> {{ $beca->updated_at->format('d/m/Y H:i') }}</p>
					</div>
				</div>

				<h4 class="mt-4">Descripción</h4>
				<p>{{ $beca->descripcion ?? 'Sin descripción disponible.' }}</p>

				<div class="mt-5">
					<a href="{{ route('index') }}" class="btn btn-secondary">
						<i class="fa-solid fa-arrow-left me-2"></i> Volver al inicio
					</a>
				</div>
			@else
				{{-- VISTA ANTIGUA (Si viene por parámetro img) --}}
				<h2 class="fw-bold">Detalle de la beca</h2>
				<p class="text-muted">.</p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non 
proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt 
mollit anim id est laborum.
			@endif
		</div>
	</div>
</div>

<script>
	// Agregar/quitar de favoritos
	const addFavoriteBtn = document.querySelector('.add-favorite-btn');
	if (addFavoriteBtn) {
		addFavoriteBtn.addEventListener('click', function(e) {
			e.preventDefault();
			const becaId = this.dataset.becaId;
			const btn = this;

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
					if (data.is_favorite) {
						btn.classList.add('btn-danger');
						btn.classList.remove('btn-outline-danger');
						Toastr(data.message, 'success');
					} else {
						btn.classList.remove('btn-danger');
						btn.classList.add('btn-outline-danger');
						Toastr(data.message, 'info');
					}
				}
			})
			.catch(error => console.error('Error:', error));
		});
	}

	// Función para mostrar notificación simple
	function Toastr(message, type = 'info') {
		const alertClass = type === 'success' ? 'alert-success' : (type === 'danger' ? 'alert-danger' : 'alert-info');
		const alertDiv = document.createElement('div');
		alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
		alertDiv.role = 'alert';
		alertDiv.innerHTML = `
			${message}
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		`;
		
		document.body.insertBefore(alertDiv, document.body.firstChild);
		setTimeout(() => alertDiv.remove(), 3000);
	}
</script>

@endsection

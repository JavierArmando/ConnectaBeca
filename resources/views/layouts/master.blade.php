<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Plataforma Web</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #ffffff !important;
      padding-top: {{ isset($hideNavbar) && $hideNavbar ? '0' : '76px' }}; 
    }

    /* --- NAVBAR --- */
    nav.custom-navbar {
      background-color:  #1976D2;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      min-height: 60px;
      padding-top: 10px; 
      padding-bottom: 10px;
    }

    /* En mobile: navbar blanco/gris claro para contrastar */
    @media (max-width: 991px) {
      nav.custom-navbar {
        background-color: #f5f5f5;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
    }

    .custom-navbar .navbar-brand {
      color: white !important; font-weight: bold;
    }
    .custom-navbar .nav-link {
      color: rgb(240, 240, 240) !important;
    }
    
    /* En mobile, ajustar colores del navbar */
    @media (max-width: 991px) {
      nav.custom-navbar .navbar-brand {
        color: #333 !important;
      }
    }
    
    /* Dropdown Generales */
    .dropdown-menu { 
        border: none; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        border-radius: 12px; 
        position: absolute; 
    }

    .offcanvas { background-color: #03d891ff; }
    .offcanvas-header .btn-close { filter: invert(1); }

    /* En mobile: offcanvas azul con transparencia cubriendo pantalla completa - SOLO PARA EL MENÚ DE NAV */
    @media (max-width: 991px) {
      #offcanvasNavbar {
        background-color: rgba(25, 118, 210, 0.75) !important;
        width: 100vw !important;
        height: 100vh !important;
      }
      #offcanvasNavbar .offcanvas-header {
        background-color: rgba(25, 118, 210, 0.75) !important;
        border-bottom: 1px solid rgba(255,255,255,0.2);
      }
      #offcanvasNavbar .offcanvas-header .btn-close {
        color: white;
      }
      #offcanvasNavbar .offcanvas-title {
        color: white !important;
      }
      #offcanvasNavbar .offcanvas-body {
        background-color: rgba(25, 118, 210, 0.75) !important;
      }
    }

    /* En desktop (lg+), el offcanvas del navbar se comporta como un contenedor normal */
    @media (min-width: 992px) {
      #offcanvasNavbar { 
        position: static !important;
        border: none !important;
        background-color: transparent !important;
        box-shadow: none !important;
        width: auto !important;
        transform: none !important;
      }
      #offcanvasNavbar .offcanvas-header { 
        display: none !important;
      }
      #offcanvasNavbar .offcanvas-body { 
        padding: 0 !important;
        display: flex !important;
      }
    }

    /* --- BOTONES Y CONTROLES HEADER --- */
    .btn-search { background-color: rgba(186, 231, 224, 1); color: white; border: none; }
    .btn-search:hover { background-color: white; color: #55c8c5; }
    
    /* ESTILOS BOTONES AUTH (Escritorio) */
    .btn-auth-login {
        background-color: white; color: #1976D2; border: none; border-radius: 20px; padding: 6px 15px; 
        font-size: 0.85rem; font-weight: 600; text-decoration: none; white-space: nowrap;
    }
    .btn-auth-login:hover { background-color: #f0f0f0; color: #1976D2; }

    .btn-auth-register {
        background-color: white; color: #1976D2; border: none; border-radius: 20px; padding: 6px 15px; 
        font-size: 0.85rem; font-weight: 800; text-decoration: none; white-space: nowrap;
    }
    .btn-auth-register:hover { background-color: #f0f0f0; }

    /* ESTILOS BOTONES AUTH (Móvil - Más compactos) */
    .mobile-auth-btn {
        padding: 4px 8px; 
        font-size: 0.7rem; 
        border-radius: 15px;
        text-decoration: none;
        white-space: nowrap;
        font-weight: 700;
    }
    .mobile-login { background-color: white; color: #1976D2; margin-left: 0; }
    .mobile-register { background-color: white; color: #1976D2; margin-left: 5px; }

    /* Iconos Móvil */
    .mobile-user-icon { color: #333; font-size: 1.5rem; padding: 5px; border: none; background: none; }

    /* Evitar mostrar dos navs/headers superpuestos si hay inclusión duplicada */
    /* Ocultar TODOS los navs por defecto, mostrar sólo el principal */
    nav { display: none !important; }
    nav.custom-navbar { display: block !important; }
    nav.mobile-bottom-nav { display: none !important; }
    header { display: none !important; }
    
    /* Mostrar barra móvil solo en mobile */
    @media (max-width: 768px) {
      nav.mobile-bottom-nav { display: flex !important; }
    }
    
    /* Estilos Base (Cards/Carrusel) */
    .card { border: none; border-radius: 15px; background-color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.2s; }
    .card:hover { transform: translateY(-5px); }
    .main-carousel { border-radius: 20px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.15); margin-top: 10px; }
    .main-carousel img, .carousel-inner img, .carousel-item img { width: 100%; height: 320px; object-fit: cover; }
    
    /* CAMBIO 4: Aumentamos el logo un 30% aprox (de 40px a 52px) para proporción */
    .logo-img { height: 52px; width: auto; border-radius: 8px; }

    /* ===== BARRA DE NAVEGACIÓN MÓVIL INFERIOR ===== */
    .mobile-bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: white;
      border-top: 1px solid #e0e0e0;
      display: none;
      height: 70px;
      z-index: 1000;
    }

    @media (max-width: 768px) {
      .mobile-bottom-nav {
        display: flex;
      }

      body {
        padding-bottom: 80px !important;
      }
    }

    .mobile-bottom-nav {
      display: flex;
      justify-content: space-around;
      align-items: center;
      gap: 0;
    }

    .nav-item-mobile {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      text-decoration: none;
      color: #666;
      font-size: 0.75rem;
      transition: all 0.3s;
      gap: 3px;
    }

    .nav-item-mobile:hover,
    .nav-item-mobile.active {
      color: #1976D2;
    }

    .nav-item-mobile i {
      font-size: 1.5rem;
    }

    .nav-item-mobile span {
      font-size: 0.65rem;
      font-weight: 500;
    }

    /* ===== BARRA LATERAL MENUS ===== */
    .sidebar-menu {
      background-color: rgba(25, 118, 210, 0.95);
      color: white;
      padding: 30px 20px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .sidebar-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 15px;
      border-radius: 8px;
      text-decoration: none;
      color: white;
      transition: all 0.3s;
      cursor: pointer;
    }

    .sidebar-item:hover {
      background-color: rgba(255, 255, 255, 0.1);
      transform: translateX(5px);
    }

    .sidebar-item i {
      font-size: 1.2rem;
    }

    .sidebar-item span {
      font-size: 0.95rem;
      font-weight: 500;
    }

    .sidebar-divider {
      height: 1px;
      background-color: rgba(255, 255, 255, 0.2);
      margin: 10px 0;
    }

    .sidebar-section-title {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.6);
      text-transform: uppercase;
      font-weight: bold;
      padding: 0 15px;
      margin-top: 10px;
    }

    /* Estilos del offcanvas personalizado para sidebar */
    .offcanvas.sidebar-offcanvas {
      width: 80% !important;
      max-width: 300px;
    }

    .offcanvas.sidebar-offcanvas .offcanvas-header {
      background-color: rgba(25, 118, 210, 0.95);
    }

    .offcanvas.sidebar-offcanvas .offcanvas-body {
      background-color: rgba(25, 118, 210, 0.95);
      padding: 0;
    }
  </style>
</head>

<body>

  @unless(isset($hideNavbar) && $hideNavbar)
      
      <nav class="navbar navbar-expand-lg custom-navbar fixed-top">
        <div class="container-fluid">

          <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
          </a>

          <div class="d-flex align-items-center d-lg-none ms-auto">
            
            @if (Route::has('login'))
                @auth
                    <div class="dropdown me-2">
                        <button class="mobile-user-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end mt-2" style="min-width: 200px;">
                            <li><span class="dropdown-item-text small text-muted">Hola, {{ Auth::user()->name }}</span></li>
                            
                            <li><a class="dropdown-item" href="{{ url('/perfil') }}">Perfil</a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                 <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-flex align-items-center me-3">
                        <a href="{{ route('login') }}" class="mobile-auth-btn mobile-login">
                            Ingresar
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="mobile-auth-btn mobile-register">
                                Crear
                            </a>
                        @endif
                    </div>
                @endauth
            @endif

            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
              <span class="navbar-toggler-icon"></span>
            </button>
          </div>

          <div class="offcanvas offcanvas-end custom-navbar" tabindex="-1" id="offcanvasNavbar">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title text-white">Menú</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            
            <div class="offcanvas-body d-lg-flex align-items-center">
              
              <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item d-lg-none">
                    <a class="nav-link" href="{{ url('/contacto') }}"><i class="fa-solid fa-envelope me-2"></i>Contacto</a>
                </li>
                <li class="nav-item dropdown d-none d-lg-block">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Más
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ url('/contacto') }}"><i class="fa-solid fa-envelope me-2"></i>Mandar mensaje o contacto</a></li>
                        <li><a class="dropdown-item" href="{{ url('/favoritos') }}"><i class="fa-solid fa-heart me-2"></i>Becas favoritas</a></li>
                    </ul>
                </li>
              </ul>

              <div class="d-none d-lg-flex align-items-center ms-auto">
                  
                  <form class="d-flex me-3" role="search" method="GET" action="{{ route('busqueda') }}">
                    <input class="form-control me-2" type="search" name="q" placeholder="Buscar..." aria-label="Search">
                    <button class="btn btn-search" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                  </form>

                  @if (Route::has('login'))
                      @auth
                          {{-- Campana de Notificaciones --}}
                          <a href="{{ url('/notificaciones') }}" class="btn btn-link text-white p-0 me-3 position-relative" title="Notificaciones">
                              <i class="fa-solid fa-bell fa-lg"></i>
                              @if(Auth::user()->unreadNotificationsCount() > 0)
                                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                      {{ Auth::user()->unreadNotificationsCount() }}
                                  </span>
                              @endif
                          </a>
                          
                          <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center text-white p-0" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-circle-user fa-2xl"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-3">
                                <li><span class="dropdown-item-text small text-muted">Hola, {{ Auth::user()->name }}</span></li>
                                
                                <li><a class="dropdown-item" href="{{ url('/perfil') }}">Perfil</a></li>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                          </div>
                      @else
                          <div class="d-flex gap-2 align-items-center">
                              <a href="{{ route('login') }}" class="btn-auth-login">
                                  Iniciar Sesión
                              </a>
                              @if (Route::has('register'))
                                  <a href="{{ route('register') }}" class="btn-auth-register">
                                      Crear Cuenta
                                  </a>
                              @endif
                          </div>
                      @endauth
                  @endif

              </div> 
            </div>
          </div>
        </div>
      </nav>

  @endunless

  {{-- ===== BARRA DE NAVEGACIÓN MÓVIL INFERIOR ===== --}}
  @auth
  <nav class="mobile-bottom-nav">
    <a href="{{ url('/') }}" class="nav-item-mobile" title="Inicio">
      <i class="fas fa-home"></i>
      <span>Inicio</span>
    </a>
    <a href="{{ url('/favoritos') }}" class="nav-item-mobile" title="Favoritos">
      <i class="fas fa-heart"></i>
      <span>Favoritos</span>
    </a>
    <a href="{{ route('busqueda') }}" class="nav-item-mobile" title="Buscar">
      <i class="fas fa-search"></i>
      <span>Buscar</span>
    </a>
    <a href="{{ url('/notificaciones') }}" class="nav-item-mobile position-relative" title="Notificaciones">
      <i class="fas fa-bell"></i>
      @if(Auth::user()->unreadNotificationsCount() > 0)
          <span class="position-absolute top-0 start-75 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
              {{ Auth::user()->unreadNotificationsCount() }}
          </span>
      @endif
      <span>Notificaciones</span>
    </a>
    <a href="{{ url('/perfil') }}" class="nav-item-mobile" title="Perfil">
      <i class="fas fa-user"></i>
      <span>Perfil</span>
    </a>
  </nav>
  @endauth

  {{-- ===== SIDEBAR OFFCANVAS ===== --}}
  @auth
  <div class="offcanvas offcanvas-start sidebar-offcanvas" tabindex="-1" id="sidebarOffcanvas">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title text-white">
        <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
      </h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="sidebar-menu">
        <a href="{{ route('contactos') }}" class="sidebar-item">
          <i class="fas fa-envelope"></i>
          <span>Contacto</span>
        </a>
      </div>
    </div>
  </div>
  @endauth

  @yield("content")

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Marcar como activo el nav item actual
    document.addEventListener('DOMContentLoaded', function() {
      const currentPath = window.location.pathname;
      const navItems = document.querySelectorAll('.nav-item-mobile');
      
      navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href)) {
          item.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>
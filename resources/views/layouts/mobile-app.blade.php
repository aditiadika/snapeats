<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Menu App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    @livewireStyles
    <style>
        /* Bottom navigation styling */
        .navbar-nav .nav-link {
            padding: 0.5rem 0;
        }

        .navbar-nav .nav-link.active {
            color: #0d6efd;
            font-weight: 500;
        }

        /* Card styling for menu items */
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        /* Make sure content doesn't hide behind bottom nav */
        body {
            padding-bottom: 56px !important;
        }
    </style>
</head>

<body style="padding-bottom: 56px;"> <!-- Space for bottom nav -->

    <div class="container-fluid p-0">
        @yield('content')
    </div>

    <!-- Bottom Navigation -->
    <nav class="navbar fixed-bottom navbar-expand navbar-light bg-light border-top">
        <div class="container-fluid">
            <ul class="navbar-nav w-100 justify-content-around">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('menu*') ? 'active' : '' }}"
                        href="{{ isset($qr_code) ? route('menu', $qr_code) : route('menu') }}">
                        <div class="text-center">
                            <i class="bi bi-menu-button-wide"></i>
                            <div class="small">Menu</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}"
                        href="{{ isset($qr_code) ? route('order-list', $qr_code) : route('order-list') }}">
                        <div class="text-center">
                            <i class="bi bi-list-check"></i>
                            <div class="small">Orders</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>

</html>

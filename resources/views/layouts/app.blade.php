<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Student Management System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

    <!-- Top Navbar[cite: 1, 3] -->
    <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom sticky-top px-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2"></i>Student Management System
            </a>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <!-- Notification Icon & Badge[cite: 3] -->
                <div class="position-relative">
                    <i class="bi bi-bell fs-5 text-secondary"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                    </span>
                </div>

                <!-- Logged-in User Dropdown[cite: 3, 5] -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0" 
                            type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span>{{ Auth::user()->name }}</span> <!-- Dynamic User Name[cite: 3, 4] -->
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-menu-item dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <!-- Breeze Logout Form[cite: 4] -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="d-flex flex-column flex-md-row">
        <!-- Sidebar Navigation[cite: 1, 3] -->
        <aside class="sidebar py-3">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i class="bi bi-people me-2"></i> Students</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i class="bi bi-journal-bookmark me-2"></i> Courses</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i class="bi bi-cash-stack me-2"></i> Fees</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i class="bi bi-credit-card me-2"></i> Payments</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i class="bi bi-bar-chart-line me-2"></i> Reports</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="bi bi-gear me-2"></i> Profile
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Page Content[cite: 1] -->
        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>

</body>
</html>
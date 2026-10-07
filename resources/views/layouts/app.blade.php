<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Student Management System') }} | @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <nav class="navbar bg-white border-bottom sticky-top px-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2" aria-hidden="true"></i>
                <span class="d-none d-sm-inline">Student Management System</span>
                <span class="d-sm-none">Student Management</span>
            </a>

            <button class="btn btn-outline-secondary d-md-none me-auto"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#dashboardSidebar"
                    aria-controls="dashboardSidebar"
                    aria-expanded="true"
                    aria-label="Toggle sidebar">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <button class="btn btn-light position-relative"
                        type="button"
                        aria-label="Notifications: 3 unread">
                    <i class="bi bi-bell fs-5 text-secondary" aria-hidden="true"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          aria-hidden="true">3</span>
                </button>

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0"
                            type="button"
                            id="userDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="bi bi-person-circle fs-5" aria-hidden="true"></i>
                        <span class="navbar-user-name">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('settings') }}">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
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
        <aside id="dashboardSidebar" class="sidebar collapse show py-3">
            <nav aria-label="Main navigation">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <i class="bi bi-people me-2" aria-hidden="true"></i>Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('courses.index') }}" class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-bookmark me-2" aria-hidden="true"></i>Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('fees.index') }}" class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}">
                            <i class="bi bi-cash-stack me-2" aria-hidden="true"></i>Fees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="bi bi-credit-card me-2" aria-hidden="true"></i>Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line me-2" aria-hidden="true"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                            <i class="bi bi-person-gear me-2" aria-hidden="true"></i>Profile
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main id="main-content" class="flex-grow-1 p-3 p-md-4">
            @yield('content')
        </main>
    </div>

</body>
</html>
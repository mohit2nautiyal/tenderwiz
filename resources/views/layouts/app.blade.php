<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdminHub</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Bootstrap 5.3.0 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"> -->
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 Bootstrap 5 Theme -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap5-theme/1.3.0/select2-bootstrap5-theme.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link rel="stylesheet" href="{{ asset('css/tenders_form.css') }}">
</head>
<body>
    <section id="sidebar">
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <i class='bx bxs-smile'></i>
                <span class="text">AdminHub</span>
            </a>
            <ul class="side-menu top">
                <li>
                    <a href="{{ route('admin.tenders.index') }}">
                        <i class='bx bxs-shopping-bag-alt'></i>
                        <span class="text">Tenders</span>
                    </a>
                </li>

                 <li>
                    <a href="{{ route('admin.users.listing') }}">
                        <i class='bx bxs-user'></i>
                        <span class="text">Users</span>
                    </a>
                </li>
            </ul>
        @else
            <a class="brand">
                <i class='bx bxs-smile'></i>
                <span class="text">TenderWiz</span>
            </a>
            <ul class="side-menu top">
                <li>
                    <a href="{{ route('profile.edit') }}">
                        <i class='bx bxs-cog'></i>
                        <span class="text">Account Settings</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.tenders.index') }}">
                        <i class='bx bxs-shopping-bag-alt'></i>
                        <span class="text">Tenders</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.company.edit') }}">
                        <i class='bx bxs-doughnut-chart'></i>
                        <span class="text">Edit Company</span>
                    </a>
                </li>
            </ul>
        @endif
        <ul class="side-menu">
            <li>
                <a href="#" class="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </section>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <input type="checkbox" id="switch-mode" hidden>
        </nav>

        <main>
            @yield('content')
        </main>
    </section>

    <!-- jQuery 3.6.0 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Bootstrap 5.3.0 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <!-- jQuery Validate 1.19.5 -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js" integrity="sha256-JPVFF+oDUE84hpg2v2Y6lAPVv3LGx6MPJH2CNrW5qOI=" crossorigin="anonymous"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/script.js') }}"></script>
    <!-- Page-specific scripts -->
    @yield('scripts')
</body>
</html>
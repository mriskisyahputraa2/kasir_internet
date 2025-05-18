<style>
    /* Header background and text color */
    #header {
        background-color: #633B48;
        color: #fff;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Logo text */
    #header .logo span {
        color: #fff;
    }

    /* Button di header */
    #header .btn-primary,
    #header .btn-danger {
        border: none;
        background-color: #7c4b5e;
    }

    #header .btn-primary:hover,
    #header .btn-danger:hover {
        background-color: #5a2d3d;
    }

    /* Ikon dan teks */
    #header .bi {
        color: #fff;
    }

    #header .nav-link {
        color: #fff;
    }

    #header .nav-link:hover {
        background-color: #7c4b5e;
        color: #fff;
    }

    /* Gambar profil */
    #header .nav-profile img {
        border-radius: 50%;
        border: 2px solid #fff;
    }

    /* Dropdown menu */
    #header .dropdown-menu {
        background-color: #633B48;
        border: none;
    }

    #header .dropdown-menu .dropdown-item {
        color: #fff;
    }

    #header .dropdown-menu .dropdown-item:hover {
        background-color: #7c4b5e;
        color: #fff;
    }

    /* Divider style */
    #header .dropdown-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.3);
    }
</style>


<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center logo gap-3">
        <a href="/dashboard" class="d-flex align-items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" />
            <span class="d-none d-lg-block">Kasir Internet</span>
        </a>
        <a href="/dashboard">
            <button class="btn btn-primary btn-circle"
                style="
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                    color: white;
                ">
                <i class="bi bi-inboxes-fill text-white"></i>
            </button>
        </a>
    </div>

    <button type="button" class="btn {{ $sesiAktif ? 'btn-danger' : 'btn-primary' }}" data-bs-toggle="modal"
        data-bs-target="#kasirModal">
        @if ($sesiAktif)
            <i class="bi bi-unlock-fill me-1"></i> Tutup Kasir
        @else
            <i class="bi bi-lock-fill me-1"></i> Buka Kasir
        @endif
    </button>

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">

            <li class="nav-item dropdown pe-3">

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" />
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ session('user')['nama'] }}</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6 style="color:#fff">{{ session('user')['nama'] }}</h6>
                        <span style="color: gray">{{ session('role') }}</span>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="/pilih-toko">
                            <i class="bi bi-shop"></i>
                            <span>Pilih Toko</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                            <i class="bi bi-gear"></i>
                            <span>Account Settings</span>
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider" />
                    </li>

                    <li>
                        <!-- <a href="{{ route('logout') }}">Logout</a> -->

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item d-flex align-items-center" type="submit">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
                <!-- End Profile Dropdown Items -->
            </li>
            <!-- End Profile Dropdown Items -->
            </li>
            <!-- End Profile Nav -->
        </ul>
    </nav>
    <!-- End Icons Navigation -->
</header>

<!-- <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

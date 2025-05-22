<style>
    /* Header */
    #header {
        background-color: #633B48;
        /* Mengubah warna latar belakang header */
        color: #fff;
        /* Mengubah warna teks menjadi putih agar kontras */
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        /* Menambahkan bayangan untuk tampilan lebih menarik */
    }

    /* Gambar profil dan nama */
    #header .nav-profile img {
        border-radius: 50%;
        border: 2px solid #fff;
        /* Membuat border putih pada gambar profil */
    }

    /* Dropdown menu */
    #header .dropdown-menu {
        background-color: #633B48;
        /* Menyesuaikan warna latar belakang menu dropdown */
        border: none;
        /* Menghilangkan border */
    }

    #header .dropdown-menu .dropdown-item {
        color: #fff;
        /* Mengubah warna teks item dropdown menjadi putih */
    }

    #header .dropdown-menu .dropdown-item:hover {
        background-color: #7c4b5e;
        /* Menambahkan warna latar belakang saat hover */
        color: #fff;
        /* Menjaga warna teks tetap putih saat hover */
    }

    /* Ikon dan teks pada header */
    #header .nav-link {
        color: #fff;
        /* Mengubah warna teks menu header menjadi putih */
    }

    #header .bi {
        color: #fff;
        /* Mengubah warna ikon menjadi putih */
    }

    /* Hover pada menu header */
    #header .nav-link:hover {
        background-color: #7c4b5e;
        /* Warna latar belakang menu saat hover */
        color: #fff;
        /* Warna teks tetap putih saat hover */
    }

    /* Menyesuaikan dropdown saat hover */
    #header .dropdown-menu-end .dropdown-item:hover {
        background-color: #7c4b5e;
        color: #fff;
    }
</style>


<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center">
        <a href="/dashboard" class="logo d-flex align-items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" />
            <span class="d-none d-lg-block" style="color:#fff">Kasir Internet</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    {{-- Tombol Buka/Tutup Kasir hanya di halaman kasir-transaksi --}}
    @if (Route::currentRouteName() === 'kasir.transaksi' && ($role === 'kasir' || $role === 'admin'))
        <button type="button"
            class="btn ms-3
        @if ($sesiAktif) text-white @else text-dark @endif"
            style="
        @if ($sesiAktif) background-color: #633B48;
        @else
            background-color: rgba(255,255,255,0.7);
            border: 1px solid #633B48; @endif"
            data-bs-toggle="modal" data-bs-target="#kasirModal">
            @if ($sesiAktif)
                <i class="bi bi-unlock-fill me-1"></i> Tutup Kasir
            @else
                <i class="bi bi-lock-fill me-1"></i> Buka Kasir
            @endif
        </button>
    @endif
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
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
            <!-- End Profile Nav -->
        </ul>
    </nav>
    <!-- End Icons Navigation -->
</header>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.toggle-sidebar-btn');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
    });
</script>

<!-- <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

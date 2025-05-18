<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <!-- End Dashboard Nav -->

        <!-- Kasir -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('kasir.transaksi') }}">
                <i class="bi bi-cash-coin"></i>
                <span>Kasir</span>
            </a>
        </li>

        <!-- Kelola Dana -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i>
                <span>Kelola Dana</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('tambah-saldo.index') }}">
                        <i class="bi bi-circle"></i>
                        <span>Saldo Awal</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pindahan-dana.index') }}">
                        <i class="bi bi-circle"></i>
                        <span>Pindahan Dana</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pinjaman-dana.index') }}">
                        <i class="bi bi-circle"></i>
                        <span>Pinjaman</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Kelola Produk -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="ri-shopping-cart-2-line"></i><span>Kelola Produk</span><i
                    class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('kategori.index') }}">
                        <i class="bi bi-circle"></i><span>Kategori</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('produk.index') }}">
                        <i class="bi bi-circle"></i><span>Produk</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kelola-stok.index') }}">
                        <i class="bi bi-circle"></i><span>Kelola Stok</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Absensi -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#absensi-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-calendar-check"></i><span>Absensi</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="absensi-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                @if (auth()->user()->role === 'superadmin')
                    <li>
                        <a href="{{ route('absensi.karyawan') }}">
                            <i class="bi bi-circle"></i><span>Absensi Karyawan</span>
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('absensi.hari-ini') }}">
                            <i class="bi bi-circle"></i><span>Absensi Hari Ini</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('absensi.daftar') }}">
                            <i class="bi bi-circle"></i><span>Daftar Absen</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <!-- Pengaturan -->
        @if (auth()->user()->role === 'superadmin')
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#settings-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-gear"></i><span>Pengaturan</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="settings-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('karyawan.index') }}">
                            <i class="bi bi-circle"></i><span>Karyawan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('toko.index') }}">
                            <i class="bi bi-circle"></i><span>Toko</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shift.index') }}">
                            <i class="bi bi-circle"></i><span>Shift</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
    </ul>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil path URL saat ini
        const currentUrl = window.location.pathname;

        // Fungsi untuk membuka parent menu dari link aktif
        function openParentMenu(activeLink) {
            const parentNavContent = activeLink.closest('.nav-content');
            if (parentNavContent) {
                parentNavContent.classList.add('show');

                const parentNavLink = parentNavContent.previousElementSibling;
                if (parentNavLink) {
                    parentNavLink.classList.add('active');
                    parentNavLink.classList.remove('collapsed');
                }
            }
        }

        // Set active state berdasarkan URL
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            if (link.href && currentUrl.startsWith(new URL(link.href).pathname)) {
                link.classList.add('active');

                // Jika ini submenu, buka parent menunya
                if (!link.hasAttribute('data-bs-toggle')) {
                    openParentMenu(link);
                }
            } else {
                link.classList.remove('active');
                if (link.hasAttribute('data-bs-toggle')) {
                    link.classList.add('collapsed');
                }
            }
        });

        // Handle click untuk menu toggle (menu utama)
        document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]').forEach(link => {
            link.addEventListener('click', function(e) {
                // Jika menu ini sudah aktif, biarkan saja
                if (this.classList.contains('active')) {
                    e.preventDefault();
                    return;
                }

                // Tutup menu utama lainnya
                document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]').forEach(
                    otherLink => {
                        if (otherLink !== this) {
                            otherLink.classList.add('collapsed');
                            otherLink.classList.remove('active');
                            const target = otherLink.getAttribute('data-bs-target');
                            const collapseMenu = document.querySelector(target);
                            if (collapseMenu) {
                                collapseMenu.classList.remove('show');
                            }
                        }
                    });

                // Toggle menu yang diklik
                this.classList.toggle('collapsed');
                this.classList.toggle('active');

                const target = this.getAttribute('data-bs-target');
                const collapseMenu = document.querySelector(target);
                if (collapseMenu) {
                    collapseMenu.classList.toggle('show');
                }
            });
        });

        // Handle click untuk submenu items
        document.querySelectorAll('.nav-content a').forEach(submenuLink => {
            submenuLink.addEventListener('click', function() {
                // Set active state
                document.querySelectorAll('.sidebar-nav a').forEach(link => {
                    link.classList.remove('active');
                });
                this.classList.add('active');

                // Buka parent menu
                openParentMenu(this);
            });
        });

        // Handle click untuk menu biasa (non-toggle)
        document.querySelectorAll('.nav-link:not([data-bs-toggle])').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-nav a').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    });
</script>

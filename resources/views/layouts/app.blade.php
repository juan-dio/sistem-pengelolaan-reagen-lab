<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Pengelolaan Reagen</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

    <!-- CSS Libraries -->

    <!-- Template CSS -->

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">

    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script> --}}
    <script src="assets/modules/jquery-3.7.0.min.js"></script>


    <!-- Select2 -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <link href="assets/modules/select2/dist/css/select2.min.css" rel="stylesheet" />

    {{-- <!-- Javascript -->
    @vite('resources/js/app.js') --}}


    <!-- Datatable Jquery -->
    {{-- <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"> --}}
    <link rel="stylesheet" href="assets/modules/datatables/jquery.dataTables.min.css">

    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.4.1/css/dataTables.dateTime.min.css"> --}}
    <link rel="stylesheet" href="assets/modules/datatables/dataTables.dateTime.min.css">

    <!-- Start GA -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>


    <!-- /END GA -->
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <div class="mr-auto d-flex align-items-center">
                    <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a>
                </div>
                <ul class="navbar-nav navbar-right">


                    <li class="sidebar-item dropdown"><a href="#" data-toggle="dropdown"
                            class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="image" src="assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
                            <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()->name }}</div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="/ubah-password" class="dropdown-item has-icon">
                                <i class="fa fa-sharp fa-solid fa-lock"></i> Ubah Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                Swal.fire({
                                    title: 'Konfirmasi Keluar',
                                    text: 'Apakah Anda yakin ingin keluar?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ya, Keluar!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        document.getElementById('logout-form').submit();
                                    }
                                });">
                                <i class="fas fa-sign-out-alt"></i> {{ __('Keluar') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">

                    <div class="sidebar-brand text-left px-2 py-4" style="height: auto; line-height: 24px">
                        <a href="/" style="font-size: 12px;">SISTEM PENGELOLAAN REAGEN LABORATORIUM KLINIK</a>
                    </div>

                    <ul class="sidebar-menu" id="accordionSidebar">
                        @if (auth()->user()->role->role === 'superadmin')
                            <li class="sidebar-item">
                                <a class="nav-link {{ Request::is('/') || Request::is('dashboard') ? 'active' : '' }}"
                                    href="/">
                                    <i class="fas fa-fire"></i> <span class="align-middle">Dashboard</span>
                                </a>
                            </li>

                            {{-- <li class="menu-header">DATA MASTER</li> --}}
                            <li class="sidebar-item dropdown">
                                <a href="#"
                                    class="nav-link has-dropdown {{ Request::is('barang') || Request::is('jenis-barang') || Request::is('satuan-barang') || Request::is('supplier') || Request::is('alat') ? 'active' : '' }}"
                                    data-toggle="dropdown" data-bs-parent="#accordionSidebar"><i class="fas fa-solid fa-table"></i><span class="align-middle">Data Master</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="nav-link {{ Request::is('barang') ? 'active' : '' }}" href="/barang"><i class="fa-solid fa-prescription-bottle"></i>Data Reagen</a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('jenis-barang') ? 'active' : '' }}" href="/jenis-barang"><i class="fa-solid fa-list-ul"></i> Jenis</a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('satuan-barang') ? 'active' : '' }}" href="/satuan-barang">
                                            <i class="fa-solid fa-cube"></i> Satuan</a></li>
                                    <li>
                                        <a class="nav-link {{ Request::is('supplier') ? 'active' : '' }}" href="/supplier"><i class="fa-solid fa-truck"></i> Supplier</a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('alat') ? 'active' : '' }}" href="/alat"><i class="fa-solid fa-microscope"></i>Alat</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="sidebar-item dropdown">
                                <a href="#"
                                    class="nav-link has-dropdown {{ Request::is('barang-masuk') || Request::is('barang-keluar') || Request::is('stok-opname') || Request::is('stok-adjustment') || Request::is('transfer') || Request::is('verifikasi') ? 'active' : '' }}"
                                    data-toggle="dropdown" data-bs-parent="#accordionSidebar"><i class="fas fa-solid fa-right-left"></i><span class="align-middle">Transaksi</span></a>
                                <ul class="dropdown-menu">
                                    {{-- <li>
                                        <a class="nav-link {{ Request::is('order') ? 'active' : '' }}" href="/order"><i class="fa-solid fa-truck-fast"></i><span>Order</span></a>
                                    </li> --}}
                                    <li>
                                        <a class="nav-link {{ Request::is('barang-masuk') ? 'active' : '' }}" href="/barang-masuk"><i class="fa fa-solid fa-arrow-right"></i><span>Barang Masuk</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('barang-keluar') ? 'active' : '' }}" href="/barang-keluar"><i class="fa fa-sharp fa-solid fa-arrow-left"></i><span>Barang Keluar</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('stok-opname') ? 'active' : '' }}" href="/stok-opname"><i class="fa-solid fa-file-pen"></i><span>Stok Opname</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('stok-adjustment') ? 'active' : '' }}" href="/stok-adjustment"><i class="fa-regular fa-pen-to-square"></i><span>Stok Adjustment</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('transfer-item') ? 'active' : '' }}" href="/transfer-item"><i class="fa fa-solid fa-arrows-rotate"></i><span>Transfer Item</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('verifikasi') ? 'active' : '' }}" href="/verifikasi"><i class="fa-solid fa-clipboard-check"></i><span>Verifikasi</span></a>
                                    </li>
                                </ul>
                            </li>

                            <li class="sidebar-item dropdown">
                                <a href="#"
                                    class="nav-link has-dropdown {{ Request::is('laporan-barang-masuk') || Request::is('laporan-barang-keluar') || Request::is('laporan-stok') || Request::is('laporan-stok-opname') || Request::is('laporan-pemakaian') || Request::is('laporan-forecast') || Request::is('laporan-rekapitulasi') || Request::is('laporan-kategori') ? 'active' : '' }}"
                                    data-toggle="dropdown" data-bs-parent="#accordionSidebar"><i class="fas fa-sharp fa-reguler fa-file"></i><span class="align-middle">Laporan</span></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="nav-link {{ Request::is('laporan-stok') ? 'active' : '' }}" href="/laporan-stok"><i class="fa fa-sharp fa-reguler fa-file"></i><span> Stok</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('laporan-barang-masuk') ? 'active' : '' }}" href="/laporan-barang-masuk"><i class="fa-solid fa-file-import"></i><span>Barang Masuk</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('laporan-barang-keluar') ? 'active' : '' }}" href="/laporan-barang-keluar"><i class="fa-solid fa-file-export"></i><span>Barang Keluar</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('laporan-stok-opname') ? 'active' : '' }}" href="/laporan-stok-opname"><i class="fa-solid fa-file-pen"></i><span>Stok Opname</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('laporan-forecast') ? 'active' : '' }}" href="/laporan-forecast"><i class="fa-solid fa-calculator"></i><span>Forecast</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('laporan-rekapitulasi') ? 'active' : '' }}" href="/laporan-rekapitulasi"><i class="fa-solid fa-clipboard-list"></i><span>Rekapitulasi</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('laporan-kategori') ? 'active' : '' }}" href="/laporan-kategori"><i class="fa-solid fa-file-lines"></i><span>Kategori</span></a>
                                    </li>
                                </ul>
                            </li>

                            <li class="sidebar-item dropdown">
                                <a href="#"
                                    class="nav-link has-dropdown {{ Request::is('data-pengguna') || Request::is('hak-akses') || Request::is('aktivitas-user') || Request::is('database') ? 'active' : '' }}"
                                    data-toggle="dropdown"><i class="fas fa-solid fa-sliders"></i><span class="align-middle">Pengaturan</span></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="nav-link {{ Request::is('data-pengguna') ? 'active' : '' }}" href="/data-pengguna"><i class="fa fa-solid fa-users"></i><span>Data Pengguna</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('hak-akses') ? 'active' : '' }}" href="/hak-akses"><i class="fa fa-solid fa-user-lock"></i><span>Hak Akses/Role</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('aktivitas-user') ? 'active' : '' }}" href="/aktivitas-user"><i class="fa fa-solid fa-user-pen"></i><span>Aktivitas User</span></a>
                                    </li>
                                    <li>
                                        <a class="nav-link {{ Request::is('database') ? 'active' : '' }}" href="/database"><i class="fa fa-solid fa-database"></i><span>Database</span></a>
                                    </li>
                                </ul>
                            </li>
                            
                        @endif

                        @if (auth()->user()->role->role === 'kepala gudang')
                            <li class="sidebar-item">
                                <a class="nav-link {{ Request::is('/') || Request::is('dashboard') ? 'active' : '' }}"
                                    href="/">
                                    <i class="fas fa-fire"></i> <span class="align-middle">Dashboard</span>
                                </a>
                            </li>

                            <li class="menu-header">LAPORAN</li>
                            <li><a class="nav-link {{ Request::is('laporan-stok') ? 'active' : '' }}"
                                    href="laporan-stok"><i
                                        class="fa fa-sharp fa-reguler fa-file"></i><span>Stok</span></a></li>
                            <li><a class="nav-link {{ Request::is('laporan-barang-masuk') ? 'active' : '' }}"
                                    href="laporan-barang-masuk"><i class="fa fa-regular fa-file-import"></i><span>Barang
                                        Masuk</span></a></li>
                            <li><a class="nav-link {{ Request::is('laporan-barang-keluar') ? 'active' : '' }}"
                                    href="laporan-barang-keluar"><i
                                        class="fa fa-sharp fa-regular fa-file-export"></i><span>Barang Keluar</span></a>
                            </li>

                            <li class="menu-header">MANAJEMEN USER</li>
                            <li><a class="nav-link {{ Request::is('aktivitas-user') ? 'active' : '' }}"
                                    href="aktivitas-user"><i class="fa fa-solid fa-list"></i><span>Aktivitas
                                        User</span></a></li>
                        @endif

                        @if (auth()->user()->role->role === 'admin gudang')
                            <li class="sidebar-item">
                                <a class="sidebar-link nav-link {{ Request::is('/') || Request::is('dashboard') ? 'active' : '' }}"
                                    href="/">
                                    <i class="fas fa-fire"></i> <span class="align-middle">Dashboard</span>
                                </a>
                            </li>

                            <li class="menu-header">DATA MASTER</li>
                            <li class="sidebar-item dropdown">
                                <a href="#"
                                    class="nav-link has-dropdown {{ Request::is('barang') || Request::is('jenis-barang') || Request::is('satuan-barang') ? 'active' : '' }}"
                                    data-toggle="dropdown"><i class="fas fa-thin fa-cubes"></i><span>Data
                                        Barang</span></a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link {{ Request::is('barang') ? 'active' : '' }}"
                                            href="/barang"><i class="fa fa-solid fa-circle fa-xs"></i> Nama Barang</a>
                                    </li>
                                    <li><a class="nav-link {{ Request::is('jenis-barang') ? 'active' : '' }}"
                                            href="/jenis-barang"><i class="fa fa-solid fa-circle fa-xs"></i> Jenis</a>
                                    </li>
                                    <li><a class="nav-link {{ Request::is('satuan-barang') ? 'active' : '' }}"
                                            href="/satuan-barang"><i class="fa fa-solid fa-circle fa-xs"></i>
                                            Satuan</a></li>
                                </ul>
                            </li>
                            <li class="sidebar-item dropdown">
                                <a href="#"
                                    class="nav-link has-dropdown {{ Request::is('supplier') || Request::is('customer') ? 'active' : '' }}"
                                    data-toggle="dropdown"><i
                                        class="fa fa-sharp fa-solid fa-building"></i><span>Perusahaan</span></a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link {{ Request::is('supplier') ? 'active' : '' }}"
                                            href="/supplier"><i class="fa fa-solid fa-circle fa-xs"></i> Supplier</a>
                                    </li>
                                    <li><a class="nav-link {{ Request::is('customer') ? 'active' : '' }}"
                                            href="/alat"><i class="fa fa-solid fa-circle fa-xs"></i> Customer</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="menu-header">TRANSAKSI</li>
                            <li><a class="nav-link {{ Request::is('barang-masuk') ? 'active' : '' }}"
                                    href="barang-masuk"><i class="fa fa-solid fa-arrow-right"></i><span>Barang
                                        Masuk</span></a></li>
                            <li><a class="nav-link {{ Request::is('barang-keluar') ? 'active' : '' }}"
                                    href="barang-keluar"><i class="fa fa-sharp fa-solid fa-arrow-left"></i>
                                    <span>Barang Keluar</span></a></li>

                            <li class="menu-header">LAPORAN</li>
                            <li><a class="nav-link {{ Request::is('laporan-stok') ? 'active' : '' }}"
                                    href="laporan-stok"><i
                                        class="fa fa-sharp fa-reguler fa-file"></i><span>Stok</span></a></li>
                            <li><a class="nav-link {{ Request::is('laporan-barang-masuk') ? 'active' : '' }}"
                                    href="laporan-barang-masuk"><i
                                        class="fa fa-regular fa-file-import"></i><span>Barang Masuk</span></a></li>
                            <li><a class="nav-link {{ Request::is('laporan-barang-keluar') ? 'active' : '' }}"
                                    href="laporan-barang-keluar"><i
                                        class="fa fa-sharp fa-regular fa-file-export"></i><span>Barang
                                        Keluar</span></a></li>
                        @endif
                    </ul>

                </aside>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">

                    @yield('content')
                    <div class="section-body">
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <div class="footer-left">
                    Copyright &copy; 2023
                </div>
                <div class="footer-right">

                </div>
            </footer>
        </div>
    </div>



    <!-- General JS Scripts -->
    <script src="assets/modules/jquery.min.js"></script>
    <script src="assets/modules/popper.js"></script>
    <script src="assets/modules/tooltip.js"></script>
    <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/modules/moment.min.js"></script>
    <script src="assets/modules/jsbarcode.all.min.js"></script>
    <script src="assets/js/stisla.js"></script>

    <!-- JS Libraies -->

    <!-- Select2 Jquery -->
    {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script type="text/javascript" src="assets/modules/select2/dist/js/select2.min.js"></script>

    {{-- <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script> --}}
    <script src="assets/modules/jquery-ui/jquery-ui.min.js"></script>

    <!-- Page Specific JS File -->

    <!-- Template JS File -->
    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- Datatables Jquery -->
    {{-- <script type="text/javascript" src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> --}}
    <script type="text/javascript" src="assets/modules/datatables/jquery.dataTables.min.js"></script>

    <!-- Sweet Alert -->
    @include('sweetalert::alert')
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> --}}
    <script src="assets/modules/sweetalert/sweetalert-2.10.min.js"></script>

    <!-- Day Js Format -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script> --}}
    <script src="assets/modules/dayjs.min.js"></script>


    @stack('scripts')

    <script>
        $(document).ready(function() {
            var currentPath = window.location.pathname;

            $('.nav-link a[href="' + currentPath + '"]').addClass('active');
        });
    </script>

</body>

</html>

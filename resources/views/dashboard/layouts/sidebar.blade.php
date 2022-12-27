<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        @canany(['administrasi','kasir','admin'])
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard')? 'active' : '' }}" aria-current="page"
                    href="/dashboard">
                    <i class="bi bi-bar-chart me-1 btn-sidebar"></i>
                    Dashboard
                </a>
            </li>
            @canany(['kasir','admin'])
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/setoran-kolektor*')? 'active' : '' }}" aria-current="page"
                    href="/dashboard/setoran-kolektor">
                    <i class="bi bi-file-earmark-check me-1 btn-sidebar"></i>
                    Setoran Kolektor
                </a>
            </li>
            @endcanany
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/data-nasabah*')? 'active' : '' }}" aria-current="page"
                    href="/dashboard/data-nasabah">
                    <i class="bi bi-people me-1 btn-sidebar"></i>
                    Data Nasabah
                </a>
            </li>
            @can('admin')  
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/data-karyawan*')? 'active' : '' }}" aria-current="page"
                href="/dashboard/data-karyawan">
                <i class="bi bi-person me-1 btn-sidebar"></i>
                Data Pengguna
            </a>
            </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/data-tabungan*')? 'active' : '' }}" href="/dashboard/data-tabungan">
                    <i class="bi bi-file-earmark-text me-1 btn-sidebar"></i>
                    Data Tabungan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/data-pinjaman*')? 'active' : '' }}" href="/dashboard/data-pinjaman">
                    <i class="bi bi-file-earmark-text me-1 btn-sidebar"></i>
                    Data Pinjaman
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/riwayat-transaksi*')? 'active' : '' }}" href="/dashboard/riwayat-transaksi">
                    <i class="bi bi-clock-history me-1 btn-sidebar"></i>
                    Riwayat Transaksi
                </a>
            </li>

        </ul>
        @can('administrasi')
            
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Transaksi</span>
        </h6>
        <ul class="nav flex-column mb-2 ml-1">
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle {{ Request::is('dashboard/transaksi-tabungan*')? 'active' : '' }}" type="button" id="transaksi-setoran" data-bs-toggle="dropdown" aria-expanded="false">
                Transaksi Tabungan
                </button>
                <ul class="dropdown-menu" aria-labelledby="transaksi-setoran">
                <li><a class="dropdown-item" href="/dashboard/transaksi-tabungan/reguler">Tabungan Reguler</a></li>
                <li><a class="dropdown-item" href="/dashboard/transaksi-tabungan/program">Tabungan Program</a></li>
                <li><a class="dropdown-item" href="/dashboard/transaksi-tabungan/berjangka">Tabungan Berjangka</a></li>
                </ul>
            </div>
        </ul>
        <ul class="nav flex-column mb-2 ml-1">
            <div class="dropdown">
                <a class="btn btn-sm {{ Request::is('dashboard/transaksi-pinjaman*')? 'active' : '' }}" href="/dashboard/transaksi-pinjaman"> Transaksi Pinjaman</a>
            </div>
        </ul>
        <ul class="nav flex-column mb-2 ml-1">
            <div class="dropdown">
                <a class="btn btn-sm {{ Request::is('dashboard/bagikan-shu*')? 'active' : '' }}" href="/dashboard/bagikan-shu">Bagikan SHU</a>
            </div>
        </ul>
        @endcan
        @endcanany

        @can('kolektor')
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard')? 'active' : '' }}" aria-current="page"
                    href="/dashboard">
                    <i class="bi bi-bar-chart me-1 btn-sidebar"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/data-nasabah*')? 'active' : '' }}" aria-current="page"
                    href="/dashboard/data-nasabah">
                    <i class="bi bi-people me-1 btn-sidebar"></i>
                    Data Nasabah
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/data-tabungan*')? 'active' : '' }}" href="/dashboard/data-tabungan">
                    <i class="bi bi-file-earmark-text me-1 btn-sidebar"></i>
                    Data Tabungan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/riwayat-transaksi*')? 'active' : '' }}" href="/dashboard/riwayat-transaksi">
                    <i class="bi bi-clock-history me-1 btn-sidebar"></i>
                    Riwayat Transaksi
                </a>
            </li>

        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Transaksi Setoran</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/transaksi-tabungan/reguler*')? 'active' : '' }}" href="/dashboard/transaksi-tabungan/reguler">
                    > Tabungan Reguler
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/transaksi-tabungan/program*')? 'active' : '' }}" href="/dashboard/transaksi-tabungan/program">
                    > Tabungan Program
                </a>
            </li>

        </ul>
        @endcan

        @can('nasabah')
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard')? 'active' : '' }}" aria-current="page"
                    href="/dashboard">
                    <i class="bi bi-bar-chart me-1 btn-sidebar"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/data-nasabah*')? 'active' : '' }} {{ Request::is('dashboard/data-pinjaman*')? 'active' : '' }} {{ Request::is('dashboard/data-tabungan*')? 'active' : '' }}" aria-current="page"
                    href="/dashboard/data-nasabah">
                    <i class="bi bi-people me-1 btn-sidebar"></i>
                    Data Saya
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/riwayat-transaksi*')? 'active' : '' }}" href="/dashboard/riwayat-transaksi">
                    <i class="bi bi-clock-history me-1 btn-sidebar"></i>
                    Riwayat Transaksi
                </a>
            </li>

        </ul>
        @endcan


    </div>
        <div class="nav-item text-nowrap bg-white">
            <span class="p-4 user-action" style="font-size : .9rem">
                <div class="btn-user">
                    <form class="d-inline-block" action="/setting" method="GET">
                        @csrf
                        <button class="btn btn-light btn-sm d-inline-block me-1" type="submit"><i class="bi bi-gear"></i></button>
                    </form>
                    <form class="d-inline-block" action="/logout" method="POST">
                    @csrf
                    <button class="btn btn-danger btn-sm d-inline-block" type="submit">Log-Out <i class="bi bi-box-arrow-right"></i></button>
                    </form>
        </div>
        </span>
        </div>
</nav>

@extends('dashboard.layouts.main')

@section('container')

@if (session()->has('success'))
    <div class="alert alert-success mt-2" role="alert">
        {{ session('success') }}
    </div>  
@endif
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h4 class="h4 ds-welcome">Selamat Datang, <span class="text-capitalize">
            @if (auth()->user()->role == 'administrasi' ||auth()->user()->role == 'kolektor' || auth()->user()->role == 'kasir'  )
            Staf 
            @endif{{ auth()->user()->role }}</span></h4>

    <div class="btn-toolbar mb-2 mb-md-0">
        <form action="" method="GET" class="me-2">
        {{ csrf_field() }}
        @csrf
            <select style="    margin-top: 1.813rem;" name="filter" id="filter-dashboard" class="form-select d-inline-block form-select margin-kecil">
                <option value="hari ini"@if ($filter=="hari ini") selected @endif> Hari ini</option>
                <option value="custom"@if ($filter=="custom") selected @endif>Tentukan Jangka Waktu</option>
                <option value="semua"@if ($filter=="semua") selected @endif>Tampilkan Semua</option>
            </select>      
            <div class="custom-filter d-inline-block">
                <label for="filter-dari" class="form-label custom-filter">Mulai Dari</label>
                <input type="date" class="form-control custom-filter margin-kecil" id="filter-dari"
                name="dari" value="{{ $dari }}">
            </div>
            <div class="custom-filter d-inline-block">
                <label for="filter-sampai" class="form-label custom-filter">Sampai</label>
                @php
                    if($sampai != ""){
                        $sampai = date('Y-m-d', strtotime( $sampai . " -1 days"));
                    }
                @endphp
                <input type="date" class="form-control custom-filter margin-kecil" id="filter-sampai"
                name="sampai" value="{{ $sampai }}">
            </div>
            <div class="d-inline-block">
                <button style="margin-top: -0.32rem;" type="submit" class="btn btn-primary ">Tampilkan</button>
            </div>
        </form>
    </div>

</div>

{{-- DASHBOARD_ADMINISTRASI --}}
@canany(['administrasi','kasir','admin'])

<div class="row">
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Tabungan Reguler</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $data['sumReguler'] }}</h5>
                <p class="qty">{{ $data['countReguler'] }} Tabungan</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Tabungan Program</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $data['sumProgram'] }}</h5>
                <p class="qty">{{ $data['countProgram'] }} Tabungan</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Tabungan Berjangka</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $data['sumBerjangka'] }}</h5>
                <p class="qty">{{ $data['countBerjangka'] }} Tabungan</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Sisa Pinjaman</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $data['sumPinjaman'] }}</h5>
                <p class="qty">{{ $data['countPinjaman'] }} Pinjaman</p>
            </div>
        </div>
    </div>
</div>


{{-- DATA --}}
<div class="row">
    <div class="col-lg-5">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Data Nasabah</p>
                </div>
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row">Anggota</th>
                            <td>:</td>
                            <td>{{ $nasabah['anggota'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Anggota Allit</th>
                            <td>:</td>
                            <td>{{ $nasabah['anggota_alit'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Calon Anggota</th>
                            <td>:</td>
                            <td>{{ $nasabah['calon_anggota'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total</th>
                            <td>:</td>
                            <td>{{ $nasabah['calon_anggota'] + $nasabah['anggota'] + $nasabah['anggota_alit'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">SHU Yang Belum Diambil</th>
                            <td>:</td>
                            <td class="rupiah-text">{{ $nasabah['shu'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Transaksi Pengambilan SHU </th>
                            <td>:</td>
                            <td class="rupiah-text">{{ $shu['pengambilan'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Data Transaksi</p>
                </div>
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th scope="row">Total Transaksi Setoran</th>
                            <td>:</td>
                            <td class="rupiah-text">{{ $setoran['reguler'] + $setoran['program'] + $setoran['berjangka'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Transaksi Penarikan</th>
                            <td>:</td>
                            <td class="rupiah-text">{{ $penarikan['reguler'] + $penarikan['program'] + $penarikan['berjangka'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Transaksi Penambahan Bunga</th>
                            <td>:</td>
                            <td class="rupiah-text">{{ $bunga['reguler'] + $bunga['program'] + $bunga['berjangka'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Transaksi Angsuran Pinjaman</th>
                            <td>:</td>
                            <td class="rupiah-text">{{ $pinjaman['angsuran'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Pemberian Pinjaman Baru</th>
                            <td>:</td>
                            <td class="rupiah-text">{{ $pinjaman['baru'] }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Pendapatan Pinjaman</th>
                            <td>:</td>
                            <td class="rupiah-text">{{ $pinjaman['biayaAdmin'] + $bunga['pinjaman'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- DETAIL TRANSAKSI --}}

<div class="row">
    <div class="col-12">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Detail Transaksi</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $setoran['reguler'] }}</span>
                            <span class="count-name">Setoran Tabungan Reguler</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $setoran['program'] }}</span>
                            <span class="count-name">Setoran Tabungan Program</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $setoran['berjangka'] }}</span>
                            <span class="count-name">Setoran Tabungan Berjangka</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $penarikan['reguler'] }}</span>
                            <span class="count-name">Penarikan Tabungan Reguler</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $penarikan['program'] }}</span>
                            <span class="count-name">Penarikan Tabungan Program</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $penarikan['berjangka'] }}</span>
                            <span class="count-name">Penarikan Tabungan Berjangka</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $bunga['reguler'] }}</span>
                            <span class="count-name">Penambahan Bunga Reguler</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $bunga['program'] }}</span>
                            <span class="count-name">Penambahan Bunga Program</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $bunga['berjangka'] }}</span>
                            <span class="count-name">Penambahan Bunga Berjangka</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $pinjaman['baru'] }}</span>
                            <span class="count-name">Pemberian Pinjaman Baru</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $pinjaman['biayaAdmin'] }}</span>
                            <span class="count-name">Pendapatan Biaya Administrasi</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $pinjaman['angsuran'] }}</span>
                            <span class="count-name">Angsuran Pinjaman</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $bunga['pinjaman'] }}</span>
                            <span class="count-name">Pendapatan Bunga Pinjaman</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $shu['penambahan'] }}</span>
                            <span class="count-name">Pembagian Sisa Hasil Usaha</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $shu['pengambilan'] }}</span>
                            <span class="count-name">Pengambilan Sisa Hasil Usaha</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

    
@endcanany



{{-- DASHBOARD_KOLEKTOR --}}
@can('kolektor')

<div class="row">
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Tabungan Reguler</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $totalReguler }}</h5>
                <p class="qty">{{ $tabunganReguler }} Tabungan</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Tabungan Program</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $totalProgram}}</h5>
                <p class="qty">{{ $tabunganProgram }} Tabungan</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Setoran Tabungan Reguler</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $setoranReguler }}</h5>
                {{-- <p class="qty">{{ $data['countBerjangka'] }} Tabungan</p> --}}
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Setoran Tabungan Program</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $setoranProgram }}</h5>
                {{-- <p class="qty">{{ $data['countPinjaman'] }} Pinjaman</p> --}}
            </div>
        </div>
    </div>
</div>

@endcan
{{-- END DASHBOARD_KOLEKTOR --}}


{{-- DASHBOARD_NASABAH --}}
@can('nasabah')

<div class="row">
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Tabungan Reguler</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $data['sumReguler'] }}</h5>
                <p class="qty">{{ $data['countReguler'] }} Tabungan</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Tabungan Program</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $data['sumProgram'] }}</h5>
                <p class="qty">{{ $data['countProgram'] }} Tabungan</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Tabungan Berjangka</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $data['sumBerjangka'] }}</h5>
                <p class="qty">{{ $data['countBerjangka'] }} Tabungan</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Sisa Pinjaman</p>
                </div>
                <h5 class="db-price rupiah-text">{{ $data['sumPinjaman'] }}</h5>
                <p class="qty">{{ $data['countPinjaman'] }} Pinjaman</p>
            </div>
        </div>
    </div>
</div>

{{-- DETAIL TRANSAKSI --}}

<div class="row">
    <div class="col-12">
        <div class="db-card card text-dark bg-light mb-3 ms-1">
            <div class="card-body">
                <div class="db-card-title mb-3">
                    <p class="title">Detail Transaksi</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $setoran['reguler'] }}</span>
                            <span class="count-name">Setoran Tabungan Reguler</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $setoran['program'] }}</span>
                            <span class="count-name">Setoran Tabungan Program</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $setoran['berjangka'] }}</span>
                            <span class="count-name">Setoran Tabungan Berjangka</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $penarikan['reguler'] }}</span>
                            <span class="count-name">Penarikan Tabungan Reguler</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $penarikan['program'] }}</span>
                            <span class="count-name">Penarikan Tabungan Program</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $penarikan['berjangka'] }}</span>
                            <span class="count-name">Penarikan Tabungan Berjangka</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $bunga['reguler'] }}</span>
                            <span class="count-name">Penambahan Bunga Reguler</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $bunga['program'] }}</span>
                            <span class="count-name">Penambahan Bunga Program</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $bunga['berjangka'] }}</span>
                            <span class="count-name">Berjangka Dapat Ditarik</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $pinjaman['baru'] }}</span>
                            <span class="count-name">Pengambilan Pinjaman Baru</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $biayaAdmin }}</span>
                            <span class="count-name">Pembayaran Biaya Administrasi</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-counter success card">
                            <span class="count-numbers rupiah-text">{{ $pinjaman['angsuran'] }}</span>
                            <span class="count-name">Angsuran Pinjaman</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    
@endcan





{{-- <div class="row">
        
            <div class="col-lg-3 col-md-6">
                <div class="card-counter success">
                    <span class="count-numbers rupiah-text">3400000</span>
                    <span class="count-name">Setoran Tabungan Reguler</span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card-counter success">
                    <span class="count-numbers rupiah-text">3400000</span>
                    <span class="count-name">Setoran Tabungan Reguler</span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card-counter success">
                    <span class="count-numbers rupiah-text">3400000</span>
                    <span class="count-name">Setoran Tabungan Reguler</span>
                </div>
            </div>
</div> --}}


    {{-- <div class="col-md-3">
        <div class="card-counter success">
            <i class="fa fa-database"></i>
            <span class="count-numbers">6875</span>
            <span class="count-name">Data</span>
        </div>
    </div> --}}
<script>
    $(document).ready(function(){
        if($("#filter-dashboard").val() != "custom"){
            $(".custom-filter").hide()
        }else{
            $(".custom-filter").show()
        }
    })

    $("#filter-dashboard").change(function(){
        if($("#filter-dashboard").val() == "custom"){
            $(".custom-filter").show();
        }else {
            $(".custom-filter").hide()
        }
    })
</script>
@endsection

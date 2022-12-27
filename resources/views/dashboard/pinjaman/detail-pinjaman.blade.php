@extends('dashboard.layouts.main')

@section('container')
@php
function rupiah($angka){
$angka = ceil($angka);
$format_rupiah = "Rp." . number_format($angka,2,',','.');
return $format_rupiah;
}
$bunga_harian = $pinjaman->bunga_dibayar /30 ;
$tgl = date_create($pinjaman->tgl_angsuran);//8
$now = date('Y-m-d');
$now = date_create($now); // waktu sekarang
$diff = date_diff( $tgl, $now );
$selisih = $diff->days ;
$selisihHari = 0 ;
if($selisih == 0){
$estimasi_pembayaran = "Hari ini";
$selisihHari = 0 ;
}
if ($tgl > $now) {
$estimasi_pembayaran = $selisih . " Hari Lagi";
$selisihHari = $selisihHari - $selisih ;
if($selisihHari < -30 ){
        $selisihHari = -30 ;
}
}
if($tgl < $now){ $estimasi_pembayaran="Terlambat " . $selisih . " Hari" ; $selisihHari=$selisihHari + $selisih ; }
    $bunga_harian=ceil($bunga_harian * $selisihHari) ; @endphp 

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Pinjaman</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        @can('administrasi')
            
        {{-- <a class="btn me-2 btn-warning" href="/dashboard/data-pinjaman/{{ Crypt::encrypt($pinjaman->id) }}/edit">Edit Pinjaman</a> --}}
        @endcan
        {{-- <a class="btn me-2 btn-warning" {{route('registration',['course_id' => Crypt::encrypt('1') ])}}>Edit Pinjaman</a> --}}
        @cannot('nasabah')
        <a class="btn me-2 btn-danger" href="/dashboard/data-pinjaman">Kembali</a>
        @endcannot
        @can('nasabah')
        <a class="btn me-2 btn-danger" href="/dashboard/data-nasabah">Kembali</a>
        @endcan
    </div>


</div>

    <div class="col-lg-8">
        <table class="table">
            <tbody>
                <tr>
                    <th scope="row">No Pinjaman</th>
                    <td>:</td>
                    <td>{{ $pinjaman->id }}</td>
                </tr>
                <tr>
                    <th scope="row">Nama Peminjam</th>
                    <td>:</td>
                    <td>{{ $pinjaman->nasabah->user->nama }}</td>
                </tr>
                <tr>
                    <th scope="row">Nomor KTP</th>
                    <td>:</td>
                    <td>{{ $pinjaman->ktp }}</td>
                </tr>
                <tr>
                    <th scope="row">Nomor KK</th>
                    <td>:</td>
                    <td>{{ $pinjaman->kk }}</td>
                </tr>
                <tr>
                    <th scope="row">Alamat</th>
                    <td>:</td>
                    <td>{{ $pinjaman->nasabah->alamat }}</td>
                </tr>
                <tr>
                    <th scope="row">Nomor Telepon</th>
                    <td>:</td>
                    <td>{{ $pinjaman->nasabah->user->no_telp }}</td>
                </tr>
                <tr>
                    <th scope="row">Jaminan</th>
                    <td>:</td>
                    <td>{{ $pinjaman->jaminan }}</td>
                </tr>
                <tr>
                    <th scope="row">Jumlah Pinjaman</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($pinjaman->pinjaman) }}</td>
                </tr>
                <tr>
                    <th scope="row">Bunga</th>
                    <td>:</td>
                    <td>{{ $pinjaman->bunga * 100 }} %</td>
                </tr>
                <tr>
                    <th scope="row">Lama Angsuran</th>
                    <td>:</td>
                    <td>{{ $pinjaman->lama_angsuran }} Bulan</td>
                </tr>
                <tr>
                    <th scope="row">Sisa Pinjaman</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($pinjaman->sisa_pinjaman) }}</td>
                </tr>
                <tr>
                    <th scope="row">Sisa Angsuran</th>
                    <td>:</td>
                    <td>{{ $pinjaman->lama_angsuran - $pinjaman->sudah_mengangsur }} x</td>
                </tr>
                <tr>
                    <th scope="row">Tgl Angsuran Selanjutnya</th>
                    <td>:</td>
                    @if ($pinjaman->tgl_angsuran != null)
                    <td>{{ date('d-m-Y' , strtotime($pinjaman->tgl_angsuran)) }} ({{ $estimasi_pembayaran }})</td>
                    @else
                    <td></td>     
                    @endif
                </tr>
                <tr>
                    <th scope="row">Angsuran Pokok</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{(ceil($pinjaman->angsuran_pokok)) }}</td>
                </tr>
                <tr>
                    <th scope="row">Bunga Pinjaman</th>
                    <td>:</td>
                    <td> <span class="rupiah-text">{{(ceil($pinjaman->bunga_dibayar)) }}</span>
                        @if ($selisihHari>0)
                        + <span class="rupiah-text"> {{($bunga_harian) }}</span> (Keterlambatan)
                        @endif
                    </td>
                </tr>
                <tr>
                    <th scope="row">Total Pembayaran Angsuran </th>
                    <td>:</td>
                    <td class="rupiah-text">
                        @if ($selisihHari>0)
                        {{($pinjaman->angsuran_pokok + $pinjaman->bunga_dibayar + $bunga_harian) }}
                        @else
                        {{($pinjaman->angsuran_pokok + $pinjaman->bunga_dibayar) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pelunasan Sekaligus</th>
                    <td>:</td>
                    <td class="rupiah-text">{{(ceil($pinjaman->sisa_pinjaman + $pinjaman->bunga_dibayar + $bunga_harian)) }}</td>
                </tr>
                <tr>
                    <th scope="row">Status Pinjaman</th>
                    <td>:</td>
                    <td class="text-capitalize">{{ $pinjaman->status }}</td>
                </tr>




            </tbody>
        </table>
    </div>
    <div>
        <h5>Riwayat Transaksi</h5>
        <div class="table-responsive">
            <table class="table table-info">
                <thead>
                    <tr>
                        <th scope="col-4">Tanggal</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Bunga Pinjaman</th>
                        <th scope="col">Angsuran Pinjaman</th>
                        <th scope="col">Sisa Pinjaman</th>
                        <th scope="col">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi as $t)
                    <tr class="{{ ($t->jenis == 'pemberian pinjaman' || $t->jenis == 'pemindahan' || $t->jenis == 'biaya administrasi'|| $t->jenis == 'penambahan waktu angsuran' ) ? 'fw-bold' : '' }}">
                        <th scope="row">{{ date('d-m-Y h:i:sa',strtotime($t->created_at)) }}</th>
                        <td class="text-capitalize">{{ $t->jenis }}</td>
                        @if ($t->jenis != 'penambahan waktu angsuran')
                        <td class="rupiah-text">{{($t->jumlah) }}</td>
                        @else
                        <td>{{($t->jumlah) }} Bulan</td>
                        @endif
                        <td class="rupiah-text">{{($t->bunga) }}</td>
                        <td class="rupiah-text">{{($t->angsuran) }}</td>
                        <td class="rupiah-text">{{($t->sisa_pinjaman) }}</td>
                        <td>{{ $t->keterangan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function(){
        var elts = document.getElementsByClassName('rupiah-text');
        for (var i = 0; i < elts.length; ++i) {
            let rp =  elts[i].innerHTML ;  
            rp = formatRupiah(rp,'Rp.') ;
            elts[i].innerHTML = rp;
        }
        })
    </script>

    @endsection

@extends('dashboard.layouts.main')

@section('container')

@php
    /* TABUNGAN PROGRAM */
    $tabungan_program = 0 ;
            $bunga =0 ;
            for ($x = 1; $x <= $tabungan->lama_program ; $x++) {
                if($x == 1 ){
                    $bunga = $tabungan->setoran_tetap * $tabungan->bunga ;
                }else {
                    $bunga = $tabungan_program * $tabungan->bunga ;
                }
                $bunga = round($bunga);

                $tabungan_program  = $tabungan_program + $tabungan->setoran_tetap + $bunga ;
                $tabungan_program = round($tabungan_program) ;
            }
            $tgl = date_create($tabungan->tgl_setoran);//8
            $sekarang = date('Y-m-d');
            $sekarang = date_create($sekarang); // waktu sekarang
            $diff = date_diff( $tgl, $sekarang );
            $selisih = $diff->days ;
            $selisihHari = 0 ;
            if($selisih == 0){
            $setoran = "Hari ini";
            $selisihHari = 0 ;
            }
            if ($tgl > $sekarang) {
            $setoran = $selisih . " Hari Lagi";
            $selisihHari = $selisihHari - $selisih ;
            }
            if($tgl < $sekarang){ $setoran="Terlambat " . $selisih . " Hari" ; $selisihHari=$selisihHari + $selisih ; }
            $statusProgram = "";
            if($tabungan->lama_program == $tabungan->sudah_setor){
                $statusProgram = "selesai";
            }


            /* TABUNGAN BERJANGKA */

        $tgl_selesai = date_create($tabungan->tgl_selesai);//8
        $tgl_mulai = date_create($tabungan->tgl_mulai);
        $now = date('Y-m-d');
        $now = date_create($now); // waktu sekarang
        $selesai = $now->diff($tgl_selesai);
        $jarak = $now->diff($tgl_mulai);
        $berjalan = $jarak->y * 12 ;
        $berjalan = $berjalan + $jarak->m ;
        if($berjalan > $tabungan->lama_program){
        $berjalan = $tabungan->lama_program;
        }
        $bunga_dpt_ditarik = (($tabungan->bunga * $tabungan->jum_deposito)*$berjalan)- intval($tabungan->bunga_diambil) ;
        if($bunga_dpt_ditarik < 0){
            $bunga_dpt_ditarik = 0;
        }
        $bungaTabungan = $tabungan->jum_deposito * $tabungan->bunga ;
        $bunga_tdk = ($tabungan->lama_program - $berjalan) * $bungaTabungan ;
        $tabungan_sekarang = $tabungan->total - $bunga_tdk  ;
        $bungaBulanan = $tabungan->jum_deposito * $tabungan->bunga ;
        $total = $tabungan->jum_deposito + (($tabungan->jum_deposito * $tabungan->bunga) * $tabungan->lama_program) ;
        $statusBerjangka = "";
        if($berjalan == $tabungan->lama_program){
            $statusBerjangka ="selesai";
        }


@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Tabungan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        @cannot('nasabah')
        <a class="btn me-2 btn-danger" href="/dashboard/data-tabungan">Kembali</a>
        @endcannot
        @can('nasabah')
        <a class="btn me-2 btn-danger" href="/dashboard/data-nasabah">Kembali</a>
        @endcan
    </div>


</div>

<div class="col-lg-8">

    @if ($tabungan->jenis != "reguler")    
        @if ($statusBerjangka == "selesai" && $tabungan->status != "selesai")
        <div class="alert alert-warning" role="alert">
            Jangka Waktu Tabungan Telah Selesai, Total Tabungan Berjangka Dapat Diatarik !
        </div>    
        @endif
        @if ($statusProgram == "selesai" && $tabungan->status != "selesai")
        <div class="alert alert-warning" role="alert">
            Setoran Program Sudah Selesai, Tabungan Dapat Ditarik pada tanggal {{ date('d-m-Y',strtotime($tabungan->tgl_selesai)) }} !
        </div>    
        @endif
    @endif
    
    <table class="table">
        <tbody>
            <tr>
                <th class="text-capitalize" scope="row">No Tabungan {{ $tabungan->jenis }}</th>
                <td>:</td>
                <td>{{ $tabungan->no }}</td>
            </tr>
            <tr>
                <th scope="row">Nama Penabung</th>
                <td>:</td>
                <td class="text-capitalize">{{ $tabungan->nasabah->user->nama }}</td>
            </tr>
            <tr>
                <th scope="row">Alamat</th>
                <td>:</td>
                <td>{{ $tabungan->nasabah->alamat }}</td>
            </tr>
            <tr>
                <th scope="row">Nomor Telepon</th>
                <td>:</td>
                <td>{{ $tabungan->nasabah->user->no_telp }}</td>
            </tr>
            <tr>
                <th scope="row">Jenis Tabungan</th>
                <td>:</td>
                <td class="text-capitalize">{{ $tabungan->jenis }}</td>
            </tr>
            @if ($tabungan->jenis != 'berjangka')
            <tr>
                <th scope="row">Kolektor</th>
                <td>:</td>
                <td class="text-capitalize">{{ $tabungan->user->nama }}</td>
            </tr>
            @endif
            <tr>
                <th scope="row">Bunga Tabungan</th>
                <td>:</td>
                <td>{{ $tabungan->bunga * 100 }} %</td>
            </tr>
    
    {{-- DETAIL TABUNGAN REGULER --}}
            @if ($tabungan->jenis =="reguler")
            <tr>
                <th scope="row">Jumlah Tabungan</th>
                <td>:</td>
                <td class="rupiah-text"> {{($tabungan->total) }}</td>
            </tr>

    {{-- DETAIL TABUNGAN PROGRAM --}}
            @elseif($tabungan->jenis == "program")
            <tr>
                <th scope="row">Setoran Tetap</th>
                <td>:</td>
                <td class="rupiah-text"> {{($tabungan->setoran_tetap) }}</td>
            </tr>
            <tr>
                <th scope="row">Jangka Waktu</th>
                <td>:</td>
                <td> {{($tabungan->lama_program / 12 ) }} Tahun</td>
            </tr>
                <tr>
                    <th scope="row">Jumlah Tabungan Setelah Program</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{$tabungan_program}}</td>
                    </tr>
                <tr>
                    <tr>
                        <th scope="row">Jumlah Tabungan Sekarang</th>
                        <td>:</td>
                        <td class="rupiah-text"> {{$tabungan->total}}</td>
                        </tr>
                    <tr>
                <tr>
                    @if($tabungan->lama_program != $tabungan->sudah_setor)
                    <th scope="row">Tanggal Setoran Berikutnya</th>
                    <td>:</td>
                    <td>
                    {{date('d-m-Y',strtotime($tabungan->tgl_setoran)) }} <b>({{ $setoran }})</b>
                    @else
                    <th scope="row">Tabungan Dapat Ditarik pada</th>
                    <td>:</td>
                    <td>
                    {{date('d-m-Y',strtotime($tabungan->tgl_selesai)) }}
                    @endif
                    </td>
                </tr>
                @if($tabungan->lama_program != $tabungan->sudah_setor)
                <tr>
                    <th scope="row">Program Selesai Dalam</th>
                    <td>:</td>
                    <td>
                    {{$tabungan->lama_program - $tabungan->sudah_setor}} Bulan
                    </td>
                </tr>
                @endif
                <tr>
                    <th scope="row">Status Tabungan</th>
                    <td>:</td>
                    <td class="text-capitalize">
                        {{$tabungan->status}} 
                    </td>
                </tr>
                
{{-- DETAIL TABUNGAN BERJANGKA --}}
                @else
                <tr>
                    <th scope="row">Jumlah Tabungan Berjangka</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($tabungan->jum_deposito) }} </td>
                </tr>
                <tr>
                    <th scope="row">Lama Tabungan Berjangka</th>
                    <td>:</td>
                    <td> {{($tabungan->lama_program / 12 ) }} Tahun </td>
                </tr>
                <tr>
                    <th scope="row">Tanggal Mulai</th>
                    <td>:</td>
                    <td> {{date('d-m-Y',strtotime($tabungan->tgl_mulai)) }} </td>
                </tr>
                <tr>
                    <th scope="row">Tanggal Selesai</th>
                    <td>:</td>
                    <td> {{date('d-m-Y',strtotime($tabungan->tgl_selesai)) }} </td>
                </tr>
                <tr>
                    <th scope="row">Jumlah Bunga Per Bulan</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($bungaBulanan) }} </td>
                </tr>
                <tr>
                    <th scope="row">Jumlah Bunga Tabungan Berjangka</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($tabungan->jum_deposito * $tabungan->bunga)* $tabungan->lama_program }} </td>
                </tr>
                <tr>
                    <th scope="row">Total Jumlah Tabungan Berjangka Setelah Selesai</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($total) }} </td>
                </tr>
                {{-- <tr>
                    <th scope="row">Jumlah Tabungan Berjangka Sekarang</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($tabungan_sekarang) }} </td>
                </tr> --}}
                
                
                <tr>
                    <th scope="row">Bunga Yang dapat Diambil</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($bunga_dpt_ditarik) }} </td>
                </tr>
                <tr>
                    <th scope="row">Bunga Yang Telah Diambil</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($tabungan->bunga_diambil) }} </td>
                </tr>
                <tr>
                    <th scope="row">Status</th>
                    <td>:</td>
                    <td class="text-capitalize"> {{($tabungan->status) }} </td>
                </tr>

                @endif



        </tbody>
    </table>
</div>
<div class="col-lg-12">
    <h5>Riwayat Transaksi</h5>
    <div class="table-responsive">
        <table class="table table-info">
            <thead>
                <tr>
                    <th scope="col-4">Tanggal</th>
                    <th scope="col">Jenis</th>
                    <th scope="col">Jumlah</th>
                    @if ($tabungan->jenis != "reguler")
                    <th scope="col">Bunga</th>
                    @endif
                    <th scope="col">Tabungan Awal</th>
                    <th scope="col">Tabungan Akhir</th>
                    @if ($tabungan->jenis == "reguler")
                    <th scope="col">Keterangan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi as $t)
                <tr class="{{ ($t->jenis == 'pemindahan') ? 'fw-bold' : '' }}">
                    <th scope="row">{{ date('d-m-Y h:i:sa',strtotime($t->created_at)) }}</th>
                    <td class="text-capitalize">{{ $t->jenis }}</td>
                    <td class="rupiah-text">{{($t->jumlah) }}</td>
                    @if ($tabungan->jenis != "reguler")
                    <td class="rupiah-text">{{ $t->bunga }}</td>
                    @endif
                    <td class="rupiah-text">{{ $t->tabungan_awal }}</td>
                    <td class="rupiah-text">{{ $t->tabungan_akhir }}</td>
                    @if ($tabungan->jenis == "reguler")
                    <td>{{ $t->keterangan }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        var elts = document.getElementsByClassName('rupiah-text');
        for (var i = 0; i < elts.length; ++i) {
            let rp = elts[i].innerHTML;
            console.log(rp)
            rp = formatRupiah(rp, 'Rp.');
            elts[i].innerHTML = rp;
        }
    })

</script>

@endsection

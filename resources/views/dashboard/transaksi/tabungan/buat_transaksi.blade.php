@extends('dashboard.layouts.main')

@section('container')
@foreach ($tabungan as $t)

{{-- PHP --}}
@php
/* PHP TABUNGAN PROGRAM */
            
    $tgl  = date_create($t->tgl_setoran);//8
    $now = date('Y-m-d');
    $now = date_create($now); // waktu sekarang
    $diff  = date_diff( $tgl, $now );
    $selisih = $diff->days ;
    $selisihHari = 0 ;
    $programselesai = date_create($t->tgl_selesai);//8

    if($selisih == 0){
        $setoran = "Hari ini";
        $selisihHari = 0 ;
    }
    if ($tgl > $now) {
        $setoran = $selisih . " Hari Lagi";
        $selisihHari = $selisihHari - $selisih ;
    }
    if($tgl < $now){
        $setoran = "Terlambat " . $selisih . " Hari";
        $selisihHari = $selisihHari + $selisih ;
    }

    /* PHP TABUNGAN BERJANGKA */

    $tglSelesai  = date_create($t->tgl_selesai);//8
    $sekarang = date('Y-m-d');
    $sekarang = date_create($sekarang); // waktu sekarang
    $jarak  = date_diff( $tglSelesai, $sekarang );
    $jarakwaktu = $jarak->days ;
    $p = 0 ;
    $selesai = 0 ;
    if ($tglSelesai >= $sekarang) {
        $selesai = $p - $jarakwaktu ;
    }else{
        $selesai = $p + $jarakwaktu ;
    }
    $tgl_selesai = date_create($t->tgl_selesai);//8
    $tgl_mulai = date_create($t->tgl_mulai);
    $tgl_sekarang = date('Y-m-d');
    $tgl_sekarang = date_create($tgl_sekarang); // waktu sekarang
    $jarak_ = $tgl_sekarang->diff($tgl_mulai);
    $berjalan = $jarak_->y * 12 ;
    $berjalan = $berjalan + $jarak_->m ;
    if($berjalan > $t->lama_program){
                $berjalan = $t->lama_program;
    }
    $bunga_dpt_ditarik = (($t->bunga * $t->jum_deposito)*$berjalan)- intval($t->bunga_diambil);
    if($bunga_dpt_ditarik < 0){
        $bunga_dpt_ditarik = 0 ;
    }

    $bungaBulanan = $t->jum_deposito * $t->bunga ;
    $total = $t->jum_deposito + $bunga_dpt_ditarik  ;
    $totalBerjangka = $t->jum_deposito + (($t->jum_deposito * $t->bunga) * $t->lama_program) ;

@endphp


<div class="col-lg-8">
<form  action="/dashboard/transaksi-tabungan" method="post">
    @csrf
    <input name="tabungan_id" type="hidden" value="{{ $t->id }}">
    <input name="jenis_tabungan" type="hidden" value="{{ $t->jenis }}">
    <input name="users_id" type="hidden" value="{{ auth()->user()->id }}">
    <div class="informasi-tabungan mt-3">
       
        <h4>Informasi Tabungan</h4>
        <table>
            <tr>
                <th width="60%" scope="row">Nama Penabung</th>
                <td width="5%">:</td>
                <td>{{ $t->nasabah->user->nama }}</td>
            </tr>
            <tr>
                <th scope="row">Jenis Tabungan</th>
                <td>:</td>
                <td class="text-capitalize">{{ $t->jenis }}</td>
            </tr>
            @if ($t->jenis == "program" || $t->jenis == "reguler")
            <tr>
                <th scope="row">Total Tabungan</th>
                <td>:</td>
                <th class="rupiah-text">{{ $t->total }}</th>
            </tr>
            @endif
            @if ($t->jenis == "program")
            <tr>
                <th scope="row">Setoran Tetap</th>
                <td>:</td>
                <th class="rupiah-text">{{ $t->setoran_tetap }}</th>
            </tr>
            @if ($t->lama_program != $t->sudah_setor)
            <tr>
                <th scope="row">Tanggal Setoran</th>
                <td>:</td>
                <td>{{ date('d-M-Y',strtotime($t->tgl_setoran)) }} ( {{ $setoran }} )</td>
            </tr>
            <tr>
                <th scope="row">Berakhir dalam</th>
                <td>:</td>
                <td class="text-capitalize">{{ $t->lama_program - $t->sudah_setor }} Bulan</td>
            </tr>
            @else
            <tr>
                <th scope="row">Tanggal Setoran</th>
                <td>:</td>
                <td> - </td>
            </tr>
            <tr>
                <th rowspan="3" scope="row">Setoran Tetap Sudah Berakir</th>
            </tr>
            @endif
                
            @endif
            @if ($t->jenis == "berjangka")
            <tr>
                <th scope="row">Jumlah Setoran Tabungan Berjangka</th>
                <td>:</td>
                <td class="rupiah-text">{{ $t->jum_deposito }}</td>
            </tr>
            <tr>
                <th scope="row">Jumlah Bunga Per Bulan</th>
                <td>:</td>
                <td class="rupiah-text">{{ $t->jum_deposito * $t->bunga }}</td>
            </tr>
            <tr>
                <th scope="row">Total Bunga Tabungan</th>
                <td>:</td>
                <td class="rupiah-text">{{ ($t->jum_deposito * $t->bunga)* $t->lama_program }}</td>
            </tr>
            <tr>
                <th scope="row">Bunga Yang Telah Ditarik</th>
                <td>:</td>
                <td class="rupiah-text">{{ $t->bunga_diambil }}</td>
            </tr>
            <tr>
                <th scope="row">Bunga Yang Dapat Ditarik Sekarang</th>
                <td>:</td>
                <th class="rupiah-text">{{ $bunga_dpt_ditarik }}</th>
            </tr>
            @endif
        </table>
    </div>
    @error('jumlah')
            <div class="alert alert-danger mt-4" role="alert" style="display: block">
                {{ $message }}
            </div>
    @enderror

    @if ($t->jenis == "reguler")
    <div class="form-reguler">
        <div class="mb-3 mt-4">
            <label for="jenis" class="form-label">Jenis Transaksi</label>
            <select name="jenis" id="jenis" class="form-select">
                <option value="setoran" selected>Setoran</option>
                @can('administrasi')
                    
                @if ($t->total > 20000)
                <option value="penarikan">Penarikan</option>
                @endif
                @endcan
                <option value="perbaikan">Perbaikan Kesalahan Input</option>
            </select>
        </div>
        <div class="mb-3" id="perbaikan-input" style="display: none">
            <label for="keterangan_perbaikan" class="form-label">Keterangan</label>
            <select name="keterangan_perbaikan" id="keterangan_perbaikan" class="form-select">
                <option value="kelebihan" selected>Keliebihan Input Tabungan</option>
                <option value="kekurangan">Kekurangan Input Tabungan</option>
            </select>
            <label for="tgl_kesalahan" class="form-label mt-3">Tanggal Kesalahan Input</label>
            <input class="form-control" type="date" id="tgl_kesalahan" name="tgl_kesalahan">
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label @error('jumlah') is-invalid @enderror"><span id="label-setoran">Jumlah</span></label><span style="display: none" id="label-penarikan">Jumlah <b>(Maksimal Penarikan <span class="rupiah-text">{{ $t->total - 20000 }})</span>)</b></span></label>
            <input class="form-control" type="text" id="jumlah" name="jumlah" required>
            
        </div>

    </div>
    @elseif($t->jenis == "program")
    @if ($t->jenis == 'program' && $t->tgl_setoran == null)
    <div class="alert alert-warning mt-4" role="alert">
        Setoran Program Sudah Selesai, Tabungan Dapat Ditarik pada tanggal {{ date('d-m-Y',strtotime($t->tgl_selesai)) }} !
    </div> 
    @endif
    <div class="form-program">
        <div class="mb-3 mt-4">
            <label for="jenis" class="form-label">Jenis Transaksi</label>
            <select name="jenis" id="jenis" class="form-select">
                @if ($t->lama_program == $t->sudah_setor)
                <option value="penarikan" selected>Penarikan</option>
                @else
                <option value="setoran" selected>Setoran</option>
                @endif
            </select>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            @if ($t->lama_program == $t->sudah_setor)
            <input type="text" readonly name="jumlah" id="jumlah" class="form-control rupiah-in" value="{{ $t->total }}">
            @else
            <input type="text" readonly name="jumlah" id="jumlah" class="form-control rupiah-in" value="{{ $t->setoran_tetap }}">
            @endif
        </div>
    </div>
    @else

    <div class="form-program">
        <div class="mb-3 mt-4">
            <label for="jenis" class="form-label">Jenis Transaksi</label>
            <select name="jenis" id="jenis" class="form-select">
                @if ($selesai >= 0)
                <option value="penarikan tabungan berjangka" selected>Penarikan Tabungan Berjangka</option>
                @else
                <option value="penarikan bunga" selected>Penarikan Bunga Tabungan</option>
                @endif
            </select>
        </div>
        <div class="mb-3">
            @if ($selesai >= 0)
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="text" readonly name="jumlah" id="jumlah" class="form-control rupiah-in" value="{{ $total }}">
            @else
            <label for="jumlah" class="form-label">Jumlah <b>(Maksimal <span class="rupiah-text">{{ $bunga_dpt_ditarik }}</span>)<b></label>
            <input type="text" name="jumlah" id="jumlah" class="form-control rupiah-in">
            @endif
        </div>
        <input type="hidden" name="max" value="{{ $bunga_dpt_ditarik }}">
    </div>
    @endif
    <div class="tombol-submit d-flex align-items-end">
        <a class="btn btn-outline-primary me-2 ms-auto" href="/dashboard/data-tabungan">Batal</a>
        <button type="submit" class="btn btn-primary">Lakukan Transaksi</button>

    </div>
</form>
</div>
@endforeach

<script>
    rupiah();
    function rupiah (){
        let getClass = document.getElementsByClassName('rupiah-in')
        for (let i = 0; i < getClass.length; i++) {
            // console.log(getClass[i].value)
            getClass[i].value =  formatRupiah(getClass[i].value,'Rp.')
        }
        // document.getElementById("total").value = convertToRupiah(total)
    }
    $("#jumlah").keyup(function(){
        let val = $("#jumlah").val();
        $("#jumlah").val(formatRupiah(val,"Rp."));
    });

    $("#jenis").change(function(){
        let jenis = $("#jenis").val();
        if(jenis == "setoran"){
            $("#label-penarikan").hide();
            $("#label-setoran").show();
            $("#perbaikan-input").hide();
            $("#tgl_kesalahan").prop("required", false);
        } else if(jenis == "penarikan") {
            $("#label-setoran").hide();
            $("#label-penarikan").show();
            $("#perbaikan-input").hide();
            $("#tgl_kesalahan").prop("required", false);
        } else {
            $("#label-penarikan").hide();
            $("#label-setoran").show();
            $("#perbaikan-input").show();
            $("#tgl_kesalahan").prop("required", true);
        }
    })

</script>
@endsection
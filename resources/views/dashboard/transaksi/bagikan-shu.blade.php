@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Bagikan Sisa Hasil Usaha</h1>

</div>
@if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>  
@endif

@php
@endphp
<div class="info-shu col-lg-8 alert-info p-2">

    @if (count($oldShu) > 0)

    <p class="h6">Pembagian Sisa Hasil Usaha Sebelumnya ({{ $oldShu[0]->tahun }})</p>
    <div>
        <table class="table table-info">
            <tbody>
                <tr>
                    <th scope="row">Tanggal</th>
                    <td>:</td>
                    <td>{{ date('d-m-Y',strtotime($oldShu[0]->created_at)) }}</td>
                </tr>
                <tr>
                    <th scope="row">Total Sisa Hasil Usaha</th>
                    <td>:</td>
                    <td class="rupiah-text">{{ $oldShu[0]->total }}</td>
                </tr>
                <tr>
                    <th scope="row">Sisa Hasil Usaha Yang Dibagikan Kepada Setiap Anggota</th>
                    <td>:</td>
                    <td class="rupiah-text">{{ $oldShu[0]->pembagian_shu }}</td>
                </tr>
                
            </tbody>
        </table>
    </div>
    @endif

    <p class="h6">Data Keanggotaan Nasabah Sekarang</p>
    <div class="col-lg-8">
        <table class="table table-info">
            <tbody>
                <tr>
                    <th scope="row">Jumlah Anggota</th>
                    <td>:</td>
                    <td>{{ $nasabah['anggota'] }} Orang</td>
                </tr>
                <tr>
                    <th scope="row">Jumlah Anggota Alit</th>
                    <td>:</td>
                    <td>{{ $nasabah['anggota_alit'] }} Orang</td>
                </tr>
                <tr>
                    <th scope="row">Jumlah Calon Anggota</th>
                    <td>:</td>
                    <td>{{ $nasabah['calon_anggota'] }} Orang</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<div class="col-lg-8 p-0 mt-3">
    <p class="h6 text-dark">Input Data Sisa Hasil Usaha Yang Akan Dibagikan</p>
    <form method="post" action="/dashboard/transaksi/bagikan-shu" class="mb-4">
        
        @csrf
        <input type="hidden" id="anggota" name="anggota" value="{{ $nasabah['anggota'] }}">
        <input type="hidden" id="anggota_alit" name="anggota_alit" value="{{ $nasabah['anggota_alit'] }}">
        <input type="hidden" id="calon_anggota" name="calon_anggota" value="{{ $nasabah['calon_anggota'] }}">
        <div class="mb-3">
            <div class="row mb-3">
                <div class="col-lg-6">
                    <label for="total" class="form-label">Total Sisa Hasil Usaha</label>
                    <input type="text" class="form-control" id="total"
                    name="total" required>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <label for="pembagian_shu" class="form-label">SHU Kepada Anggota</label>
                    <input readonly type="text" class="form-control" id="pembagian_shu"
                    name="pembagian_shu">
                </div>
                <div class="col-lg-6">
                    <label for="shu_alit" class="form-label">SHU Kepada Anggota Alit</label>
                    <input readonly type="text" class="form-control" id="shu_alit"
                    name="shu_alit">
                </div>
            </div>
        </div>
        <div class="tombol-submit d-flex align-items-end">
            <a class="btn btn-outline-primary me-2 ms-auto" href="/dashboard">Batal</a>
            <button onclick="return confirm('apakah anda yakin membagikan sisa hasil usaha ini ?')" type="submit" class="btn btn-primary">Bagikan SHU</button>
        </div>
    </form>
</div>

<script>
    
$( document ).ready(function() {
    var anggota = parseInt($("#anggota").val());
    var anggotaAlit = parseInt($("#anggota_alit").val());
    var calonAnggota = parseInt($("#calon_anggota").val());
    var pembagi = (anggota * 2 ) + anggotaAlit ;
    console.log(pembagi);

    $("#total").keyup(function(){
        let val = $("#total").val();
        $("#total").val(formatRupiah(val,"Rp."));
        let total = $("#total").val();
        total = total.replace(/\D/g, "");
        total = parseInt(total);
        shuAlit = Math.floor(total / pembagi) ;
        let shu = shuAlit * 2 ;
        shu = shu.toString();
        shuAlit = shuAlit.toString();
        $("#pembagian_shu").val(formatRupiah(shu,"Rp."));
        $("#shu_alit").val(formatRupiah(shuAlit,"Rp."));
        
    });
});


</script>

@endsection

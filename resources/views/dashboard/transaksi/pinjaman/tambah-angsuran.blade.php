@extends('dashboard.layouts.main')

@section('container')
@foreach ($pinjaman as $p)
@php
function rupiah($angka){
    $angka = ceil($angka);
    $format_rupiah = "Rp." . number_format($angka,2,',','.');
    return $format_rupiah;
}
    $bunga_harian = $p->bunga_dibayar /30 ;
    $tgl = date_create($p->tgl_angsuran);//8
    $now = date('Y-m-d');
    $now = date_create($now); // waktu sekarang
    $diff = date_diff( $tgl, $now );
    $selisih = $diff->days ;
    $selisihHari = 0 ;
    if ($tgl > $now) {
    $estimasi_pembayaran = $selisih . " Hari Lagi";
    $selisihHari = $selisihHari - $selisih ;
    }
    if($tgl < $now) {
    $estimasi_pembayaran = "Terlambat " . $selisih . " Hari";
    $selisihHari = $selisihHari + $selisih ;
    }
    $bunga_harian = ceil($bunga_harian * $selisihHari) ;
@endphp
<div class="col-lg-8">
<form  action="/dashboard/transaksi-pinjaman" method="post">
    @csrf
    <input name="pinjaman_id" type="hidden" value="{{ $p->id }}">
    <input name="bunga_harian" type="hidden" value="{{ $bunga_harian }}">
    <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama"
            name="nama" disabled required value="{{ $p->nasabah->user->nama}}">
    </div>

    <div class="mb-3">
        <label for="sisa_angsuran" class="form-label">Sisa Angsuran</label>
        <input type="text" class="form-control" id="sisa_angsuran"
        name="sisa_angsuran" disabled required value="{{ $p->lama_angsuran - $p->sudah_mengangsur}} x">
    </div>

    
    <a class="btn btn-danger" href="{{ url('/dashboard/transaksi-pinjaman') }}">Batal</a>
    {{-- <button class="btn btn-primary" type="submit">Lakukan Transaksi</button> --}}
    <button class="btn btn-primary" type="submit" >Lakukan Transaksi</button>
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
</script>
@endsection
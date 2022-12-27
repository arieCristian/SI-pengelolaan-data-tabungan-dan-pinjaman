@extends('dashboard.layouts.main')

@section('container')
@foreach ($peminjam as $p)
@php
function rupiah($angka){
    $angka = ceil($angka);
    $format_rupiah = "Rp." . number_format($angka,0,"",".");
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
    if($selisihHari < -30 ){
        $selisihHari = -30 ;
    }
    }
    if($tgl < $now) {
    $estimasi_pembayaran = "Terlambat " . $selisih . " Hari";
    $selisihHari = $selisihHari + $selisih ;
    }

    $bunga_harian = ceil($bunga_harian * $selisihHari) ;
@endphp
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Transaksi Pinjaman</h1>

</div>
<div class="col-lg-8">
    @if ($p->lama_angsuran - $p->sudah_mengangsur == 1)
    <div class="alert alert-danger mt-4" role="alert">
        Ini adalah angsuran terakhir nasabah !
        Jika Tidak melunasi sekarang maka harus memperpanjang angsuran pembayaran pinjaman !
    </div> 
    @endif
    <table>
        <tr>
            <th width="60%" scope="row">Nama Peminjam</th>
            <td width="5%">:</td>
            <td>{{ $p->nasabah->user->nama }}</td>
        </tr>
        <tr>
            <th width="60%" scope="row">Sisa Pinjaman</th>
            <td width="5%">:</td>
            <td class="rupiah-text">{{ $p->sisa_pinjaman }}</td>
        </tr>
        <tr>
            <th width="60%" scope="row">Jumlah Angsuran Pokok</th>
            <td width="5%">:</td>
            <td class="rupiah-text">{{ $p->angsuran_pokok }}</td>
        </tr>
        <tr>
            <th width="60%" scope="row">Jumlah Bunga Pinjaman</th>
            <td width="5%">:</td>
            <td class="rupiah-text">{{ $p->bunga_dibayar }}</td>
        </tr>
        <tr>
            <th width="60%" scope="row">Jumlah Pelunasan Lanngsung</th>
            <td width="5%">:</td>
            <td class="rupiah-text">{{ (intval($p->bunga_dibayar) + $bunga_harian) + $p->sisa_pinjaman }}</td>
        </tr>
    </table>
<form  action="/dashboard/transaksi-pinjaman" method="post">
    @csrf
    <input name="pinjaman_id" type="hidden" value="{{ $p->id }}">
    <input name="bunga_harian" type="hidden" value="{{ $bunga_harian }}">
    <div class="mb-3 mt-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama"
            name="nama" disabled required value="{{ $p->nasabah->user->nama}}">
    </div>
    <div class="row">
        <div class="mb-3 col-sm-8">
            <label for="tgl" class="form-label">Tanggal Jatuh Tempo</label>
            <input type="text" class="form-control" id="tgl"
                name="tgl" disabled required value="{{ date('d-m-Y' , strtotime($p->tgl_angsuran))}}">
        </div>
        <div class="mb-3 col-sm-4">
            <label for="sisa_angsuran" class="form-label">Sisa Angsuran</label>
            <input type="text" class="form-control" id="sisa_angsuran"
                name="sisa_angsuran" disabled required value="{{ $p->lama_angsuran - $p->sudah_mengangsur}} x">
        </div>
    </div>
    
    <div class="mb-3">
        <label for="jenis" class="form-label">Jenis Transaksi</label>
        <select onchange="ubahJumlah(this)" class="form-select" name="jenis" id="jenis">
            @if (old('jenis') == 'Jumlah Bebas' )
            <option value="Full Angsuran">Full Angsuran</option>
            <option value="Hanya Bunga">Hanya Bunga</option>
            <option value="Pelunasan">Langsung Lunas</option>
            <option value="Jumlah Bebas" selected>Jumlah Bebas</option>
            @else
            <option value="Full Angsuran" selected>Full Angsuran</option>
            <option value="Hanya Bunga">Hanya Bunga</option>
            <option value="Pelunasan">Langsung Lunas</option>
            <option value="Jumlah Bebas">Jumlah Bebas</option>
            @endif
        </select>
    </div>
    <input id="bunga-dibayar" type="hidden" name="bunga-dibayar" value="{{ $p->bunga_dibayar }}">
    @if ( $p->sisa_pinjaman < $p->angsuran_pokok)
    <input id="full" type="hidden" name="full" value="{{ $p->sisa_pinjaman + $p->bunga_dibayar }}">
    @else
    <input id="full" type="hidden" name="full" value="{{ $p->angsuran_pokok + $p->bunga_dibayar }}">
    @endif
    <input id="lunas" type="hidden" name="lunas" value="{{ $p->sisa_pinjaman + $p->bunga_dibayar }}">
    <input id="angsuran_pokok" type="hidden" name="angsuran_pokok" value="{{ $p->angsuran_pokok }}">
    <input id="bunga-harian" type="hidden" name="lunas" value="{{ $bunga_harian }}">
    @if (old('jenis') == 'Jumlah Bebas' )
    <div class="mb-3">
        <label for="jumlah" class="form-label @error('jumlah') is-invalid @enderror">Jumlah <span id="min-jumlah"> <b> (Minimal Pembayaran <span class="rupiah-text">{{($p->bunga_dibayar + $bunga_harian) }}</span>)</b></span></label>
        <input onkeyup="jumlahCustom(this.value)" type="text" class="form-control" id="jumlah"
            name="jumlah" required value="{{ old('jumlah')}}">
            @error('jumlah')
            <div class="invalid-feedback" id="error-m" style="display: block">
                {{ $message }}
            </div>
            @enderror
    </div>
    <div id="input-lengkap" style="display: none">
        @if ($selisihHari > 0)
        <div class="mb-3">
            <input type="hidden" name="keterangan" id="keterangan" value="Membayar penalti keterlambatan pembayaran ({{ $estimasi_pembayaran }}) sebesar  {{ rupiah($bunga_harian)}}">
            <label for="penalti" class="form-label">Penalti Keterlambatan Pembayaran <b>( {{ $estimasi_pembayaran }})</b></label>
            <input type="text" class="form-control rupiah-in" id="penalti"
                name="penalti" readonly required value="{{ $bunga_harian}}">
        </div>
        @endif
    
        @if($selisihHari < 0)
        <div class="mb-3">
            <input type="hidden" name="keterangan" id="keterangan" value="Pembayaran Lebih Awal ({{ $estimasi_pembayaran }}) mendapatkan potongan bunga pinjman sebesar {{ rupiah(abs($bunga_harian))}}">
            <label for="penalti" class="form-label">Potongan Pembayaran Sebelum Jatuh Tempo <b>({{ $estimasi_pembayaran }})</b></label>
            <input type="text" class="form-control rupiah-in" id="penalti"
                name="penalti" readonly required value="{{ abs($bunga_harian)}}">
        </div>
        @endif
        <div class="mb-3">
            <label for="total" class="form-label">Total Pembayaran</label>
            @if ( $p->sisa_pinjaman < $p->angsuran_pokok)
            <input type="text" class="form-control rupiah-in" id="total"
                name="total" readonly required value="{{ $p->sisa_pinjaman + $p->bunga_dibayar + $bunga_harian}}">
            @else
            <input type="text" class="form-control rupiah-in" id="total"
                name="total" readonly required value="{{ $p->angsuran_pokok + $p->bunga_dibayar + $bunga_harian}}">
            @endif
        </div>
    </div>
            @else
            <div class="mb-3">
                <label for="jumlah" class="form-label @error('jumlah') is-invalid @enderror">Jumlah <span style="display: none" id="min-jumlah"> <b>(Minimal Pembayaran <span class="rupiah-text">{{($p->bunga_dibayar + $bunga_harian) }}</span>)</b></span></label>
                <input onkeyup="jumlahCustom(this.value)" type="text" class="form-control rupiah-in" id="jumlah"
                    name="jumlah" readonly required value="{{ ($p->sisa_pinjaman < $p->angsuran_pokok)? $p->sisa_pinjaman + $p->bunga_dibayar : $p->angsuran_pokok + $p->bunga_dibayar }}"> 
                    @error('jumlah')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
            </div>
            <div id="input-lengkap" style="display: block">
                @if ($selisihHari > 0)
                <div class="mb-3">
                    <input type="hidden" name="keterangan" id="keterangan" value="Membayar penalti keterlambatan pembayaran ({{ $estimasi_pembayaran }}) sebesar  {{ rupiah($bunga_harian)}}">
                    <label for="penalti" class="form-label">Penalti Keterlambatan Pembayaran <b>( {{ $estimasi_pembayaran }})</b></label>
                    <input type="text" class="form-control rupiah-in" id="penalti"
                        name="penalti" readonly required value="{{ $bunga_harian}}">
                </div>
                @endif
            
                @if($selisihHari < 0)
                <div class="mb-3">
                    <input type="hidden" name="keterangan" id="keterangan" value="Pembayaran Lebih Awal ({{ $estimasi_pembayaran }}) mendapatkan potongan bunga pinjman sebesar {{ rupiah(abs($bunga_harian))}}">
                    <label for="penalti" class="form-label">Potongan Pembayaran Sebelum Jatuh Tempo <b>({{ $estimasi_pembayaran }})</b></label>
                    <input type="text" class="form-control rupiah-in" id="penalti"
                        name="penalti" readonly required value="{{ abs($bunga_harian)}}">
                </div>
                @endif
                <div class="mb-3">
                    <label for="total" class="form-label">Total Pembayaran</label>
                    @if ( $p->sisa_pinjaman < $p->angsuran_pokok)
                    <input type="text" class="form-control rupiah-in" id="total"
                        name="total" readonly required value="{{ $p->sisa_pinjaman + $p->bunga_dibayar + $bunga_harian}}">
                    @else
                    <input type="text" class="form-control rupiah-in" id="total"
                        name="total" readonly required value="{{ $p->angsuran_pokok + $p->bunga_dibayar + $bunga_harian}}">
                    @endif
                    
                </div>
            </div>
            @endif
    <div class="tombol-submit d-flex align-items-end mb-3">
        <a class="btn btn-outline-primary me-2 ms-auto" href="{{ url('/dashboard/transaksi-pinjaman') }}">Batal</a>
        <button type="submit" class="btn btn-primary">Lakukan Transaksi</button>
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
    var bungaAja = parseInt(document.getElementById('bunga-dibayar').value) ;
    var full = parseInt(document.getElementById('full').value) ;
    var lunas = parseInt(document.getElementById('lunas').value) ;
    var bungaHarian = parseInt(document.getElementById('bunga-harian').value) ;
    var jum = document.getElementById('jumlah') ;
    var total = document.getElementById('total') ;
    var textBungaAja = formatRupiah(document.getElementById('bunga-dibayar').value,'Rp.') ;
    var textFull = formatRupiah(document.getElementById('full').value,'Rp.') ;
    var textLunas = formatRupiah(document.getElementById('lunas').value,'Rp.') ;
    var minJumlah = document.getElementById('min-jumlah') ;
    var keterangan = document.getElementById('keterangan') ;
    var errorM = document.getElementById('error-m') ;
    // var inputLengkap = document.getElementById('input-lengkap') ;
    var totBungaAja = bungaAja + bungaHarian;
    totBungaAja = totBungaAja.toString();
    totBungaAja = formatRupiah(totBungaAja,'Rp.');
    var totFull = full + bungaHarian;
    totFull = totFull.toString();
    totFull = formatRupiah(totFull,'Rp.');
    var totLunas = lunas + bungaHarian;
    totLunas = totLunas.toString();
    totLunas = formatRupiah(totLunas,'Rp.');
    // console.log(totBungaAja);
    function ubahJumlah(val){
        let pilihan = val.value ;
        if(pilihan == 'Hanya Bunga'){
            jum.value = textBungaAja;            
            document.getElementById("input-lengkap").style.display = "block";
            total.value = totBungaAja;
            jum.readOnly = true ;
            minJumlah.style.display = "none" ;
            errorM.style.display = "none";
        } else if(pilihan == 'Full Angsuran'){
            jum.value = textFull ;
            total.value = totFull;
            console.log(totFull);
            jum.readOnly = true ;
            minJumlah.style.display = "none" ;
            document.getElementById("input-lengkap").style.display = "block";
            errorM.style.display = "none";
        } else if(pilihan == 'Pelunasan') {
            jum.value = textLunas ;
            jum.readOnly = true ;
            total.value = totLunas;
            minJumlah.style.display = "none" ;
            document.getElementById("input-lengkap").style.display = "block";
            errorM.style.display = "none";
        } else {

            jum.value = '' ;
            jum.readOnly = false ;
            jum.autofocus = true ;
            minJumlah.style.display = "inline" ;
            document.getElementById("input-lengkap").style.display = "none";

        }
    }
    function jumlahCustom(val){
        jum.value = formatRupiah(val, 'Rp.');
    }
</script>
@endsection
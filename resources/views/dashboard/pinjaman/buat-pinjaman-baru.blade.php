@extends('dashboard.layouts.main')

@section('container')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Pinjaman Baru</h1>

</div>
<div class="col-lg-8 mt-3">
    <form action="/dashboard/data-pinjaman" method="post">
        @csrf
        <input name="nasabah_id" type="hidden" value="{{ $nasabah[0]->id }}">
        <input type="hidden" name="potongan-admin" value="0" id="input-potongan-admin">
        <div class="row">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Peminjam</label>
                <input type="text" class="form-control" id="nama" value="{{ $nasabah[0]->user->nama }}" readonly>
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="ktp" class="form-label">Nomor KTP</label>
                <input type="number" class="form-control @error('ktp') is-invalid @enderror" id="ktp" name="ktp" required>
                @error('ktp')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="mb-3 col-md-6">
                <label for="kk" class="form-label">Nomor KK</label>
                <input type="number" class="form-control" id="kk" name="kk" required>
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-md-6">
                <label for="jaminan" class="form-label">Jaminan</label>
                <input type="text" class="form-control" id="jaminan" name="jaminan" required>
            </div>
            <div class="mb-3 col-md-6">
                <label for="pemindahan" class="form-label">Kebaruan</label>
                <select class="form-select" name="pemindahan" id="pemindahan">
                    <option selected value="baru">Baru</option>
                    <option value="pemindahan">Pemindahan</option>
                </select>
            </div>
            <div class="mb-3 col-md-6">
                <label for="lama_angsuran" class="form-label">Lama Angsuran /Bulan</label>
                <input type="number" class="form-control" id="lama_angsuran" name="lama_angsuran" required>
            </div>
            <div class="mb-3 col-md-6 angsuran-lama" style="display: none">
                <label for="sudah_mengangsur" class="form-label">Sudah Mengangsur /Bulan</label>
                <input type="number" class="form-control" id="sudah_mengangsur" name="sudah_mengangsur" value="0" required>
            </div>
            
            <div class="mb-3 col-md-6">
                <label for="pinjaman" class="form-label">Jumlah Pinjaman</label>
                <input type="text" class="form-control angka" id="pinjaman" name="pinjaman" required>
            </div>
            <div class="mb-3 col-md-6 angsuran-lama" style="display: none">
                <label for="sisa_pinjaman" class="form-label">Sisa Pinjaman</label>
                <input type="text" class="form-control angka" id="sisa_pinjaman" name="sisa_pinjaman">
            </div>
            <div class="mb-3 col-md-6">
                <label for="bunga" class="form-label">Bunga Pinjaman</label>
                <select class="form-select" name="bunga" id="bunga">
                    <option value="0.025">2,5 %</option>
                    <option value="0.020">2 %</option>
                    <option value="0.018">1,8 %</option>
                </select>
            </div>
            <div class="col-md-6 angsuran-lama" style="display: none">
                <label for="tgl_angsuran" class="form-label">Tanggal Angsuran Terakhir</label>
                <input type="date" class="form-control @error('tgl_angsuran') is-invalid @enderror" id="tgl_angsuran"
                name="tgl_angsuran" value="{{ old('tgl_angsuran')}}">
                @error('tgl_angsuran')
                        <div class="invalid-feedback invalid-program" id="error-m" style="display: block">
                            {{ $message }}
                        </div>
                @enderror
            </div>
            
            
        </div>
<table style="width: 100% ; display:none" class="table table-info" id="data-pinjaman-baru">
    <tbody>
        <tr>
            <th scope="row" style="width: 40%">Jumlah Pinjaman</th>
            <td>:</td>
            <td class="rupiah-text" id="pinjaman-baru"></td>
        </tr>
        <tr>
            <th scope="row">Bunga</th>
            <td>:</td>
            <td id="bunga-baru"></td>
        </tr>
        <tr>
            <th scope="row">Potongan Administrasi</th>
            <td>:</td>
            <td>4 %</td>
        </tr>
        <tr>
            <th scope="row">Jumlah Potongan Administrasi</th>
            <td>:</td>
            <td id="potongan-admin" class="rupiah-text"></td>
        </tr>
        <tr>
            <th scope="row">Jumlah Pinjaman Diterima</th>
            <td>:</td>
            <td id="pinjaman-diterima"></td>
        </tr>
        <tr>
            <th scope="row">Lama Angsuran</th>
            <td>:</td>
            <td id="lama-angsuran-baru"></td>
        </tr>
        <tr>
            <th scope="row">Angsuran Pokok</th>
            <td>:</td>
            <td class="rupiah-text" id="angsuran-pokok-baru"></td>
        </tr>
        <tr>
            <th scope="row">Bunga Pinjaman</th>
            <td>:</td>
            <td class="rupiah-text" id="bunga-pinjaman-baru"></td>
        </tr>

    </tbody>
</table>
<div class="tombol-submit d-flex align-items-end mb-3">
    <a class="btn btn-outline-primary me-2 ms-auto" href="/dashboard/data-pinjaman">Batal</a>
    <span onclick="cek()" class="btn btn-info me-2">CEK</span>
    {{-- <button onclick="cek()" class="btn btn-warning me-2">CEK</button> --}}
    <button onclick="cek()" type="submit" class="btn btn-primary">Tambah</button>
</div>
</form>
</div>


<script>
    $("#pemindahan").change(function(){
            let val = $("#pemindahan").val() ;
            if(val == "baru"){
                $('#sudah_mengangsur').val(0);
                $('#sisa_pinjaman').val($('#pinjaman').val());
                $(".angsuran-lama").hide();
            }else{
                $(".angsuran-lama").show();
            }
        });


    function ubahJumllah(val) {
        let bungaAja = document.getElementById('bunga-dibayar').value;
        let full = document.getElementById('full').value;
        let jum = document.getElementById('jumlah');
        let pilihan = val.value;
        if (pilihan == 'Bunga') {
            jum.value = bungaAja;
        } else {
            jum.value = full;
        }
    }

    $("#pinjaman").keyup(function () {
        let val = $("#pinjaman").val();
        $("#pinjaman").val(formatRupiah(val, "Rp."));
    });
    $("#sisa_pinjaman").keyup(function () {
        let val = $("#sisa_pinjaman").val();
        $("#sisa_pinjaman").val(formatRupiah(val, "Rp."));
    });

    function cek() {
        let lamaAngsuran = $("#lama_angsuran").val();
        lamaAngsuran = parseInt(lamaAngsuran);
        let pinjaman = $("#pinjaman").val();
        pinjaman = pinjaman.replace(/\D/g, "");
        pinjaman = parseInt(pinjaman);
        let bunga = $("#bunga").val();
        bunga = parseFloat(bunga);
        let pinjamanDiterima = pinjaman - (Math.ceil(pinjaman * 0.04));
        pinjamanDiterima = pinjamanDiterima.toString();
        pinjamanDiterima = formatRupiah(pinjamanDiterima, "Rp.")
        let potonganAdmin = Math.ceil(pinjaman * 0.04);
        $('#input-potongan-admin').val(potonganAdmin);
        potonganAdmin = potonganAdmin.toString();
        potonganAdmin = formatRupiah(potonganAdmin, "Rp.")
        let bungaDibayar = bunga * pinjaman;
        bungaDibayar = Math.ceil(bungaDibayar);
        bungaDibayar = bungaDibayar.toString();
        bungaDibayar = formatRupiah(bungaDibayar, "Rp.")
        angsuranPokok = pinjaman / lamaAngsuran;
        angsuranPokok = Math.ceil(angsuranPokok);
        angsuranPokok = angsuranPokok.toString();
        angsuranPokok = formatRupiah(angsuranPokok, "Rp.");
        // $("#input-lama-angsuran").val(lamaAngsuran);
        lamaAngsuran = lamaAngsuran + " Bulan";
        if(bunga == 0.018){
            bunga = 1.8
        }else{
            bunga = bunga * 100;
        }
        bunga = bunga + " %"
        pinjaman = pinjaman.toString()
        pinjaman = formatRupiah(pinjaman, "Rp.");
        $("#pinjaman-baru").html(pinjaman);
        $("#bunga-baru").html(bunga);
        $("#lama-angsuran-baru").html(lamaAngsuran);
        $("#angsuran-pokok-baru").html(angsuranPokok);
        $("#bunga-pinjaman-baru").html(bungaDibayar);
        $("#pinjaman-diterima").html(pinjamanDiterima);
        $("#potongan-admin").html(potonganAdmin);
        // $("#input-pinjaman").val(pinjaman);
        // $("#input-bunga-dibayar").val(bungaDibayar);
        // $("#input-angsuran-pokok").val(angsuranPokok);


        $("#data-pinjaman-baru").show();
        // console.log(Pinjaman);

    }

</script>
@endsection

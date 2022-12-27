
@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Perbarui Pinjaman Nasabah</h1>

    
    </div>
    <div class="col-lg-8">
        <table class="table">
            <tbody>
                <tr>
                    <th scope="row" style="width: 40%">Nama Peminjam</th>
                    <td>:</td>
                    <td>{{ $pinjaman->nasabah->user->nama }}</td>
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
                    <th scope="row">Angsuran Pokok</th>
                    <td>:</td>
                    <td class="rupiah-text">{{ $pinjaman->angsuran_pokok }}</td>
                </tr>
                <tr>
                    <th scope="row">Sisa Pinjaman</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($pinjaman->sisa_pinjaman) }}</td>
                </tr>
                <tr>
                    <th scope="row">Bunga Pinjaman</th>
                    <td>:</td>
                    <td class="rupiah-text"> {{($pinjaman->bunga_dibayar) }}</td>
                </tr>
                <tr>
                    <th scope="row">Sisa Angsuran</th>
                    <td>:</td>
                    <td>{{ $pinjaman->lama_angsuran - $pinjaman->sudah_mengangsur }} X</td>
                </tr>

            </tbody>
        </table>
        <table class="table table-info" id="data-pinjaman-baru" style="display: none">
            <tbody>
                <tr>
                    <th scope="row" style="width: 40%">Jumlah Pinjaman Baru</th>
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
                    <td id="ket-admin"></td>
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
                    <th scope="row">Lama Angsuran Baru</th>
                    <td>:</td>
                    <td id="lama-angsuran-baru"></td>
                </tr>
                <tr>
                    <th scope="row">Angsuran Pokok Baru</th>
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
        <form class="mb-4" method="post" action="/dashboard/data-pinjaman/{{ $pinjaman->id }}">
            @method('put')
            @csrf
            <input type="hidden" name="potongan-admin" value="0" id="input-potongan-admin">
            <div class="row">
                <div class="mb-3 col-lg-7 col-sm-12">
                    <input type="hidden" name="id" value="{{ $pinjaman->id }}">
                    <input type="hidden" value = "" id="input-pinjaman" name="pinjaman">
                    <input type="hidden" value = "" id="input-bunga-dibayar" name="bunga_dibayar">
                    <input type="hidden" value = "" id="input-angsuran-pokok" name="angsuran_pokok">
                    <input type="hidden" value = "" id="input-lama-angsuran" name="lama_angsuran">
                    <input type="hidden" id="pinjaman-lama" value="{{ $pinjaman->sisa_pinjaman }}">
                    <input type="hidden" id="lama-angsuran-lama" value="{{ $pinjaman->lama_angsuran - $pinjaman->sudah_mengangsur }}">
                    <input type="hidden" id="bunga-lama" value="{{ $pinjaman->bunga }}">
                    <label for="jumlah" class="form-label">Tambah Jumlah Pinjaman</label>
                    <input onkeyup="rupiah(this.value)" type="text" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah') }}">
                    @error('jumlah')
                        <div class="invalid-feedback">
                        {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3 col-lg-2 col-sm-6">
                    <input type="hidden" name="id" value="{{ $pinjaman->id }}">
                    <label for="jumlah" class="form-label">Ganti Bunga</label>
                    <select class="form-select" name="bunga" id="bunga">
                            @if ($pinjaman->bunga == 0.025)
                            <option selected value="0.025">2,5 %</option>
                            @else
                            <option value="0.025">2,5 %</option>
                            @endif
                            @if ($pinjaman->bunga == 0.020)
                            <option selected value="0.020">2 %</option>
                            @else
                            <option value="0.020">2 %</option>
                            @endif
                            @if ($pinjaman->bunga == 0.018)
                            <option selected value="0.018">1,8 %</option>
                            @else
                            <option value="0.018">1,8 %</option>
                            @endif
                    </select>
                </div>
                @if ( $pinjaman->lama_angsuran == $pinjaman->sudah_mengangsur)
                <div class="mb-3 col-lg-3 col-sm-6">
                    <label for="tambah_lama_angsuran" class="form-label">Tambah Waktu / Bulan</label>
                    <input type="text" class="form-control @error('tambah_lama_angsuran') is-invalid @enderror" id="tambah_lama_angsuran" name="tambah_lama_angsuran" value="{{ old('tambah_lama_angsuran') }}" required>
                    @error('tambah_lama_angsuran')
                        <div class="invalid-feedback">
                        {{ $message }}
                        </div>
                    @enderror
                </div>
                @else
                <div class="mb-3 col-lg-3 col-sm-6">
                    <label for="tambah_lama_angsuran" class="form-label">Tambah Waktu / Bulan</label>
                    <input type="text" class="form-control @error('tambah_lama_angsuran') is-invalid @enderror" id="tambah_lama_angsuran" name="tambah_lama_angsuran" value="{{ old('tambah_lama_angsuran') }}">
                    @error('tambah_lama_angsuran')
                        <div class="invalid-feedback">
                        {{ $message }}
                        </div>
                    @enderror
                </div>
                @endif
                

            </div>
            
            <div class="tombol-submit d-flex align-items-end">
                <a class="btn btn-outline-primary me-2 ms-auto" href="/dashboard/data-pinjaman">Batal</a>
                <span onclick="cek()" class="btn btn-info me-2">CEK</span>
                {{-- <button onclick="cek()" class="btn btn-warning me-2">CEK</button> --}}
                <button onclick="cek()" type="submit" class="btn btn-warning">Perbarui</button>
            </div>
        </form>
    </div>

<script>
    var bungaLama = parseInt($("#bunga-lama").val());
    var sisaPinjaman = parseInt($("#pinjaman-lama").val());
    var lamaAngsuranLama = parseInt($("#lama-angsuran-lama").val());
    
    var jumTambahan ;
    var pinjaman ,biayaAdmin,pinjamanDiterima;
    var angsuranPokok,bungaDibayar ;

    // console.log(bungaLama,sisaPinjaman,lamaAngsuranLama);
    // $("#jumlah").keyup(function(val){
    //     // jumTambahan = $("#jumlah").val();
    //     // tambahan = formatRupiah($("#jumlah").val());
    //     $("#jumlah").val(formatRupiah(val)) ;
    // })

    function rupiah(val){
        $("#jumlah").val(formatRupiah(val,"Rp."));
    }
    function cek(){
        if($("#jumlah").val() == ""){
            pinjaman = sisaPinjaman;
            pinjamanDiterima = 0 ;
            if($("#tambah_lama_angsuran").val() != ""){
            biayaAdmin = Math.ceil(pinjaman* 0.04) ;
            $("#ket-admin").html("4 % dari sisa pinjaman sebelumnya");
            }else{
                biayaAdmin = 0 ;
                $("#ket-admin").html(" - ");
            }
        }else {
            jumTambahan = $("#jumlah").val().replace(/\D/g, "");
            jumTambahan = parseInt(jumTambahan);
            pinjaman = sisaPinjaman + jumTambahan ;
            if($("#tambah_lama_angsuran").val() == ""){
                biayaAdmin = Math.ceil(jumTambahan* 0.04) ;
                pinjamanDiterima = jumTambahan - biayaAdmin ;
                $("#ket-admin").html("4 % dari jumlah penambahan pinjaman");
            }else {
                biayaAdmin = Math.ceil(pinjaman* 0.04) ;
                pinjamanDiterima = jumTambahan - biayaAdmin ;
                $("#ket-admin").html("4 % dari jumlah pinjaman baru");
            }
        }
        if($("#tambah_lama_angsuran").val() == ""){
            lamaAngsuran = lamaAngsuranLama;
        }else {
            lamaAngsuran = $("#tambah_lama_angsuran").val() ;
            lamaAngsuran = parseInt(lamaAngsuran);      
            lamaAngsuran = lamaAngsuran + lamaAngsuranLama ;
        }
        $("#input-potongan-admin").val(biayaAdmin);
        
        biayaAdmin = biayaAdmin.toString();
        biayaAdmin = formatRupiah(biayaAdmin, "Rp.")
        pinjamanDiterima = pinjamanDiterima.toString();
        pinjamanDiterima = formatRupiah(pinjamanDiterima, "Rp.")
        let bunga = $("#bunga").val();
        bunga = parseFloat(bunga) ;
        bungaDibayar = bunga * pinjaman ;
        bungaDibayar = Math.ceil(bungaDibayar);
        bungaDibayar = bungaDibayar.toString() ;
        bungaDibayar = formatRupiah(bungaDibayar,"Rp.")
        angsuranPokok = pinjaman/lamaAngsuran ;
        angsuranPokok = Math.ceil(angsuranPokok);
        angsuranPokok = angsuranPokok.toString();
        angsuranPokok = formatRupiah(angsuranPokok,"Rp.");
        $("#input-lama-angsuran").val(lamaAngsuran);
        lamaAngsuran = lamaAngsuran + " Bulan";
        if(bunga == 0.018){
            bunga = 1.8
        }else{
            bunga = bunga * 100;
        }
        bunga = bunga + " %"
        pinjaman = pinjaman.toString()
        pinjaman = formatRupiah(pinjaman,"Rp.");
        $("#pinjaman-baru").html(pinjaman);
        $("#bunga-baru").html(bunga);
        $("#lama-angsuran-baru").html(lamaAngsuran);
        $("#angsuran-pokok-baru").html(angsuranPokok);
        $("#bunga-pinjaman-baru").html(bungaDibayar);
        $("#input-pinjaman").val(pinjaman);
        $("#input-bunga-dibayar").val(bungaDibayar);
        $("#input-angsuran-pokok").val(angsuranPokok);
        $("#pinjaman-diterima").html(pinjamanDiterima);
        $("#potongan-admin").html(biayaAdmin);
        
        
        $("#data-pinjaman-baru").show() ;
        // console.log(Pinjaman);
        
    }
</script>
@endsection


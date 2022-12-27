@extends('dashboard.layouts.main')

@section('container')
@php
    // dd($nasabah);
@endphp
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Tabungan Baru</h1>

</div>
<div class="col-lg-8 mt-4">
    {{-- <table class="table">
        <tbody>
            <tr>
                <th scope="row">Nama Penabung</th>
                <td>:</td>
                <td>{{ $nasabah[0]->user->nama }}</td>
            </tr>
        </tbody>
    </table> --}}

       
  
        @if (count($tabungan) > 0)
        <h6>{{ $nasabah[0]->user->nama }} Memiliki {{ count($tabungan) }} Tabungan :</h6>
        <ul class="list">
            @foreach ($tabungan as $t)
            @if ($t->jenis == "reguler")
            <li class="list-group-item">Tabungan Reguler sebanyak : <span class="rupiah-text">{{ $t->total }}</span> </li>    
            @elseif($t->jenis == "program")
                @if ($t->status != "selesai")
                <li class="list-group-item">Tabungan Program berakhir dalam : <b>{{ $t->lama_program - $t->sudah_setor }} Bulan</b> </li>   
                @endif
            @else
                @if ($t->status != "selesai")
                <li class="list-group-item">Tabungan Berjangka berakhir pada : <b>{{ $t->tgl_selesai }}</b> </li>   
                @endif
            
            @endif

            @endforeach
        </ul>
            
        @endif
    <form class="mb-3" action="/dashboard/data-tabungan" method="post">
            @csrf
            
        <input name="nasabah_id" type="hidden" value="{{ $nasabah[0]->id }}">
        <input name="users_id" type="hidden" value="{{ auth()->user()->id }}">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Penabung</label>
            <input type="text" class="form-control" id="nama"
            name="nama" required readonly value="{{ $nasabah[0]->user->nama }}">
        </div>
        @canany(['administrasi', 'admin'])
        <div class="row">
            <div class="mb-3 col-lg-3 col-md-4">
                <label for="pemindahan" class="form-label">Kebaruan</label>
                <select class="form-select" name="pemindahan" id="pemindahan">
                    <option selected value="baru">Baru</option>
                    <option value="pemindahan">Pemindahan</option>
                </select>
            </div>
            <div class="mb-3 col-lg-9 col-md-8" id="pilih-kolektor">
                <label for="kolektor_id" class="form-label">Nama Kolektor</label>
                <select class="form-select" name="kolektor_id" id="kolektor_id">
                    @foreach ($kolektor as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endcanany
        @can('kolektor')
        <input type="hidden" value="{{ auth()->user()->id }}" name="kolektor_id">
            
        @endcan
        <div class="mb-3">
            <div class="row">
                <div class="col-8">
                    <label for="jenis" class="form-label">Jenis Tabungan</label>
                    <select class="form-select" name="jenis" id="jenis">
                        <option selected value="reguler">Reguler</option>
                        @canany(['administrasi', 'admin'])
                        <option value="program">Program</option>
                        <option value="berjangka">Berjangka</option>
                        @endcanany
                    </select>
                </div>
                <div class="col-4">
                    <label for="bunga" class="form-label">Bunga</label>
                    <select class="form-select" name="bunga" id="bunga">
                        <option id="bunga-val" value="0.002">0,2 %</option>
                    </select>
                </div>
            </div>
        </div>
        <div id="form-reguler" style="display: block">
            
            <div class="mb-3" >
                <label for="total" class="form-label">Tabungan Awal</label>
                <input type="text" class="form-control  @error('total') is-invalid @enderror" id="total"
                name="total" required>
                @error('total')
                    <div class="invalid-feedback">
                    {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- FORM PROGRAM --}}

        <div id="form-program" style="display: none">
            
            <div class="row">
                <div class="col-8">
                    <div class="mb-3">
                        <label for="setoran_tetap" class="form-label">Setoran Tetap</label>
                        <input type="text" class="form-control @error('setoran_tetap') is-invalid @enderror" id="setoran_tetap"
                        name="setoran_tetap" value="{{ old('setoran_tetap')}}">
                        @error('setoran_tetap')
                        <div class="invalid-feedback invalid-program" id="error-m" style="display: block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="lama_program" class="form-label">Lama Program /Tahun
                        </label>
                        <input type="number" class="form-control @error('lama_program') is-invalid @enderror" id="lama_program"
                        name="lama_program" value="{{ old('lama_program')}}">
                        @error('lama_program')
                        <div class="invalid-feedback invalid-program" id="error-m" style="display: block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-8">
                    <label for="tgl_setoran" class="form-label">Tanggal Setoran</label>
                    <input type="date" class="form-control @error('tgl_setoran') is-invalid @enderror" id="tgl_setoran"
                    name="tgl_setoran" value="{{ old('tgl_setoran')}}">
                    @error('tgl_setoran')
                            <div class="invalid-feedback invalid-program" id="error-m" style="display: block">
                                {{ $message }}
                            </div>
                    @enderror
                </div>
                <div class="col-4">
                    <label for="sudah_setor" class="form-label">Sudah Setor / Bulan
                    </label>
                    <input readonly type="number" class="form-control @error('sudah_setor') is-invalid @enderror" id="sudah_setor"
                    name="sudah_setor" value="{{ old('sudah_setor')}}1">
                    @error('sudah_setor')
                    <div class="invalid-feedback invalid-program" id="error-m" style="display: block">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <table class="table table-info" id="perkiraan-program" style="display: none">
            </table>
        </div>

        {{-- FORM BERJANGKA --}}
        <div id="form-berjangka" style="display: none">
            <div class="row">
                <div class="col-8">
                    <div class="mb-3">
                        <label for="jum_deposito" class="form-label">Jumlah Deposito</label>
                        <input type="text" class="form-control" id="jum_deposito"
                        name="jum_deposito">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="lama_jangka" class="form-label">Lama Program /Tahun
                        </label>
                        <input type="number" class="form-control" id="lama_jangka"
                        name="lama_jangka">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-md-6" id="form_bunga_diambil" style="display: none">
                    <div class="mb-3">
                        <label for="bunga_diambil" class="form-label">Bunga Diambil</label>
                        <input type="text" class="form-control" id="bunga_diambil"
                        name="bunga_diambil">
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3">
                        <label for="tgl_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tgl_mulai"
                        name="tgl_mulai">
                    </div>
                </div>
                
            </div>
            <table class="table table-info" id="perkiraan-berjangka" style="display: none">
                <tbody>
                    <tr>
                        <th scope="row" style="width: 60%">Perkiraan Bunga Deposito Per Bulan</th>
                        <td>:</td>
                        <td class="rupiah-text" id="bunga-berjangka"></td>
                    </tr>
                    <tr>
                        <th scope="row">Perkiraan Total Bunga Deposito</th>
                        <td>:</td>
                        <td class="rupiah-text" id="total-bunga-berjangka"></td>
                    </tr>
                    <tr>
                        <th scope="row">Perkiraan Total Tabungan Setelah Jangka Waktu Berakhir</th>
                        <td>:</td>
                        <td class="rupiah-text" id="total-berjangka"></td>
                    </tr>

                </tbody>
            </table>
        </div>
        <div class="tombol-submit d-flex align-items-end">
            <a class="btn btn-outline-primary me-2 ms-auto" href="/dashboard/data-tabungan">Batal</a>
            <span onclick="cek()" class="btn btn-info me-2" id="cek" style="display: none">CEK</span>
            {{-- <button onclick="cek()" class="btn btn-warning me-2">CEK</button> --}}
            <button onclick="cek()" type="submit" class="btn btn-primary">Tambah</button>
        </div>
    </form>
</div>


<script>
    $("#pemindahan").change(function(){
        let val = $("#pemindahan").val() ;
        let pilihan = $("#jenis").val() ;
        if(val == "baru"){
            $('#sudah_setor').val(1);
            $('#sudah_setor').prop('readonly', true);
            $("#form_bunga_diambil").hide();
        }else{
            if(pilihan == 'berjangka'){
                $("#form_bunga_diambil").show();
            }else{
                $("#form_bunga_diambil").hide();
            }
            $('#sudah_setor').prop('readonly', false);
        }
    });

    $( "#jenis" ).change(function() {
    let val = $("#jenis").val()
    let baru = $("#pemindahan").val()
        if(val == "program"){
            $("#form-reguler").hide();
            $("#form-berjangka").hide();
            $("#pilih-kolektor").show();
            $("#form-program").show();
            $("#bunga-val").val(0.005);
            $("#bunga-val").html("0,5 %");
            $("#lama_program").prop("required", true);
            $("#setoran_tetap").prop("required", true);
            $("#tgl_setoran").prop("required", true);
            $("#lama_jangka").prop("required", false);
            $("#jum_deposito").prop("required", false);
            $("#tgl_mulai").prop("required", false);
            $("#total").prop("required", false);
            $("#cek").show();
        }
        else if(val == "berjangka"){
            if(baru == 'baru'){
                $("#form_bunga_diambil").hide();
            }else{
                $("#form_bunga_diambil").show();
            }
            $("#form-reguler").hide();
            $("#form-program").hide();
            $("#pilih-kolektor").hide();
            $("#form-berjangka").show();
            $("#bunga-val").val(0.008);
            $("#bunga-val").html("0,8 %");
            $("#lama_jangka").prop("required", true);
            $("#jum_deposito").prop("required", true);
            $("#tgl_mulai").prop("required", true);
            $("#lama_program").prop("required", false);
            $("#setoran_tetap").prop("required", false);
            $("#tgl_setoran").prop("required", false);
            $("#total").prop("required", false);
            $("#cek").show();
        }else {
            $("#pilih-kolektor").show();
            $("#form-reguler").show();
            $("#form-program").hide();
            $("#form-berjangka").hide();
            $("#bunga-val").val(0.002);
            $("#bunga-val").html("0,2 %");
            $("#total").prop("required", true);
            $("#lama_program").prop("required", false);
            $("#setoran_tetap").prop("required", false);
            $("#tgl_setoran").prop("required", false);
            $("#lama_jangka").prop("required", false);
            $("#jum_deposito").prop("required", false);
            $("#tgl_mulai").prop("required", false);
            $("#cek").hide();
        }
    });
    $("#jum_deposito").keyup(function(){
        let val = $("#jum_deposito").val();
        $("#jum_deposito").val(formatRupiah(val,"Rp."));
    });
    $("#bunga_diambil").keyup(function(){
        let val = $("#bunga_diambil").val();
        $("#bunga_diambil").val(formatRupiah(val,"Rp."));
    });
    $("#setoran_tetap").keyup(function(){
        let val = $("#setoran_tetap").val();
        $("#setoran_tetap").val(formatRupiah(val,"Rp."));
        
    });
    $("#total").keyup(function(){
        let val = $("#total").val()
        $("#total").val(formatRupiah(val,"Rp."));
    });
    function cek (){
        let bunga = $("#bunga").val();
        let val = $("#jenis").val();
        if(val == "program"){
            let lamaProgram = $("#lama_program").val();
            lamaProgram = lamaProgram * 12 ;
            let setoranTetap = $("#setoran_tetap").val();
            setoranTetap = setoranTetap.replace(/\D/g, "");
            setoranTetap = parseInt(setoranTetap);
            $("#perkiraan-berjangka").hide();
            let perkiraanProgram ="<thead><tr><th scope='col' style='text-align : center'>Bulan</th><th scope='col'>Setoran</th><th scope='col'>Bunga</th><th scope='col'>Total Tabungan</th></tr></thead><tbody>" ;
            var totalProgram = 0 ;
            var bungaProgram = 0 ;
            var totalBungaProgram = 0 ;
            for(let i = 1 ; i <= lamaProgram ; i++){
                if( i == 1 ){
                    bungaProgram = setoranTetap * bunga ;
                    bungaProgram = Math.round(bungaProgram);
                }else {
                    bungaProgram = totalProgram * bunga ;
                    bungaProgram = Math.round(bungaProgram);
                }
                totalBungaProgram = totalBungaProgram + bungaProgram ;
                totalProgram = totalProgram + setoranTetap + bungaProgram ;
                totalProgram = Math.round(totalProgram);
                let totalProgramTeks = formatRupiah(totalProgram.toString(),"Rp.");
                let bungaProgramTeks = formatRupiah(bungaProgram.toString(),"Rp.");
                let setoranTetapTeks = formatRupiah(setoranTetap.toString(),"Rp.");
                let bulan = i.toString();
                perkiraanProgram = perkiraanProgram.concat("<tr><th style='text-align : center' width='10%' scope='row'>",bulan,"</th><td>",setoranTetapTeks,"</td><td>",bungaProgramTeks,"</td><td>",totalProgramTeks,"</td></tr>");

            }
            totalBungaProgram = formatRupiah(totalBungaProgram.toString(),"Rp.");
            perkiraanProgram = perkiraanProgram.concat("<tr><th colspan='2'>Akumulasi Bunga Program : </th> <th colspan='2'> ",totalBungaProgram,"</th></tr></tbody>");
            $("#perkiraan-program").html(perkiraanProgram);
            $("#perkiraan-program").show();
        }
        else if(val == "berjangka"){
            $("#perkiraan-program").hide();
            let lamaJangka = $("#lama_jangka").val();
            lamaJangka = lamaJangka * 12 ;
            let jumDeposito = $("#jum_deposito").val().replace(/\D/g, "");
            jumDeposito = parseInt(jumDeposito);
            let bungaPerBulan = bunga * jumDeposito ;
            let bungaTotal = bungaPerBulan * lamaJangka ;
            let totalBerjangka = jumDeposito + bungaTotal ;
            bungaPerBulan = bungaPerBulan.toString();
            bungaTotal = bungaTotal.toString();
            totalBerjangka = totalBerjangka.toString();
            bungaPerBulan = formatRupiah(bungaPerBulan ,"Rp.");
            bungaTotal = formatRupiah(bungaTotal ,"Rp.");
            totalBerjangka = formatRupiah(totalBerjangka ,"Rp.");
            $("#bunga-berjangka").html(bungaPerBulan) ;
            $("#total-bunga-berjangka").html(bungaTotal) ;
            $("#total-berjangka").html(totalBerjangka) ;
            $("#perkiraan-berjangka").show();
        }else {
            $("#perkiraan-program").hide();
            $("#perkiraan-berjangka").hide();
        }
        
    }


</script>
@endsection
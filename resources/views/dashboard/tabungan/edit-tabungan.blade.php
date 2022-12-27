@extends('dashboard.layouts.main')

@section('container')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Perbarui Tabungan</h1>
@php

@endphp
</div>
<div class="col-lg-8 mt-4">
    <form class="mb-3" action="/dashboard/data-tabungan/{{$tabungan->id}}" method="post">
        @method('put')
        @csrf
        <input type="hidden" value="{{ $tabungan->id }}" name="id">
        <div class="mb-3">
            <label for="nama" class="form-label">No Tabungan</label>
            <input type="text" class="form-control" id="nama"
            name="nama"disabled value="{{ $tabungan->no }}">
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Jenis Tabungan</label>
            <input type="text" class="form-control" id="nama"
            name="nama"disabled value="{{ $tabungan->jenis }}">
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Penabung</label>
            <input type="text" class="form-control" id="nama"
            name="nama"disabled value="{{ $tabungan->nasabah->user->nama }}">
        </div>
        @if ($tabungan->jenis != 'berjangka')
        <div class="mb-3">
            <label for="users_id" class="form-label">Nama Kolektor</label>
            <select class="form-select" name="users_id" id="users_id">
                @foreach ($kolektor as $k)
                @if ($tabungan->users_id == $k->id)
                <option selected value="{{ $k->id }}">{{ $k->nama }}</option>
                @else
                <option value="{{ $k->id }}">{{ $k->nama }}</option>  
                @endif
                @endforeach
            </select>
        </div>
        @endif
        @if ($tabungan->jenis == 'reguler')
            
        <div class="mb-3">
            <label for="status" class="form-label">Status Tabungan</label>
            <select class="form-select" name="status" id="status">
                @if ($tabungan->status != "selesai")
                <option selected value="masih berjalan">Masih Berjalan</option>
                <option value="selesai">Selesai</option>
                @else
                <option value="masih berjalan">Masih Berjalan</option>
                <option selected value="selesai">Selesai</option>
                @endif
            </select>
        </div>
        @endif
        <div class="tombol-submit d-flex align-items-end">
            <a class="btn btn-outline-primary me-2 ms-auto" href="/dashboard/data-tabungan">Batal</a>
            {{-- <button onclick="cek()" class="btn btn-primary me-2">CEK</button> --}}
            <button type="submit" class="btn btn-primary">Perbarui</button>
        </div>
    </form>
</div>


<script>

    $( "#jenis" ).change(function() {
    let val = $("#jenis").val()
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
            $("#cek").show();
        }
        else if(val == "berjangka"){
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
            $("#cek").show();
        }else {
            $("#pilih-kolektor").show();
            $("#form-reguler").show();
            $("#form-program").hide();
            $("#form-berjangka").hide();
            $("#bunga-val").val(0.002);
            $("#bunga-val").html("0,2 %");
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
@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <div class="header">
        <h1 class="h2">Riwayat Transaksi</h1>
    </div>
</div>
<div class="form-filter border-bottom mb-2">
    <form action="" method="get" class="me-2 mb-2">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    @canany(['administrasi', 'admin','kasir'])
                    <div class="col-lg-3">
                        <label for="user" class="form-label">Transaksi Oleh</label>
                        <select name="user" id="user" class="form-select form-select">
                            <option value="administrasi"@if ($user=="administrasi") selected @endif>Staf Administrasi</option>
                            @foreach ($kolektor as $k)
                            <option value="{{ $k->id }}"@if ($user== $k->id ) selected @endif>{{ $k->nama }}</option>
                            @endforeach
                            <option value="semua"@if ($user=="semua") selected @endif>Tampilkan Semua</option>
                        </select>
                    </div>
                    @endcanany
                    <div class="col-lg-3">
                        <label for="filter-riwayat" class="form-label">Waktu Transaksi</label>
                        <select name="filter" id="filter-riwayat" class="form-select form-select">
                            <option value="hari ini"@if ($filter=="hari ini") selected @endif> Hari ini</option>
                            <option value="custom"@if ($filter=="custom") selected @endif>Tentukan Jangka Waktu</option>
                            <option value="semua"@if ($filter=="semua") selected @endif>Tampilkan Semua</option>
                        </select>
                    </div>         
                    <div class="col-lg-2 custom-filter">
                        <label for="filter-dari" class="form-label custom-filter">Mulai Dari</label>
                        <input type="date" class="form-control custom-filter" id="filter-dari"
                        name="dari" value="{{ $dari }}">
                    </div>
                    <div class="col-lg-2 custom-filter">
                        <label for="filter-sampai" class="form-label custom-filter">Sampai</label>
                        @php
                            if($sampai != ""){
                                $sampai = date('Y-m-d', strtotime( $sampai . " -1 days"));
                            }
                        @endphp
                        <input type="date" class="form-control custom-filter" id="filter-sampai"
                        name="sampai" value="{{ $sampai }}">
                    </div>
                    <div class="col-lg-2">
                        <button style="margin-top: 1.813rem;" type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>
                </div>
            </div>
        </div>
        
    
    </form>
</div>
@canany(['administrasi','kasir','admin'])
    @if ($user == 'administrasi' || $user =='semua')
        
   
    {{-- TRANSAKSI PENAMBAHAN BUNGA --}}
    @if (count($bungaReguler) > 0)
    <div class="table-riwayat mb-4 mt-3">
        <div class="header-riwayat d-flex">
            <h5 class="h5">Transaksi Penambahan Bunga Tabungan Reguler </h5>
        </div>
        <div class="table-responsive col-lg-8">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Jumlah</th>
                        @can('administrasi')
                        <th scope="col">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($bungaReguler as $br)
                    <tr>
                        <th scope="row">{{ date('d-m-Y h:i:sa',strtotime($br->created_at)) }}</th>
                        <td class="rupiah-text">{{($br->jumlah) }}</td>
                        @can('administrasi')
                        <td>
                            {{-- <a class="btn btn-sm btn-danger me-1" href="/dashboard/transaksi-tabungan/batal-bunga-reguler?id={{ $br->id }}">Batal</a> --}}
                            <form action="/dashboard/transaksi-tabungan/batal-bunga-reguler" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" value="{{ $br->id }}" name="id">
                                <button class="btn btn-sm btn-danger me-1" onclick="return confirm('apakah anda yakin membatalkan transaksi pembagian bunga reguler kepada seluruh nasabah ?')">Batal</button>
                            </form> 
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- TRANSAKSI PEMBAGIAN SHU --}}
    @if (count($shu) > 0)
    <div class="table-riwayat mb-4 mt-3">
        <div class="header-riwayat d-flex">
            <h5 class="h5">Transaksi Pembagian Sisa Hasil Usaha </h5>
        </div>
        <div class="table-responsive col-lg-8">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Tahun</th>
                        <th scope="col">Total</th>
                        <th scope="col">SHU per Anggota</th>
                        @can('administrasi')
                        <th scope="col">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($shu as $s)
                    <tr>
                        <th scope="row">{{ date('d-m-Y h:i:sa',strtotime($s->created_at)) }}</th>
                        <td>{{($s->tahun) }}</td>
                        <td class="rupiah-text">{{($s->total) }}</td>
                        <td class="rupiah-text">{{($s->pembagian_shu) }}</td>
                        @can('administrasi')
                        <td>
                            <form action="/dashboard/transaksi/batal-shu" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" value="{{ $s->id }}" name="id">
                                <button class="btn btn-sm btn-danger me-1" onclick="return confirm('apakah anda yakin membatalkan pembagian sisa hasil usaha kepada seluruh anggota koperasi ?')">Batal</button>
                            </form> 
                            {{-- <a class="btn btn-sm btn-danger me-1" href="/dashboard/transaksi/batal-shu?id={{ $s->id }}">Batal</a> --}}
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif
@endcanany

        @cannot('nasabah')
    

{{-- TRANSAKSI TABUNGAN REGULER --}}
        <div class="table-riwayat mb-4 mt-3">
                <div class="header-riwayat d-flex">
                    <h5 class="h5">Transaksi Tabungan Reguler </h5>
                    <div class="btn-toolbar mb-2 mb-md-0 ms-auto me-4">
                        <form action="">    
                            <input name="cari" id="cari-reguler" class="form-control me-2 form-control-sm" type="search" placeholder="Cari ..." aria-label="Search">
                        </form>
                    </div>
                </div>
                <div id="tabungan-reguler">
                    {!! $tableReguler !!}
                </div>
        </div>

            {{-- TRANSAKSI TABUNGAN PROGRAM --}}
        <div class="table-riwayat mb-4">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Tabungan Program </h5>
                <div class="btn-toolbar mb-2 mb-md-0 ms-auto me-4">
                    <form action="">    
                        <input name="cari" id="cari-program" class="form-control me-2 form-control-sm" type="search" placeholder="Cari ..." aria-label="Search">
                    </form>
                </div>
            </div>
            <div id="tabungan-program">
                {!! $tableProgram !!}
            </div>
        </div>


            {{-- TRANSAKSI TABUNGAN BERJANGKA --}}
        @canany(['administrasi','kasir','admin'])
        @if ($user == 'administrasi' || $user =='semua')
        <div class="table-riwayat mb-4">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Tabungan Berjangka </h5>
                <div class="btn-toolbar mb-2 mb-md-0 ms-auto me-4">
                    <form action="">    
                        <input name="cari" id="cari-berjangka" class="form-control me-2 form-control-sm" type="search" placeholder="Cari ..." aria-label="Search">
                    </form>
                </div>
            </div>
            <div id="tabungan-berjangka">
                {!! $tableBerjangka !!}
            </div>
        </div>

        

            {{-- TRANSAKSI PINJAMAN --}}
        
        <div class="table-riwayat mb-4">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Pinjaman</h5>
                <div class="btn-toolbar mb-2 mb-md-0 ms-auto me-4">
                    <form action="">    
                        <input name="cari" id="cari-pinjaman" class="form-control me-2 form-control-sm" type="search" placeholder="Cari ..." aria-label="Search">
                    </form>
                </div>
            </div>
            <div id="pinjaman">
                {!! $tablePinjaman !!}
            </div>
        </div>
        @if(count($trShu) > 0)
        <div class="table-riwayat mb-4">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Sisa Hasil Usaha</h5>
                <div class="btn-toolbar mb-2 mb-md-0 ms-auto me-4">
                    <form action="">    
                        <input name="cari" id="cari-shu" class="form-control me-2 form-control-sm" type="search" placeholder="Cari ..." aria-label="Search">
                    </form>
                </div>
            </div>
            <div id="shu">
                {!! $tableShu !!}
            </div>
        </div>
        @endif
        @endif
        @endcanany
        @endcannot


        {{-- RIWAYAT NASABAH --}}
        @can('nasabah')
        {{-- TRANSAKSI TABUNGAN REGULER --}}
        <div class="table-riwayat mb-4 mt-3">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Tabungan Reguler</h5>
            </div>
            <div id="tabungan-reguler">
                {!! $tableReguler !!}
            </div>
        </div>
          {{-- TRANSAKSI TABUNGAN PROGRAM --}}
          <div class="table-riwayat mb-4">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Tabungan Program </h5>
            </div>
            <div id="tabungan-program">
                {!! $tableProgram !!}
            </div>
        </div>
             {{-- TRANSAKSI TABUNGAN BERJANGKA --}}
        <div class="table-riwayat mb-4">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Tabungan Berjangka </h5>
            </div>
            <div id="tabungan-berjangka">
                {!! $tableBerjangka !!}
            </div>
        </div>

        {{-- TRANSAKSI PINJAMAN --}}
        <div class="table-riwayat mb-4">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Pinjaman</h5>
            </div>
            <div id="pinjaman">
                {!! $tablePinjaman !!}
            </div>
        </div>
        

        @if(count($trShu) > 0)
        <div class="table-riwayat mb-4">
            <div class="header-riwayat d-flex">
                <h5 class="h5">Transaksi Sisa Hasil Usaha</h5>

            </div>
            <div id="shu">
                {!! $tableShu !!}
            </div>
        </div>
        @endif
        @endcan

    <script>
        $(document).ready(function(){
        var elts = document.getElementsByClassName('rupiah-text');
        for (var i = 0; i < elts.length; ++i) {
            let rp =  elts[i].innerHTML ;  
            rp = formatRupiah(rp,'Rp.') ;
            elts[i].innerHTML = rp;
        }
        if($("#filter-riwayat").val() != "custom"){
            $(".custom-filter").hide()
        }else{
            $(".custom-filter").show()
        }
        })

        $("#filter-riwayat").change(function(){
            if($("#filter-riwayat").val() == "custom"){
                $(".custom-filter").show();
            }else {
                $(".custom-filter").hide()
            }
        })

        /* CARI RIWAYAT */
        $('#cari-reguler').keyup(function(){
        let cari = $('#cari-reguler').val();
        let filter = $('#filter-riwayat').val();
        let dari = $('#filter-dari').val();
        let sampai = $('#filter-sampai').val();
        let user = $("#user").val();
        let jenis = "reguler"
            $.get("{{ URL::to('dashboard/riwayat-transaksi') }}",{user:user,filter:filter,dari:dari,sampai:sampai,cari:cari,jenis:jenis}, function(data){
                $('#tabungan-reguler').html(data);
            })
        });

        $('#cari-program').keyup(function(){
        let cari = $('#cari-program').val();
        let filter = $('#filter-riwayat').val();
        let dari = $('#filter-dari').val();
        let sampai = $('#filter-sampai').val();
        let user = $("#user").val();
        let jenis = "program"
            $.get("{{ URL::to('dashboard/riwayat-transaksi') }}",{user:user,filter:filter,dari:dari,sampai:sampai,cari:cari,jenis:jenis}, function(data){
                $('#tabungan-program').html(data);
            })
        });


        $('#cari-berjangka').keyup(function(){
        let cari = $('#cari-berjangka').val();
        let filter = $('#filter-riwayat').val();
        let dari = $('#filter-dari').val();
        let sampai = $('#filter-sampai').val();
        let user = $("#user").val();
        let jenis = "berjangka"
            $.get("{{ URL::to('dashboard/riwayat-transaksi') }}",{user:user,filter:filter,dari:dari,sampai:sampai,cari:cari,jenis:jenis}, function(data){
                $('#tabungan-berjangka').html(data);
            })
        });
        $('#cari-pinjaman').keyup(function(){
        let cari = $('#cari-pinjaman').val();
        let filter = $('#filter-riwayat').val();
        let dari = $('#filter-dari').val();
        let sampai = $('#filter-sampai').val();
        let user = $("#user").val();
        let jenis = "pinjaman"
            $.get("{{ URL::to('dashboard/riwayat-transaksi') }}",{user:user,filter:filter,dari:dari,sampai:sampai,cari:cari,jenis:jenis}, function(data){
                $('#pinjaman').html(data);
            })
        });

        $('#cari-pinjaman').keyup(function(){
        let cari = $('#cari-shu').val();
        let filter = $('#filter-riwayat').val();
        let dari = $('#filter-dari').val();
        let sampai = $('#filter-sampai').val();
        let user = $("#user").val();
        let jenis = "shu"
            $.get("{{ URL::to('dashboard/riwayat-transaksi') }}",{user:user,filter:filter,dari:dari,sampai:sampai,cari:cari,jenis:jenis}, function(data){
                $('#pinjaman').html(data);
            })
        });

        $(function() {
            $(document).on("click","#pagination-reguler a",function(){
            var url=$(this).attr("href");
            var append=url.indexOf("?")==-1?"?":"&";
            var finalURL=url+append+$("#user").serialize()+append+$("#cari-reguler").serialize()+append+$("#filter-riwayat").serialize()+append+$("#filter-dari").serialize()+append+$("#filter-sampai").serialize()+append+"jenis=reguler";
            window.history.pushState({},null, finalURL);
            
            $.get(finalURL,function(data){
            $("#tabungan-reguler").html(data);
            });
            return false;
            })
        });

        $(function() {
            $(document).on("click","#pagination-program a",function(){
            var url=$(this).attr("href");
            var append=url.indexOf("?")==-1?"?":"&";
            var finalURL=url+append+$("#cari-program").serialize()+append+$("#filter-riwayat").serialize()+append+$("#filter-dari").serialize()+append+$("#filter-sampai").serialize()+append+"jenis=program";
            window.history.pushState({},null, finalURL);
            
            $.get(finalURL,function(data){
            $("#tabungan-program").html(data);
            });
            return false;
            })
        });

        $(function() {
            $(document).on("click","#pagination-berjangka a",function(){
            var url=$(this).attr("href");
            var append=url.indexOf("?")==-1?"?":"&";
            var finalURL=url+append+$("#cari-berjangka").serialize()+append+$("#filter-riwayat").serialize()+append+$("#filter-dari").serialize()+append+$("#filter-sampai").serialize()+append+"jenis=berjangka";
            window.history.pushState({},null, finalURL);
            
            $.get(finalURL,function(data){
            $("#tabungan-berjangka").html(data);
            });
            return false;
            })
        });
        $(function() {
            $(document).on("click","#pagination-pinjaman a",function(){
            var url=$(this).attr("href");
            var append=url.indexOf("?")==-1?"?":"&";
            var finalURL=url+append+$("#cari-pinjaman").serialize()+append+$("#filter-riwayat").serialize()+append+$("#filter-dari").serialize()+append+$("#filter-sampai").serialize()+append+"jenis=pinjaman";
            window.history.pushState({},null, finalURL);
            
            $.get(finalURL,function(data){
            $("#pinjaman").html(data);
            });
            return false;
            })
        });

        $(function() {
            $(document).on("click","#pagination-shu a",function(){
            var url=$(this).attr("href");
            var append=url.indexOf("?")==-1?"?":"&";
            var finalURL=url+append+$("#cari-shu").serialize()+append+$("#filter-riwayat").serialize()+append+$("#filter-dari").serialize()+append+$("#filter-sampai").serialize()+append+"jenis=shu";
            window.history.pushState({},null, finalURL);
            
            $.get(finalURL,function(data){
            $("#shu").html(data);
            });
            return false;
            })
        });
    </script>

    @endsection

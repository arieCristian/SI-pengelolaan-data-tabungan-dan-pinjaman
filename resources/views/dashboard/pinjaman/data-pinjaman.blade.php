
@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Pinjaman Nasabah</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        @can('administrasi')
        <a href="/dashboard/data-pinjaman/create" class="btn btn-primary me-2"><i class="bi bi-plus-lg"></i> Pinjaman</a>
        @endcan
        <form action="" class="me-2">
            {{ csrf_field() }}
                <select name="status" id="status-pinjaman" class="form-select form-select">
                    <option selected value="Belum Lunas">Belum Lunas</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </form>
        <form class="d-flex">
        {{ csrf_field() }}
        <input name="cari" id="cari-pinjaman" class="form-control me-2 form-control-sm" type="search" autofocus placeholder="Cari pinjaman..." aria-label="Search">
        </form>
</div>

</div>
@if (session()->has('success'))
<div class="alert alert-success" role="alert">
    {{ session('success') }}
</div>  
@endif
@if (session()->has('gagal'))
<div class="alert alert-danger" role="alert">
    {{ session('gagal') }}
</div>  
@endif
{{-- DATA PINJAMAN --}}
<div id="table-data-pinjaman" class="table-responsive">   
    <div id="table-data-pinjaman" class="table-responsive">
        {!! $table !!}
    </div>
</div>



<script>
    $(document).ready(function(){
    var elts = document.getElementsByClassName('rupiah-text');
    for (var i = 0; i < elts.length; ++i) {
        let rp =  elts[i].innerHTML ;  
        rp = formatRupiah(rp,'Rp.') ;
        elts[i].innerHTML = rp;
    }
    })

    $('#cari-pinjaman').keyup(function(){
        var status = $('#status-pinjaman').val();
    var cari = $('#cari-pinjaman').val();
        $.get("{{ URL::to('dashboard/data-pinjaman') }}",{status:status,cari:cari}, function(data){
            $('#table-data-pinjaman').html(data);
        })
    });

    $('#status-pinjaman').change(function(){
    var status = $('#status-pinjaman').val();
    var cari = $('#cari-pinjaman').val();
        $.get("{{ URL::to('dashboard/data-pinjaman') }}",{status:status, cari:cari}, function(data){
            $('#table-data-pinjaman').html(data);
        })
    });

    $(function() {
    $(document).on("click","#pagination a",function(){
        var url=$(this).attr("href");
        var append=url.indexOf("?")==-1?"?":"&";
        var finalURL=url+append+$("#cari-pinjaman").serialize();
        window.history.pushState({},null, finalURL);
        
        $.get(finalURL,function(data){
        $("#table-data-pinjaman").html(data);
        });
        return false;
    })
    });
</script>
@endsection


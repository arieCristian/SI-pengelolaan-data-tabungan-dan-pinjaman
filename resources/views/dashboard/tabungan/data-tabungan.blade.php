@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Tabungan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        @canany(['administrasi','kolektor'])
            
        <a href="/dashboard/data-tabungan/create" class="me-2 mb-2 btn btn-primary"><i class="bi bi-plus-lg"></i> Tabungan</a>
        @endcanany
            <form action="" class="me-2 mb-2">
            {{ csrf_field() }}
                <select name="filter" id="filter-tabungan" class="form-select form-select">
                    {{-- <option selected>Tampilkan Semua</option> --}}
                    <option value="reguler">Tabungan Reguler</option>
                    <option value="program">Tabungan Program</option>
                    @canany(['administrasi', 'admin','kasir'],)
                    <option value="berjangka">Tabungan Berjangka</option>
                    @endcanany
                </select>
            </form>
            <form action="" class="me-2 mb-2">
                {{ csrf_field() }}
                    <select name="status" id="status-tabungan" class="form-select form-select">
                        <option value="masih berjalan">Masih Berjalan</option>
                        <option value="selesai">Selesai</option>
                        <option value="semua">Tampilkan Semua</option>
                    </select>
                </form>
          <form class="d-flex">
            {{ csrf_field() }}
            <input name="cari" id="cari-tabungan" class="form-control me-2 mb-2 form-control" type="search" autofocus placeholder="Cari tabungan..." aria-label="Search">
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
<div id="table-data-nasabah" class="table-responsive">
    
    <div id="table-data-tabungan" class="table-responsive">
        {!! $table !!}

        {{-- @foreach ($tabungan as $t)
           <p>aswesa  {{ $t->total }} </p>
        @endforeach --}}
    </div>
        {{-- {{ $nasabah->links() }} --}}

</div>

<script>
    var elts = document.getElementsByClassName('rupiah-text');
    for (var i = 0; i < elts.length; ++i) {
        let rp =  elts[i].innerHTML ;  
        rp = formatRupiah(rp,'Rp.') ;
        elts[i].innerHTML = rp;
    }

    $('#filter-tabungan').change(function(){
    var status = $('#status-tabungan').val();
    var filter = $('#filter-tabungan').val();
    var cari = $('#cari-tabungan').val();
        $.get("{{ URL::to('dashboard/data-tabungan') }}",{filter:filter,status:status, cari:cari}, function(data){
            $('#table-data-tabungan').html(data);
        })
    });

    $('#status-tabungan').change(function(){
    var status = $('#status-tabungan').val();
    var filter = $('#filter-tabungan').val();
    var cari = $('#cari-tabungan').val();
        $.get("{{ URL::to('dashboard/data-tabungan') }}",{filter:filter,status:status, cari:cari}, function(data){
            $('#table-data-tabungan').html(data);
        })
    });
    
    $('#cari-tabungan').keyup(function(){
    var status = $('#status-tabungan').val();
    var cari = $('#cari-tabungan').val();
    var filter = $('#filter-tabungan').val();
        $.get("{{ URL::to('dashboard/data-tabungan') }}",{filter:filter,status:status, cari:cari}, function(data){
            $('#table-data-tabungan').html(data);
        })
    });

    $(function() {
    $(document).on("click","#pagination a",function(){
        var url=$(this).attr("href");
        var append=url.indexOf("?")==-1?"?":"&";
        var finalURL=url+append+$("#filter-tabungan").serialize()+append+$("#status-tabungan").serialize()+append+$("#cari-tabungan").serialize();
        window.history.pushState({},null, finalURL);
        
        $.get(finalURL,function(data){
        $("#table-data-tabungan").html(data);
        });
        return false;
    })
    });
</script>

@endsection

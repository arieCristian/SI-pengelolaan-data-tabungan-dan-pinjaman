@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Nasabah</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        @canany(['administrasi','kolektor'])
        <a href="/dashboard/data-nasabah/create" class="me-2 btn btn-primary mb-2"><i class="bi bi-plus-lg"></i> Nasabah</a>
        @endcannot
            <form action="" class="me-2 mb-2">
            {{ csrf_field() }}
                <select name="filter" id="filter-nasabah" class="form-select form-select">
                    <option selected>Tampilkan Semua</option>
                    <option value="shu">Mempunyai Sisa Hasil Usaha</option>
                    <option value="anggota">Hanaya Anggota</option>
                    <option value="anggota alit">Hanya Anggota Alit</option>
                    <option value="calon anggota">Hanya Calon Anggota</option>
                </select>
            </form>
        <form class="d-flex mb-2">
            <input name="cari" id="cari-nasabah" class="form-control me-2 form-control" type="search" autofocus placeholder="Cari Nasabah..." aria-label="Search">
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
<div id="table-data-nasabah" class="">
    
    <div id="table-data-nasabah" class="">
        {!! $table !!}
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

    $('#filter-nasabah').change(function(){
    var filter = $('#filter-nasabah').val();
    var cari = $('#cari-nasabah').val();
        $.get("{{ URL::to('dashboard/data-nasabah') }}",{filter:filter, cari:cari}, function(data){
            $('#table-data-nasabah').html(data);
        })
    });
    $('#cari-nasabah').keyup(function(){
    var cari = $('#cari-nasabah').val();
    var filter = $('#filter-nasabah').val();
        $.get("{{ URL::to('dashboard/data-nasabah') }}",{filter:filter, cari:cari}, function(data){
            $('#table-data-nasabah').html(data);
        })
    });

    $(function() {
    $(document).on("click","#pagination a",function(){
        var url=$(this).attr("href");
        var append=url.indexOf("?")==-1?"?":"&";
        var finalURL=url+append+$("#filter-nasabah").serialize()+append+$("#cari-nasabah").serialize();
        window.history.pushState({},null, finalURL);
        
        $.get(finalURL,function(data){
        $("#table-data-nasabah").html(data);
        });
        return false;
    })
    });
</script>

@endsection

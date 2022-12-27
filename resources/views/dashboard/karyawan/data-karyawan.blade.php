@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Pengguna</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/dashboard/data-karyawan/create" class="me-2 btn btn-primary"><i class="bi bi-plus-lg"></i> Pengguna</a>
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
<div class="table-responsive">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Nama</th>
                <th scope="col">Pekerjaan</th>
                <th scope="col">No. Telepon</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($karyawan as $n)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $n->nama }}</td>
                <td class="text-capitalize">Staf {{ $n->role }}</td>
                <td>{{ $n->no_telp }}</td>
                <td>
                    
                    <a class="btn btn-sm btn-primary me-lg-1" href="/dashboard/data-karyawan/{{ Crypt::encrypt($n->id) }}"><i class="bi bi-eye-fill"></i></a>
                    <a class="btn btn-sm btn-warning me-lg-1" href="/dashboard/data-karyawan/{{ Crypt::encrypt($n->id) }}/edit"> <i class="bi bi-pencil-square"></i></a> 
                    <form action="/dashboard/data-karyawan/{{ $n->id }}" method="POST" class="d-inline">
                        @method('delete')
                        @csrf
                        <button class="btn btn-sm btn-danger border-0" onclick="return confirm('apakah anda yakin ?')"><i class="bi bi-x-circle-fill"></i></button>
                    </form>                  
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
        $.get("{{ URL::to('dashboard/data-karyawan') }}",{filter:filter, cari:cari}, function(data){
            $('#table-data-karyawan').html(data);
        })
    });
    $('#cari-nasabah').keyup(function(){
    var cari = $('#cari-nasabah').val();
    var filter = $('#filter-nasabah').val();
        $.get("{{ URL::to('dashboard/data-karyawan') }}",{filter:filter, cari:cari}, function(data){
            $('#table-data-karyawan').html(data);
        })
    });

    $(function() {
    $(document).on("click","#pagination a",function(){
        var url=$(this).attr("href");
        var append=url.indexOf("?")==-1?"?":"&";
        var finalURL=url+append+$("#filter-nasabah").serialize()+append+$("#cari-nasabah").serialize();
        window.history.pushState({},null, finalURL);
        
        $.get(finalURL,function(data){
        $("#table-data-karyawan").html(data);
        });
        return false;
    })
    });
</script>

@endsection

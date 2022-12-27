@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Tabungan Baru</h1>

</div>
@if (session()->has('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>  
    @endif

<div class="col-lg-8">
    <form method="post" action="">
        @csrf
        {{ csrf_field() }}
        <div class="mb-3">
            <label for="search" class="form-label">Nama Penabung</label>
            <input placeholder="Cari Nama Nasabah Yang Akan Membuat Tabungan Baru" type="search" class="form-control" id="search" name="search"
            autofocus>
        </div>
    </form>
    <div id="result" class="panel panel-default" style="">
        <ul class="list-group" id="memList"></ul>
    </div>
</div>

<script>

    // function search(val){
    //     var content = document.getElementById("result")


    //         var xhr = new XMLHttpRequest();

    //         xhr.onreadystatechange = function(){
    //             if( xhr.readyState == 4 && xhr.status == 200){
    //                 content.innerHTML = xhr.responseText;
    //             }
    //         }

    //         xhr.open('GET','/dashboard/transaksi-pinjaman/search?search=' + val.value, true ) ;
    //         xhr.send(); 
    // }

    $(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
      
    $('#search').keyup(function(){
        var search = $('#search').val();
        var result = $('#result');
        if(search==""){
            $("#memList").html("");
            $('#result').hide();
        }
        else{
            $.get("{{ URL::to('dashboard/data-tabungan/search') }}",{search:search}, function(data){
                $('#memList').empty().html(data);
                $('#result').show();
            })
        }
    });
});
</script>

@endsection

@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Transaksi Tabungan Berjangka</h1>

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
            <input id="jenis" type="hidden" value="berjangka" name="jenis">
            <label for="search" class="form-label">Cari Tabungan</label>
            <input type="search" class="form-control" id="search" name="search"
            autofocus placeholder="Cari nama penabunga atau nomor tabungan ..">
        </div>
    </form>
    <div id="result" class="panel panel-default" style="">
        <ul class="list-group" id="taabunganList"></ul>
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
        var jenis = $('#jenis').val();
        var result = $('#result');
        // console.log(jenis);
        if(search==""){
            $("#taabunganList").html("");
            $('#result').hide();
        }
        else{
            $.get("{{ URL::to('dashboard/transaksi-tabungan/search') }}",{search:search , jenis:jenis}, function(data){
                $('#taabunganList').empty().html(data);
                $('#result').show();
            })
        }
    });
});
</script>

@endsection

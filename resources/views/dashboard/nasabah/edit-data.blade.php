
@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Data Nasabah</h1>

    
    </div>
    <div class="col-lg-8">
        <form class="mb-4" method="post" action="/dashboard/data-nasabah/{{ $nasabah->id }}" enctype="multipart/form-data">
            @method('put')
            @csrf
            <input type="hidden" id="oldAnggota" value="{{ $nasabah->keanggotaan }}">
            <div class="mb-3">
                <input type="hidden" name="id" value="{{ $nasabah->id }}">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" required value="{{ old('nama', $nasabah->user->nama) }}">
                @error('nama')
                    <div class="invalid-feedback">
                    {{ $message }}
                    </div>
                @enderror
            </div>
            @canany(['admin', 'administrasi', 'kasir']) 
            <div class="mb-3">
                <label for="keanggotaan" class="form-label">Keanggotaan</label>
                <select class="form-select" name="keanggotaan" id="keanggotaan">
                    @if($nasabah->keanggotaan == 'calon anggota')
                    <option selected value="calon anggota">Calon Anggota</option>
                    @else
                    <option value="calon anggota">Calon Anggota</option>
                    @endif
                    @if($nasabah->keanggotaan == 'anggota')
                    <option selected value="anggota">Anggota</option>
                    @else
                    <option value="anggota">Anggota</option>
                    @endif
                    @if($nasabah->keanggotaan == 'anggota alit')
                    <option selected value="anggota alit">Anggota Alit</option>
                    @else
                    <option value="anggota alit">Anggota Alit</option>
                    @endif
                </select>
            </div>
            <div class="mb-3">
                <label for="kolektor" class="form-label">Kolektor</label>
                <select class="form-select" name="kolektor" id="kolektor">
                    @if ($ada == null)
                    <option selected value="{{ $nasabah->kolektor }}"> - </option>
                    @endif
                    @foreach ($kolektor as $k)
                    @if ($nasabah->kolektor == $k->id)
                    <option selected value="{{ $k->id }}">{{ $k->nama }}</option>
                    @else
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            @endcanany
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" required value="{{ old('username', $nasabah->user->username) }}">
                @error('username')
                    <div class="invalid-feedback">
                    {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}">
                @error('password')
                    <div class="invalid-feedback">
                    {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Ulangi Password</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}">
                @error('password_confirmation')
                    <div class="invalid-feedback">
                    {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="no_telp" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" required value="{{ old('no_telp', $nasabah->user->no_telp) }}">
                @error('no_telp')
                    <div class="invalid-feedback">
                    {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" required value="{{ old('alamat', $nasabah->alamat) }}">
                @error('alamat')
                    <div class="invalid-feedback">
                    {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="tombol-submit d-flex align-items-end">
                <a class="btn btn-outline-primary me-2 ms-auto" href="/dashboard/data-nasabah">Batal</a>
                <button type="submit" class="btn btn-primary" id="perbarui">Perbarui</button>
            </div>
        </form>
    </div>


<script>
window.onload = function() {
    var lama = $("#keanggotaan").val();
    var box, oldValue='';
    box = document.getElementById('keanggotaan');
    if (box.addEventListener) {
        box.addEventListener("change", changeHandler, false);
    }
    else if (box.attachEvent) {
        box.attachEvent("onchange", changeHandler);
    }
    else {
        box.onchange = changeHandler;
    }
    function changeHandler(event) {
        var index, newValue;
        index = this.selectedIndex;
        if (index >= 0 && this.options.length > index) {
            newValue = this.options[index].value;
        }
        if(lama == "anggota alit" && newValue == "anggota"){
            var answer = confirm("Apakah benar Nasabah ini telah membayar untuk pendaftaran untuk menjadi Anggota Koperasi ?");
        }else if(lama == "anggota alit" && newValue == "calon anggota"){
            var answer = confirm("Apakah ada sebuah kesalahan saat menambahkan Nasabah ini menjadi seorang Anggota Alit Koperasi, sehingga diturunkan menjadi Calon Anggota Koperasi ?");
        }else if(lama == "anggota" && newValue == "calon anggota"){
            var answer = confirm("Apakah ada sebuah kesalahan saat menambahkan Nasabah ini menjadi seorang Anggota Koperasi, sehingga diturunkan menjadi Calon Anggota Koperasi ?");
        }else if(lama == "anggota" && newValue == "anggota alit"){
            var answer = confirm("Apakah ada sebuah kesalahan saat menambahkan Nasabah ini menjadi seorang Anggota Koperasi, sehingga diturunkan menjadi Anggota Alit Koperasi ?");
        }else if(lama == "calon anggota" && newValue == "anggota"){
            var answer = confirm("Apakah benar Nasabah ini telah membayar untuk pendaftaran untuk menjadi Anggota Koperasi ?");
        }else if(lama == "calon anggota" && newValue == "anggota alit"){
            var answer = confirm("Apakah benar Nasabah ini telah membayar untuk pendaftaran untuk menjadi Anggota Alit Koperasi ?");
        }

        if(answer)
        {
            oldValue = newValue;
        }else{
            box.value = lama;
        }
    }
}



// var baru,lama
// $(function() {
//     lama = $("#keanggotaan").val()
//     baru = $("#keanggotaan").val()
//     console.log(baru,lama)
// })

// $("#keanggotaan").change(function(){
//     baru = $("#keanggotaan").val()
//     console.log(baru,lama)
// })


//     if(lama != baru){
//         $("#perbarui").confirm({
//         title:"Delete confirmation",
//         text:"This is very dangerous, you shouldn't do it! Are you really really sure?",
//         confirm: function(button) {
//             $("#keanggotaan").val(baru).change()
//             alert("You just confirmed.");
//         },
//         cancel: function(button) {
//             $("#keanggotaan").val(lama).change()
//             alert("You aborted the operation.");
//         },
//         confirmButton: "Yes I am",
//         cancelButton: "No"
//         });
//     }
            
</script>

@endsection





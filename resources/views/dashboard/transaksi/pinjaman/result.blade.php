

<div id="content">
    
    @if(count($peminjam) > 0)
    @foreach($peminjam as $p)
    <ul>
        <li class="list-group-item">
            {{-- <a href="{{ url('/dashboard/transaksi-pinjaman/create?id='.$p->id) }}"><span class="badge bg-secondary me-2">{{ $p->id }}</span>{{ $p->nasabah->user->nama }} </a> --}}
            <form action="/dashboard/transaksi-pinjaman/create" method="GET" class="d-inline">
                @csrf
                <input type="hidden" value="{{ Crypt::encrypt($p->id) }}" name="id">
                <button style="width: 100% ; text-align :left ;border-left : 2px solid #007bff;" class="btn btn-light btn-sm text-left"><span class="badge bg-secondary me-2">{{ $p->id }}</span>{{ $p->nasabah->user->nama }}</button>
            </form> 
        </li>

    </ul>
    @endforeach
    @else
    <ul>
        <li class="list-group-item">No Results Found</li>
    </ul>

    @endif

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


    
//     function tampilPeminjam(val){
//         var peminjam = val ;
//         var content = $('#content');
//         var xhr = new XMLHttpRequest();

//         xhr.onreadystatechange = function(){
//             if( xhr.readyState == 4 && xhr.status == 200){
//                 content.innerHTML = xhr.responseText;
//             }
//         }
//         xhr.open('GET','dashboard/transaksi-pinjaman/buat.php?id=' + val.value, true ) ;
//         xhr.send();   
//     }
// </script>

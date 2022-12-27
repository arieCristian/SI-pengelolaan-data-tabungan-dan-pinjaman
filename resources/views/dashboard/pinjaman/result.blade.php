

<div id="content">
    
    @if(count($nasabah) > 0)
    @foreach($nasabah as $p)
    <ul>        
        <li class="list-group-item">
            {{-- <a class="text-decoration-none" href="{{ url('/dashboard/data-pinjaman/buat?id='.$p->id) }}"><span class="badge bg-secondary me-2">{{ $p->id }}</span>{{ $p->user->nama }}</a> --}}
            <form action="/dashboard/data-pinjaman/buat" method="GET" class="d-inline">
                @csrf
                <input type="hidden" value="{{ Crypt::encrypt($p->id) }}" name="id">
                <button style="width: 100% ; text-align :left ; border-left : 2px solid #007bff;" class="btn btn-light btn-sm text-left">{{ $p->user->nama }}</button>
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

// </script>

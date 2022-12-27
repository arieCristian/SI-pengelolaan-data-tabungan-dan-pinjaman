

<div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Keanggotan</th>
                    <th scope="col">Sisa Hasil Usaha</th>
                    <th scope="col">Aksi</th>
            </thead>
            <tbody>
                @foreach ($nasabah as $n)
                @php
                $tgl  = date_create($n->created_at);//8
                $now = date('Y-m-d');
                $now = date_create($now); // waktu sekarang
                $diff  = date_diff( $tgl, $now );
                $selisih = $diff->days ;
                @endphp
                <tr>
                    <td>
                        {{ $n->user->nama }} 
                    </td>
                    <td>{{ $n->alamat }}</td>
                    <td class="text-capitalize">{{ $n->keanggotaan }}</td>
                    @if ($n->shu < 0)
                    <td> - <span class="rupiah-text">{{ $n->shu }}</span></td>
                    @else
                    <td class="rupiah-text">{{ $n->shu }}</td>
                        
                    @endif
                    <td>
                        <a class="btn btn-sm btn-primary me-lg-1" href="/dashboard/data-nasabah/{{ Crypt::encrypt($n->id) }}"><i class="bi bi-eye-fill"></i></a>
                        @can('kolektor')
                            
                        <a class="btn btn-sm btn-warning me-lg-1" href="/dashboard/data-nasabah/{{ Crypt::encrypt($n->id) }}/edit"> <i class="bi bi-pencil-square"></i></a>
                        @endcan
                        @can('administrasi')
                        <a class="btn btn-sm btn-warning me-lg-1" href="/dashboard/data-nasabah/{{ Crypt::encrypt($n->id) }}/edit"> <i class="bi bi-pencil-square"></i></a>
                        <form action="/dashboard/data-nasabah/ambil-shu" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" value="{{ $n->id }}" name="id">
                            @if ($n->shu > 0)
                            <button class="btn btn-sm btn-success border-0 me-lg-1" onclick="return confirm('{{ $n->user->nama }} mengambil SHU sekarang ?')"><i class="bi bi-check-lg"></i></button>
                            {{-- <a class="btn btn-sm btn-success border-0 me-lg-1" href="/dashboard/data-nasabah/ambil-shu?id={{ $n->id }}"> <i class="bi bi-check-lg"></i></a> --}}
                            @else 
                            <button disabled class="btn btn-sm btn-secondary border-0 me-lg-1"><i class="bi bi-check-lg"></i></button>
                            {{-- <a aria-disabled="true" class="btn btn-sm btn-secondary border-0 me-lg-1" href="/dashboard/data-nasabah/ammbil-shu?id={{ $n->id }}"> <i class="bi bi-check-lg"></i></a> --}}
                            @endif
                        </form> 
                        <form action="/dashboard/data-nasabah/{{ $n->id }}" method="POST" class="d-inline">
                            @method('delete')
                            @csrf
                            <button class="btn btn-sm btn-danger border-0" onclick="return confirm('apakah anda yakin ?')"><i class="bi bi-x-circle-fill"></i></button>
                        </form>                  
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table class="table border-top">
            <thead>
                <tr>
                    <th></th>
                    <th colspan="3">TOTAL NASABAH</th>
                    <th colspan="2">{{ $total }} Nasabah</th>
                    <table></table>
                </tr>
            </thead>
        </table>
        <div id="pagination">
            {{ $nasabah->links() }}
        </div>
</div>

<script>
     var elts = document.getElementsByClassName('rupiah-text');
    for (var i = 0; i < elts.length; ++i) {
        let rp =  elts[i].innerHTML ;  
        rp = formatRupiah(rp,'Rp.') ;
        elts[i].innerHTML = rp;
    }
</script>


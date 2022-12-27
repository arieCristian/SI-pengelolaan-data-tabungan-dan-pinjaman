<div class="table-responsive">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
            <th scope="col">Tanggal</th>
            {{-- <th scope="col">No</th> --}}
            @cannot('nasabah')
            <th scope="col">Penabung</th>
            @endcannot
            @can('nasabah')
            <th scope="col">Transaksi Oleh</th>
            @endcan
            <th scope="col">Jenis</th>
            <th scope="col">Jumlah</th>
            <th scope="col">Bunga</th>
            <th scope="col">Tabungan Awal</th>
            <th scope="col">Tabungan Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tabunganProgram as $tp)
            <tr>
                <th scope="row">{{ date('d-m-Y h:i:sa',strtotime($tp->created_at)) }}</th>
                {{-- <td>{{ $tp->tabungan->no }}</td> --}}
                @cannot('nasabah')
                <td>{{ $tp->tabungan->nasabah->user->nama }}</td>
                @endcannot
                @can('nasabah')
                @if ($tp->user->role =='administrasi')
                <td>Staf Administrasi</td>
                @else
                <td>{{ $tp->user->nama }}</td>
                @endif
                @endcan
                <td>{{($tp->jenis) }}</td>
                <td class="rupiah-text">{{($tp->jumlah) }}</td>
                <td class="rupiah-text">{{($tp->bunga) }}</td>
                <td class="rupiah-text">{{($tp->tabungan_awal) }}</td>
                <td class="rupiah-text">{{($tp->tabungan_akhir) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div id="pagination-program">
        {{ $tabunganProgram->links() }}
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
</script>
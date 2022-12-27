<div class="table-responsive">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
            <th scope="col">Tanggal</th>
            {{-- <th scope="col">No</th> --}}
            @cannot('nasabah')
            <th scope="col">Peminjam</th>
            @endcannot
            @can('nasabah')
            <th scope="col">Transaksi Oleh</th>
            @endcan
            <th scope="col">Jenis</th>
            <th scope="col">Jumlah</th>
            <th scope="col">Bunga Pinjaman</th>
            <th scope="col">Angsuran Pinjaman</th>
            <th scope="col">Sisa Pinjaman</th>
            </tr>
        </thead>
        <tbody>
        
            @foreach ($pinjaman as $p)
            <tr>
                <th scope="row">{{ date('d-m-Y h:i:sa',strtotime($p->created_at)) }}</th>
                {{-- <td>{{ $p->pinjaman->id }}</td> --}}
                @cannot('nasabah')
                <td>{{ $p->pinjaman->nasabah->user->nama }}</td>
                @endcannot
                @can('nasabah')
                <td> Staf Administrasi</td>
                @endcan
                <td>{{($p->jenis) }}</td>
                @if ($p->jenis != 'penambahan waktu angsuran')
                        <td class="rupiah-text">{{($p->jumlah) }}</td>
                        @else
                        <td>{{($p->jumlah) }} Bulan</td>
                        @endif
                <td class="rupiah-text">{{($p->bunga) }}</td>
                <td class="rupiah-text">{{($p->angsuran) }}</td>
                <td class="rupiah-text">{{($p->sisa_pinjaman) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div id="pagination-reguler">
        {{ $pinjaman->links() }}
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
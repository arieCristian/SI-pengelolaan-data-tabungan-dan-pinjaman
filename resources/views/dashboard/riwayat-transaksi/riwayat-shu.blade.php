<div class="table-responsive">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
            <th scope="col">Tanggal</th>
            @cannot('nasabah')
            <th scope="col">Nama</th>
            @endcannot
            <th scope="col">Jenis</th>
            <th scope="col">Jumlah</th>
            </tr>
        </thead>
        <tbody>
        
            @foreach ($trShu as $p)
            <tr>
                <th scope="row">{{ date('d-m-Y h:i:sa',strtotime($p->created_at)) }}</th>
                @cannot('nasabah')
                <td class="text-capitalize">{{ $p->nasabah->user->nama }}</td>
                @endcannot
                <td class="text-capitalize">{{($p->jenis) }}</td>
                <td class="rupiah-text">{{($p->jumlah) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div id="pagination-shu">
        {{ $trShu->links() }}
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
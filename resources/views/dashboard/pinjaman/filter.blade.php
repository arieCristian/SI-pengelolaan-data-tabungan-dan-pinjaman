

<div id="table-responsive">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
            <th scope="col">Nama</th>
            <th scope="col">Tanggal Angsuran</th>
            <th scope="col">Estimasi</th>
            <th scope="col">Angsuran Sekarang</th>
            <th scope="col">Sisa Angsuran</th>
            <th scope="col">Sisa Pinjaman</th>
            <th scope="col">Status</th>
            <th scope="col">Aksi</th>
        </thead>
        <tbody>
            @php
                function rupiah($angka){
                $format_rupiah = "Rp." . number_format($angka,2,',','.');
                return $format_rupiah;
            }
            
            @endphp
            @foreach ($pinjaman as $p)
            @php
                $bunga_harian = $p->bunga_dibayar /30 ;
                $tgl  = date_create($p->tgl_angsuran);//8
                $now = date('Y-m-d');
                $now = date_create($now); // waktu sekarang
                $diff  = date_diff( $tgl, $now );
                $selisih = $diff->days ;
                $selisihHari = 0 ;
                if($selisih == 0){
                    $estimasi_pembayaran = "Hari ini";
                    $selisihHari = 0 ;
                }
                if ($tgl > $now) {
                    $estimasi_pembayaran = $selisih . " Hari Lagi";
                    $selisihHari = $selisihHari - $selisih ;
                }
                if($tgl < $now){
                    $estimasi_pembayaran = "Terlambat " . $selisih . " Hari";
                    $selisihHari = $selisihHari + $selisih ;
                }
                $bunga_harian = $bunga_harian * $selisihHari ;
            @endphp
                <tr class="{{ ($selisihHari > 0 ) ? 'table-danger' : '' }}">
                <td>{{ $p->nasabah->user->nama }}</td>
                @if ($p->tgl_angsuran != null)
                    
                <td>{{ date("d-m-Y",strtotime($p->tgl_angsuran)) }}</td>
                <td>{{ $estimasi_pembayaran }}</td>
                @else
                    <td></td>
                    <td></td>    
                @endif
                <td class="rupiah-text">{{(ceil($p->angsuran_pokok + $p->bunga_dibayar + $bunga_harian)) }}</td>
                <td>{{ $p->lama_angsuran - $p->sudah_mengangsur }} x </td>
                <td class="rupiah-text">{{($p->sisa_pinjaman) }}</td>
                <td>{{ $p->status }}</td>
                <td>
                    <a class="btn btn-primary btn-sm me-1" href="/dashboard/data-pinjaman/{{ Crypt::encrypt($p->id) }}"><i class="bi bi-eye-fill"></i></a>
                    @can('administrasi')
                    <a class="btn btn-sm btn-warning me-1" href="/dashboard/data-pinjaman/{{ Crypt::encrypt($p->id) }}/edit"> <i class="bi bi-pencil-square"></i></a>
                    <form action="/dashboard/data-pinjaman/{{ $p->id }}" method="POST" class="d-inline">
                        @method('delete')
                        @csrf
                        <button class="btn btn-sm btn-danger" onclick="return confirm('apakah anda yakin ingin menghapus pinjaman ini ?')"><i class="bi bi-x-circle-fill"></i></button>
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
                    <th colspan="3">TOTAL SISA PINJAMAN</th>
                    <th colspan="2" class="rupiah-text">{{ $total }}</th>
                    <table></table>
                </tr>
            </thead>
        </table>
    <div id="pagination">
        {{ $pinjaman->links() }}
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


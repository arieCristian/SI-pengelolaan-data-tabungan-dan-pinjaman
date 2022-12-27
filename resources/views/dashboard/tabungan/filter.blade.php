

<div id="content">
    <div class="table-responsive">
    <table class="table table-hover table-sm">

        {{-- TABUNGAN REGULER --}}

        @if ($filter == 'reguler')
            
        <thead>
            <tr>
                <th scope="col">Nama</th>
                @if (auth()->user()->role != 'kolektor')
                <th scope="col">Dipungut Oleh</th>
                @else
                <th scope="col">Alamat</th>
                @endif
                <th scope="col">Bunga Tabungan</th>
                <th scope="col">Total Tabungan</th>
                <th scope="col">Aksi</th>
        </thead>
        <tbody>
            @foreach ($tabungan as $t)
            <tr>
                <td>{{ $t->nasabah->user->nama }}</td>
                @if (auth()->user()->role != 'kolektor')
                <td>{{ $t->user->nama }}</td>
                @else
                <td>{{ $t->nasabah->alamat }}</td>
                @endif
                <td>{{ $t->bunga * 100 }} %</td>
                <td class="rupiah-text">{{ $t->total }}</td>
                <td>
                    <a class="btn btn-sm btn-primary me-1" href="/dashboard/data-tabungan/{{ Crypt::encrypt($t->id) }}"><i class="bi bi-eye-fill"></i></a>
                    @can('administrasi')
                    <a class="btn btn-sm btn-warning me-1" href="/dashboard/data-tabungan/{{ Crypt::encrypt($t->id) }}/edit"> <i class="bi bi-pencil-square"></i></a>
                    <form action="/dashboard/data-tabungan/{{ $t->id }}" method="POST" class="d-inline">
                        @method('delete')
                        @csrf
                        <button class="btn btn-sm btn-danger border-0" onclick="return confirm('apakah anda yakin ingin menghapus tabungan ini ?')"><i class="bi bi-x-circle-fill"></i></button>
                    </form>
                    @endcan
                </tr>
            @endforeach
        </tbody>

        {{-- TABUNGAN BERJANGKA --}}

        @elseif($filter == 'berjangka')
        <thead>
            <tr>
                <th scope="col">Nama</th>
                {{-- <th scope="col">Alamat</th> --}}
                <th scope="col">Total Deposito</th>
                <th scope="col">Tanggal Mulai</th>
                <th scope="col">Tanggal Berakhir</th>
                <th scope="col">Dapat Ditarik</th>
                <th scope="col">Aksi</th>
        </thead>
        <tbody>
            @foreach ($tabungan as $t)
            @php
            $tgl_selesai  = date_create($t->tgl_selesai);//8
            $tgl_mulai = date_create($t->tgl_mulai);
            $now = date('Y-m-d');
            $now = date_create($now); // waktu sekarang
            $selesai = $now->diff($tgl_selesai);
            $sekarangBerjangka = date('Y-m-d');
            $sekarangBerjangka = strtotime($sekarangBerjangka);
            $selesaiBerjangka = strtotime($t->tgl_selesai);
            // $selesai_dalam  = date_diff( $tglMulai, $now );
            // $selisih = $diff->format('%m months');
            // Bunga Dapat Ditarik
            $jarak = $now->diff($tgl_mulai);
            $berjalan = $jarak->y * 12 ;
            $berjalan = $berjalan + $jarak->m ;
            if($berjalan > $t->lama_program){
                $berjalan = $t->lama_program;
            }
            $bunga_dpt_ditarik = (($t->bunga * $t->jum_deposito)*$berjalan)- intval($t->bunga_diambil) ;
            if($bunga_dpt_ditarik < 0){
                $bunga_dpt_ditarik = 0;
            }

            @endphp
            <tr class="{{ ($selesaiBerjangka  <= $sekarangBerjangka ) ? 'table-success' : '' }}">
                <td>{{ $t->nasabah->user->nama }}</td>
                {{-- <td>{{ $t->nasabah->alamat }}</td> --}}
                <td class="rupiah-text">{{ $t->jum_deposito }}</td>
                <td>{{ date('d-m-Y',strtotime($t->tgl_mulai)) }}</td>
                <td>{{ date('d-m-Y',strtotime($t->tgl_selesai)) }}</td>
                <td class="rupiah-text">{{ $bunga_dpt_ditarik }}</td>
                <td>
                    <a class="btn btn-sm btn-primary me-1" href="/dashboard/data-tabungan/{{ Crypt::encrypt($t->id) }}"><i class="bi bi-eye-fill"></i></a>
                    @can('administrasi')
                    <form action="/dashboard/data-tabungan/{{ $t->id }}" method="POST" class="d-inline">
                        @method('delete')
                        @csrf
                        <button class="btn btn-sm btn-danger border-0" onclick="return confirm('apakah anda yakin ingin menghapus tabungan ini ?')"><i class="bi bi-x-circle-fill"></i></button>
                    </form>
                    @endcan
                </td>
                
            </tr>
            @endforeach
        </tbody>

        {{-- TABUNGAN PROGRAM --}}

        @elseif($filter == 'program')
        <thead>
            <tr>
                <th scope="col">Nama</th>
                @if (auth()->user()->role != 'kolektor')
                <th scope="col">Dipungut Oleh</th>
                @endif
                <th scope="col">Setoran Tetap</th>
                <th scope="col">Sisa Waktu</th>
                <th scope="col">Setoran Berikutnya</th>
                <th scope="col">Tabungan</th>
                <th scope="col">Aksi</th>
        </thead>
        <tbody>
            @foreach ($tabungan as $t)
            @php
            $tgl  = date_create($t->tgl_setoran);//8
            $now = date('Y-m-d');
            $now = date_create($now); // waktu sekarang
            $diff  = date_diff( $tgl, $now );
            $created = $t->created_at ;
            $baru = date_diff($created, $now);
            $selisih = $diff->days ;
            $selisihHari = 0 ;
            if($selisih == 0){
                $setoran = "Hari ini";
                $selisihHari = 0 ;
            }
            if ($tgl > $now) {
                $setoran = $selisih . " Hari Lagi";
                $selisihHari = $selisihHari - $selisih ;
            }
            if($tgl < $now){
                $setoran = "Terlambat " . $selisih . " Hari";
                $selisihHari = $selisihHari + $selisih ;
            }

            @endphp
            <tr class="{{ ($selisihHari > 0 ) ? 'table-danger' : '' }} {{ ($t->tgl_setoran == null ) ? 'table-success' : '' }} {{ ($selisihHari == 0 && $t->tgl_setoran != null) ? 'table-warning' : '' }} ">
                <td class="">{{ $t->nasabah->user->nama }}</td>
                @if (auth()->user()->role != 'kolektor')
                <td class="">{{ $t->user->nama }}</td>
                @endif
                <td class="rupiah-text ">{{ $t->setoran_tetap }}</td>
                @if ($t->lama_program != $t->sudah_setor)
                <td class="">
                    {{ $t->lama_program - $t->sudah_setor }} Bulan
                </td>
                    @else
                    
                    @endif
                    @if ($t->tgl_setoran != null)
                    <td  class="">
                        {{ date('d-m-Y',strtotime($t->tgl_setoran)) }}
                    </td>    
                    @else
                    <td colspan="2">Dapat Ditarik <b>{{ date('d-m-Y',strtotime($t->tgl_selesai)) }}</b></td>
                    @endif
               
                <td class="rupiah-text ">{{ $t->total }}</td>
                <td class="">
                    <a class="btn btn-sm btn-primary me-1" href="/dashboard/data-tabungan/{{ Crypt::encrypt($t->id) }}"><i class="bi bi-eye-fill"></i></a>
                @can('administrasi')
                <a class="btn btn-sm btn-warning me-1" href="/dashboard/data-tabungan/{{ Crypt::encrypt($t->id) }}/edit"> <i class="bi bi-pencil-square"></i></a>
                <form action="/dashboard/data-tabungan/{{ $t->id }}" method="POST" class="d-inline">
                    @method('delete')
                    @csrf
                    <button class="btn btn-sm btn-danger border-0" onclick="return confirm('apakah anda yakin ingin menghapus tabungan ini ?')"><i class="bi bi-x-circle-fill"></i></button>
                </form>
                @endcan
                </td>
                {{-- <td>{{ $selesai->y." tahun, ". $selesai->m. " Bulan ". $selesai->d." Hari" }}</td> --}}
                
            </tr>
            @endforeach
        </tbody>
        @endif
    </table>
    </div>
    <table class="table border-top">
        <thead>
            <tr>
                <th></th>
                <th colspan="3">TOTAL TABUNGAN</th>
                <th colspan="2" class="rupiah-text">{{ $total }}</th>
                <table></table>
            </tr>
        </thead>
    </table>
    <div id="pagination">
        {{ $tabungan->links() }}
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


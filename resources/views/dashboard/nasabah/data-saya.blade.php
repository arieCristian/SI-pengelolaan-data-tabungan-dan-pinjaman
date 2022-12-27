@extends('dashboard.layouts.main')

@section('container')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Informasi Saya</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <form class="d-inline-block" action="/setting" method="GET">
            @csrf
            <button class="btn btn-warning d-inline-block me-1" type="submit"><i class="bi bi-gear"></i> Edit Data</button>
        </form>
    </div>
</div>

<div class="col-lg-8">
    <table class="table">
        <tbody>
            <tr>
                <th scope="row">Nama</th>
                <td>:</td>
                <td>{{ $nasabah->user->nama }}</td>
            </tr>
            <tr>
                <th scope="row">Jenis Keanggotan</th>
                <td>:</td>
                <td class="text-capitalize">{{ $nasabah->keanggotaan }}</td>
            </tr>
            <tr>
                <th scope="row">Alamat</th>
                <td>:</td>
                <td>{{ $nasabah->alamat }}</td>
            </tr>
            <tr>
                <th scope="row">Nomor Telepon</th>
                <td>:</td>
                <td>{{ $nasabah->user->no_telp }}</td>
            </tr>
            <tr>
                <th scope="row">Sisa Hasil Usaha</th>
                <td>:</td>
                @if ($nasabah->shu < 0) <td> - <span class="rupiah-text">{{ $nasabah->shu }}</span></td>
                    @else
                    <td class="rupiah-text">{{ $nasabah->shu }}</td>

                    @endif
            </tr>




        </tbody>
    </table>
    <div class="card p-3 mb-4 mt-4">
        <p class="fw-2 fw-bolder">Informasi Tabungan dan Pinjaman</p>
        <table class="table table-borderless">
            <tbody>
                @if (count($tabungan) > 0)
                @foreach ($tabungan as $t)
                <tr>
                    @if ($t->jenis == "reguler")
                    <td>{{ $t->no }}</td>
                    <td>Tabungan Reguler</td>
                    <td>:</td>
                    <td class="rupiah-text">{{ $t->total }}</td>
                    <td><a href="/dashboard/data-tabungan/{{ Crypt::encrypt($t->id) }}" class="btn btn-sm btn-primary">Detail</a></td>
                    @elseif($t->jenis == "program")
                    @if ($t->status != "selesai")
                    <td>{{ $t->no }}</td>
                    <td>Tabungan Program</td>
                    <td>:</td>
                    <td>Berakhir Dalam {{ $t->lama_program - $t->sudah_setor }} Bulan</td>
                    <td><a href="/dashboard/data-tabungan/{{ Crypt::encrypt($t->id) }}" class="btn btn-sm btn-primary">Detail</a></td>
                    @endif
                    @else
                    @if ($t->status != "selesai")
                    <td>{{ $t->no }}</td>
                    <td>Tabungan Berjangka</td>
                    <td>:</td>
                    <td>Berakhir Pada {{ date('d-m-Y',strtotime($t->tgl_selesai)) }}</td>
                    <td><a href="/dashboard/data-tabungan/{{ Crypt::encrypt($t->id) }}" class="btn btn-sm btn-primary">Detail</a></td>
                    @endif
                    @endif
                </tr>
                @endforeach
                @endif

                @if (count($pinjaman) > 0)
                @foreach ($pinjaman as $p)
                @if ($p->status != "Lunas")
                    <td >{{ $p->id }}</td>
                    <td>Sisa Pinjaman Sebanyak</td>
                    <td>:</td>
                    <td class="rupiah-text">{{ $p->sisa_pinjaman }}</td>
                    <td><a href="/dashboard/data-pinjaman/{{ Crypt::encrypt($p->id) }}" class="btn btn-sm btn-primary">Detail</a></td>
                @endif    
                @endforeach
                @endif
            </tbody>
        </table>
        
    </div>
</div>
<div>
    <h5>Riwayat Sisa Hasil Usaha</h5>
    <div class="col-lg-8">
        <table class="table table-info">
            <thead>
                <tr>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Jenis</th>
                    <th scope="col">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi as $t)
                <tr>
                    <th scope="row">{{ date('d-M-Y',strtotime($t->created_at)) }}</th>
                    <td class="text-capitalize">{{($t->jenis) }}</td>
                    <td class="rupiah-text">{{($t->jumlah) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
</script>

@endsection

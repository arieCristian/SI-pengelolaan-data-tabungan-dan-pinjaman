@extends('dashboard.layouts.main')

@section('container')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Pengguna</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <form class="d-inline-block" action="/dashboard/data-karyawan" method="GET">
            @csrf
            <button class="btn btn-danger d-inline-block me-1" type="submit"> Kembali</button>
        </form>
    </div>
</div>

<div class="col-lg-8">
    <table class="table">
        <tbody>
            <tr>
                <th scope="row">Nama</th>
                <td>:</td>
                <td>{{ $user->nama }}</td>
            </tr>
            <tr>
                <th scope="row">Pekerjaan</th>
                <td>:</td>
                <td class="text-capitalize">Staf {{ $user->role }}</td>
            </tr>
            <tr>
                <th scope="row">Nomor Telepon</th>
                <td>:</td>
                <td>{{ $user->no_telp }}</td>
            </tr>
            @if ($user->role == 'kolektor')
            <tr>
                <th scope="row">Jumlah Tabungan Reguler</th>
                <td>:</td>
                <td>{{ $tabungan['reguler'] }} Tabungan</td>
            </tr>
            <tr>
                <th scope="row">Total Tabungan Reguler</th>
                <td>:</td>
                <td class="rupiah-text">{{ $tabungan['total_reguler'] }}</td>
            </tr>
            <tr>
                <th scope="row">Jumlah Tabungan Program</th>
                <td>:</td>
                <td>{{ $tabungan['program'] }} Tabungan</td>
            </tr>
            <tr>
                <th scope="row">Total Tabungan Program</th>
                <td>:</td>
                <td class="rupiah-text">{{ $tabungan['total_program'] }}</td>
            </tr>
            @endif

        </tbody>
    </table>
    {{-- <div class="p-3 card mb-3">
        <h5 class="h5">Informasi Tabungan Kolektor </h5>
            <div class="table-responsive">
                @if ($tabungan['reguler'] > 0) 
                <h5><small class="text-muted">{{ $tabungan['reguler'] }} Tabungan Reguler</small></h5>
                @else
                <h5><small class="text-muted">Tabungan Reguler</small></h5>
                @endif
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th  width="50%" scope="col">Nama</th>
                            <th  width="25%" scope="col">Jumlah</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if ($tabungan['reguler'] > 0)  
                        @foreach ($tabunganReguler as $r)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $r->nasabah->user->nama }}</td>
                            <td class="rupiah-text">{{($r['total']) }}</td>
                            <td>
                                <a href="/dashboard/data-tabungan/{{ Crypt::encrypt($r->id) }}" class="btn btn-sm btn-primary">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <th>TOTAL</th>
                            <th class="rupiah-text">{{ $tabungan['total_reguler'] }}</th>
                            <td></td>
                        </tr>
                    @else
                    <th colspan="4" align="center" class="text-center">Tidak Memiliki Tabungan Reguler</th>
                    @endif
                    </tbody>
                </table>
                <div id="pagination">
                    {{ $tabunganReguler->links() }}
                </div>
            </div>
            <div class="table-responsive">
                <h5><small class="text-muted">Tabungan Program</small></h5>
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th  width="50%" scope="col">Nama</th>
                            <th  width="25%" scope="col">Jumlah</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if ($tabungan['program'] > 0)  
                        @foreach ($tabunganProgram as $r)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $r->nasabah->user->nama }}</td>
                            <td class="rupiah-text">{{($r['total']) }}</td>
                            <td>
                                <a href="/dashboard/data-tabungan/{{ Crypt::encrypt($r->id) }}" class="btn btn-sm btn-primary">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                        <td></td>
                        <th>TOTAL</th>
                        <th class="rupiah-text">{{ $tabungan['total_program'] }}</th>
                        <td></td>
                    @else
                    <th colspan="4" align="center" class="text-center">Tidak Memiliki Tabungan Program</th>
                    @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div> --}}
</div>
{{-- <div>
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
</div> --}}

<script>
</script>

@endsection

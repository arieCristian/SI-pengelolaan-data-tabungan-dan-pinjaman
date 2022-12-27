@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div class="header">
        <h1 class="h2">Setoran Kolektor</h1>
    </div>
</div>
<div class="row">
    {{-- BELUM_SETOR --}}
        <div class="col-lg-8 p-3 card mb-3">
            <h5 class="h5">Setoran Tabungan Yang Belum Disetorkan </h5>
                <div class="table-responsive">
                    <h5><small class="text-muted">Tabungan Reguler</small></h5>
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th  width="50%" scope="col">Nama</th>
                                <th  width="25%" scope="col">Jumlah</th>
                                @can('kasir')
                                <th scope="col">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                        @if ($total['reguler'] > 0)  
                            @foreach ($trReguler as $r)
                            @if ($r['setoran'] > 0)
                                
                            
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $r['nama'] }}</td>
                                <td class="rupiah-text">{{($r['setoran']) }}</td>
                                @can('kasir')
                                <td>
                                    <form action="/dashboard/transaksi-tabungan/setorkan" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" value="reguler" name="jenis">
                                        <input type="hidden" value="{{ $r['id'] }}" name="id">
                                        <button class="btn btn-sm btn-success me-1" onclick="return confirm('Yakin {{ $r['nama'] }} Telah Menyetorkan Semua Uang Setoran Tabungan Reguler Yang Belum Disetorkan  ?')"><i class="bi bi-check-lg"></i><span id="sudah-setor"> Sudah Setor </span></button>
                                    </form> 
                                </td>
                                @endcan
                            </tr>
                            @endif
                            @endforeach
                            <td></td>
                            <th>TOTAL</th>
                            <th class="rupiah-text">{{ $total['reguler'] }}</th>
                            @can('kasir')
                            <td></td>
                            @endcan
                        @else
                        <th colspan="4" align="center" class="text-center">Tidak Ada Setoran Yang Belum Disetorkan</th>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <h5><small class="text-muted">Tabungan Program</small></h5>
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th  width="50%" scope="col">Nama</th>
                                <th  width="25%" scope="col">Jumlah</th>
                                @can('kasir')
                                <th scope="col">Aksi</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                        @if ($total['program'] > 0)  
                            @foreach ($trProgram as $p)
                            @if ($p['setoran'] > 0)
                                
                            
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $p['nama'] }}</td>
                                <td class="rupiah-text">{{($p['setoran']) }}</td>
                                @can('kasir')
                                <td>
                                    <form action="/dashboard/transaksi-tabungan/setorkan" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" value="program" name="jenis">
                                        <input type="hidden" value="{{ $p['id'] }}" name="id">
                                        <button class="btn btn-sm btn-success me-1" onclick="return confirm('Yakin {{ $p['nama'] }} Telah Menyetorkan Semua Uang Setoran Tabungan Program Yang Belum Disetorkan  ?')"><i class="bi bi-check-lg"></i><span id="sudah-setor"> Sudah Setor </span> </button>
                                    </form> 
                                </td>
                                @endcan
                            </tr>
                            @endif
                            @endforeach
                            <td></td>
                            <th>TOTAL</th>
                            <th class="rupiah-text">{{ $total['program'] }}</th>
                            @can('kasir')
                            <td></td>
                            @endcan
                        @else
                        <th colspan="4" align="center" class="text-center">Tidak Ada Setoran Yang Belum Disetorkan</th>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    {{-- SUDAH_SETOR --}}
    <div class="row">
        <div class="col-lg-8 p-3 card">
            <div class="btn-toolbar mb-2 setoran-toolbar">
                <form action="" method="GET" class="me-2" style="width: 100%">
                    @csrf
                    <div class="row">
                    <div class="col-lg-4 col-sm-6">
                        <select style="    margin-top: 1.813rem;" name="filter" id="filter-dashboard" class="form-select form-select-sm">
                            <option value="hari ini"@if ($filter=="hari ini") selected @endif> Hari ini</option>
                            <option value="custom"@if ($filter=="custom") selected @endif>Tentukan Jangka Waktu</option>
                            <option value="semua"@if ($filter=="semua") selected @endif>Tampilkan Semua</option>
                        </select>  
                    </div>
                    <div class="custom-filter col-lg-3 col-sm-6">
                        <label for="filter-dari" class="form-label custom-filter">Mulai Dari</label>
                        <input type="date" class="form-control form-control-sm custom-filter" id="filter-dari"
                        name="dari" value="{{ $dari }}">
                    </div>  
                    <div class="custom-filter col-lg-3 col-sm-6">
                        <label for="filter-sampai" class="form-label custom-filter">Sampai</label>
                        @php
                            if($sampai != ""){
                                $sampai = date('Y-m-d', strtotime( $sampai . " -1 days"));
                            }
                        @endphp
                        <input type="date" class="form-control form-control-sm custom-filter" id="filter-sampai"
                        name="sampai" value="{{ $sampai }}">
                    </div>
                    <div class="col-lg-2 col-sm-6 order-last">
                        <button style="margin-top: 1.813rem;" type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                    </div>
                </div>
                    </form>

            </div>
            <h5 class="h5">Setoran Tabungan Yang Sudah Disetorkan </h5>
                <div class="table-responsive">
                    <h5><small class="text-muted">Tabungan Reguler</small></h5>
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th  width="70%" scope="col">Nama</th>
                                <th  width="25%" scope="col">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($total['sudah_reguler'] > 0)  
                            @foreach ($sudahReguler as $r)
                            @if ($r['setoran'] > 0)
                                
                            
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $r['nama'] }}</td>
                                <td class="rupiah-text">{{($r['setoran']) }}</td>
                            </tr>
                            @endif
                            @endforeach
                            <td></td>
                            <th>TOTAL</th>
                            <th class="rupiah-text">{{ $total['sudah_reguler'] }}</th>
                        @else
                        <th colspan="3" align="center" class="text-center">Tidak Ada Setoran Yang Sudah Disetorkan</th>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <h5><small class="text-muted">Tabungan Program</small></h5>
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th  width="70%" scope="col">Nama</th>
                                <th  width="25%" scope="col">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if ($total['sudah_program'] > 0)  
                            @foreach ($sudahProgram as $p)
                            @if ($p['setoran'] > 0)
                                
                            
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $p['nama'] }}</td>
                                <td class="rupiah-text">{{($p['setoran']) }}</td>
                            </tr>
                            @endif
                            @endforeach
                            <td></td>
                            <th>TOTAL</th>
                            <th class="rupiah-text">{{ $total['sudah_program'] }}</th>
                        @else
                        <th colspan="3" align="center" class="text-center">Tidak Ada Setoran Yang Sudah Disetorkan</th>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>










    <script>
        $(document).ready(function(){
        if($("#filter-dashboard").val() != "custom"){
            $(".custom-filter").hide()
        }else{
            $(".custom-filter").show()
        }
    })

    $("#filter-dashboard").change(function(){
        if($("#filter-dashboard").val() == "custom"){
            $(".custom-filter").show();
        }else {
            $(".custom-filter").hide()
        }
    })

    </script>

@endsection

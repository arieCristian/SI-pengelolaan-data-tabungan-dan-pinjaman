<?php

namespace App\Http\Controllers;

use App\Models\BungaReguler;
use App\Models\Nasabah;
use App\Models\Shu;
use App\Models\TransaksiPinjaman;
use App\Models\TransaksiShu;
use App\Models\TransaksiTabungan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function setting(Request $request){
        $id = auth()->user()->id ;
        if(auth()->user()->role == 'nasabah'){
            $alamat = Nasabah::query()->where('users_id',$id)->first();
            return view('dashboard.setting',[
                'user' => User::findOrFail($id),
                'alamat' => $alamat->alamat
            ]);
        }else{
            return view('dashboard.setting',[
                'user' => User::findOrFail($id)
            ]);
        }
    }
/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        $rules = [
            'no_telp' => 'max:255',
            'nama' => 'required|max:255'
        ];
        $dataUsers = [];
        $n = User::findOrFail($request->input('id'));
        if($request->input('username') != $n->username){
            $rules['username'] = 'required|unique:users|min:5|max:255';
        }else {
            $dataUsers['username'] = $request->input('username');
        }
        if($request->input('password') != null ){
            $rules['password'] = 'required|min:5|max:255|confirmed';
        }
        $dataUsers  += $request->validate($rules);
        if($request->input('password') != null ){
            $dataUsers['password'] = bcrypt($dataUsers['password']);
        }
        
        User::where('id', $n->id)
        ->update($dataUsers);
        if(auth()->user()->role =='nasabah'){
            $nId = Nasabah::query()->where('users_id', auth()->user()->id)->first();
            $nId = $nId->id ;
            $nasabah['alamat'] = $request->input('alamat');
            Nasabah::where('id',$nId)
            ->update($nasabah);
        }

        return redirect('/dashboard')->with('success', 'Berhasil Mengubah Biodata!');
    }



    public function riwayat(Request $request){
        $user = '';
        $dari = '';
        $sampai = '';
        $filter = 'hari ini';
        if($request['filter'] == null) {
            $request['filter'] = $filter;
        }
        $transaksiReguler = TransaksiTabungan::query();
        $transaksiProgram = TransaksiTabungan::query();
        $transaksiBerjangka = TransaksiTabungan::query();
        $transaksiPinjaman = TransaksiPinjaman::query()->where('jenis','!=','angsuran dibayar');
        $bungaReguler = BungaReguler::query() ;
        $transaksiShu = TransaksiShu::query();
        $shu = Shu::query();
        $kolektor = User::query()->where('role','kolektor')->get();
        $administrasi = User::query()->where('role','administrasi')->get();
        
        if($request['filter'] != "semua"){
            if($request['filter'] == "custom"){
                $filter = $request['filter'];
                $dari = $request['dari'];
                $sampai =  $request['sampai'];
                $sampai = date('Y-m-d', strtotime( $sampai . " +1 days"));
            }elseif($request['filter'] == "hari ini" ){
                $filter = $request['filter'];
                $dari = date('Y-m-d');
                $sampai = date('Y-m-d', strtotime( $dari . " +1 days"));
            }
            $transaksiReguler = $transaksiReguler
            ->whereBetween('created_at', [$dari, $sampai]);
            $transaksiProgram = $transaksiProgram
            ->whereBetween('created_at', [$dari, $sampai]);
            $transaksiBerjangka = $transaksiBerjangka
            ->whereBetween('created_at', [$dari, $sampai]);
            $transaksiPinjaman = $transaksiPinjaman
            ->whereBetween('created_at', [$dari, $sampai]);
            $transaksiShu = $transaksiShu
            ->whereBetween('created_at', [$dari, $sampai]);
            $bungaReguler = $bungaReguler
            ->whereBetween('created_at', [$dari, $sampai])->orderby('created_at','desc')->get();
            $shu = $shu
            ->whereBetween('created_at', [$dari, $sampai])->orderby('created_at','desc')->get();

        }else{
            $filter = $request['filter'];
            $bungaReguler = $bungaReguler->get();
            $shu = $shu->get();
            
        }
        if(auth()->user()->role == 'nasabah'){
            $transaksiShu = $transaksiShu
            ->orderby('created_at','desc')
            ->whereRelation('nasabah','users_id',auth()->user()->id);

            $tabunganReguler = $transaksiReguler
            ->orderby('created_at','desc')
            ->whereRelation('tabungan','jenis','reguler')
            ->where(function (Builder $query){
                $query->whereHas('tabungan', function (Builder $tabungan){
                    $tabungan->whereRelation('nasabah','users_id',auth()->user()->id);
                }); 
            });
            $tabunganProgram = $transaksiProgram
            ->orderby('created_at','desc')
            ->whereRelation('tabungan','jenis','program')
            ->where(function (Builder $query){
                $query->whereHas('tabungan', function (Builder $tabungan){
                    $tabungan->whereRelation('nasabah','users_id',auth()->user()->id);
                }) ; 
            });
            $tabunganBerjangka = $transaksiBerjangka
            ->orderby('created_at','desc')
            ->whereRelation('tabungan','jenis','berjangka')
            ->where(function (Builder $query){
                $query->whereHas('tabungan', function (Builder $tabungan){
                    $tabungan->whereRelation('nasabah','users_id',auth()->user()->id);
                }) ; 
            });
            $pinjaman = $transaksiPinjaman
            ->orderby('created_at','desc')
            ->where(function (Builder $query){
                $query->whereHas('pinjaman', function (Builder $pinjaman){
                    $pinjaman->whereRelation('nasabah','users_id',auth()->user()->id);
                }) ; 
            });
        }


        if(auth()->user()->role == "administrasi" || auth()->user()->role == 'kasir' ||auth()->user()->role == 'admin' ){
            if($request['user'] == null){
                    $user = "administrasi";
            }else{
                if($request['user'] != "administrasi"){
                    $id = $request['user'];
                    $user = $request['user'];
                }else{
                    $id = auth()->user()->id ;
                    $user = 'administrasi';
                }
            }
        }elseif(auth()->user()->role == "kolektor"){
            $user = auth()->user()->id ;
            $id = $user ;
        }


        // else if(auth()->user()->role == 'kasir' ||auth()->user()->role == 'admin'  ){
        //     if($request['user'] == null){
        //         $test = User::query()->where('role','administrasi')->first() ;
        //         $user = $test->id;
        //         $id = $test->id ;
        //     }else{
        //         $id = $request['user'];
        //         $user = $request['user'];
        //     }
        // }

        if(auth()->user()->role !== 'nasabah'){
        $search =$request['cari'];
        $tabunganReguler = $transaksiReguler->orderby('created_at','desc')->whereRelation('tabungan','jenis','reguler')
        ->Where(function (Builder $query) use ($search) {
            $query->whereHas('tabungan', function (Builder $tabungan) use($search){
                $tabungan->whereHas('nasabah', function (Builder $nasabah) use ($search){
                    $nasabah->whereRelation('user','nama','LIKE',"%$search%");
                });
            });
        });
        $tabunganProgram = $transaksiProgram->orderby('created_at','desc')->whereRelation('tabungan','jenis','program')->Where(function (Builder $query) use ($search) {
            $query->whereHas('tabungan', function (Builder $tabungan) use($search){
                $tabungan->whereHas('nasabah', function (Builder $nasabah) use ($search){
                    $nasabah->whereRelation('user','nama','LIKE',"%$search%");
                });
            });
        });
        $tabunganBerjangka = $transaksiBerjangka->orderby('created_at','desc')->whereRelation('tabungan','jenis','berjangka')->Where(function (Builder $query) use ($search) {
            $query->whereHas('tabungan', function (Builder $tabungan) use($search){
                $tabungan->whereHas('nasabah', function (Builder $nasabah) use ($search){
                    $nasabah->whereRelation('user','nama','LIKE',"%$search%");
                });
            });
        });
        if(auth()->user()->role == 'kolektor'){
            $tabunganReguler = $tabunganReguler->where('users_id',$id);
            $tabunganProgram = $tabunganProgram->where('users_id',$id);
            $tabunganBerjangka = $tabunganBerjangka->where('users_id',$id);
        }else{
            if($user != 'semua'){
                if($user != 'administrasi'){
                $tabunganReguler = $tabunganReguler->where('users_id',$id);
                $tabunganProgram = $tabunganProgram->where('users_id',$id);
                $tabunganBerjangka = $tabunganBerjangka->where('users_id',$id);
                }else{
                    $tabunganReguler = $tabunganReguler
                    ->whereRelation('user','role','administrasi');
                    $tabunganProgram = $tabunganProgram
                    ->whereRelation('user','role','administrasi');
                    $tabunganBerjangka = $tabunganBerjangka
                    ->whereRelation('user','role','administrasi');
                }
            }
        }
        $pinjaman = $transaksiPinjaman->orderby('created_at','desc')->Where(function (Builder $query) use ($search) {
            $query->whereHas('pinjaman', function (Builder $p) use($search){
                $p->whereHas('nasabah', function (Builder $nasabah) use ($search){
                    $nasabah->whereRelation('user','nama','LIKE',"%$search%");
                });
            });
        });
        }

        $transaksiShu = $transaksiShu->paginate(10);
        $totalReguler = $tabunganReguler->get();
        $totalProgram = $tabunganProgram->get();
        $totalBerjangka = $tabunganBerjangka->get();
        $totalPinjaman = $pinjaman->get();
        $tabunganReguler = $tabunganReguler->paginate(10);
        $tabunganProgram = $tabunganProgram->paginate(10);
        $tabunganBerjangka = $tabunganBerjangka->paginate(10);
        $pinjaman = $pinjaman->paginate(10);
        $totReguler = 0;
        foreach ($totalReguler as $r){
            $totReguler += $r['jumlah'];
        }
        $totProgram = 0;
        foreach ($totalProgram as $p){
            $totProgram += $p['jumlah'];
        }
        $totBerjangka = 0;
        foreach ($totalBerjangka as $b){
            $totBerjangka += $b['jumlah'];
        }
        $totPinjaman = 0;
        foreach ($totalPinjaman as $p){
            $totPinjaman += $p['jumlah'];
        }


        if ($request->ajax()) {
            if($request['jenis'] == "reguler"){
                return view('dashboard.riwayat-transaksi.riwayat-reguler', [
                    'tabunganReguler' => $tabunganReguler ,
                    'totalReguler' => $totReguler
                ]);
            }else if ($request['jenis'] == "program"){
                return view('dashboard.riwayat-transaksi.riwayat-program', [
                    'tabunganProgram' => $tabunganProgram ,
                    'totalProgram' => $totProgram
                ]);
            }else if ($request['jenis'] == "pinjaman"){
                return view('dashboard.riwayat-transaksi.riwayat-pinjaman', [
                    'pinjaman' => $pinjaman ,
                    'totalPinjaman' => $totPinjaman
                ]);
            }else if ($request['jenis'] == "shu"){
                return view('dashboard.riwayat-transaksi.riwayat-shu', [
                    'trShu' => $transaksiShu 
                ]);
            }else {
                return view('dashboard.riwayat-transaksi.riwayat-berjangka', [
                    'tabunganBerjangka' => $tabunganBerjangka ,
                    'totalBerjangka' => $totBerjangka
                ]);
            }
        }
        $tableReguler = view('dashboard.riwayat-transaksi.riwayat-reguler', [
            'tabunganReguler' => $tabunganReguler ,
            'totalReguler' => $totReguler
        ])->render();
        $tableProgram = view('dashboard.riwayat-transaksi.riwayat-program', [
            'tabunganProgram' => $tabunganProgram,
            'totalProgram' => $totProgram
        ])->render();
        $tableBerjangka = view('dashboard.riwayat-transaksi.riwayat-berjangka', [
            'tabunganBerjangka' => $tabunganBerjangka ,
            'totalBerjangka' => $totBerjangka
        ])->render();
        $tablePinjaman = view('dashboard.riwayat-transaksi.riwayat-pinjaman', [
            'pinjaman' => $pinjaman ,
            'totalPinjaman' => $totPinjaman
        ])->render();
        $tableShu = view('dashboard.riwayat-transaksi.riwayat-shu', [
            'trShu' => $transaksiShu
        ])->render();
        
        return view('dashboard.riwayat-transaksi.index',[
            'kolektor' => $kolektor,
            'administrasi' => $administrasi,
            'user' =>$user,
            'tabunganReguler' => $tabunganReguler ,
            'tabunganProgram' => $tabunganProgram,
            'tabunganBerjangka' => $tabunganBerjangka,
            'pinjaman' => $pinjaman,
            'bungaReguler' => $bungaReguler,
            'shu' => $shu,
            'trShu' => $transaksiShu,
            'tableShu' => $tableShu,
            'tableReguler' => $tableReguler,
            'tableBerjangka' => $tableBerjangka,
            'tableProgram' => $tableProgram,
            'tablePinjaman' => $tablePinjaman,
            'filter' => $filter ,
            'dari' => $dari,
            'sampai' => $sampai
        ]);
    }
}

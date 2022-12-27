<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Tabungan;
use App\Models\TransaksiTabungan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TabunganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kasir' || auth()->user()->role == 'kolektor' || auth()->user()->role == 'admin'){
        if($request->status){
            $status = $request->status;
        }else{
            $status = 'masih berjalan';
        }
        if($request->filter){
            $filter = $request->filter;
        }else{
            $filter = 'reguler';
        }
        $cari = $request->cari;
        $tabungan = Tabungan::query();
        
        if($status != "semua"){
            if ($cari) {
                $tabungan->where('no', $cari)->where('jenis', $filter)->where('status',$status)
                ->orWhere(function (Builder $query) use ($cari) {
                    $query->whereHas('nasabah', function (Builder $nasabah) use ($cari)  {
                        $nasabah->whereRelation('user','nama','LIKE',"%$cari%");
                    }) ; 
                })->where('jenis', $filter)->where('status',$status)
                ->orWhere(function (Builder $query) use ($cari) {
                    $query->whereHas('user', function (Builder $user) use ($cari)  {
                        $user->where('nama','LIKE',"%$cari%");
                    }) ; 
                })->where('jenis', $filter)->where('status',$status)
                ;
            }
        }else{
            if ($cari) {
                $tabungan->where('no', $cari)->where('jenis', $filter)
                ->orWhere(function (Builder $query) use ($cari) {
                    $query->whereHas('nasabah', function (Builder $nasabah) use ($cari)  {
                        $nasabah->whereRelation('user','nama','LIKE',"%$cari%");
                    }) ; 
                })->where('jenis', $filter)
                ->orWhere(function (Builder $query) use ($cari) {
                    $query->whereHas('user', function (Builder $user) use ($cari)  {
                        $user->where('nama','LIKE',"%$cari%");
                    }) ; 
                })->where('jenis', $filter)
                ;
            }
        }
        
        
        $tabungan->where('jenis', $filter);
        
        if($status != "semua"){
            $tabungan->where('status', $status);
        }
        if(auth()->user()->role == 'kolektor'){
            $tabungan->where('users_id', auth()->user()->id);
        }
        
        
        $total = $tabungan->sum('total');
        if($filter == 'reguler') {
            $tabungan->orderBy('updated_at','desc');
        } elseif ($filter == 'program'){
            $tabungan->orderBy('tgl_setoran','asc');
        } else{
            $tabungan->orderBy('tgl_selesai','asc');
        }
        
        if ($request->ajax()) {
            
            $tabungan = $tabungan->paginate(10);
            return view('dashboard.tabungan.filter', [
                'tabungan' => $tabungan ,
                'filter' => $filter,
                'status' => $status,
                'total' => $total
            ]);
        }
        // $tabungan->where('jenis', 'reguler')->orderByDesc('created_at');
        $tabungan = $tabungan->paginate(10);
        $table = view('dashboard.tabungan.filter', [
            'tabungan' => $tabungan,
            'filter' => $filter,
            'status' => $status,
            'total' => $total
        ])->render();
 
        return view('dashboard.tabungan.data-tabungan', [
            'tabungan' => $tabungan,
            'table' => $table,
            'total' => $total
        ]);
        }else {
            abort(403);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(auth()->user()->role == 'administrasi' ||auth()->user()->role == 'kolektor'){
        return view('dashboard.tabungan.buat-tabungan',[
            'pinjaman' => Tabungan::all()
        ]);
        }else{
            abort(403);
        }
    }
    public function search(Request $request){
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor'){
        $search = $request->input('search');
        $nasabah = Nasabah::query()->orderby('created_at', 'DESC') ;
        if(auth()->user()->role == 'kolektor'){
            $nasabah->where('kolektor',auth()->user()->id);
        }
        $nasabah = $nasabah->where(function (Builder $query) use($search) {
                $query->whereRelation('user','nama','LIKE',"%$search%");
            })            
        ->get();       
        
        return view('dashboard.tabungan.result')->with('nasabah', $nasabah);
        }else{
            abort(403);
        }
    }
    
    public function buat(Request $request)
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor'){
        $search = Crypt::decrypt($request->input('id'));
        // dd($search);
        $nasabah = Nasabah::where('id',$search)->get();
        if(auth()->user()->role == 'kolektor'){
            $myN = Nasabah::query()->where('id',$search)->where('kolektor',auth()->user()->id)->count();
            if($myN == 0){
                abort(403);
            }
        }
        
        $kolektor = User::where('role','kolektor')->get();
        // dd($nasabah);
        $tabungan = Tabungan::where('nasabah_id',$search)->get();
        return view('dashboard.tabungan.buat-tabungan-baru',[
            'nasabah' => $nasabah ,
            'tabungan' => $tabungan,
            'kolektor'=> $kolektor
        ]); 
        }else{
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor'){
        $role = auth()->user()->role ;
        $kebaruan = $request->input('pemindahan');
        if($role == 'kolektor'){
            $transaksi['setor'] = "belum";
            $kebaruan = 'baru';
        }else {
            $transaksi['setor']= "sudah";
        }
        $data = $request->validate([
            'nasabah_id' => 'required',
            'bunga' => 'required',
            'jenis' => 'required'
        ]);
        if($request['jenis'] == "reguler"){
            $data = $request->validate([
                'nasabah_id' => 'required',
                'bunga' => 'required',
                'jenis' => 'required',
                'total' => 'required'
            ]);
            $data['users_id'] = $request['kolektor_id'];
            $total = str_replace("Rp.", "", $request['total']);
            $total = str_replace(".", "", $total);
            $total = intval($total);
            $data['total'] = $total ;
            $no = Tabungan::where('jenis','reguler')->orderBy('no','desc')->first();
            if($no == null){
                $data['no'] = 1 ;
            }else{
                $data['no'] = $no['no'] +1 ;
            }
            $data['status'] = "masih berjalan";
            $tabungan = Tabungan::create($data);
            if($kebaruan == 'baru'){
                $transaksi['jenis'] = 'setoran';
            }else{
                $transaksi['jenis'] = 'pemindahan';
            }
            $transaksi['jumlah'] = $data['total'];
            $transaksi['tabungan_awal'] = 0;
            $transaksi['tabungan_akhir'] = $data['total'];
            $transaksi['tabungan_id'] = $tabungan->id;
            $transaksi['users_id'] = $request['users_id'];
            TransaksiTabungan::create($transaksi);
            $kolektor['kolektor'] = $data['users_id'];
            Nasabah::where('id',$data['nasabah_id'])->update($kolektor);
        } else if ($request['jenis'] == "program"){
            
            $request->validate([
                'tgl_setoran' => 'required',
                'setoran_tetap' => 'required',
                'lama_program' => 'required',
                'sudah_setor' => 'required'

            ]);
            $data['sudah_setor'] = $request['sudah_setor'];
            $bunga = floatval($data['bunga']);
            $data['users_id'] = $request['kolektor_id'];
            $data['setoran_tetap'] = str_replace("Rp.", "", $request['setoran_tetap']);
            $data['setoran_tetap'] = str_replace(".", "", $data['setoran_tetap']);
            $data['lama_program'] = intval($request['lama_program']) * 12;
            $data['total'] = (intval($data['setoran_tetap']) * $bunga) + $data['setoran_tetap']  ;
            $time = strtotime($request['tgl_setoran']);
            if($data['lama_program'] == intval($data['sudah_setor'])){
                $data['tgl_setoran'] = null ;
                $data['tgl_selesai'] = date("Y-m-d", strtotime("+1 month", $time));
            }else{
                $data['tgl_setoran'] = date("Y-m-d", strtotime("+1 month", $time));
            }
            $no = Tabungan::where('jenis','program')->orderBy('no','desc')->first();
            if($no == null){
                $data['no'] = 1 ;
            }else{
                $data['no'] = $no['no'] +1 ;
            }
            $data['status'] = "masih berjalan" ;
            if($data['sudah_setor'] == 1 ){
                $transaksi['jenis'] = 'setoran';
                $transaksi['tabungan_awal'] = 0;
                $transaksi['tabungan_akhir'] = $data['total'];
                $transaksi['bunga'] = intval($data['setoran_tetap']) * $bunga ;
                $transaksi['jumlah'] = $data['setoran_tetap'];
            } else {
                $totalProgram = 0 ;
                for($i = 1 ; $i <= $data['sudah_setor']; $i++){
                    if($i == 1 ){
                        $bungaProgram = $data['setoran_tetap'] * $bunga ;
                    }else{
                        $bungaProgram = $totalProgram * $bunga ;
                    }
                    $bungaProgram = round($bungaProgram); 
                    $totalProgram = $totalProgram + $bungaProgram + $data['setoran_tetap'];
                    $totalProgram = round($totalProgram);
                }
                $transaksi['jenis'] = 'pemindahan' ;
                $transaksi['tabungan_awal'] = 0 ;
                $transaksi['bunga'] = 0 ;
                $transaksi['tabungan_akhir'] = $totalProgram ;
                $transaksi['jumlah'] = $totalProgram;
            }
            $data['total'] = $transaksi['tabungan_akhir'];
            $tabungan = Tabungan::create($data);
            $transaksi['tabungan_id'] = $tabungan->id;
            $transaksi['users_id'] = $request['users_id'];
            TransaksiTabungan::create($transaksi);
            $kolektor['kolektor'] = $data['users_id'];
            Nasabah::where('id',$data['nasabah_id'])->update($kolektor);
        }else {
            $request->validate([
                'tgl_mulai' => 'required',
                'jum_deposito' => 'required',
                'lama_jangka' => 'required'
            ]);
            $data['users_id'] = $request['users_id'];
            $data['tgl_mulai'] = $request['tgl_mulai'];
            $tglSelesai = strtotime($request['tgl_mulai']);
            $data['jum_deposito'] = str_replace("Rp.", "", $request['jum_deposito']);
            $data['jum_deposito'] = str_replace(".", "", $data['jum_deposito']);
            $data['lama_program'] = intval($request['lama_jangka']) * 12;
            $lama = $data['lama_program'];
            $data['tgl_selesai'] = date("Y-m-d", strtotime("+$lama month", $tglSelesai));
            $bungaBerjangka = floatval($data['bunga']);
            $jumDeposito = intval($data['jum_deposito']);
            $bungaBerjangka = $bungaBerjangka * $jumDeposito ;
            $data['bunga_program'] = $bungaBerjangka * $data['lama_program'];
            $no = Tabungan::where('jenis','berjangka')->orderBy('no','desc')->first();
            if($no == null){
                $data['no'] = 1 ;
            }else{
                $data['no'] = $no['no'] +1 ;
            }
            $data['status'] = "masih berjalan";
            if($kebaruan == 'baru'){
                $data['bunga_diambil'] = 0 ;
                $data['total'] = $data['jum_deposito'] + $data['bunga_program'];
                $transaksi['jenis'] = 'setoran berjangka';
                $transaksi['tabungan_awal'] = 0;
                $transaksi['tabungan_akhir'] = $data['total'];
                $transaksi['jumlah'] = $data['jum_deposito'];
                $transaksi['bunga'] = $data['bunga_program'];
            }else{
                $data['bunga_diambil'] = str_replace("Rp.", "", $request['bunga_diambil']);
                $data['bunga_diambil'] = intval(str_replace(".", "", $data['bunga_diambil']));
                $data['total'] = $data['jum_deposito'] + $data['bunga_program'] - $data['bunga_diambil'];
                $transaksi['jenis'] = 'pemindahan';
                $transaksi['tabungan_awal'] = 0;
                $transaksi['tabungan_akhir'] = $data['total'];
                $transaksi['jumlah'] = $data['total'];
                $transaksi['bunga'] = 0;
            }
            $tabungan = Tabungan::create($data);
            $transaksi['tabungan_id'] = $tabungan->id;
            $transaksi['users_id'] = $request['users_id'];
            TransaksiTabungan::create($transaksi);
        }
        
        return redirect('/dashboard/data-tabungan')->with('success', 'Data Tabungan baru Telah Ditambahkan ! ');
        }else{
            abort(403);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tabungan  $tabungan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $id = Crypt::decrypt($id);
        if(auth()->user()->role == 'nasabah'){
            $myN = Tabungan::query()->where('id',$id)->whereRelation('nasabah','users_id',auth()->user()->id)->count();
            if($myN == 0){
                abort(403);
            }
        }
        if(auth()->user()->role == 'kolektor'){
            $myT = Tabungan::query()->where('id',$id)->where('users_id',auth()->user()->id)->count();
            if($myT == 0){
                abort(403);
            }
        }
        return view('dashboard.tabungan.detail-tabungan',[
            'tabungan' => Tabungan::findOrFail($id),
            'transaksi' => TransaksiTabungan::where('tabungan_id' ,$id)->orderBy('created_at','asc')->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tabungan  $tabungan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(auth()->user()->role == 'administrasi'){
        $id = Crypt::decrypt($id);
        return view('dashboard.tabungan.edit-tabungan',[
            'tabungan' => Tabungan::findOrFail($id),
            'kolektor' => User::query()->where('role','kolektor')->get()
        ]);
        }else{
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tabungan  $tabungan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tabungan $tabungan)
    {
        if(auth()->user()->role == 'administrasi'){
        $id = $request['id'];
        $t = Tabungan::findOrFail($id);
        if($t['jenis'] == 'program'){
            $data = $request->validate([
                'users_id' => 'required'
            ]);
        }else if($t['jenis'] == 'reguler') {
            $data = $request->validate([
                'users_id' => 'required',
                'status' => 'required'
            ]);
        }
        Tabungan::where('id', $id)
        ->update($data);
        return redirect('/dashboard/data-tabungan')->with('success', 'Data Tabungan Telah Diberbarui !');
        }else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tabungan  $tabungan
     * @return \Illuminate\Http\Response
     */
    public function destroy($tabungan)
    {
        if(auth()->user()->role == 'administrasi'){
        $transaksi = TransaksiTabungan::query()->where('tabungan_id',$tabungan)->count();
        $n = Tabungan::query()->where('id',$tabungan)->first();
        if($transaksi > 1 ){
            return redirect('/dashboard/data-tabungan')->with('gagal', 'Tidak Dapat Menghapus Data Tabungan Yang Telah Berjalan !');
        }else {
            // if($n['jenis'] == 'reguler'){
            //     if($n['total'] <= 20000){
            //         Tabungan::destroy($tabungan);
            //         return redirect('/dashboard/data-tabungan')->with('success', 'Berhasil Menghapus Data Tabungan !');
            //     }else {
            //         return redirect('/dashboard/data-tabungan')->with('gagal', 'Tidak Dapat Menghapus Data Tabungan Reguler Yang Masih Memiliki Sisa Tabungan Lebih Dart Rp. 20.000 !');
            //     }
            // }else{
                Tabungan::destroy($tabungan);
                DB::table('transaksi_tabungan')->where('tabungan_id', $tabungan)->delete();
                return redirect('/dashboard/data-tabungan')->with('success', 'Berhasil Menghapus Data Tabungan !');
            // }

        }

        }else{
            abort(403);
        }
            
        
    }
}

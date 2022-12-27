<?php

namespace App\Http\Controllers;

use App\Models\BungaReguler;
use App\Models\Tabungan;
use App\Models\TransaksiTabungan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiTabunganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function reguler()
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor' ){
        return view('dashboard.transaksi.tabungan.tr-tabungan-reguler',[
            'pinjaman' => Tabungan::all()
        ]);
        }else {
            abort(403);
        }
    }
    public function program()
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor' ){
        return view('dashboard.transaksi.tabungan.tr-tabungan-program',[
            'pinjaman' => Tabungan::all()
        ]);
        }else{
            abort(403);
        }
    }
    public function berjangka()
    {
        if(auth()->user()->role == 'administrasi' ){
        return view('dashboard.transaksi.tabungan.tr-tabungan-berjangka',[
            'pinjaman' => Tabungan::all()
        ]);
        }else{
            abort(403);
        }
    }
    public function search(Request $request){
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor' ){
        $search = $request->search ;
        $jenis = $request->jenis ;
        if(auth()->user()->role == 'kolektor'){
            if($jenis == 'program'){
                $tabungan = Tabungan::query()->orderby('created_at', 'DESC')
                ->where('no', "$search")->where('jenis', "$jenis")
                ->where('status','!=','selesai')
                ->where('tgl_setoran','!=',null)
                ->where('users_id',auth()->user()->id)
                ->orWhere(function (Builder $query) use ($search) {
                    $query->whereHas('nasabah', function (Builder $nasabah) use ($search)  {
                        $nasabah->whereRelation('user','nama','LIKE',"%$search%");
                    }) ;  
                })->where('jenis', "$jenis")
                ->where('status','!=','selesai')
                ->where('tgl_setoran','!=',null)
                ->where('users_id',auth()->user()->id)->get();
            }else{
                $tabungan = Tabungan::query()->orderby('created_at', 'DESC')
                ->where('no', "$search")->where('jenis', "$jenis")
                ->where('status','!=','selesai')->where('users_id',auth()->user()->id)
                ->orWhere(function (Builder $query) use ($search) {
                    $query->whereHas('nasabah', function (Builder $nasabah) use ($search)  {
                        $nasabah->whereRelation('user','nama','LIKE',"%$search%");
                    }) ;  
                })->where('jenis', "$jenis")->where('status','!=','selesai')
                ->where('users_id',auth()->user()->id)->get();
            }
        }else{
            $tabungan = Tabungan::query()->orderby('created_at', 'DESC')
            ->where('no', "$search")->where('jenis', "$jenis")->where('status','!=','selesai')
            ->orWhere(function (Builder $query) use ($search) {
                $query->whereHas('nasabah', function (Builder $nasabah) use ($search)  {
                    $nasabah->whereRelation('user','nama','LIKE',"%$search%");
                }) ;  
            })->where('jenis', "$jenis")->where('status','!=','selesai')->get(); 
        }
        return view('dashboard.transaksi.tabungan.result')->with('tabungan', $tabungan);
        }else{
            abort(403);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor' ){
        $search = Crypt::decrypt($request->input('id'));
        if(auth()->user()->role == 'kolektor'){
            $tabungan = Tabungan::where('id',$search)
            ->where('jenis','!=','berjangka')
            ->where('status','!=','selesai')
            ->where('users_id',auth()->user()->id)->get();
            if(count($tabungan) > 0){
                if($tabungan[0]['jenis'] == 'program' && $tabungan[0]['tgl_setoran'] == null){
                    abort(403);
                }
                if($tabungan[0]['jenis'] == 'berjangka'){
                    abort(403);
                }
            }

        }else{
            $tabungan = Tabungan::where('id',$search)
            ->where('status','!=','selesai')->get();
        }
        if (count($tabungan) > 0){

            return view('dashboard.transaksi.tabungan.buat_transaksi')->with('tabungan', $tabungan);
        }else{
            abort(403);
        }
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
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor' ){
        $id = $request['tabungan_id'];
        $tabungan = Tabungan::where("id", $request['tabungan_id'])->get();
        $data = $request->validate([
            'jenis' => 'required',
            'jumlah' => 'required',
            'tabungan_id' => 'required',
            'users_id' => 'required'
        ]);
        $tabunganId = Crypt::encrypt($data['tabungan_id']);
        $data['jumlah'] = str_replace("Rp.", "", $data['jumlah']);
        $data['jumlah'] = str_replace(".", "", $data['jumlah']);
        $data['jumlah'] = intval($data['jumlah']);
        if($request['jenis_tabungan'] == "reguler"){
            $data['tabungan_awal'] = $tabungan[0]['total'];
            if($data['jenis'] == "penarikan"){
                if( $data['jumlah'] > ($tabungan[0]['total'] - 20000)){
                    $validator = Validator::make($request->all(), [
                        'jumlah' => [
                            function ($attribute, $value, $fail) {
                                    $fail('Jumlah yang ingin ditarik lebih banyak dibandingkan jumlah tabungan yang dapat ditarik !');
                            },
                        ],
                    ]);
                    if ($validator->fails()) {
                        return redirect('/dashboard/transaksi-tabungan/create?id='.$tabunganId)
                                    ->withErrors($validator)
                                    ->withInput();
                    }
                }
                $data['tabungan_akhir'] = $data['tabungan_awal'] - $data['jumlah'];
                
            } elseif($data['jenis']== "setoran") {
                $data['tabungan_akhir'] = $data['tabungan_awal'] + $data['jumlah'];
                $role = auth()->user()->role ;
                if($role == 'kolektor'){
                    $data['setor'] = "belum";
                }else {
                    $data['setor']= "sudah";
                }
            } else{
                $kesalahan = $request['keterangan_perbaikan'];
                $tglKesalahan = $request['tgl_kesalahan'];
                $tglKesalahan = date("d-m-Y", strtotime($tglKesalahan));
                if($kesalahan == "kekurangan"){
                    $data['tabungan_akhir'] = $data['tabungan_awal'] + $data['jumlah'];
                    $data['keterangan'] = "Penambahan tabungan sejumlah ". $request['jumlah'] ." karena kesalahan input pada tanggal " .$tglKesalahan ;
                } else {
                    $data['jumlah'] = $data['jumlah'] * -1 ;
                    $data['tabungan_akhir'] = $data['tabungan_awal'] + $data['jumlah'];
                    $data['keterangan'] = "Pengurangan tabungan sejumlah ". $request['jumlah'] ." karena kesalahan input pada tanggal " .$tglKesalahan ;
                }
            }
            $updateTabungan['total'] = $data['tabungan_akhir'];
            $role = auth()->user()->role ;
                if($role == 'kolektor'){
                    $data['setor'] = "belum";
                }else {
                    $data['setor']= "sudah";
                }
            
        }elseif($request['jenis_tabungan'] == "program" ){
            $data['tabungan_awal'] = $tabungan[0]['total'];
            if($data['jenis'] == "setoran"){
                $role = auth()->user()->role ;
                if($role == 'kolektor'){
                    $data['setor'] = "belum";
                }else {
                    $data['setor']= "sudah";
                }
                $bunga = $tabungan[0]['total'] * $tabungan[0]['bunga'];
                $data['tabungan_akhir'] = round($bunga + $tabungan[0]['total'] + $data['jumlah']);
                $data['bunga'] = round($bunga);
                $updateTabungan['sudah_setor'] = $tabungan[0]['sudah_setor'] + 1 ;
                $time = strtotime($tabungan[0]['tgl_setoran']);
                if($updateTabungan['sudah_setor'] == $tabungan[0]['lama_program']){
                    $updateTabungan['tgl_setoran'] = null ;
                    $updateTabungan['tgl_selesai'] = date("Y-m-d", strtotime("+1 month", $time));
                }else {
                    $updateTabungan['tgl_setoran'] = date("Y-m-d", strtotime("+1 month", $time));
                }
            } else {
                $updateTabungan['status'] = "selesai";
                $data['tabungan_akhir'] = 0 ;
            }
            $updateTabungan['total'] = $data['tabungan_akhir'];
        }else{
            $data['tabungan_awal'] = $tabungan[0]['total'];
            if($data['jenis'] == "penarikan bunga"){
                $max = $request->input('max');
                $max = intval($max);
                if($data['jumlah'] <= $max){
                    $updateTabungan['bunga_diambil'] = $tabungan[0]['bunga_diambil'] + $data['jumlah'];
                    $updateTabungan['total'] = $tabungan[0]['total'] - $data['jumlah'];
                }else{
                    $validator = Validator::make($request->all(), [
                        'jumlah' => [
                            function ($attribute, $value, $fail) {
                                    $fail('Jumlah yang ingin ditarik lebih banyak dibandingkan bunga tabungan yang dapat ditarik !');
                            },
                        ],
                    ]);
                    if ($validator->fails()) {
                        return redirect('/dashboard/transaksi-tabungan/create?id='.$tabunganId)
                                    ->withErrors($validator)
                                    ->withInput();
                    }
                }
            }else {
                $updateTabungan['total']  = 0 ;
                $updateTabungan['status']  = "selesai";
                $updateTabungan['bunga_diambil'] = ($tabungan[0]['jum_deposito'] * $tabungan[0]['bunga']) * $tabungan[0]['lama_program'];
                
            }
            $data['tabungan_akhir'] = $updateTabungan['total'];
        }
        TransaksiTabungan::create($data);
        Tabungan::where('id', $data['tabungan_id'])
        ->update($updateTabungan);
       
        return redirect('/dashboard/data-tabungan/'.$tabunganId)->with('success', 'Berhasil Melakukan Transaksi!');
        }else{
            abort(403);
        }
 
            
    }

    public function bungaReguler (){
        if(auth()->user()->role == 'administrasi'){
        $tabungan = Tabungan::query()->where('jenis','reguler')->where('status','!=','selesai')->get();
        $i = 0 ;
        $bungaReguler['jumlah'] = 0 ;
        foreach ($tabungan as $a){
            $bunga = $a['total'] * $a['bunga'];
            $bunga = round($bunga);
            $bungaReguler['jumlah'] += $bunga ;
        }
        $idBungaReguler = BungaReguler::create($bungaReguler);
        $id = $idBungaReguler->id ;
        foreach ($tabungan as $t) {
            $bunga = $t['total'] * $t['bunga'];
            $bunga = round($bunga);
            $transaksi['jumlah'] = $bunga ;
            $transaksi['jenis'] = "penambahan bunga";
            $transaksi['tabungan_id'] = $t['id'];
            $transaksi['users_id'] = auth()->user()->id ;
            $transaksi['tabungan_awal'] = $t['total'];
            $transaksi['tabungan_akhir'] =$t['total'] + $bunga ;
            $transaksi['bunga_id'] = $id ;
            $data['total'] = $transaksi['tabungan_akhir'];
            TransaksiTabungan::create($transaksi);
            Tabungan::where('id', $t['id'])
            ->update($data);
         $i++ ;
        }
     
        return redirect('/dashboard/data-tabungan')->with('success', 'Berhasil Melakukan Transaksi!');
        }else{
            abort(403);
        }
    }

    public function batalBungaReguler(Request $request){
        if(auth()->user()->role == 'administrasi'){
        $id = $request->input('id');
        $trBunga = TransaksiTabungan::query()->where('bunga_id',$id)->get();
        $idTrBunga = [];
        foreach ($trBunga as $bunga){
            array_push($idTrBunga,$bunga->id);
        }
        foreach ($trBunga as $tb){
            $tabungan['total'] = $tb->tabungan->total - $tb->jumlah ;
            $tabunganId = $tb->tabungan->id ;
            $trLewat = DB::table('transaksi_tabungan')
            ->join('tabungan','transaksi_tabungan.tabungan_id','=','tabungan.id')
            ->where('tabungan.id',$tabunganId)
            ->where('transaksi_tabungan.id','>',$tb->id)
            ->whereNotIn('transaksi_tabungan.id',$idTrBunga)
            ->select('transaksi_tabungan.*')
            ->get();

            foreach ($trLewat as $a){
                // echo $a->tabungan_awal."<br>";
                $updateTr['tabungan_awal'] = $a->tabungan_awal - $tb->jumlah ;
                $updateTr['tabungan_akhir'] = $a->tabungan_akhir - $tb->jumlah ;
                $idTr = $a->id ;
                // echo $idTr."| Tabungan Awal = " .$a->tabungan_awal. "-". $tb->jumlah."<br>" ;
                TransaksiTabungan::where('id', $idTr)
                ->update($updateTr);

            }
            Tabungan::where('id', $tabunganId)
            ->update($tabungan);
            TransaksiTabungan::where('id', $tb->id)->delete();
            // echo $tb->tabungan->total." - ".$tb->jumlah." = ".$tabungan['total']."<br>";
        }
        BungaReguler::where('id', $id)->delete();

        return redirect('/dashboard/riwayat-transaksi')->with('success', 'Berhasil Membatalkan Transaksi!');
        }else{
            abort(403);
        }
    }

    /* SETORAN_KOLEKTOR */

    public function setoranKolektor(Request $request){

        $dari = '';
        $sampai = '';
        $filter = 'hari ini';
        if($request['filter'] == null) {
            $request['filter'] = $filter;
        }

        if(auth()->user()->role == 'kasir' || auth()->user()->role == 'admin'){
        $user = User::query()->where('role','kolektor')->get();
        $kolektor = [];
        foreach ($user as $u ){
            array_push($kolektor,$u->id);
        }

        $users = User::query()->where('role','kolektor')->get();
        $penabung = [];
        foreach ($users as $us ){
            array_push($penabung,$us->id);
        }

        /* TRANSAKSI REGULER */
        $trReguler = [];
        $i = 0 ;
        foreach ($kolektor as $k) {
        $trReguler[$i]['id'] = $k ;
        $trReguler[$i]['setoran'] = TransaksiTabungan::query()
        // ->where('jenis','setoran')
        ->whereRelation('tabungan','jenis','reguler')
        ->where('users_id',$k)
        ->where('setor','belum')
        ->sum('jumlah');
        $nama = User::findOrFail($k);
        $trReguler[$i]['nama'] = $nama['nama'];
        $i++;
        }
        $sudahReguler = [];
        $i = 0 ;
        foreach ($penabung as $k) {
        $sudahReguler[$i]['id'] = $k ;
        $sudahReguler[$i]['setoran'] = TransaksiTabungan::query()
        // ->where('jenis','setoran')
        ->whereRelation('tabungan','jenis','reguler')
        ->where('users_id',$k)
        ->where('setor','sudah');
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
            $sudahReguler[$i]['setoran'] = $sudahReguler[$i]['setoran']
            ->whereBetween('created_at', [$dari, $sampai])
            ->sum('jumlah');
        }else{
            $sudahReguler[$i]['setoran'] = $sudahReguler[$i]['setoran']
            ->sum('jumlah');
            $filter = $request['filter'];
            
        }
        $nama = User::findOrFail($k);
        $sudahReguler[$i]['nama'] = $nama['nama'];
        $i++;
        }


        /* TRANSAKSI PROGRAM */

        $trProgram = [];
        $i = 0 ;
        foreach ($kolektor as $k) {
        $trProgram[$i]['id'] = $k ;
        $trProgram[$i]['setoran'] = TransaksiTabungan::query()
        ->whereRelation('tabungan','jenis','program')
        ->where('users_id',$k)
        ->where('setor','belum')
        ->sum('jumlah');
        $nama = User::findOrFail($k);
        $trProgram[$i]['nama'] = $nama['nama'];
        $i++;
        }
        $sudahProgram = [];
        $i = 0 ;
        foreach ($penabung as $k) {
        $sudahProgram[$i]['id'] = $k ;
        $sudahProgram[$i]['setoran'] = TransaksiTabungan::query()
        // ->where('jenis','setoran')
        ->whereRelation('tabungan','jenis','program')
        ->where('users_id',$k)
        ->where('setor','sudah');
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
            $sudahProgram[$i]['setoran'] = $sudahProgram[$i]['setoran']
            ->whereBetween('created_at', [$dari, $sampai])
            ->sum('jumlah');
        }else{
            $sudahProgram[$i]['setoran'] = $sudahProgram[$i]['setoran']
            ->sum('jumlah');
            $filter = $request['filter'];
            
        }
        $nama = User::findOrFail($k);
        $sudahProgram[$i]['nama'] = $nama['nama'];
        $i++;
        }

        /* GET TOTAL */

        $total['program'] = TransaksiTabungan::query()
        ->whereRelation('tabungan','jenis','program')
        ->where('setor','belum')
        ->sum('jumlah');
        $total['reguler'] = TransaksiTabungan::query()
        ->whereRelation('tabungan','jenis','reguler')
        ->where('setor','belum')
        ->sum('jumlah');

        $total['sudah_program'] = DB::table('transaksi_tabungan')
        ->join('tabungan', 'transaksi_tabungan.tabungan_id', '=', 'tabungan.id')
        ->where('tabungan.jenis','program')
        ->whereIn('transaksi_tabungan.users_id',$kolektor)
        ->where('setor','sudah');
        $total['sudah_reguler'] = DB::table('transaksi_tabungan')
        ->join('tabungan', 'transaksi_tabungan.tabungan_id', '=', 'tabungan.id')
        ->where('tabungan.jenis','reguler')
        ->whereIn('transaksi_tabungan.users_id',$kolektor)
        ->where('setor','sudah');

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
            $total['sudah_reguler'] = $total['sudah_reguler']
            ->whereBetween('transaksi_tabungan.created_at', [$dari, $sampai])
            ->sum('jumlah');
            $total['sudah_program'] = $total['sudah_program']
            ->whereBetween('transaksi_tabungan.created_at', [$dari, $sampai])
            ->sum('jumlah');
        }else{
            $total['sudah_reguler'] = $total['sudah_reguler']
            ->sum('jumlah');
            $total['sudah_program'] = $total['sudah_program']
            ->sum('jumlah');
            $filter = $request['filter'];
            
        }


        return view('dashboard.transaksi.setoran-kolektor',[
            'trReguler' => $trReguler ,
            'trProgram' => $trProgram,
            'sudahProgram' => $sudahProgram,
            'sudahReguler' => $sudahReguler,
            'total' => $total,
            'filter' => $filter,
            'dari' => $dari,
            'sampai' => $sampai
        ]);
        }else{
            abort(403);
        }
    }

    public function setorkan(Request $request){
        if(auth()->user()->role == 'kasir'){
        $id = $request->input('id');
        $jenis = $request->input('jenis');
        $transaksi = TransaksiTabungan::query()
        ->whereRelation('tabungan','jenis',$jenis)
        ->where('users_id',$id)
        ->where('setor','belum')
        ->get();
        $data['setor'] = 'sudah';
        foreach ($transaksi as $tr){
            TransaksiTabungan::where('id',$tr->id)
            ->update($data);
        }

        return redirect('/dashboard/setoran-kolektor')->with('success', 'Berhasil Mengubah Status Setoran Menjadi Sudah Disetorkan !');
        }else{
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransaksiTabungan  $transaksiTabungan
     * @return \Illuminate\Http\Response
     */
    public function show(TransaksiTabungan $transaksiTabungan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransaksiTabungan  $transaksiTabungan
     * @return \Illuminate\Http\Response
     */
    public function edit(TransaksiTabungan $transaksiTabungan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransaksiTabungan  $transaksiTabungan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransaksiTabungan $transaksiTabungan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransaksiTabungan  $transaksiTabungan
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransaksiTabungan $transaksiTabungan)
    {
    }
}

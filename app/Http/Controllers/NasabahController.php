<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\Shu;
use App\Models\Tabungan;
use App\Models\TransaksiShu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use phpDocumentor\Reflection\Types\Null_;

class NasabahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if(auth()->user()->role == 'nasabah'){
            $tabungan = Tabungan::query()->whereRelation('nasabah','users_id',auth()->user()->id)->get();
            $pinjaman = Pinjaman::query()->whereRelation('nasabah','users_id',auth()->user()->id)->get();
            $transaksi = TransaksiShu::query()->whereRelation('nasabah','users_id',auth()->user()->id)->get();
            return view('dashboard.nasabah.data-saya', [
                'tabungan' => $tabungan,
                'pinjaman' => $pinjaman,
                'transaksi' => $transaksi,
                'nasabah' => Nasabah::query()->where('users_id',auth()->user()->id)->first()
            ]);
        }
        $filter = $request->filter;
        $cari = $request->cari;
        $nasabah = Nasabah::query(); // apakah ini tidak dapat query "->whereIn()" ?
        if($filter == 'shu') {
            $nasabah->where('shu', '>', 0)->orderByDesc('shu');
        } elseif ($filter == 'anggota'){
            $nasabah->where('keanggotaan', 'anggota')->orderByDesc('updated_at');
        } elseif ($filter == 'calon anggota'){
            $nasabah->where('keanggotaan', 'calon anggota')->orderByDesc('updated_at');
        } elseif ($filter == 'anggota alit'){
            $nasabah->where('keanggotaan', 'anggota alit')->orderByDesc('updated_at');
        }else{
            $nasabah->orderByDesc('updated_at');
        }
 
        if ($cari) {
            $nasabah->where(function (Builder $query) use ($cari) {
                $query->whereHas('user', function (Builder $user) use ($cari) {
                    $user->where('nama', 'LIKE', "%$cari%");
                })
                ->orWhere('alamat', 'LIKE', "%$cari%");
            });
        }
        // dd($nasabah);
        if(auth()->user()->role == 'kolektor'){
            $nasabah->where('kolektor',auth()->user()->id);
        }
        $total = $nasabah->count();
        $nasabah = $nasabah->paginate(10); //tidak dapat diakses jika menggunakan whereIn()
 
        if ($request->ajax()) {
            return view('dashboard.nasabah.filter', [
                'nasabah' => $nasabah,
                'total' => $total
            ]);
        }
        $table = view('dashboard.nasabah.filter', [
            'nasabah' => $nasabah,
            'total' => $total
        ])->render();
 
        return view('dashboard.nasabah.data-nasabah', [
            'nasabah' => $nasabah,
            'table' => $table,
        ]);
    }
    public function ambilShu(Request $request){
        if(auth()->user()->role == 'administrasi'){
        $id = $request->input('id');
        $nasabah = Nasabah::query()->where('id',$id)->first();
        $data['shu'] = 0 ;
        $transaksi['jumlah'] = $nasabah['shu'];
        $transaksi['nasabah_id'] = $id ;
        $transaksi['jenis']= 'pengambilan shu';
        TransaksiShu::create($transaksi);
        Nasabah::where('id', $id)
        ->update($data);
        return redirect('/dashboard/data-nasabah')->with('success', 'Berhasil Mengambil Sisa Hasil Usaha Nasabah!');
        }else{
            abort(403);
        }
    }
    public function bagikanShu(){
        if(auth()->user()->role == 'administrasi'){

        
        $shu = Shu::query()->count();
        if($shu > 0){
            $oldShu = Shu::latest()->first()->get();
        }else {
            $oldShu = Shu::query()->get();
        }
        $anggota = Nasabah::query()->where('keanggotaan','anggota')->count();
        $anggotaAlit = Nasabah::query()->where('keanggotaan','anggota alit')->count();
        $calonAnggota = Nasabah::query()->where('keanggotaan','calon anggota')->count();
        // dd($anggota,$anggotaAlit,$calonAnggota);
        $nasabah = array(
            'anggota' => $anggota,
            'anggota_alit' => $anggotaAlit,
            'calon_anggota' => $calonAnggota
        );

        return view('dashboard.transaksi.bagikan-shu',[
            'oldShu' => $oldShu,
            'nasabah' => $nasabah
        ]);
        }else {
            abort(403);
        }
    }

    public function storeShu(Request $request){
        if(auth()->user()->role == 'administrasi'){
        $data = $request->validate([
            'total' => 'required',
            'pembagian_shu' => 'required'
        ]);
        $shuAlit = $request['shu_alit'];
        $shuAlit = str_replace("Rp.", "", $shuAlit);
        $shuAlit = str_replace(".", "", $shuAlit);
        $shuAlit = intval($shuAlit);
        $data['total'] = str_replace("Rp.", "", $data['total']);
        $data['total'] = str_replace(".", "", $data['total']);
        $data['total'] = intval($data['total']);
        $data['pembagian_shu'] = str_replace("Rp.", "", $data['pembagian_shu']);
        $data['pembagian_shu'] = str_replace(".", "", $data['pembagian_shu']);
        $data['pembagian_shu'] = intval($data['pembagian_shu']);
        $data['tahun'] = date("Y");
        $shu = Shu::create($data);
        $trShu['jenis'] = 'penambahan shu';
        $trShu['jumlah'] = $data['pembagian_shu'];
        $trShu['shu_id'] = $shu->id ;
        $anggota = Nasabah::query()->where('keanggotaan','anggota')->get();
        foreach ($anggota as $a){
            $nasabah['shu'] = $data['pembagian_shu'] + $a->shu;
            Nasabah::where('id', $a->id)
            ->update($nasabah);
            $trShu['nasabah_id'] = $a->id ;
            TransaksiShu::create($trShu);
        }
        $trShu['jumlah'] = $shuAlit ;
        $anggotaAlit = Nasabah::query()->where('keanggotaan','anggota alit')->get();
        foreach ($anggotaAlit as $aa){
            $nasabah['shu'] = $shuAlit + $aa->shu ;
            Nasabah::where('id', $aa->id)
            ->update($nasabah);
            $trShu['nasabah_id'] = $aa->id ;
            TransaksiShu::create($trShu);
        }

        return redirect('/dashboard/data-nasabah')->with('success', 'Berhasil Membagikan Sisa Hasil Usaha Kepada Nasabah !');
        }else {
            abort(403);
        }

    }

    public function batalShu (Request $request){
        if(auth()->user()->role == 'administrasi'){
        $id = $request->input('id');
        $shu = Shu::query()->where('id',$id)->get();
        $trShu = TransaksiShu::query()->where('shu_id',$id)->get();
        foreach ($trShu as $t){
            $nasabah = Nasabah::findOrFail($t->nasabah_id);
            $data['shu'] = $nasabah['shu'] - $t['jumlah'];
            Nasabah::where('id', $nasabah->id)
            ->update($data);
            TransaksiShu::destroy($t->id);
        }
        Shu::destroy($id);

        return redirect('/dashboard/riwayat-transaksi')->with('success', 'Berhasil Membatalkan Transaksi!');
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
        if(auth()->user()->role == 'administrasi' ||auth()->user()->role == 'kolektor' ){
        return view('dashboard.nasabah.buat-nasabah',[
            'nasabah' => Nasabah::all()
        ]);
        } else {
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
        if(auth()->user()->role == 'administrasi' ||auth()->user()->role == 'kolektor'){
        $userData = $request->validate([
            'nama' => 'required|max:255',
            'username' => 'required|unique:users',
            'password' => 'required|min:5|max:255|confirmed',
            'no_telp' => 'required',
        ]); 
        $userData['role'] = 'nasabah';
        $userData['password'] = bcrypt($userData['password']);

        $user = User::create($userData); //cara dapatin id dari record tabel ini gmn ?

        $nasabahData = $request->validate([
            'alamat' =>'required',
            'keanggotaan' => 'required',
        ]);
        $nasabahData['users_id'] = $user->id; 
        $nasabahData['kolektor'] = auth()->user()->id ;       
        $nasabahData['shu'] = 0 ;
        Nasabah::create($nasabahData);
        return redirect('/dashboard/data-nasabah')->with('success', 'Berhasil Menambahkan Nasabah Baru !');
        }else{
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor' || auth()->user()->role == 'kasir' || auth()->user()->role == 'admin'){
        $id = Crypt::decrypt($id);
        $tabungan = Tabungan::query()->where('nasabah_id' ,$id)->orderby('jenis','desc');
        $pinjaman = Pinjaman::query()->where('nasabah_id' ,$id)->get();
        $transaksi = TransaksiShu::query()->where('nasabah_id' ,$id)->get();
        if(auth()->user()->role == 'kolektor'){
            $myN = Nasabah::query()->where('id',$id)->where('kolektor',auth()->user()->id)->count();
            if($myN == 0){
                abort(403);
            }
            $tabungan = $tabungan->where('users_id',auth()->user()->id)->get();
        }else{
            $tabungan = $tabungan->get();
        }
        return view('dashboard.nasabah.detail-nasabah',[
            'nasabah' => Nasabah::findOrFail($id),
            'tabungan' => $tabungan,
            'pinjaman' => $pinjaman,
            'transaksi' => $transaksi
        ]);
        }else {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor'){
        $id = Crypt::decrypt($id);
        if(auth()->user()->role == 'kolektor'){
            $myN = Nasabah::query()->where('id',$id)->where('kolektor',auth()->user()->id)->count();
            if($myN == 0){
                abort(403);
            }
        }
        $nasabah = Nasabah::findOrFail($id);
        $user = User::findOrFail($nasabah->kolektor);
        if($user->role == 'administrasi'){
            $kolektor = null ;
        }else{
            $kolektor = 'ada';
        }
        return view('dashboard.nasabah.edit-data',[
            'nasabah' => Nasabah::findOrFail($id),
            'kolektor' => User::where('role','kolektor')->get(),
            'ada' => $kolektor
            // 'transaksi' => TransaksiPinjaman::where('pinjaman_id' ,$id)->get()
        ]);
        }else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Nasabah $nasabah)
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kolektor'){
        if(auth()->user()->role == 'administrasi'){
            $rulesNasabah = [
            'keanggotaan' => 'required',
            'alamat' => 'max:255'
        ]; 
        }else{
            $rulesNasabah = [
                'alamat' => 'max:255'
            ]; 
        }
        $rules = [
            'no_telp' => 'max:255',
            'nama' => 'required|max:255'
        ];
        $dataUsers = [];
        $n = Nasabah::findOrFail($request['id']);
        if($request['username'] != $n->user->username){
            $rules['username'] = 'required|unique:users|min:5|max:255';
        }else {
            $dataUsers['username'] = $request['username'];
        }
        if(auth()->user()->role == 'administrasi'){
            if($request['kolektor'] != $n->kolektor){
                $rulesNasabah['kolektor'] = 'required';
                // $dataNasabah['kolektor'] = $request['kolektor'];
                $kolektor['users_id'] = $request['kolektor'];
                $tabunganNasabah = Tabungan::query()->where('nasabah_id',$n->id)->where('jenis','!=','berjangka')->get();
                foreach ($tabunganNasabah as $tn){
                    Tabungan::where('id',$tn['id'])->update($kolektor);
                }
            }
        }
        if($request['password'] != null ){
            $rules['password'] = 'required|min:5|max:255|confirmed';
            // $dataUsers['password'] = $request->validate($rules);
        }
        $dataUsers  += $request->validate($rules);
        $dataNasabah = $request->validate($rulesNasabah);
        // dd($dataUsers,$dataNasabah);
        if($request['password'] != null ){
            $dataUsers['password'] = bcrypt($dataUsers['password']);
        }
        Nasabah::where('id', $request['id'])
        ->update($dataNasabah);
        User::where('id', $n->user->id)
        ->update($dataUsers);
        return redirect('/dashboard/data-nasabah')->with('success', 'Data Nasabah Telah Diberbarui !');
        }else {
        abort(403);
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function destroy($nasabah)
    {
        if(auth()->user()->role == 'administrasi'){
        $n = Nasabah::query()->where('id',$nasabah)->first();
        $tabungan = Tabungan::query()->whereRelation('nasabah','nasabah_id',$nasabah)->count();
        $pinjaman = Pinjaman::query()->whereRelation('nasabah','nasabah_id',$nasabah)->count();

        if($tabungan > 0 || $pinjaman > 0 ){
            return redirect('/dashboard/data-nasabah')->with('gagal', 'Tidak Dapat Menghapus Data Nasabah Karena Nasabah Masih Mempunyai Tabungan atau Pinjaman!');
        }else {
            Nasabah::destroy($nasabah);
            
            return redirect('/dashboard/data-nasabah')->with('success', 'Berhasil Menghapus Data Nasbaah !');
        }
        }else{
            abort(403);
        }
        
    }
}

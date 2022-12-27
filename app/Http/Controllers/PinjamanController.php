<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\TransaksiPinjaman;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kasir' || auth()->user()->role == 'admin'){
        if($request->status){
            $status = $request->status;
        }else{
            $status = 'Belum Lunas';
        }
        $cari = $request->cari;
        $pinjaman = Pinjaman::query()->where('status' , $status)->orderby('tgl_angsuran','asc');
        if ($cari) {
            $pinjaman->where(function (Builder $query) use ($cari) {
                $query->whereHas('nasabah', function (Builder $nasabah) use ($cari) {
                    $nasabah->whereRelation('user','nama', 'LIKE', "%$cari%");
                });
            });
        }
        $total = $pinjaman->sum('sisa_pinjaman');
        $pinjaman = $pinjaman->paginate(12);
 
        if ($request->ajax()) {
            return view('dashboard.pinjaman.filter', [
                'pinjaman' => $pinjaman,
                'total' => $total
            ]);
        }
        $table = view('dashboard.pinjaman.filter', [
            'pinjaman' => $pinjaman,
            'total' => $total
        ])->render();
 
        return view('dashboard.pinjaman.data-pinjaman', [
            'pinjaman' => $pinjaman,
            'table' => $table,
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
        if(auth()->user()->role == 'administrasi'){
        return view('dashboard.pinjaman.buat-pinjaman',[
            'pinjaman' => Pinjaman::all()
        ]);
        }else {
            abort(403);
        }
    }
    public function buat(Request $request)
    {
        if(auth()->user()->role == 'administrasi'){
        $search = Crypt::decrypt($request->input('id'));
        $nasabah = Nasabah::where('id',$search)
            ->get();
        return view('dashboard.pinjaman.buat-pinjaman-baru')->with('nasabah', $nasabah);
        }else {
            abort(403);
        }
    }
    public function search(Request $request){
        if(auth()->user()->role == 'administrasi'){
        $search = $request->input('search');
        $nasabah = Nasabah::query()->orderby('created_at', 'DESC') 
        ->where(function (Builder $query){
            $query->where('keanggotaan','!=','calon anggota');
        })
        ->where(function (Builder $query) {
            $query->whereRelation('pinjaman','status','!=','Belum Lunas')        
            ->orDoesntHave('pinjaman');
            })->where(function (Builder $query) use($search) {
                $query->whereRelation('user','nama','LIKE',"%$search%");
            })            
        ->get();       
        return view('dashboard.pinjaman.result')->with('nasabah', $nasabah);
        }else {
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
        if(auth()->user()->role == 'administrasi'){
            if($request->input('pemindahan') != 'baru'){
                $dataPinjaman = $request->validate([
                    'ktp' => 'required',
                    'kk' => 'required',
                    'nasabah_id' => 'required',
                    'pinjaman' => 'required',
                    'jaminan' => 'required',
                    'bunga' => 'required',
                    'lama_angsuran' => 'required',
                    'sudah_mengangsur' => 'required',
                    'tgl_angsuran' => 'required',
                    'sisa_pinjaman' => 'required'
                ]); 
                $tglAngsuran = $dataPinjaman['tgl_angsuran'];
                $future_timestamp = strtotime($tglAngsuran."+1 month");
            }else{
                $dataPinjaman = $request->validate([
                    'ktp' => 'required',
                    'kk' => 'required',
                    'nasabah_id' => 'required',
                    'pinjaman' => 'required',
                    'jaminan' => 'required',
                    'bunga' => 'required',
                    'lama_angsuran' => 'required',
                    'sudah_mengangsur' => 'required'
                ]); 
                
                $dataPinjaman['sudah_mengangsur'] = 0 ;
                $future_timestamp = strtotime("+1 month");
            }
            $dataPinjaman['tgl_angsuran'] = date('Y-m-d', $future_timestamp);
            $dataPinjaman['pinjaman'] = str_replace("Rp.", "", $dataPinjaman['pinjaman']);
            $dataPinjaman['pinjaman'] = str_replace(".", "", $dataPinjaman['pinjaman']);
            $dataPinjaman['pinjaman'] = intval($dataPinjaman['pinjaman']);
            $dataPinjaman['angsuran_pokok'] = intval(ceil(floatval($dataPinjaman['pinjaman']) / floatval($dataPinjaman['lama_angsuran'])));
            $dataPinjaman['status'] = 'Belum Lunas';
            if($request->input('pemindahan') == 'baru'){
                
                $dataPinjaman['sisa_pinjaman'] = $dataPinjaman['pinjaman'];
                $dataPinjaman['bunga_dibayar'] = intval(ceil(floatval($dataPinjaman['sisa_pinjaman']) * floatval($dataPinjaman['bunga']))) ;
                $dataTransaksi['jumlah'] = $dataPinjaman['pinjaman'] ;
                $dataTransaksi['jenis'] = 'pemberian pinjaman';
                $dataTransaksi['sisa_pinjaman'] = $dataPinjaman['sisa_pinjaman'];
            }else{
                $dataPinjaman['sisa_pinjaman'] = str_replace("Rp.", "", $dataPinjaman['sisa_pinjaman']);
                $dataPinjaman['sisa_pinjaman'] = str_replace(".", "", $dataPinjaman['sisa_pinjaman']);
                $dataPinjaman['sisa_pinjaman'] = intval($dataPinjaman['sisa_pinjaman']);
                $dataPinjaman['bunga_dibayar'] = intval(ceil(floatval($dataPinjaman['sisa_pinjaman']) * floatval($dataPinjaman['bunga']))) ;
                $dataTransaksi['jumlah'] = $dataPinjaman['sisa_pinjaman'] ;
                $dataTransaksi['jenis'] = 'pemindahan';
                $dataTransaksi['sisa_pinjaman'] = $dataPinjaman['sisa_pinjaman'];
            }

        $id = $dataPinjaman['nasabah_id'];
        $pinjamanKTP = $dataPinjaman['ktp'];
        $pinjaman = Pinjaman::query()->orderby('created_at', 'DESC') 
        ->where(function (Builder $query) use($pinjamanKTP) {
            $query->where('ktp',"$pinjamanKTP");
            })->where(function (Builder $query) {
                $query->where('status','Belum Lunas');      
            })->get();        
        if(count($pinjaman) > 0){
            $validator = Validator::make($request->all(), [
                'ktp' => [
                    function ($attribute, $value, $fail) {
                            $fail('Nomor KTP ini masih memiliki pinjaman yang Belum Dilunasi !');
                    },
                ],
            ]);
            if ($validator->fails()) {
                return redirect('/dashboard/data-pinjaman/buat?id='.Crypt::encrypt($id))
                            ->withErrors($validator)
                            ->withInput();
            }
        }
            $pinjaman = Pinjaman::create($dataPinjaman);
            $dataTransaksi['pinjaman_id'] = $pinjaman->id ;
            TransaksiPinjaman::create($dataTransaksi);
            if($request->input('pemindahan') == 'baru'){
                $transaksi['pinjaman_id'] = $pinjaman->id ;
                $transaksi['jenis'] = 'biaya administrasi' ;
                $transaksi['jumlah'] = $request->input('potongan-admin');
                $transaksi['sisa_pinjaman'] = $pinjaman->sisa_pinjaman ;
                TransaksiPinjaman::create($transaksi);
            }
        return redirect('/dashboard/data-pinjaman')->with('success', 'Data Pinjaman baru Telah Ditambahkan ! ');

    } else {
        abort(403);
    }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pinjaman  $pinjaman
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        if(auth()->user()->role == 'administrasi' || auth()->user()->role == 'kasir' || auth()->user()->role == 'admin' || auth()->user()->role == 'nasabah'){
            if(auth()->user()->role == 'nasabah'){
                $myN = Pinjaman::query()->where('id',$id)->whereRelation('nasabah','users_id',auth()->user()->id)->count();
                if($myN == 0){
                    abort(403);
                }
            }
        return view('dashboard.pinjaman.detail-pinjaman',[
            'pinjaman' => Pinjaman::findOrFail($id),
            'transaksi' => TransaksiPinjaman::where('pinjaman_id' ,$id)->get(),
        ]);
        }else {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pinjaman  $pinjaman
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(auth()->user()->role == 'administrasi'){
        $id = Crypt::decrypt($id);
        return view('dashboard.pinjaman.edit-pinjaman',[
            'pinjaman' => Pinjaman::findOrFail($id)
        ]);
        }else {
            abort(403);
        }
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pinjaman  $pinjaman
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pinjaman $pinjaman)
    {
        if(auth()->user()->role == 'administrasi'){
        function rupiahToInt($val){
            $val = str_replace("Rp.", "", $val);
            $val = str_replace(".", "", $val);
            $val = intval($val) ;
            return $val ;
        }
        $id = $request['id'];
        $id = Crypt::encrypt($id);
        $data = [];
        
        $data['sudah_mengangsur'] = 0 ;
        $data['status'] = "Belum Lunas";
        $future_timestamp = strtotime("+1 month");
        $data['tgl_angsuran'] = date('Y-m-d', $future_timestamp);
        $data['pinjaman'] = $request['pinjaman'];
        $data['pinjaman'] = str_replace("Rp.", "", $data['pinjaman']);
        $data['pinjaman'] = str_replace(".", "", $data['pinjaman']);
        $data['pinjaman'] = intval($data['pinjaman']);
        $data['sisa_pinjaman'] = $request['pinjaman'];
        $data['bunga'] = $request['bunga'];
        $data['bunga_dibayar'] = $request['bunga_dibayar'];
        $data['lama_angsuran'] = $request['lama_angsuran'];
        $data['angsuran_pokok'] = $request['angsuran_pokok'];
        $data['sisa_pinjaman'] = rupiahToInt($data['sisa_pinjaman']);
        $data['bunga_dibayar'] = rupiahToInt($data['bunga_dibayar']);
        $data['angsuran_pokok'] = rupiahToInt($data['angsuran_pokok']);
        if($request['tambah_lama_angsuran'] == null && $request['jumlah'] == null){
            $pp = Pinjaman::findOrFail(Crypt::decrypt($id));
            $data['tgl_angsuran'] = $pp->tgl_angsuran ;
         }
        Pinjaman::where('id', $request['id'])
        ->update($data);
        $potonganAdmin = intval($request->input('potongan-admin'));
        $jumlah = $request['jumlah'];
        $jumlah = str_replace("Rp.", "", $jumlah);
        $jumlah = str_replace(".", "", $jumlah);
        $jumlah = intval($jumlah);
        if($potonganAdmin != 0){
                if($jumlah != 0 ){
                    $transaksi['jumlah'] = $jumlah ;
                    $transaksi['jenis'] = 'pemberian pinjaman';
                }else{
                    $transaksi['jumlah'] = $request['tambah_lama_angsuran'];
                    $transaksi['jenis'] = 'penambahan waktu angsuran';
                }
                $transaksi['sisa_pinjaman'] = $data['pinjaman'];
                $transaksi['pinjaman_id'] = $request['id'];
                TransaksiPinjaman::create($transaksi);
                $transaksi['jumlah'] = $potonganAdmin;
                $transaksi['jenis'] = 'biaya administrasi';
                TransaksiPinjaman::create($transaksi);
        }
        return redirect('/dashboard/data-pinjaman/'.$id)->with('success', 'Berhasil Memperbarui Pinjaman !');
        }else{
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pinjaman  $pinjaman
     * @return \Illuminate\Http\Response
     */
    public function destroy($pinjaman)
    {
        if(auth()->user()->role == 'administrasi'){
        $p = Pinjaman::query()->where('id',$pinjaman)->first();
        if($p['status'] != 'lunas'){
            $angsuranPinjaman = TransaksiPinjaman::query()
            ->where('pinjaman_id',$p['id'])
            ->where('jenis','!=','pemindahan')->where('jenis','!=','pemberian pinjaman')->count();
            if($angsuranPinjaman > 0){
                return redirect('/dashboard/data-pinjaman')->with('gagal', 'Tidak Dapat Menghapus Data Pinjaman Yang Masih Berjalan !');
            }else {
                Pinjaman::destroy($pinjaman);
                DB::table('transaksi_pinjaman')->where('pinjaman_id', $p['id'])->delete();
                return redirect('/dashboard/data-pinjaman')->with('success', 'Berhasil Menghapus Data Pinjaman !');
            }
        }else {
            return redirect('/dashboard/data-pinjaman')->with('gagal', 'Tidak Dapat Menghapus Data Pinjaman yang sudah lunas, untuk pengelolaan data !');
            // Pinjaman::destroy($pinjaman);
            // return redirect('/dashboard/data-pinjaman')->with('success', 'Berhasil Menghapus Data Pinjaman !');
        }
        }else{
            abort(403);
        }
    }
    
}

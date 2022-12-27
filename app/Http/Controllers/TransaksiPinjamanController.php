<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use Illuminate\Http\Request;
use App\Models\TransaksiPinjaman;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiPinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.transaksi.tr_pinjaman',[
            'pinjaman' => Pinjaman::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $search = Crypt::decrypt($request->input('id'));
        $pId = $request->input('id');
        $pinjaman = Pinjaman::where('id',$search)
        ->get();
        if($pinjaman[0]->lama_angsuran == $pinjaman[0]->sudah_mengangsur){
            return redirect('/dashboard/data-pinjaman/'.$pId.'/edit');
        }
        return view('dashboard.transaksi.pinjaman.buat_transaksi')->with('peminjam', $pinjaman);
    }
    public function search(Request $request){
        $search = $request->input('search');
        $pinjaman = Pinjaman::query()->orderby('created_at', 'DESC')
        ->where(function (Builder $query) use($search) {
            $query->where('id',"$search")     
            ->orWhereHas('nasabah', function ($nasabah) use ($search) {
                $nasabah->whereHas('user', function ($user) use ($search) {
                    $user->where('nama', 'LIKE', "%$search%");
                });       
            }) ;
        })->where('status','!=','Lunas')   
        ->get();   


        return view('dashboard.transaksi.pinjaman.result')->with('peminjam', $pinjaman);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $transaksi = $request->validate([
            'pinjaman_id' => 'required',
            'jenis' => 'required',
            'jumlah' => 'required'
        ]); 
        $pId = Crypt::encrypt($transaksi['pinjaman_id']);
        $transaksi['bunga'] = $request['bunga-dibayar'];
        $pinjaman = Pinjaman::where('id',$transaksi['pinjaman_id'])->get();
        $data['status'] = $pinjaman[0]['status'];
        $data['id'] = $transaksi['pinjaman_id'] ;
        $bunga_harian = $request['bunga_harian'] ;
        $transaksi['keterangan'] = $request['keterangan']; 
        $future_timestamp = strtotime("+1 month"); 
        if ($transaksi['jenis'] == 'Full Angsuran'){
            if($pinjaman[0]['angsuran_pokok'] < $pinjaman[0]['sisa_pinjaman']){
                $transaksi['angsuran'] = $pinjaman[0]['angsuran_pokok'];       
                $transaksi['jumlah'] = $pinjaman[0]['angsuran_pokok'] + $pinjaman[0]['bunga_dibayar'] + $bunga_harian ;
                $data['sisa_pinjaman'] = $pinjaman[0]['sisa_pinjaman'] - $pinjaman[0]['angsuran_pokok'] ;
                $data['bunga_dibayar'] = ceil($data['sisa_pinjaman'] * $pinjaman[0]['bunga']) ;
                $transaksi['sisa_pinjaman'] = $data['sisa_pinjaman'];
            }else{
                $total = str_replace("Rp.", "", $request->input('total'));
                $total = str_replace(".", "", $total);
                $total = intval($total) ;
                $transaksi['jumlah'] = $total; 
                $transaksi['angsuran'] = $pinjaman[0]['sisa_pinjaman']; 
                $transaksi['sisa_pinjaman'] =  0 ;
                $data['sisa_pinjaman'] = 0 ;
                $data['bunga_dibayar'] = 0; 
            }
            $data['tgl_angsuran'] = date('Y-m-d', $future_timestamp);
            $data['sudah_mengangsur'] = $pinjaman[0]['sudah_mengangsur'] + 1 ;
            
            Pinjaman::where('id', $data['id'])
            ->update($data);
            TransaksiPinjaman::create($transaksi);
        }
        elseif($transaksi['jenis'] == 'Pelunasan'){
            $transaksi['jumlah'] = $pinjaman[0]['sisa_pinjaman'] + $pinjaman[0]['bunga_dibayar'] + $bunga_harian ;
            $transaksi['angsuran'] = $pinjaman[0]['sisa_pinjaman'];
            $data['sudah_mengangsur'] = $pinjaman[0]['sudah_mengangsur'] + 1 ;
            $data['sisa_pinjaman'] = 0 ;
            $data['bunga_dibayar'] = 0 ;
            $data['angsuran_pokok'] = 0 ;
            $data['sudah_mengangsur'] = $pinjaman[0]['lama_angsuran'];
            $data['tgl_angsuran'] = null ;
            $transaksi['sisa_pinjaman'] = $data['sisa_pinjaman'];
            // dd($data);
            Pinjaman::where('id', $data['id'])
            ->update($data);
            TransaksiPinjaman::create($transaksi);
        

            // echo $pinjaman[0]['bunga_dibayar'] ;
            // Pinjaman::where('id',$transaksi['pinjaman_id'])
            // Post::where('id', $post->id)
            // ->update($validateData);
        } else if ($transaksi['jenis'] == 'Hanya Bunga') {
            $data['tgl_angsuran'] = date('Y-m-d', $future_timestamp);
            $data['sudah_mengangsur'] = $pinjaman[0]['sudah_mengangsur'] + 1 ;
            $transaksi['jumlah'] = $pinjaman[0]['bunga_dibayar'] + $bunga_harian ;
            $transaksi['sisa_pinjaman'] = $pinjaman[0]['sisa_pinjaman'];
            $transaksi['angsuran'] = 0 ;
            // dd($data);
            Pinjaman::where('id', $data['id'])
            ->update($data);
            TransaksiPinjaman::create($transaksi);
        } else {
            $transaksi['jumlah'] = str_replace("Rp.", "", $transaksi['jumlah']);
            $transaksi['jumlah'] = str_replace(".", "", $transaksi['jumlah']);
            $jumlah = intval($transaksi['jumlah']) ;
            // dd($transaksi);
            if( $jumlah < ($pinjaman[0]['bunga_dibayar'] + $bunga_harian)){
                $validator = Validator::make($request->all(), [
                    'jumlah' => [
                        function ($attribute, $value, $fail) {
                                $fail('Nominal yang dimasukan kurang dari jumlah bunga yang harus dibayar !');
                        },
                    ],
                ]);
                if ($validator->fails()) {
                    return redirect('/dashboard/transaksi-pinjaman/create?id='.$pId)
                                ->withErrors($validator)
                                ->withInput();
                }
            } else {
                $data['tgl_angsuran'] = date('Y-m-d', $future_timestamp);
                $data['sudah_mengangsur'] = $pinjaman[0]['sudah_mengangsur'] + 1 ;
                $data['sisa_pinjaman'] = $pinjaman[0]['sisa_pinjaman'] - ($jumlah- ($pinjaman[0]['bunga_dibayar']+ $bunga_harian)) ;
                $transaksi['angsuran'] = $jumlah- ($pinjaman[0]['bunga_dibayar']+ $bunga_harian) ;
                $data['bunga_dibayar'] = ceil($data['sisa_pinjaman'] * $pinjaman[0]['bunga']);
                $transaksi['sisa_pinjaman'] = $data['sisa_pinjaman'];
                Pinjaman::where('id', $data['id'])
                ->update($data);
                TransaksiPinjaman::create($transaksi);
            }
            
        } 
        $sisaPinjaman = ($pinjaman[0]['sisa_pinjaman'] + $pinjaman[0]['bunga_dibayar'] + $bunga_harian) - $transaksi['jumlah'] ;
        // dd($sisaPinjaman,$pinjaman,$transaksi);
        if($sisaPinjaman <= 0){
            $data['status'] = 'Lunas';
            $data['sisa_pinjaman'] = 0 ;
            $data['bunga_dibayar'] = 0 ;
            $data['angsuran_pokok'] = 0 ;
            $data['sudah_mengangsur'] = $pinjaman[0]['lama_angsuran'];
            $data['tgl_angsuran'] = null ;
            Pinjaman::where('id', $data['id'])
            ->update($data);
        }
        // dd($data['sudah_mengangsur'] ,$pinjaman[0]['lama_angsuran'] );
        if($data['sudah_mengangsur'] == $pinjaman[0]['lama_angsuran'] && $data['status'] != "Lunas"){
            return redirect('/dashboard/data-pinjaman/'.$pId.'/edit');
        } else {
            return redirect('/dashboard/data-pinjaman/'.$pId)->with('success', 'Berhasil Melakukan Transaksi!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransaksiPinjaman  $transaksiPinjaman
     * @return \Illuminate\Http\Response
     */
    public function show(TransaksiPinjaman $transaksiPinjaman)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransaksiPinjaman  $transaksiPinjaman
     * @return \Illuminate\Http\Response
     */
    public function edit(TransaksiPinjaman $transaksiPinjaman)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransaksiPinjaman  $transaksiPinjaman
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransaksiPinjaman $transaksiPinjaman)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransaksiPinjaman  $transaksiPinjaman
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransaksiPinjaman $transaksiPinjaman)
    {
        //
    }
}

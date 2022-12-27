<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\Shu;
use App\Models\Tabungan;
use App\Models\TransaksiPinjaman;
use App\Models\TransaksiShu;
use App\Models\TransaksiTabungan;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function index(Request $request) {

        $dari = '';
        $sampai = '';
        $filter = 'hari ini';
        if($request['filter'] == null) {
            $request['filter'] = $filter;
        }

        $role = auth()->user()->role ;
        
        $anggota = Nasabah::query()->where('keanggotaan','anggota')->count();
        $anggotaAlit = Nasabah::query()->where('keanggotaan','anggota alit')->count();
        $calonAnggota = Nasabah::query()->where('keanggotaan','calon anggota')->count();
        $shu = Nasabah::query()->sum('shu');
        $tabunganReguler = Tabungan::query()->where('jenis','reguler')->where('status','!=','selesai');
        $tabunganProgram = Tabungan::query()->where('jenis','program')->where('status','!=','selesai');
        $tabunganBerjangka = Tabungan::query()->where('jenis','berjangka')->where('status','!=','selesai');
        $pinjaman = Pinjaman::query()->where('status','!=','selesai');
        $setoranReguler = TransaksiTabungan::query()->whereRelation('tabungan','jenis','reguler')->where('jenis','setoran');
        $penarikanReguler = TransaksiTabungan::query()->whereRelation('tabungan','jenis','reguler')->where('jenis','penarikan');
        $perbaikanReguler = TransaksiTabungan::query()->whereRelation('tabungan','jenis','reguler')->where('jenis','perbaikan');
        $setoranProgram = TransaksiTabungan::query()->whereRelation('tabungan','jenis','program')->where('jenis','setoran');
        $penarikanProgram = TransaksiTabungan::query()->whereRelation('tabungan','jenis','program')->where('jenis','penarikan');
        $setoranBerjangka = TransaksiTabungan::query()->whereRelation('tabungan','jenis','Berjangka')->where('jenis','setoran berjangka');
        $penarikanBerjangka = TransaksiTabungan::query()->whereRelation('tabungan','jenis','berjangka')->Where('jenis','penarikan tabungan berjangka');
        $penarikanBungaBerjangka = TransaksiTabungan::query()->whereRelation('tabungan','jenis','berjangka')->where('jenis','penarikan bunga');


        $bungaReguler = TransaksiTabungan::query()->whereRelation('tabungan','jenis','reguler')->where('jenis','penambahan bunga');
        $bungaProgram = TransaksiTabungan::query()->whereRelation('tabungan','jenis','program')->where('jenis','setoran');
        $bungaBerjangka = TransaksiTabungan::query()->whereRelation('tabungan','jenis','berjangka')->where('jenis','setoran berjangka');
        $bungaPinjaman = TransaksiPinjaman::query()->where('jenis','!=','pemindahan')->where('jenis','!=','pemberian pinjaman')->where('jenis','!=','biaya administrasi')->where('jenis','!=','penambahan waktu angsuran');
        $adminPinjaman = TransaksiPinjaman::query()->where('jenis','biaya administrasi');
        

        $baruPinjaman = TransaksiPinjaman::query()->where('jenis','pemberian pinjaman');
        $angsuranPinjaman = TransaksiPinjaman::query()->where('jenis','!=','pemindahan')->where('jenis','!=','pemberian pinjaman');
        $penambahanShu = Shu::query();
        $pengambilanShu = TransaksiShu::query()->where('jenis','pengambilan shu');

        if($role == 'kolektor'){
            $tabunganReguler = $tabunganReguler->where('users_id',auth()->user()->id);
            $tabunganProgram = $tabunganProgram->where('users_id',auth()->user()->id);
            $setoranReguler = $setoranReguler->where('users_id',auth()->user()->id);
            $perbaikanReguler = $perbaikanReguler->where('users_id',auth()->user()->id);
            $setoranProgram = $setoranProgram->where('users_id',auth()->user()->id);
        }elseif($role == 'nasabah'){
            $nId = Nasabah::query()->where('users_id',auth()->user()->id)->first();
            $nId = $nId['id'];
            $tabunganReguler = $tabunganReguler->whereRelation('nasabah','id',$nId);
            $tabunganProgram = $tabunganProgram->whereRelation('nasabah','id',$nId);
            $tabunganBerjangka = $tabunganBerjangka->whereRelation('nasabah','id',$nId);
            $pinjaman = $pinjaman->whereRelation('nasabah','id',$nId);
            $setoranReguler = $setoranReguler
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            });  
            $setoranProgram = $setoranProgram
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            });  
            $setoranBerjangka = $setoranBerjangka
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            });  
            $angsuranPinjaman = $angsuranPinjaman
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('pinjaman', function (Builder $pinjaman) use ($nId)  {
                $pinjaman->whereRelation('nasabah','id',$nId);
                });            
            }); 
            $baruPinjaman = $baruPinjaman
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('pinjaman', function (Builder $pinjaman) use ($nId)  {
                $pinjaman->whereRelation('nasabah','id',$nId);
                });            
            }); 

            $penarikanReguler = $penarikanReguler
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            });
            $penarikanProgram = $penarikanProgram
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            });
            $penarikanBerjangka = $penarikanBerjangka
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            });
            $penarikanBungaBerjangka = $penarikanBungaBerjangka
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            }); 

            $bungaReguler = $bungaReguler
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            }); 

            $bungaProgram = $bungaProgram
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('tabungan', function (Builder $tabungan) use ($nId)  {
                $tabungan->whereRelation('nasabah','id',$nId);
                });            
            }); 
            $adminPinjaman = $adminPinjaman
            ->where(function (Builder $query) use($nId) {
                $query->whereHas('pinjaman', function (Builder $pinjaman) use ($nId)  {
                $pinjaman->whereRelation('nasabah','id',$nId);
                });            
            }); 
            
            


        }



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
            $setoranReguler = $setoranReguler
            ->whereBetween('created_at', [$dari, $sampai]);
            $perbaikanReguler = $perbaikanReguler
            ->whereBetween('created_at', [$dari, $sampai]);
            $penarikanReguler = $penarikanReguler
            ->whereBetween('created_at', [$dari, $sampai]);
            $setoranProgram = $setoranProgram
            ->whereBetween('created_at', [$dari, $sampai]);
            $penarikanProgram = $penarikanProgram
            ->whereBetween('created_at', [$dari, $sampai]);
            $setoranBerjangka = $setoranBerjangka
            ->whereBetween('created_at', [$dari, $sampai]);
            $penarikanBerjangka = $penarikanBerjangka
            ->whereBetween('created_at', [$dari, $sampai]);
            $penarikanBungaBerjangka = $penarikanBungaBerjangka
            ->whereBetween('created_at', [$dari, $sampai]);
            $bungaBerjangka = $bungaBerjangka
            ->whereBetween('created_at', [$dari, $sampai]);
            $bungaProgram = $bungaProgram
            ->whereBetween('created_at', [$dari, $sampai]);
            $bungaReguler = $bungaReguler
            ->whereBetween('created_at', [$dari, $sampai]);
            $bungaPinjaman = $bungaPinjaman
            ->whereBetween('created_at', [$dari, $sampai]);
            $adminPinjaman = $adminPinjaman
            ->whereBetween('created_at', [$dari, $sampai]);
            $baruPinjaman = $baruPinjaman
            ->whereBetween('created_at', [$dari, $sampai]);
            $angsuranPinjaman = $angsuranPinjaman
            ->whereBetween('created_at', [$dari, $sampai]);
            $penambahanShu = $penambahanShu
            ->whereBetween('created_at', [$dari, $sampai]);
            $pengambilanShu = $pengambilanShu
            ->whereBetween('created_at', [$dari, $sampai]);
        }else{
            $filter = $request['filter'];
            
        }

        /* GET SETORAN */
        $setoranReguler = intval($setoranReguler->sum('jumlah'));
        $perbaikanReguler = intval($perbaikanReguler->sum('jumlah'));
        $setoranReguler = $setoranReguler + $perbaikanReguler ;
        $setoranProgram = intval($setoranProgram->sum('jumlah'));
        $setoranBerjangka = intval($setoranBerjangka->sum('jumlah'));
        $penarikanReguler = $penarikanReguler->sum('jumlah');
        $penarikanProgram = $penarikanProgram->sum('jumlah');
        $penarikanBerjangka = $penarikanBerjangka->sum('jumlah');
        $penarikanBungaBerjangka = $penarikanBungaBerjangka->sum('jumlah');
        $penarikanBerjangka = $penarikanBerjangka + $penarikanBungaBerjangka ;


        /* GET BUNGA */
        $bungaReguler = $bungaReguler->sum('jumlah');
        $bungaProgram =  $bungaProgram->sum('bunga');
        $bungaBerjangka = $bungaBerjangka->sum('bunga');
        $bungaPinjaman = $bungaPinjaman->get();
        $totalBungaPinjaman = 0 ;
        foreach($bungaPinjaman as $bp){
            $totalBungaPinjaman += ($bp->jumlah - $bp->angsuran);
        }
        $adminPinjaman = $adminPinjaman->sum('jumlah');

        /* GET PINJAMAN */
        $baruPinjaman = $baruPinjaman->sum('jumlah');
        $angsuranPinjaman = $angsuranPinjaman->sum('angsuran');

        /* GET SHU */
        $pengambilanShu = $pengambilanShu->sum('jumlah');
        $penambahanShu = $penambahanShu->sum('total');

        /* GET DATA TOTAL */
        $sumReguler = $tabunganReguler->sum('total');
        $countReguler = $tabunganReguler->count();
        $sumProgram = $tabunganProgram->sum('total');
        $countProgram = $tabunganProgram->count();
        $sumBerjangka = $tabunganBerjangka->sum('total');
        $countBerjangka = $tabunganBerjangka->count();
        $sumPinjaman = $pinjaman->sum('sisa_pinjaman');
        $countPinjaman = $pinjaman->count();

        if($role == 'nasabah'){
            $tabunganBerjangka = $tabunganBerjangka->get();
            $bungaBerjangkaDptDitarik = 0 ;
            foreach($tabunganBerjangka as $tb){
                $tgl_selesai = date_create($tb->tgl_selesai);//8
                $tgl_mulai = date_create($tb->tgl_mulai);
                $now = date('Y-m-d');
                $now = date_create($now); // waktu sekarang
                $jarak = $now->diff($tgl_mulai);
                $berjalan = $jarak->y * 12 ;
                $berjalan = $berjalan + $jarak->m ;
                if($berjalan > $tb->lama_program){
                $berjalan = $tb->lama_program;
                }
                $bunga_dpt_ditarik = (($tb->bunga * $tb->jum_deposito)*$berjalan)- $tb->bunga_diambil;
                $bungaBerjangkaDptDitarik =+ $bunga_dpt_ditarik ;
            }
        }
        /* SAVE TO ARRAY */
        $nasabah = array(
            'anggota' => $anggota,
            'anggota_alit' => $anggotaAlit,
            'calon_anggota' => $calonAnggota,
            'shu' => $shu
        );
        $data = array(
            'sumReguler' => $sumReguler,
            'sumProgram' => $sumProgram,
            'sumBerjangka' => $sumBerjangka,
            'sumPinjaman' => $sumPinjaman,
            'countReguler' => $countReguler,
            'countProgram' => $countProgram,
            'countBerjangka' => $countBerjangka,
            'countPinjaman' => $countPinjaman
        );

        $setoran = array(
            'reguler' => $setoranReguler ,
            'program' => $setoranProgram,
            'berjangka' => $setoranBerjangka
        );
        $penarikan = array(
            'reguler' => $penarikanReguler ,
            'program' => $penarikanProgram,
            'berjangka' => $penarikanBerjangka
        );

        if($role != 'nasabah'){
            $bunga = array(
                'reguler' => $bungaReguler,
                'program' => $bungaProgram,
                'berjangka' => $bungaBerjangka,
                'pinjaman' => $totalBungaPinjaman
            );
        }else{
            $bunga = array(
                'reguler' => $bungaReguler,
                'program' => $bungaProgram,
                'berjangka' => $bungaBerjangkaDptDitarik
            );
        }
        

        $pinjaman = array(
            'baru' => $baruPinjaman ,
            'angsuran' => $angsuranPinjaman,
            'biayaAdmin' => $adminPinjaman 
        );

        $shu = array (
            'penambahan' => $penambahanShu ,
            'pengambilan' => $pengambilanShu 
        );
        if($role == 'administrasi' || $role == 'kasir' || $role == 'admin'){
            return view('dashboard.index',[
                'nasabah' => $nasabah,
                'data' => $data,
                'setoran' => $setoran,
                'penarikan' => $penarikan,
                'bunga' => $bunga,
                'pinjaman' => $pinjaman,
                'shu' => $shu,
                'filter' => $filter ,
                'dari' => $dari,
                'sampai' => $sampai
            ]);
        }elseif($role == 'kolektor'){
            return view('dashboard.index',[
                'tabunganReguler' => $data['countReguler'],
                'tabunganProgram' => $data['countProgram'],
                'totalReguler' => $data['sumReguler'],
                'totalProgram' => $data['sumProgram'],
                'setoranReguler' => $setoranReguler,
                'setoranProgram' => $setoranProgram ,
                'dari' => $dari,
                'sampai' => $sampai,
                'filter' => $filter
            ]);
        }else{
            return view('dashboard.index',[
                'data' => $data,
                'setoran' => $setoran,
                'penarikan' => $penarikan,
                'bunga' => $bunga,
                'pinjaman' => $pinjaman,
                'biayaAdmin' => $adminPinjaman,
                'filter' => $filter ,
                'dari' => $dari,
                'sampai' => $sampai
            ]);
        }
    }
}

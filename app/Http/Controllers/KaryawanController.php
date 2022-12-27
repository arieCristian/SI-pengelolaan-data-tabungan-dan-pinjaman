<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use App\Models\TransaksiPinjaman;
use App\Models\TransaksiShu;
use App\Models\TransaksiTabungan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.karyawan.data-karyawan',[
            'karyawan' => User::query()->where('role','!=','nasabah')
            ->where('id','!=',auth()->user()->id)
            ->where('nonaktif', null)
            ->orderBy('role','asc')
            ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.karyawan.buat-karyawan');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userData = $request->validate([
            'nama' => 'required|max:255',
            'username' => 'required|unique:users',
            'password' => 'required|min:5|max:255|confirmed',
            'no_telp' => 'required',
            'role' => 'required'
        ]); 
        $userData['password'] = bcrypt($userData['password']);
        User::create($userData); 
        return redirect('/dashboard/data-karyawan')->with('success', 'Berhasil Menambahkan Karyawan Baru !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id) ;
        $user = User::findOrFail($id);
        $tabunganReguler = Tabungan::query()
        ->where('jenis','reguler')
        ->where('status','!=','selesai')
        ->where('users_id',$id) ;
        $tabunganProgram = Tabungan::query()
        ->where('jenis','program')
        ->where('status','!=','selesai')
        ->where('users_id',$id) ;
        $tabungan['reguler'] = $tabunganReguler->count();
        $tabungan['total_reguler'] = $tabunganReguler->sum('total');
        $tabungan['program'] = $tabunganProgram->count();
        $tabungan['total_program'] = $tabunganProgram->sum('total');

        return view('dashboard.karyawan.detail-karyawan',[
            'tabungan' => $tabungan,
            'user' => $user,
            'tabunganReguler' => $tabunganReguler->paginate(3),
            'tabunganProgram' => $tabunganProgram->paginate(3)
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('dashboard.karyawan.edit-karyawan',[
            'karyawan' => User::findOrFail(Crypt::decrypt($id))
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'no_telp' => 'max:255',
            'nama' => 'required|max:255'
        ];
        $dataUsers = [];
        $n = User::findOrFail($id);
        if($request['username'] != $n->username){
            $rules['username'] = 'required|unique:users|min:5|max:255';
        }else {
            $dataUsers['username'] = $request['username'];
        }
        if($request['password'] != null ){
            $rules['password'] = 'required|min:5|max:255|confirmed';
        }
        $dataUsers  += $request->validate($rules);
        if($request['password'] != null ){
            $dataUsers['password'] = bcrypt($dataUsers['password']);
        }
        User::where('id',$id)->update($dataUsers);
        return redirect('/dashboard/data-karyawan')->with('success', 'Data Karyawan Telah Diberbarui !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user = $user->role ;
        if($user == 'kolektor'){
        $tabungan = Tabungan::query()->where('users_id',$id)->where('status','!=','selesai')->count();
        $trTabungan = TransaksiTabungan::query()->where('users_id',$id)->count();
        if($tabungan > 0 ){
            return redirect('/dashboard/data-karyawan')->with('gagal', 'Tidak Dapat Menghapus Data Staf Kolektor Yang Masih Memiliki Tabungan Berjalan, Harap Memindahkan Tabungan ke Kolektor Yang Lain Terlebih Dahulu !');
        }else{
            if($trTabungan > 0){
            
            function getRandom($n) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
                for ($i = 0; $i < $n; $i++) {
                    $index = rand(0, strlen($characters) - 1);
                    $randomString .= $characters[$index];
                }
                return $randomString;
            }
            $data['nonaktif'] = 1;
            $data['password'] = bcrypt(getRandom(10));
            $data['no_telp'] = '-';
            User::where('id',$id)->update($data);
            return redirect('/dashboard/data-karyawan')->with('success', 'Data Staf Kolektor Tidak dihapus karena memiliki transaksi tabungan, kolektor hanya dinonaktifkan agar data transaksi masih dapat dikelola !');
            }else {
                User::destroy($id);
                return redirect('/dashboard/data-karyawan')->with('success', 'Data Staf Kolektor Berhasil Dihapus !');
            }
        }
        }elseif($user == 'administrasi'){
            $tabungan = Tabungan::query()->where('users_id',$id)->where('status','!=','selesai')->count();
            $trTabungan = TransaksiTabungan::query()->where('users_id',$id)->count();
            if($tabungan > 0 || $trTabungan > 0){
                return redirect('/dashboard/data-karyawan')->with('gagal', 'Tidak Dapat Menghapus Data Staf Administrasi yang sudah memiliki tabungan atau pernah melakukan transaksi apapun, harap hanya melakukan perbauran Data Administrasi !');
            }else {
                User::destroy($id);
                return redirect('/dashboard/data-karyawan')->with('success', 'Data Staf Administrasi Berhasil Dihapus !');
            }
        }else {
            User::destroy($id);
                return redirect('/dashboard/data-karyawan')->with('success', 'Data Pengguna Berhasil Dihapus !');
        }
    }
}

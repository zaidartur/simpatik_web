<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:aplikasi', ['only' => ['index', 'store', 'update', 'destroy', 'change_pwd', 'check_user']]);
    }

    public function index()
    {
        $data = [
            'lists'     => User::all(),
            'instansi'  => DB::table('instansi')->get(),
            'badge'     => [
                'administrator' => 'badge-primary',
                'operator'      => 'badge-secondary',
            ],
        ];
        
        return view('main.users', $data);
    }

    public function save(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'email'     => 'nullable|email',
            'instansi'  => 'required|string',
            'level'     => 'required|string',
            'username'  => 'required|string|unique:users|max:50',
            'password'  => 'required|string|max:50',
        ]);

        $ins  = DB::table('instansi')->where('kode', $request->instansi)->first();
        $uuid = Str::uuid();
        $user = new User();
        $user->uuid         = $uuid;
        $user->nama_lengkap = $request->nama;
        $user->username     = $request->username;
        $user->email        = $request->email;
        $user->jurusan      = $ins->instansi;
        $user->level        = $request->level;
        $user->password     = Hash::make($request->password);
        $user->blokir       = 'N';
        $user->kode         = 'ID3331';
        if ($user->save()) {
            $users = User::where('uuid', $uuid)->first();
            if ($request->level == 'administrator') {
                $users->assignRole('administrator');
            } else {
                $users->assignRole($request->instansi);
            }
            return redirect()->back()->with('success', 'User berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('failed', 'User gagal ditambahkan.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'uid'       => 'required|numeric',
            'nama'      => 'required|string|max:100',
            'email'     => 'nullable|email',
            'instansi'  => 'required|string',
            'level'     => 'required|string',
        ]);
        $user = User::find($request->uid);
        if (!$user) return redirect()->back()->with('failed', 'User tidak ditemukan.');

        $ins = DB::table('instansi')->where('kode', $request->instansi)->first();
        $user->nama_lengkap = $request->nama;
        // $user->username     = $request->username;
        $user->email        = $request->email;
        $user->jurusan      = $ins->instasi;
        $user->level        = $request->level;
        if ($user->save()) {
            $user->syncRoles([]);
            if ($request->level == 'administrator') {
                $user->assignRole('administrator');
            } else {
                $user->assignRole($request->instansi);
            }
            return redirect()->back()->with('success', 'User berhasil diperbarui.');
        } else {
            return redirect()->back()->with('failed', 'User gagal diperbarui.');
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'uid'   => 'required|string'
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) return response()->json(['status' => 'failed', 'message' => 'User id tidak diketahui.']);

        $drop = User::where('uuid', $id)->delete();
        if ($drop) {
            return response()->json(['status' => 'success', 'message' => 'User berhasil dihapus.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'User gagal dihapus.']);
        }
    }

    public function check_user(Request $request)
    {
        $request->validate([
            'pass'  => 'required|string|max:255',
            'uid'   => 'required|string',
        ]);

        $pass = base64_decode($request->pass);
        $id = Crypt::decryptString($request->uid);
        if (!$id) return response()->json(['status' => 'failed', 'message' => 'User id tidak dikenal.']);

        $user = User::find(Auth::user()->id);
        if (!$user) return response()->json(['status' => 'failed', 'message' => 'User tidak ditemukan di database.']);

        $check = Hash::check($pass, $user->password);
        if ($check) {
            return response()->json(['status' => 'success', 'message' => 'Password sesuai.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Password tidak sesuai.']);
        }
    }

    public function change_pwd(Request $request) 
    {
        $request->validate([
            'uid'       => 'required|string',
            'pass'      => 'required|string',
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) return response()->json(['status' => 'failed', 'message' => 'User id tidak dikenal.']);

        $user = User::find($id);
        if (!$user) return response()->json(['status' => 'failed', 'message' => 'User tidak ditemukan di database.']);

        $password = base64_decode($request->pass);
        $user->password = Hash::make($password);
        if ($user->save()) {
            if (Auth::user()->id == $id) {
                return response()->json(['status' => 'success', 'message' => 'Password berhasil diupdate.', 'data' => 'relog']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Password berhasil diupdate.', 'data' => 'none']);
            }
        } else {
            return response()->json(['status' => 'success', 'message' => 'Password gagal diupdate.']);
        }
    }
}

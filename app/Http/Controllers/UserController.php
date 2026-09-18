<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Instansi;
use App\Models\LevelUser;
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
        $this->middleware('permission:aplikasi', ['only' => ['index', 'store', 'update', 'destroy', 'change_pwd', 'check_user', 'toggle_status']]);
    }

    public function index()
    {
        $data = [
            'lists'     => User::with('leveluser')->get(),
            // 'instansi'  => DB::table('instansi')->get(),
            'instansi'  => Instansi::all(),
            'level'     => LevelUser::all(),
        ];
        
        return view('main.users', $data);
    }

    public function store(UserStoreRequest $request)
    {
        $level = LevelUser::findOrFail(intval($request->level));
        $roles = DB::table('roles')->where('id', $level->roles)->first();

        $uuid = Str::uuid()->toString();
        $user = new User();
        $user->uuid         = $uuid;
        $user->nama_lengkap = $request->nama;
        $user->username     = $request->username;
        $user->email        = $request->email;
        $user->level        = intval($request->level);
        $user->password     = Hash::make($request->password);
        $user->blokir       = 'N';

        if ($user->save()) {
            if ($roles) {
                $user->syncRoles([]);
                $user->assignRole($roles->name);
            }
            return redirect()->back()->with('success', 'User berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('failed', 'User gagal ditambahkan.');
        }
    }

    public function update(UserUpdateRequest $request)
    {
        $user = is_numeric($request->uid)
            ? User::find($request->uid)
            : User::where('uuid', $request->uid)->first();

        if (!$user) return redirect()->back()->with('failed', 'User tidak ditemukan.');

        $level = LevelUser::findOrFail(intval($request->level));
        $roles = DB::table('roles')->where('id', $level->roles)->first();

        $user->nama_lengkap = $request->nama;
        $user->email        = $request->email;
        $user->level        = intval($request->level);

        if ($request->filled('blokir')) {
            if ($user->id == Auth::id() && $request->blokir === 'Y') {
                return redirect()->back()->with('failed', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            }
            $user->blokir = $request->blokir;
        }

        if ($user->save()) {
            if ($roles) {
                $user->syncRoles([]);
                $user->assignRole($roles->name);
            }
            \App\Services\ActivityLogService::log(
                'update_user',
                'user',
                "Memperbarui data pengguna: {$user->nama_lengkap} ({$user->username}), status: " . ($user->blokir === 'N' ? 'Aktif' : 'Nonaktif'),
                $user
            );
            return redirect()->back()->with('success', 'User berhasil diperbarui.');
        } else {
            return redirect()->back()->with('failed', 'User gagal diperbarui.');
        }
    }

    public function toggle_status(Request $request)
    {
        $request->validate([
            'uid'   => 'required|string'
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) return response()->json(['status' => 'failed', 'message' => 'User ID tidak dikenal.']);

        $user = is_numeric($id) ? User::find($id) : User::where('uuid', $id)->first();
        if (!$user) return response()->json(['status' => 'failed', 'message' => 'User tidak ditemukan.']);

        if ($user->id == Auth::id()) {
            return response()->json(['status' => 'failed', 'message' => 'Anda tidak dapat mengubah status akun Anda sendiri.']);
        }

        $newStatus = ($user->blokir === 'Y') ? 'N' : 'Y';
        $user->blokir = $newStatus;

        if ($user->save()) {
            $actionText = ($newStatus === 'Y') ? 'Menonaktifkan / Memblokir' : 'Mengaktifkan';
            \App\Services\ActivityLogService::log(
                'toggle_status',
                'user',
                "{$actionText} pengguna: {$user->nama_lengkap} ({$user->username})",
                $user
            );

            return response()->json([
                'status'     => 'success',
                'message'    => 'Status akun berhasil diubah menjadi ' . ($newStatus === 'N' ? 'Aktif' : 'Nonaktif') . '.',
                'new_status' => $newStatus,
            ]);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Status akun gagal diperbarui.']);
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

        $pass = $request->pass;
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
            'pass'      => ['required', 'string', 'min:8'],
        ], [
            'pass.min'  => 'Password minimal harus 8 karakter.',
        ]);

        $id = Crypt::decryptString($request->uid);
        if (!$id) return response()->json(['status' => 'failed', 'message' => 'User id tidak dikenal.']);

        $user = User::find($id);
        if (!$user) return response()->json(['status' => 'failed', 'message' => 'User tidak ditemukan di database.']);

        $password = $request->pass;
        $user->password = Hash::make($password);
        if ($user->save()) {
            \App\Services\ActivityLogService::log(
                'change_password',
                'user',
                "Mengubah password untuk pengguna: {$user->nama_lengkap} ({$user->username})",
                $user
            );

            if (Auth::user()->id == $id) {
                Auth::logoutOtherDevices($password);
                return response()->json(['status' => 'success', 'message' => 'Password berhasil diupdate.', 'data' => 'relog']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Password berhasil diupdate.', 'data' => 'none']);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Password gagal diupdate.']);
        }
    }
}

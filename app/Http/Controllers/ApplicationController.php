<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ApplicationController extends Controller
{
    public function index()
    {
        $data = [
            'roles'         => Role::all(),
            'permissions'   => Permission::all(),
        ];
        return view('main.application', $data);
    }

    public function update_permission(Request $request)
    {
        $request->validate([
            'uid'           => 'required|string',
            'permission'    => 'nullable',
        ]);

        $role = Role::findById($request->uid);
        if (!$role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan!');
        }

        $decode = json_decode($request->permission ?? []);
        $permission = $decode ? array_map(function ($item) {
            return $item->name;
        }, $decode) : [];
        $role->syncPermissions($permission);
        return redirect()->back()->with('success', 'Permissions berhasil diperbarui!');
        // return response()->json($permission);
    }
}

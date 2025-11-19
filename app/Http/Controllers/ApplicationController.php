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
}

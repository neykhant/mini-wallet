<?php

namespace App\Http\Controllers\Backend;

use App\AdminUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

// use Yajra\Datatables\Datatables;
// use Datatables;


class AdminUserController extends Controller
{
    public function index()
    {
        // $users = AdminUser::all();
        return view('backend.admin_user.index');
    }

    public function ssd()
    {
        $data = AdminUser::query();

        return Datatables::of($data)->make(true);
    }

    public function create(){
        return view('backend.admin_user.create');
    }

    public function store(Request $request){
        $admin_user = new AdminUser();
        $admin_user->name = $request->name;
        $admin_user->email = $request->email;
        $admin_user->phone = $request->phone;
        $admin_user->password =  Hash::make($request->password);
        $admin_user->save();

        return redirect()->route('admin.admin-user.index')->with('create', 'Your data has been created!');
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\AdminUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;
use Jenssegers\Agent\Agent;

// use Jenssegers\Agent\Facades\Agent;


// use Yajra\Datatables\Datatables;
// use Datatables;


class UserController extends Controller
{
    public function index()
    {
        // $users = AdminUser::all();
        return view('backend.user.index');
    }

    public function ssd()
    {
        $data = User::query();

        return Datatables::of($data)
            ->editColumn('user_agent', function ($each) {
                if($each->user_agent){
                     
                    $agent = new Agent();
                    $agent->setUserAgent($each->user_agent);
                    $device = $agent->device();
                    $platform = $agent->platform();
                    $browser = $agent->browser();
    
                    return '<table class="table table-bordered">
                        <tboody>
                        <tr><td>Device</td><td>' . $device . '</td></tr>
                        <tr><td>Platform</td><td>' . $platform . '</td></tr>
                        <tr><td>Browser</td><td>' . $browser . '</td></tr>
                        </tbody>
                    </table>';
                    return $platform . $device . $browser;
                }
                return '-'; 
            })
            ->editColumn('created_at', function($each){
                return Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function($each){
                return Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('action', function ($each) {
                $edit_icon = '<a href="' . route('admin.user.edit', $each->id) . '" class="text-warning"><i class="fas fa-edit"></i></a>';
                $delete_icon = '<a href="#" class="text-danger delete" data-id="' . $each->id . '" ><i class="fas fa-trash-alt"></i></a>';

                return '<div class="action-icon" >' . $edit_icon . $delete_icon . '</div>';
            })
            ->rawColumns(['user_agent', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('backend.admin_user.create');
    }

    public function store(StoreAdminUserRequest $request)
    {
        $admin_user = new AdminUser();
        $admin_user->name = $request->name;
        $admin_user->email = $request->email;
        $admin_user->phone = $request->phone;
        $admin_user->password = Hash::make($request->password);
        $admin_user->save();

        return redirect()->route('admin.admin-user.index')->with('create', 'Your data has been created!');
    }

    public function edit($id)
    {
        $admin_user = AdminUser::findOrFail($id);
        return view('backend.admin_user.edit', compact('admin_user'));
    }

    public function update($id, UpdateAdminUserRequest $request)
    {

        $admin_user = AdminUser::findOrFail($id);
        $admin_user->name = $request->name;
        $admin_user->email = $request->email;
        $admin_user->phone = $request->phone;
        $admin_user->password = $request->password ? Hash::make($request->password) : $admin_user->password;
        $admin_user->update();

        return redirect()->route('admin.admin-user.index')->with('update', 'Your data has been updated!');
    }

    public function destroy($id)
    {
        $admin_user = AdminUser::findOrFail($id);
        $admin_user->delete();
        return 'success';
    }
}

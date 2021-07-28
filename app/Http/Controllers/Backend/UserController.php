<?php

namespace App\Http\Controllers\Backend;

use App\AdminUser;
use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\User;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;


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
                if ($each->user_agent) {

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
            ->editColumn('created_at', function ($each) {
                return Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function ($each) {
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
        return view('backend.user.create');
    }

    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = new  User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            Wallet::firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'account_number' => UUIDGenerate::accountNumber(),
                    'amount' => 0,
                ]
            );

            DB::commit();

            return redirect()->route('admin.user.index')->with('create', 'Your data has been created!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['fail' => 'Something Wrong!' . $e->getMessage()])->withInput();
        }




        // $wallet = new Wallet();
        // $wallet->user_id = $user->id;
        // $wallet->account_number = '1234123412341234';
        // $wallet->amount =0;
        // $wallet->save();


    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('backend.user.edit', compact('user'));
    }

    public function update($id, UpdateUserRequest $request)
    {

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = $request->password ? Hash::make($request->password) : $user->password;
            $user->update();

            Wallet::firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'account_number' => UUIDGenerate::accountNumber(),
                    'amount' => 0,
                ]
            );
            DB::commit();
            return redirect()->route('admin.user.index')->with('update', 'Your data has been updated!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['fail' => 'Something Wrong!' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return 'success';
    }
}

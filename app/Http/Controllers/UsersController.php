<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\MasterController;

class UsersController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }
    public function getData()
    {
        $master = new MasterController();
        $users = User::all();
        return DataTables::of($users)
        ->editColumn('role', function(User $user) use ($master) {
            if ($user->role !== null) {
                return $master->checkUserRole($user->role);
            } else {
                return 'No role assigned'; // or whatever default value you want to display
            }
        })
        ->make(true);

    }
}

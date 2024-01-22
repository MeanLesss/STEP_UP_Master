<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }
    public function getData()
    {
        $users = User::all();
        /* `return DataTables::of()->make(true);` is using the DataTables library to create a
        JSON response for the users data. */
        return DataTables::of($users)->make(true);
    }
}

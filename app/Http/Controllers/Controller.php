<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\MasterController;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    //use AuthorizesRequests, ValidatesRequests;
    public function dashboard(){
        $masterController = new MasterController();
        #user by by month
        $users = DB::table('users')
        ->select(DB::raw('count(*) as user_count,MONTH(created_at) as month'))
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month','asc')
        ->get();

        $service_order = DB::table('service_order')
        ->select(DB::raw('count(*) as order_count, MONTH(completed_at) as month'))
        ->whereYear('completed_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        $old_service_order = DB::table('service_order')
        ->select(DB::raw('count(*) as order_count, MONTH(completed_at) as month'))
        ->whereYear('completed_at', date('Y') - 1)
        ->groupBy(DB::raw('MONTH(completed_at)'))
        ->orderBy('month', 'asc')
        ->get();

        $service_status = DB::table('service')
        ->select(DB::raw('count(*) as count, status as status'))
        ->whereYear('created_at', date('Y'))
        ->groupBy('status')
        ->orderBy('status', 'asc')
        ->get();

        return view('home',['users'=>$users,'service_order'=>$service_order,'old_service_order'=>$old_service_order,'service_status'=>$service_status]);
    }
}

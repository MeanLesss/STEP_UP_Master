<?php

namespace App\Http\Controllers;

use App\Models\TopUpLog;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Validator;

class TrancsactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function topUpBalance(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'balance'=>'required | numeric'
                //card_number
                //card_name
                //card_cvv
                //card_date
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please check the range and page field!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }
            if(Auth::check()){
                if(Auth::user()->tokenCan('tranc:top-up')){
                    $result = UserDetail::where('user_id',Auth::user()->id)->first();

                    if(isset($result)){
                        $topUpLog = new TopUpLog();
                        $topUpLog->user_id = Auth::user()->id;
                        $topUpLog->balance = $request->balance;
                        $topUpLog->created_by = Auth::user()->id;
                        $topUpLog->created_at = Carbon::now();
                        $topUpLog->updated_by = Auth::user()->id;
                        $topUpLog->updated_at = Carbon::now();
                        $topUpLog->save();

                        $newBalance = $result->balance + $request->balance;
                        $result->update(['balance'=>$newBalance,'updated_at'=>Carbon::now(),'updated_by'=>Auth::user()->id]);
                        $result->save();
                    }else{
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => 'Top up fail! Seem like your account cannot be found ,please contact our support!',
                        ],401);
                    }

                    $emailController = new EmailController();
                    // Send alert email (Turn back on when linode approve)
                    $subject = 'Order Success';
                    $content = 'Dear '.Auth::user()->name.',' . "\n\n" .
                    'Your top up transaction has been successfully added to your balance.' . "\n\n" .
                    'Transaction Details:' . "\n" .
                    'Trancsaction ID: ' . $topUpLog->id . "\n" .
                    'Top Up Balance: $' . $topUpLog->balance . "\n\n" .
                    'Total Balance: $' . $result->balance . "\n\n" .
                    'Order Date: ' .  $topUpLog->created_at . "\n\n" .
                    'Thank you for choosing our services.';

                    $emailController->sendTextEmail(Auth::user()->email, $subject, $content);

                    return response()->json([
                        'verified' => true,
                        'status' =>  'success',
                        'msg' => 'Top up Successful!',
                    ],200);
                }else{
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' => 'Top up fail! Try again I the problem still occur ,please contact our support!',
                    ],401);
                }
            }
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...'),
            ],500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

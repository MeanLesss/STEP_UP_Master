<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\TopUpLog;
use App\Models\UserDetail;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\MasterController;

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

// Client Cancel before due date 95%
    public function clientCancelBeforeDueDate(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'service_id' => 'required',
                'cancel_desc' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please input all of the required fields!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }
            $user = Auth::user();

            $order = ServiceOrder::where('service_id',$request->service_id)
            ->where('id',$request->order_id)
            ->where('order_status',1)
            ->where('expected_end_date','>',Carbon::now())
            ->where('order_by',$user->id)
            ->first();
            if($order){
                $order->order_status = 4;
                $order->cancel_desc = $request->cancel_desc;
                $order->cancel_at = Carbon::now();
                $order->cancel_by = Auth::user()->id;
                $order->save();
                $service = Service::where('id',$order->service_id)->first();
                $user = User::where('id',$order->order_by)->first();
                //service order cancel status is 4
                if($user && $service){
                    //send back refund
                    UserDetail::where('user_id', $user->id)->increment('balance', $service->price * 0.50);
                    //Send Email Client Part
                    $this->sendCancellationEmailClient($user, $service, $order);

                    $freelancer = User::where('id',$order->freelancer_id)->first();
                    UserDetail::where('user_id', $freelancer->id)->increment('balance', $service->price * 0.50);
                    $this->sendCancellationEmailFreelancer($freelancer, $service, $order);

                    //Refund part
                }

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => "Cancellation success! You will get 50% refund after the cancellation !",
                ],200);
            }else{
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' => "Sorry the action can not be made!",
                ],401);

            }

        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
            ],500);
        }
    }
// Freelancer Cancel before due date 100% refund 10 score deducteds
    public function freelancerCancelBeforeDueDate(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'service_id' => 'required',
                'cancel_desc' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please input all of the required fields!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }
            $freelancer = Auth::user();

            $order = ServiceOrder::where('service_id',$request->service_id)
            ->where('id',$request->order_id)
            ->where('order_status',1)
            ->where('expected_end_date','>',Carbon::now())
            ->where('freelancer_id',$freelancer->id)
            ->first();
            if($order){
                $order->order_status = 4;
                $order->cancel_desc = $request->cancel_desc;
                $order->cancel_at = Carbon::now();
                $order->cancel_by = $freelancer->id;
                $order->save();
                $service = Service::where('id',$order->service_id)->first();
                $freelancer = User::where('id',$order->order_by)->first();
                //service order cancel status is 4
                if($freelancer && $service){
                    //deduct freelancer score
                    UserDetail::where('user_id', $freelancer->id)->decrement('credit_score', 5);
                    //Send Email Freelancer Part
                    $this->sendCancellationEmailFreelancer2($freelancer, $service, $order);

                    //Refund part
                    $user = User::where('id',$order->order_by)->first();
                    //Send refund to client 100%
                    UserDetail::where('user_id', $user->id)->increment('balance', $service->price);
                    $this->sendCancellationEmailClient2($user, $service, $order);

                }
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => "Cancellation success! You will get 5 credit score deducted!",
                ],200);
            }else{
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' => "Sorry the action can not be made!",
                ],401);

            }

        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
            ],500);
        }
    }

// After due date Action By Client
//----------------->Not delivered on time
    public function ClientCancelAfterDueDate(Request $request)//Not delivered on time and cancel by the client
    {
        try{
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'service_id' => 'required',
                'cancel_desc' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please input all of the required fields!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }
            $user = Auth::user();

            $order = ServiceOrder::where('service_id',$request->service_id)
            ->where('id',$request->order_id)
            ->whereIn('order_status',[1,2,4])
            ->where('expected_end_date','<=',Carbon::now())
            ->where('order_by',$user->id)
            ->first();
            if($order){
                $order->order_status = 4;
                $order->cancel_desc = $request->cancel_desc;
                $order->cancel_at = Carbon::now();
                $order->cancel_by = Auth::user()->id;
                $order->save();
                $service = Service::where('id',$order->service_id)->first();
                $user = User::where('id',$order->order_by)->first();
                //service order cancel status is 4
                if($user && $service){
                    //send back refund
                    UserDetail::where('user_id', $user->id)->increment('balance', $service->price * 0.95);
                    //Send Email Client Part
                    $this->sendCancellationEmailClient3($user, $service, $order);

                    $freelancer = User::where('id',$order->freelancer_id)->first();
                    //UserDetail::where('user_id', $freelancer->id)->decrement('credit_score', 10)->increment('balance', $service->price * 0.05);
                    UserDetail::where('user_id', $freelancer->id)->update([
                        'credit_score' => DB::raw('credit_score - 10'),
                        'balance' => DB::raw('balance + ' . ($service->price * 0.05))
                    ]);
                    $this->sendCancellationEmailFreelancer3($freelancer, $service, $order);

                    //Refund part
                }

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => "Cancellation success! You will get 50% refund after the cancellation !",
                ],200);
            }else{
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' => "Sorry the action can not be made!",
                ],401);

            }

        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
            ],500);
        }
    }

    public function ClientAgreeToExpandTime(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'service_id'=>'required',
                'isExpand'=>'required | boolean',
                'expand_start_date'=>'required_if:isExpand,true',
                'expand_end_date'=>'required_if:isExpand,true ',
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
                if(Auth::user()->role == 101){
                    // check isExpand
                    if($request->isExpand){
                        // check cant expand time anymore
                        $order = ServiceOrder::where('service_id',$request->service_id)
                        ->where('id',$request->order_id)
                        // ->where('expected_expand_date', null)
                        // ->where('expand_end_date', null)
                        ->first();
                        if($order){ //if the expand is null approve to add expand
                            if(!empty($order->expected_expand_date) && !empty($order->expand_end_date)){
                                $cancelReq = new Request();
                                $cancelReq->service_id = $request->service_id;
                                $cancelReq->cancel_desc = 'Expand date have exceed limit.';
                                $this->ClientCancelAfterDueDate($cancelReq);
                                return response()->json([
                                    'verified' => false,
                                    'status' =>  'error',
                                    'msg' => "You can not expand the time anymore! This order will cancel automatically. You will get 95% refund and freelancer will get 10 credit score penalty." ,
                                ],401);
                            }

                            $order->update([
                                'expected_expand_date' => $request->expand_start_date,
                                'expand_end_date'=>$request->expand_end_date,
                                'order_status' => 1,
                                'updated_at'=>Carbon::now()
                            ]);
                            $service = Service::where('id',$request->service_id)->first();
                            $this->sendExpandEmailClient($user, $service, $order);



                        }else{

                        }
                        return response()->json([
                                'verified' => true,
                                'status' =>  'success',
                                'msg' => 'Top up Successful!',
                            ],200);
                    }else{

                        $cancelReq = new Request();
                        $cancelReq->service_id = $request->service_id;
                        $cancelReq->cancel_desc = 'Client refused to expand time.';
                        $this->ClientCancelAfterDueDate($cancelReq);
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => "Thank you for your time you will get 95% refund and freelancer will get 10 credit score penalty." ,
                        ],401);
                    }
                }else{
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' => "Oop you don't have enough permission! Try contact our support for help." ,
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



    // ----------------->Expand time email
    private function sendExpandEmailClient($user, $service, $order)
    {
        $emailController = new EmailController();
        $masterController = new MasterController();

        $subject = 'Service Order Cancellation';
        $content = 'Dear '.$user->name.',' . "\n\n" .
                   'Your order has been expand the progress successfully.' . "\n\n" .
                   'Service Details:' . "\n" .
                   'Service ID: ' . $service->id . "\n" .
                   'Service Title: ' . $service->id . "\n" .
                   'Service Description: ' . $service->description . "\n" .
                   'Service Type: ' . $service->service_type . "\n\n" .
                   'Service Requirement: ' . $service->requirement . "\n" .
                   'Service Start Date: ' . $service->start_date . "\n" .
                   'Service End Date: ' . $service->end_date . "\n" .
                   'Status: ' . $masterController->checkServiceStatus($order->order_status) . "\n\n" .
                   'Discount: ' . $service->discount . "%\n\n" .
                   'Price: $' . $service->price . "\n\n" .
                   'Expand Start Date : ' . $order->expected_expand_date ."\n\n" .
                   'Expand End Date : ' . $order->expand_end_date ."\n\n" .
                   'This price amount will be refunded 95% if the freelancer still cannot delivered on time and will get penalty.' . "\n\n" .
                   'Thank you for choosing our platform.';
        $emailController->sendTextEmail($user->email, $subject, $content);
    }



    // ---------------------->Cancellation emails

    private function sendCancellationEmailClient($user, $service, $order)
    {
        $emailController = new EmailController();
        $masterController = new MasterController();

        $subject = 'Service Order Cancellation';
        $content = 'Dear '.$user->name.',' . "\n\n" .
                   'Your service order has been cancelled successfully.' . "\n\n" .
                   'Service Details:' . "\n" .
                   'Service ID: ' . $service->id . "\n" .
                   'Service Title: ' . $service->id . "\n" .
                   'Service Description: ' . $service->description . "\n" .
                   'Service Type: ' . $service->service_type . "\n\n" .
                   'Service Requirement: ' . $service->requirement . "\n" .
                   'Service Start Date: ' . $service->start_date . "\n" .
                   'Service End Date: ' . $service->end_date . "\n" .
                   'Status: ' . $masterController->checkServiceStatus($order->order_status) . "\n\n" .
                   'Discount: ' . $service->discount . "%\n\n" .
                   'Price: $' . $service->price . "\n\n" .
                   'This price amount will be refunded 50% ,due to cancellatation before the due date' . "\n\n" .
                   'Thank you for choosing our platform.';
        $emailController->sendTextEmail($user->email, $subject, $content);
    }
    private function sendCancellationEmailClient2($user, $service, $order)
    {
        $emailController = new EmailController();
        $masterController = new MasterController();

        $subject = 'Service Order Cancellation';
        $content = 'Dear '.$user->name.',' . "\n\n" .
                   'Your service order has been cancelled by the freelancer.' . "\n\n" .
                   'Service Details:' . "\n" .
                   'Service ID: ' . $service->id . "\n" .
                   'Service Title: ' . $service->id . "\n" .
                   'Service Description: ' . $service->description . "\n" .
                   'Service Type: ' . $service->service_type . "\n\n" .
                   'Service Requirement: ' . $service->requirement . "\n" .
                   'Service Start Date: ' . $service->start_date . "\n" .
                   'Service End Date: ' . $service->end_date . "\n" .
                   'Status: ' . $masterController->checkServiceStatus($order->order_status) . "\n\n" .
                   'Discount: ' . $service->discount . "%\n\n" .
                   'Price: $' . $service->price . "\n\n" .
                   'This price amount will be refunded 100% ,due to cancellatation before the due date the freelancer will get 5 credit score as penalty.' . "\n\n" .
                   'Thank you for choosing our platform.';
        $emailController->sendTextEmail($user->email, $subject, $content);
    }
    private function sendCancellationEmailClient3($user, $service, $order)
    {
        $emailController = new EmailController();
        $masterController = new MasterController();

        $subject = 'Service Order Cancellation';
        $content = 'Dear '.$user->name.',' . "\n\n" .
                   'Your have cancelled you order after the due date.' . "\n\n" .
                   'Service Details:' . "\n" .
                   'Service ID: ' . $service->id . "\n" .
                   'Service Title: ' . $service->id . "\n" .
                   'Service Description: ' . $service->description . "\n" .
                   'Service Type: ' . $service->service_type . "\n\n" .
                   'Service Requirement: ' . $service->requirement . "\n" .
                   'Service Start Date: ' . $service->start_date . "\n" .
                   'Service End Date: ' . $service->end_date . "\n" .
                   'Status: ' . $masterController->checkServiceStatus($order->order_status) . "\n\n" .
                   'Discount: ' . $service->discount . "%\n\n" .
                   'Price: $' . $service->price . "\n\n" .
                   'This price amount will be refunded 95%, due to cancellatation after the due date the freelancer will get 10 credit score as penalty.' . "\n\n" .
                   'Thank you for choosing our platform.';
        $emailController->sendTextEmail($user->email, $subject, $content);
    }
    private function sendCancellationEmailFreelancer($user, $service, $order)
    {
        $emailController = new EmailController();
        $masterController = new MasterController();

        $subject = 'Service Order Cancellation';
        $content = 'Dear '.$user->name.',' . "\n\n" .
                   'Your service order has been cancelled by the client.' . "\n\n" .
                   'Service Details:' . "\n" .
                   'Service ID: ' . $service->id . "\n" .
                   'Service Title: ' . $service->id . "\n" .
                   'Service Description: ' . $service->description . "\n" .
                   'Service Type: ' . $service->service_type . "\n\n" .
                   'Service Requirement: ' . $service->requirement . "\n" .
                   'Service Start Date: ' . $service->start_date . "\n" .
                   'Service End Date: ' . $service->end_date . "\n" .
                   'Status: ' . $masterController->checkServiceStatus($order->order_status) . "\n\n" .
                   'Discount: ' . $service->discount . "%\n\n" .
                   'Price: $' . $service->price . "\n\n" .
                   'Cancel Description : ' . "\n\n" .
                    $order->cancel_desc . "\n\n" ."\n\n" .
                   'You will get 50% from the cancellation as your service charge.' . "\n\n" .
                   'Thank you for choosing our platform.';
        $emailController->sendTextEmail($user->email, $subject, $content);
    }
    private function sendCancellationEmailFreelancer2($user, $service, $order)
    {
        $emailController = new EmailController();
        $masterController = new MasterController();

        $subject = 'Service Order Cancellation';
        $content = 'Dear '.$user->name.',' . "\n\n" .
                   'You have cancelled an order.' . "\n\n" .
                   'Service Details:' . "\n" .
                   'Service ID: ' . $service->id . "\n" .
                   'Service Title: ' . $service->id . "\n" .
                   'Service Description: ' . $service->description . "\n" .
                   'Service Type: ' . $service->service_type . "\n\n" .
                   'Service Requirement: ' . $service->requirement . "\n" .
                   'Service Start Date: ' . $service->start_date . "\n" .
                   'Service End Date: ' . $service->end_date . "\n" .
                   'Status: ' . $masterController->checkServiceStatus($order->order_status) . "\n\n" .
                   'Discount: ' . $service->discount . "%\n\n" .
                   'Price: $' . $service->price . "\n\n" .
                   'Cancel Description : ' . "\n\n" .
                    $order->cancel_desc . "\n\n" ."\n\n" .
                   'You will get 5 credit score deducted from the cancellation as penalty.' . "\n\n" .
                   'Thank you for choosing our platform.';
        $emailController->sendTextEmail($user->email, $subject, $content);
    }
    private function sendCancellationEmailFreelancer3($user, $service, $order)
    {
        $emailController = new EmailController();
        $masterController = new MasterController();

        $subject = 'Service Order Cancellation';
        $content = 'Dear '.$user->name.',' . "\n\n" .
                   'Your client have cancel the order after the due date.' . "\n\n" .
                   'Service Details:' . "\n" .
                   'Service ID: ' . $service->id . "\n" .
                   'Service Title: ' . $service->id . "\n" .
                   'Service Description: ' . $service->description . "\n" .
                   'Service Type: ' . $service->service_type . "\n\n" .
                   'Service Requirement: ' . $service->requirement . "\n" .
                   'Service Start Date: ' . $service->start_date . "\n" .
                   'Service End Date: ' . $service->end_date . "\n" .
                   'Status: ' . $masterController->checkServiceStatus($order->order_status) . "\n\n" .
                   'Discount: ' . $service->discount . "%\n\n" .
                   'Price: $' . $service->price . "\n\n" .
                   'Cancel Description : ' . "\n\n" .
                    $order->cancel_desc . "\n\n" ."\n\n" .
                   'You will get 10 credit score deducted from the cancellation as penalty.' . "\n\n" .
                   'Thank you for choosing our platform.';
        $emailController->sendTextEmail($user->email, $subject, $content);
    }
}

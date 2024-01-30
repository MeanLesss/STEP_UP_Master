<?php

namespace App\Http\Controllers;

use stdClass;
use Exception;
use App\Models\User;
use App\Models\Service;
use App\Models\UserDetail;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\MasterController;

class ServiceOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function showOrdersForWeb(Request $request){
        try{
            if(Auth::user()->tokenCan('serviceOrder:view')){
                $query = ServiceOrder::query();

                if (isset($request->service)) {
                    $query->where('service_type', $request->service);
                }
                if (isset($request->status)) {
                    $query->where('order_status', $request->status);
                }
                if (isset($request->input_email)) {
                    $query->join('users as u','u.id','=','ServiceOrder.order_by')
                    ->where('u.email', $request->input_email);
                }

                $result = $query->orderBy('created_at', 'desc')->get();
                $masterController = new MasterController();
                $result->transform(function ($item) use ($masterController) {

                    if(isset($item->attachments) ){
                        $attachments = json_decode($item->attachments, true);
                        if(is_array($attachments) && count($attachments) > 0){
                            foreach($attachments as &$attachment){
                                $attachment = asset('storage/'.$attachment);
                            }
                            $item->attachments = $attachments;
                        }else{
                            $item->attachments = new stdClass;
                        }
                    }
                    return $item;
                });

                return DataTables::of($result)
                ->make(true);
            }else{
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' => 'Please Login to view orders!',
                ],401);

            }
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...'),
            ],500);
        }
    }

    public function showOrdersForAll($isOrder = true){ //True for my order and false for my work
        try{
            $isOrder = filter_var($isOrder, FILTER_VALIDATE_BOOLEAN);
            if(Auth::user()->tokenCan( 'serviceOrder:view')){
                if(Auth::user()->role == 100){
                    if($isOrder){ //True for my order and false for my work
                        $result = ServiceOrder::where('order_by', Auth::user()->id)->orderBy('created_at', 'desc')->get();
                    }else{
                        $result = ServiceOrder::where('freelancer_id',Auth::user()->id)->orderBy('created_at', 'desc')->get();
                    }
                }else if(Auth::user()->role == 101){
                    $result = ServiceOrder::where('order_by',Auth::user()->id)->orderBy('created_at', 'desc')->get();
                }else if(Auth::user()->role == 1000){
                    $result = ServiceOrder::where('freelancer_id',Auth::user()->id)->orderBy('created_at', 'desc')->get();
                }
                if($result){
                    // $order_by = User::select('name')->where('id',$result->order_by)->first();
                    // $request->merge(['order_by_name' => Carbon::now()]);
                    // foreach( $result as $data ){
                        // }
                    $result->transform(function ($item, $key) use ($isOrder) {
                        $masterController = new MasterController();
                        $status = $masterController->checkServiceStatus($item->order_status);
                        $item->stringStatus = $status;
                        if(isset($item->order_attachments) ){
                            if(is_string($item->order_attachments)){
                                $attachments = json_decode($item->order_attachments, true);
                                if(is_array($attachments) && count($attachments) > 0){
                                    foreach($attachments as &$attachment){
                                        $attachment = asset('storage/'.$attachment);
                                    }
                                    $item->order_attachments = $attachments;
                                }else{
                                    $item->order_attachments= new stdClass;
                                }
                            }
                        }
                        if(isset($item->completed_attachments) ){
                            $attachments = json_decode($item->completed_attachments, true);
                            if(is_array($attachments) && count($attachments) > 0){
                                foreach($attachments as &$attachment){
                                    $attachment = asset('storage/'.$attachment);
                                }
                                $item->completed_attachments = $attachments;
                            }else{
                                $item->completed_attachments= new stdClass;
                            }
                        }
                        $item->contact = User::select('name','email')->where('id',$isOrder ? $item->freelancer_id : $item->order_by)->first();
                        return $item;
                    });
                    return response()->json([
                        'verified' => true,
                        'status' =>  'success',
                        'msg' => 'Success',
                        'data'=>['result'=>$result],
                    ],200);
                }else{
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' => 'Retrive failed! Nothing found!',
                        // 'data'=>['result'=>$result],
                    ],401);
                }
            }else{
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' => 'Please Login to view purchase!',
                ],401);

            }
        }catch(Exception $ex){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($ex->getMessage(), 150, '...'),
            ],500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
        if(Auth::user()->tokenCan('service:purchase')){
            return response()->json([
                'verified' => true,
                'status' =>  'success',
                'msg' => 'Proceed!',
            ],200);
        }else{
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => 'Please Login or Create a new account for purchasing!',
            ],401);

        }
    }

    /**
     * Confirm agreement before saving data
     */
    public function showAgreement(){
        return response()->json([
            'verified' => true,
            'status' => 'success',
            'msg' => '
        - Should you cancel the purchase of any service after the freelancer has confirmed the order, a 30% fee will be deducted from your refund to compensate our freelancer.

        - If you cancel before our freelancer confirms the order, you will receive a full refund with no fees deducted.

        - In the event that a freelancer fails to deliver a completed product on time, you have the option to grant them additional time or lodge a complaint with our support team for investigation. Refunds in such cases will be based on the degree of project completion.

        - If a project is not completed and the freelancer is found to have attempted to defraud or deliver a faulty product or service, you will receive a full refund and the freelancer will face penalties.

        - If there is no response from the freelancer within 7 days of purchasing the product, a full refund will be issued. Please confirm the agreement before proceed.',

        ],200);
    }

// This will be store when click create service
    public function confirmPurchase(Request $request){
        if(Auth::user()->tokenCan('service:purchase')){
            if($request->isAgreementAgreed == 1){
                 return $this->store($request);
            }else{
                return response()->json([
                    'verified' => true,
                    'status' =>  'cancel',
                    'msg' => 'The purchase is cancelled!',
                ],200);
            }
        }else{
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => 'Please Login or Create a new account!',
            ],401);

        }
    }
    public function ShowSummary(Request $request){
        $validator = Validator::make($request->all(), [
            'service_id' => 'required',
            'isAgreementAgreed' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  'Something wrong with the field!',
                // 'error_msg' => $validator->errors(),
            ],401);
        }
        if(Auth::user()->tokenCan('service:purchase')){
            if($request->isAgreementAgreed == 1){
                // return $this->store($request);
                $service = Service::where('id',$request->service_id)->first();
                $serviceOrder = new ServiceOrder($request->all());

                if($service && $serviceOrder){


                if($request->hasFile('attachment_files')) {
                    $fileNames = array();
                    // Check if 'attachment_files' is an array of files or a single file
                    $files = is_array($request->file('attachment_files')) ? $request->file('attachment_files') : [$request->file('attachment_files')];
                    foreach ($files as $file) {
                        $originalName = $file->getClientOriginalName();
                        $fileNames[] = $originalName;
                    }
                    $fileNames= json_encode($fileNames);
                    $serviceOrder->order_attachments= json_decode($fileNames, true);

                }
                $masterController = new MasterController();
                $taxRate = 0.10; // 10% tax
                $priceWithTax = $service->price * (1 + $taxRate);
                $totalPrice = $masterController->calculateTotalAmount($priceWithTax,$service->discount);
                $serviceOrder->tax = '10% Tax will be included.';
                $serviceOrder->price = '$'.$service->price;
                $serviceOrder->discount = $service->discount.'%';
                $serviceOrder->totalPrice = '$'.$totalPrice;
                $serviceOrder->taxAmount = '$'.$priceWithTax-$service->price;

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Summary',
                    'data'=> ['result'=>$serviceOrder]
                ],200);
            }else{
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Service not found!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }
            }else{
                return response()->json([
                    'verified' => true,
                    'status' =>  'cancel',
                    'msg' => 'The purchase is cancelled!',
                ],200);
            }
        }else{
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => 'Please Login or Create a new account!',
            ],401);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     */
    public function store(Request $request)
    {
        //
        try{
            $validator = Validator::make($request->all(), [
                'order_title' => 'required',
                'order_description' => 'required',
                'expected_start_date' => 'required',
                'expected_end_date' => 'required',
                // 'attachment_files.*' => 'file|max:3032'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please input all the required field!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }

            if(Auth::user()->tokenCan('service:purchase')){
                $totalPrice = 0;
                try{
                    $service = Service::where('id',$request->service_id)->first();
                    if(!isset($service)){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => 'Service not found! Invalid Service! You can contact our support if it still occur.',
                        ],401);
                    }
                    if($service->created_by == Auth::user()->id){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => 'Sorry you can not order your own service.',
                        ],401);
                    }

                    //check user balance
                    $userDetail = UserDetail::where('user_id',Auth::user()->id)->first();
                    if(isset($userDetail) && $userDetail->balance <= 0 ){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => 'Insufficiant balance for this purchase. Please refill your balance!',
                        ],401);
                    }

                    $masterController = new MasterController();
                    $taxRate = 0.10; // 10% tax
                    $priceWithTax =  $service->price * (1 + $taxRate);
                    $totalPrice = $masterController->calculateTotalAmount($priceWithTax, $service->discount);
                    if($userDetail->balance < $totalPrice){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => 'Insufficiant balance for this purchase. Please top up your balance!',
                        ],401);
                    }

                    $orderCheck = ServiceOrder::where('service_id',$request->service_id)
                    ->where('order_by',Auth::user()->id)
                    ->whereIn('order_status', [0, 1, 2])
                    ->first();
                    if(isset($orderCheck)){

                        $stringStatus = $masterController->checkServiceStatus($orderCheck->order_status);
                        $orderCheck->stringStatus = $stringStatus;
                        $orderCheck->isReadOnly  = true;
                        // Attachment
                        $attachments = json_decode($orderCheck->order_attachments, true);
                        if(is_array($attachments) && count($attachments) > 0){
                            foreach($attachments as &$attachment){
                                $attachment = asset('storage/'.$attachment);
                            }
                            $orderCheck->order_attachments = $attachments;
                        }else{
                            $orderCheck->ordeer_attachments= new stdClass;
                        }
                        // $orderCheck->order_attachments = $attachments && count($attachments) <= 0 ? new stdClass() : $attachments;

                        //completed Attachment
                        $attachments = json_decode($orderCheck->completed_attachments, true);
                        if(is_array($attachments) && count($attachments) > 0){
                            foreach($attachments as &$attachment){
                                $attachment = asset('storage/'.$attachment);
                            }
                            $orderCheck->completed_attachments = $attachments;
                        }else{
                            $orderCheck->completed_attachments= new stdClass;
                        }
                        // $orderCheck->completed_attachments = $attachments && count($attachments) <= 0 ? new stdClass() : $attachments;



                        return response()->json([
                            'verified' => false,
                            'status' =>  'warning',
                            'msg' => "You're already bought the service and still in progress !",
                            'data'=>['result'=>$orderCheck],
                        ],401);
                    }

                    // $userDetail->balance -= $priceWithTax;
                    // $userDetail->update(['balance'=>$userDetail->balance]);
                    $userDetail->decrement('balance',$totalPrice);
                    // Update accountant balance
                    UserDetail::where('user_id',2)->increment('balance',$totalPrice);

                    $serviceOrder = new ServiceOrder($request->all());

                    if($request->hasFile('attachment_files')) {
                        $filePaths = array();
                        // Check if 'attachment_files' is an array of files or a single file
                        $files = is_array($request->file('attachment_files')) ? $request->file('attachment_files') : [$request->file('attachment_files')];
                        foreach ($files as $file) {
                            $originalName = $file->getClientOriginalName();
                            $extension = $file->getClientOriginalExtension();
                            $nameWithoutExtension = str_replace("." . $extension, "", $originalName);
                            $encryptedName = base64_encode($nameWithoutExtension);
                            $encryptedNameWithExtension = $encryptedName . '.' . $extension;
                            $path = 'orderUploads/'. Auth::user()->id;
                            $file->storeAs('storage/'.$path, $encryptedNameWithExtension);
                            $filePaths[$originalName] = $path . '/' . $encryptedNameWithExtension;
                        }

                        // $array = array(
                        //     "array.jpg" => "orderUploads/16/YXJyYXk=.jpg",
                        //     "Capture.PNG" => "orderUploads/16/Q2FwdHVyZQ==.PNG"
                        // );
                        // $json = json_encode($array,JSON_UNESCAPED_SLASHES);
                        // $array = json_decode($json, true);
                        // return var_dump($array);
                        $serviceOrder->order_attachments = json_encode($filePaths,JSON_UNESCAPED_SLASHES);
                        $serviceOrder->completed_attachments = json_encode(new stdClass);
                        // $serviceOrder->order_attachments = json_encode($filePaths);
                    }else{
                        $serviceOrder->order_attachments = json_encode(new stdClass);
                        $serviceOrder->completed_attachments = json_encode(new stdClass);
                    }

                }catch(Exception $e){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' =>  Str::limit($e->getMessage(), 150, '...'),
                    ],500);
                }
                $serviceOrder->service_order = $service->service_type;
                $serviceOrder->freelancer_id = $service->created_by;
                $serviceOrder->isAgreementAgreed = 1;
                $serviceOrder->order_by = Auth::user()->id;
                $serviceOrder->created_by = Auth::user()->id;
                $serviceOrder->created_at = Carbon::now();
                $serviceOrder->updated_by = Auth::user()->id;
                $serviceOrder->updated_at = Carbon::now();
                $serviceOrder->save();
                $service->increment('service_ordered_count');

                $transaction = new Transaction();
                $transaction->client_id = $serviceOrder->order_by;
                $transaction->free_id = $serviceOrder->freelancer_id;
                $transaction->order_id = $serviceOrder->id;
                $transaction->client_status = 0;
                $transaction->freelancer_status = 0;
                $transaction->isComplain = 0;
                // $transaction->tranc_attachments = new stdClass();
                $transaction->tranc_status = 0;
                $transaction->created_by = Auth::user()->id;
                $transaction->updated_by = Auth::user()->id;
                $transaction->created_at = Carbon::now();
                $transaction->updated_at = Carbon::now();
                $transaction->save();


                $masterController = new MasterController();
                $emailController = new EmailController();
                // Send alert email to client
                $subject = 'Order Success';
                $content = 'Dear '.Auth::user()->name.',' . "\n\n" .
                'Your order has been successfully placed and is currently awaiting acceptance from the freelancer.' . "\n\n" .
                'Order Details:' . "\n" .
                'Order ID: ' . $serviceOrder->id . "\n" .
                'Service ID: ' . $service->id . "\n" .
                'Service Title: ' . $service->title . "\n" .
                'Price: $' . $service->price . "\n\n" .
                'Discount: ' . $service->discount . "%" . "\n\n" .
                'Tax: 10%'  . "\n\n" .
                'Total : $' .$totalPrice . "\n\n" .
                'This amount has been deducted from your balance. We will notify you as soon as the freelancer accepts your order.' . "\n\n" .
                'A full refund will be made within 7days if freelancer is not accept the order.' . "\n\n" .
                'Thank you for choosing our services.';

                $emailController->sendTextEmail(Auth::user()->email, $subject, $content);


                //Send email to freelancer

                $emailController = new EmailController();
                $freelancer = User::where('id',$service->created_by)->first();
                $subject2 = 'New Order';
                $content2 = 'Dear '. $freelancer->name.',' . "\n\n" .
                Auth::user()->name .' has place an order on your service.'. $service->title . "\n\n" .
                'Order Details:' . "\n" .
                'Order ID: ' . $serviceOrder->id . "\n" .
                'Service ID: ' . $service->id . "\n" .
                'Service Title: ' . $service->title . "\n" .
                'Price: $' . $service->price . "\n\n" .
                'Discount: ' . $service->discount . "%" . "\n\n" .
                'Tax: 10%'  . "\n\n" .
                'Total : $' .$totalPrice  . "\n\n" .
                'A full refund will be made within 7days if you did not accept the order.' . "\n\n" .
                'Thank you for choosing our services.';

                $emailController->sendTextEmail($freelancer->email, $subject2, $content2);

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Order successfully! 🎊 Waiting for confirmation from freelancer! After 1 week ordered without confirmation or cancel by freelancer a fully refund will be issued automatically!',
                    // 'error_msg' => '',
                ],200);
            }
            /**
             * If the user have no authorization for the action.
             */
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => "Oops! Looks like you don't have the right permissions for this. Please contact our support for more detail !",
            ],401);
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...'),
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, $isClient = false) //$id is serviceOrder id , true for client false for freelancer
    {
        $isClient = filter_var($isClient, FILTER_VALIDATE_BOOLEAN);
        //View specific ordered service
        if(Auth::user()->tokenCan('serviceOrder:view')){
            if(!$isClient){
                $orderCheck = ServiceOrder::where('id',$id)
                ->where('freelancer_id',Auth::user()->id)
                // ->whereIn('order_status', [0, 1, 2])
                ->first();
                // return var_dump([$id,Auth::user()->id,Auth::user()->name]);
            }else {
                $orderCheck = ServiceOrder::where('id',$id)
                ->where('order_by',Auth::user()->id)
                // ->whereIn('order_status', [0, 1, 2])
                ->first();
            }

            if(isset($orderCheck)){
                $masterController = new MasterController();
                $stringStatus = $masterController->checkServiceStatus($orderCheck->order_status);
                $orderCheck->stringStatus = $stringStatus;

                // $attachmentsString = json_encode($orderCheck->order_attachments);
                $attachmentsString = $orderCheck->order_attachments;
                $attachments = json_decode($attachmentsString,true);
                foreach($attachments as &$attachment){
                    // $attachment = env('APP_URL').$attachment;
                    $attachment = asset('storage/'.$attachment);
                    // Download and store the attachment
                    $url = $attachment;
                    $contents = file_get_contents($url);
                    $name = basename($url);
                    Storage::put($name, $contents);
                }
                $orderCheck->order_attachments = count($attachments) <= 0 ? new stdClass() : $attachments;

                // //order complete
                // $attachmentsString = $orderCheck->completed_attachments;
                // $attachments = json_decode($attachmentsString,true);
                // foreach($attachments as &$attachment){
                //     // $attachment = env('APP_URL').$attachment;
                //     $attachment = asset('storage/'.$attachment);
                // }
                // $orderCheck->completed_attachments = count($attachments) <= 0 ? new stdClass() : $attachments;


                // order complete
                $attachmentsString = $orderCheck->completed_attachments;
                $attachments = json_decode($attachmentsString,true);
                foreach($attachments as &$attachment){
                    // $attachment = env('APP_URL').$attachment;
                    $attachment = asset('storage/'.$attachment);

                    // Download and store the attachment
                    $url = $attachment;
                    $contents = file_get_contents($url);
                    $name = basename($url);
                    Storage::put($name, $contents);
                }
                $orderCheck->completed_attachments = count($attachments) <= 0 ? new stdClass() : $attachments;


                //Get current service detail
                $orderCheck->service = Service::select('title','description','price','requirement','discount')
                ->where('id',$orderCheck->service_id)
                ->first();

                //Get contact detail
                if(!$isClient){
                    $orderCheck->contact = User::select('name','email')
                    ->where('id',$orderCheck->order_by)
                    ->first();

                }else{
                    $orderCheck->contact = User::select('name','email')
                    ->where('id',$orderCheck->freelancer_id)
                    ->first();
                }
                // $orderCheck->isReadOnly  = true;
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => "success",
                    'data'=>['result'=>$orderCheck]
                ],200);
            }else{
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' => "Sorry nothing found!",
                ],401);
            }
        }else{
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => "Ops! Look like you don't have enough permission.",
            ],401);
        }

    }
    public function accept(Request $request,string $id)
    {
        //is accept
        $validator = Validator::make($request->all(), [
            'isAccept' => 'required',
            // 'attachment_files.*' => 'file|max:3032'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  'Invalid Request try again! If the issue still occur please contact our support.',
                // 'error_msg' => $validator->errors(),
            ],401);
        }
        if(Auth::user()->tokenCan('serviceOrder:accept')){

            $orderCheck = ServiceOrder::where('id',$id)
            ->where('freelancer_id',Auth::user()->id)
            // ->whereIn('order_status', [0, 1, 2])
            ->first();
            if(isset($orderCheck)){

                if($orderCheck->status != 0){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' => "Sorry you can not accept the order other than pending order!",
                    ],401);
                }

                $status = $request->isAccept ? 1 : -1;
                $message = $request->isAccept ? 'The order has been accepted ! You can start now.': 'The order has been cancel!';
                $orderCheck->update([
                    'order_status'=>$status,
                    'accepted_at' => Carbon::now(),
                    'start_at' => Carbon::now()
                ]);
                // $orderCheck->isReadOnly  = true;

                $emailController = new EmailController();
                // Send alert email to client
                $client = User::where('id',$orderCheck->order_by)->first();
                $emailStatus = $request->isAccept ? ' accepted ': 'cancelled' ;
                $subject = 'Order Confrimation';
                $content = 'Dear '.$client->name.',' . "\n\n" .
                'Your order has been'. $emailStatus .' by the freelancer.' . "\n\n" .
                'Order Details:' . "\n" .
                'Order ID: ' . $orderCheck->id . "\n" .
                'Service ID: ' . $orderCheck->service_id . "\n" .
                'Order Title: ' . $orderCheck->order_title . "\n" .
                'Order Description: ' . $orderCheck->order_description . "\n" .

                'Thank you for choosing our services.';

                $emailController->sendTextEmail($client->email, $subject, $content);

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => $message,
                ],200);
            }else{
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' => "Sorry nothing found!",
                ],401);
            }
        }else{
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => "Ops! Look like you don't have enough permission.",
            ],401);
        }
        //$id is service id

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

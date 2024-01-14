<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Service;
use App\Models\UserDetail;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

                $result = $query->get();
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
                            $item->attachments = [];
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

    public function showOrdersForFreelancer(){
        if(Auth::user()->tokenCan( 'serviceOrder:view')){
            $result = ServiceOrder::where('freelancer_id',Auth::user()->id)->get();
            if($result){
                // $order_by = User::select('name')->where('id',$result->order_by)->first();
                // $request->merge(['order_by_name' => Carbon::now()]);
                // foreach( $result as $data ){
                    // }
                $result->transform(function ($item, $key) {
                    $masterController = new MasterController();
                    $status = $masterController->checkServiceStatus($item->order_status);
                    $item->stringStatus = $status;
                    if(isset($item->order_attachments) ){
                        $attachments = json_decode($item->order_attachments, true);
                        if(is_array($attachments) && count($attachments) > 0){
                            foreach($attachments as &$attachment){
                                $attachment = asset('storage/'.$attachment);
                            }
                            $item->order_attachments = $attachments;
                        }else{
                            $item->order_attachments = [];
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
                            $item->completed_attachments = [];
                        }
                    }
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
        if(Auth::user()->tokenCan('service:purchase')){
            if($request->isAgreementAgreed == 1){
                // return $this->store($request);
                $service = Service::where('id',$request->service_id)->first();
                $serviceOrder = new ServiceOrder($request->all());

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

                $taxRate = 0.10; // 10% tax
                $priceWithTax = $service->price * (1 + $taxRate);
                $serviceOrder->tax = '10% Tax will be included.';
                $serviceOrder->price = '$'.$service->price;
                $serviceOrder->totalPrice = '$'.$priceWithTax;

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Summary',
                    'data'=> ['resuilt'=>$serviceOrder]
                ],200);
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
                try{
                    //check user balance
                    $userDetail = UserDetail::where('user_id',Auth::user()->id)->first();
                    if(isset($userDetail) && $userDetail->balance <= 0 ){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => 'Insufficiant balance for this purchase. Please refill your balance!',
                        ],401);
                    }

                    $service = Service::where('id',$request->service_id)->first();
                    if(!isset($service)){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => 'Service not found! Invalid Service! You can contact our support if it still occur.',
                        ],401);
                    }

                    $taxRate = 0.10; // 10% tax
                    $priceWithTax = $service->price * (1 + $taxRate);
                    if($userDetail->balance < $priceWithTax){
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
                        $masterController = new MasterController();
                        $stringStatus = $masterController->checkServiceStatus($orderCheck->order_status);
                        $orderCheck->stringStatus = $stringStatus;
                        $orderCheck->isReadOnly  = true;
                        return response()->json([
                            'verified' => false,
                            'status' =>  'warning',
                            'msg' => "You're already bought the service and still in progress !",
                            'data'=>['result'=>$orderCheck],
                        ],200);
                    }

                    $userDetail->balance -= $priceWithTax;
                    $userDetail->update(['balance'=>$userDetail->balance]);

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
                        // $serviceOrder->order_attachments = json_encode($filePaths);
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
                $emailController = new EmailController();
                // Send alert email (Turn back on when linode approve)
                $subject = 'Order Success';
                $content = 'Dear '.Auth::user()->name.',' . "\n\n" .
                'Your order has been successfully placed and is currently awaiting acceptance from the freelancer.' . "\n\n" .
                'Order Details:' . "\n" .
                'Order ID: ' . $serviceOrder->id . "\n" .
                'Service ID: ' . $service->id . "\n" .
                'Service Title: ' . $service->title . "\n" .
                'Price: $' . $service->price . "\n\n" .
                'This amount has been deducted from your balance. We will notify you as soon as the freelancer accepts your order.' . "\n\n" .
                'A full refund will be made within 7days if freelancer is not accept the order.' . "\n\n" .
                'Thank you for choosing our services.';

                $emailController->sendTextEmail(Auth::user()->email, $subject, $content);

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
    public function show(string $id) //$id is serviceOrder id
    {
        //View specific ordered service
        if(Auth::user()->tokenCan('serviceOrder:view')){
            if(Auth::user()->role == 100){

                $orderCheck = ServiceOrder::where('id',$id)
                ->where('freelancer_id',Auth::user()->id)
                // ->whereIn('order_status', [0, 1, 2])
                ->first();
            }else if(Auth::user()->role == 101){
                $orderCheck = ServiceOrder::where('id',$id)
                ->where('order_by',Auth::user()->id)
                // ->whereIn('order_status', [0, 1, 2])
                ->first();
            }else if(Auth::user()->role == 1000){
                $orderCheck = ServiceOrder::where('id',$id)
                ->where('freelancer_id',Auth::user()->id)
                // ->whereIn('order_status', [0, 1, 2])
                ->first();
            }

            if(isset($orderCheck)){
                $masterController = new MasterController();
                $stringStatus = $masterController->checkServiceStatus($orderCheck->order_status);
                $orderCheck->stringStatus = $stringStatus;

                $attachments = json_decode($orderCheck->order_attachments,true);
                foreach($attachments as &$attachment){
                    // $attachment = env('APP_URL').$attachment;
                    $attachment = asset('storage/'.$attachment);
                }
                $orderCheck->order_attachments = $attachments;
                //Get current service detail
                $orderCheck->service = Service::select('title','description','price','requirement','discount')
                ->where('id',$orderCheck->service_id)
                ->first();
                //Get client detail
                $orderCheck->client = User::select('name','email')
                ->where('id',$orderCheck->order_by)
                ->first();
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

                $status = $request->isAccept ? 1 : -1;
                $message = $request->isAccept ? 'The order has been accepted ! You can start now.': 'The order has been cancel!';
                $orderCheck->update(['order_status'=>$status]);
                // $orderCheck->isReadOnly  = true;
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

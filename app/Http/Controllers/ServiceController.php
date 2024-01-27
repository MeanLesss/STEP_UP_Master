<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Str;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\MasterController;

class ServiceController extends Controller
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

    public function getAllServicesWeb(Request $request){
        try{
            $query = Service::query();

            if (isset($request->service)) {
                $query->where('service_type', $request->service);
            }
            if (isset($request->status)) {
                $query->where('status', $request->status);
            }
            $result = $query->get();

            // return var_dump($result);
            $masterController = new MasterController();
            $result->transform(function ($item) use ($masterController) {
                // $stringStatus = $masterController->checkMyServiceStatus($item->status);
                // $item->stringStatus = $stringStatus;
                // $item->statusString = $stringStatus;
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
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...'),
            ],500);
        }
    }
    public function getAllServices(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'range' => 'required | numeric',
                'page'=>'required | numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please check the range and page field!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }

            $page = $request->get('page', 1);
            $query = Service::query();
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
            if(isset($request->service_type)){
                $query->where('service_type',$request->service_type);
            }
            if($request->has('price') && isset($request->price)){
                //$result = Service::paginate($request->range);
                $query->where('price',$request->price);
            }

            $query->where('status', 1);
            $result = $query->paginate($request->range);

            $transformedCollection = $result->getCollection()->transform(function ($item, $key) {
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


            $result->setCollection($transformedCollection);

            return response()->json([
                'verified' => true,
                'status' =>  'success',
                'msg' => 'Enjoy!',
                'data'=> ['result'=>$result],
            ],200);
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
        $masterController = new MasterController();
        try{
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'price' => 'required|numeric|min:5',
                'discount' => 'numeric|min:0|max:100',
                'service_type' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                // 'attachment_files.*' => 'file|max:3032'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please input all the required fields!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }

            if(Auth::user()->tokenCan('service:create')){
                try{
                    $service = new Service($request->all());

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
                            $path = 'uploads/'. Auth::user()->id;
                            $file->storeAs('storage/'.$path, $encryptedNameWithExtension);

                            $filePaths[$originalName] = $path . '/' . $encryptedNameWithExtension;
                        }
                        $service->attachments = json_encode($filePaths);
                    }else{
                        $service->attachments = json_encode(new stdClass);
                    }

                }catch(Exception $e){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' =>    Str::limit($e->getMessage(), 150, '...') ,
                    ],500);
                }

                $service->status = 0;
                $service->created_by = Auth::user()->id;
                $service->created_at = Carbon::now();
                $service->updated_by = Auth::user()->id;
                $service->updated_at = Carbon::now();
                $service->save();
                $emailController = new EmailController();
                // Send alert email (Turn back on when linode approve)
                $subject = 'Service Creation';
                $content = 'Dear '.Auth::user()->name.',' . "\n\n" .
                'Your service has been successfully created and will be ready for order soon.' . "\n\n" .
                'Service Details:' . "\n" .
                'Service ID: ' . $service->id . "\n" .
                'Service Title: ' . $service->id . "\n" .
                'Service Description: ' . $service->description . "\n" .
                'Service Type: ' . $service->service_type . "\n\n" .
                'Service Requirement: ' . $service->requirement . "\n" .
                'Service Start Date: ' . $service->start_date . "\n" .
                'Service End Date: ' . $service->end_date . "\n" .
                'Status: ' . $masterController->checkMyServiceStatus($service->status) . "\n\n" .
                'Discount: ' . $service->discount . "%\n\n" .
                'Price: $' . $service->price . "\n\n" .
                'This amount will be display without tax included. We will notify you as soon as the service is approved.' . "\n\n" .
                'Thank you for choosing our platform.';
                $emailController->sendTextEmail(Auth::user()->email, $subject, $content);

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Your service created successfully',
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
                'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
            ],500);
        }
    }

    public function updateService(Request $request){
        $masterController = new MasterController();
        try{
            $validator = Validator::make($request->all(), [
                'service_id' => 'required | numeric',
                'is_active' => 'required | boolean', //true active ,false inactive
                'start_date'=> 'required_if:is_active,true',
                'end_date'=> 'required_if:is_active,true'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please input all the required fields!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }

            if(Auth::user()->tokenCan('service:update')){

                $service =  Service::where('id',$request->service_id)->where('created_by',Auth::user()->id)->first();

                if($service->status == 1){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'warning',
                        'msg' => 'Your service is already active!',
                    ],401);
                }

                $service->status = $request->is_active ? 1 : 2;
                if($request->is_active){
                    $service->start_date = $request->start_date;
                    $service->end_date = $request->end_date;
                }
                $service->created_by = Auth::user()->id;
                $service->created_at = Carbon::now();
                $service->updated_by = Auth::user()->id;
                $service->updated_at = Carbon::now();
                $service->save();
                $emailController = new EmailController();
                // Send alert email (Turn back on when linode approve)

                $status = $request->is_active ? 'ACTIVATED' : 'DEACTIVATED';
                $subject = 'Service Activation';
                $content = 'Dear '.Auth::user()->name.',' . "\n\n" .
                'Your service has been successfully '.$status.'.' . "\n\n" .
                'Service Details:' . "\n" .
                'Service ID: ' . $service->id . "\n" .
                'Service Title: ' . $service->id . "\n" .
                'Service Description: ' . $service->description . "\n" .
                'Service Type: ' . $service->service_type . "\n\n" .
                'Service Requirement: ' . $service->requirement . "\n" .
                'Service Start Date: ' . $service->start_date . "\n" .
                'Service End Date: ' . $service->end_date . "\n" .
                'Status: ' . $masterController->checkMyServiceStatus($service->status) . "\n\n" .
                'Discount: ' . $service->discount . "%\n\n" .
                'Price: $' . $service->price . "\n\n" .
                'This amount will be display without tax included.' . "\n\n" .
                'Thank you for choosing our platform.';
                $emailController->sendTextEmail(Auth::user()->email, $subject, $content);

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Your service ' . $status .' successfully',
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
                'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         //check if the service is already purchase  and in progress
        //  $orderCheck = ServiceOrder::where('service_id',$request->service_id)
        $orderCheck = ServiceOrder::where('service_id',$id)
        ->where('order_by',Auth::user()->id)
        ->whereIn('order_status', [0, 1, 2])
        ->first();
        // if(isset($orderCheck)){

        //     $masterController = new MasterController();
        //     $stringStatus = $masterController->checkServiceStatus($orderCheck->order_status);
        //     $orderCheck->stringStatus = $stringStatus;
        //     $attachments = json_decode($orderCheck->order_attachments);
        //     //Convert Order_attachment
        //     if(isset($orderCheck->order_attachments) ){
        //         $attachments = json_decode($orderCheck->order_attachments, true);
        //         if(is_array($attachments) && count($attachments) > 0){
        //             foreach($attachments as &$attachment){
        //                 $attachment = asset('storage/'.$attachment);
        //             }
        //             $orderCheck->order_attachments = $attachments;
        //         }else{
        //             $orderCheck->order_attachments = new stdClass;
        //         }
        //     }else{
        //         $orderCheck->order_attachments = new stdClass;
        //     }

        //     //Convert Complete_Attachment
        //     if(isset($orderCheck->completed_attachments) ){
        //         $attachments2 = json_decode($orderCheck->completed_attachments, true);
        //         if(is_array($attachments2) && count($attachments2) > 0){
        //             foreach($attachments2 as &$attachment){
        //                 $attachment = asset('storage/'.$attachment);
        //             }
        //             $orderCheck->completed_attachments = $attachments2;
        //         }else{
        //             $orderCheck->completed_attachments = new stdClass;
        //         }
        //     }else{

        //         $orderCheck->completed_attachments = new stdClass;
        //     }
        //     $orderCheck->completed_attachments = $attachments2;

        // }
        $result = Service::where('id',$id)->increment('view');
        $result = Service::where('id',$id)->first();

        $attachments = json_decode($result->attachments);
        foreach($attachments as &$attachment){
            // $attachment = env('APP_URL').$attachment;
            $attachment = asset('storage/'.$attachment);
        }
        $result->attachments = $attachments;
        $result->isReadOnly  = false;
       /* The above code is incomplete and does not have any logic or condition specified within the if
       statement. Therefore, it is not possible to determine what the code is intended to do without
       further information. */
        if(isset($orderCheck)){
            $result->isReadOnly  = true;
            return response()->json([
                'verified' => false,
                'status' =>  'warning',
                'msg' => "You're already bought the service and still in progress !",
                'data'=>['result'=>$result],
            ],200);
        }
        return response()->json([
            'verified' => true,
            'status' =>  'success',
            'msg' => '',
            'data'=>['result'=>$result],
        ],200);
    }
    public function showAllMyService()
    {
         //check if the service is already purchase  and in progress
        //  $orderCheck = ServiceOrder::where('service_id',$request->service_id)
        try{
            $result = Service::where('created_by',Auth::user()->id)->get();

            $masterController = new MasterController();
            $result->transform(function ($item) use ($masterController) {
                $stringStatus = $masterController->checkMyServiceStatus($item->status);
                $item->stringStatus = $stringStatus;
                // $item->statusString = $stringStatus;
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

            if(isset($result) && count($result)>0){
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => '',
                    'data' =>  ['result'=>$result],
                ],200);
            }
            return response()->json([
                'verified' => true,
                'status' =>  'success',
                'msg' => 'No service created yet, Try create some.😊',
                'data'=> ['result'=>$result],
            ],401);
        }catch(Exception $ex){
            return response()->json([
                'verified' => true,
                'status' =>  'success',
                'msg' => $ex->getMessage(),
            ],500);
        }
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
        try{
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'price' => 'required|numeric|min:5',
                'service_type' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                // 'attachment_files.*' => 'file|max:3032'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Please input all of the required fields!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }

            if(Auth::user()->tokenCan('service:update')){
                $service = Service::find($id);
                if($service){
                    try{
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
                                $path = 'uploads/'. Auth::user()->id;
                                $file->storeAs('storage/'.$path, $encryptedNameWithExtension);

                                $filePaths[$originalName] = $path . '/' . $encryptedNameWithExtension;
                            }
                            $request->merge(['attachments' => json_encode($filePaths)]);
                        }
                    }catch(Exception $e){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
                        ],500);
                    }
                    $request->merge(['updated_at' => Carbon::now(),'updated_by'=>Auth::user()->id]);
                    $service->update($request->all());

                    return response()->json([
                        'verified' => true,
                        'status' =>  'success',
                        'msg' => 'Your service updated successfully',
                    ],200);
                }else{
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' => 'Service not found!',
                    ],401);
                }
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
                'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
            ],500);
        }
    }
    public function serviceApproval(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'service_id' => 'required',
                'isApprove' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Oops somthing not right!',
                    // 'error_msg' => $validator->errors(),
                ],401);
            }

            // if(Auth::user()->tokenCan('service:approval')){
            if(Auth::user()->role == 1000){
                try{
                    $service = Service::find($request->service_id);
                    $service->update(['updated_at' => Carbon::now(),'updated_by'=>Auth::user()->id,'status'=>$request->isApprove ? 1 : -1]);
                    $masterController = new MasterController();

                    $creator = User::where('id',$service->created_by)->first();
                    if($creator){
                        $emailController = new EmailController();
                        $subject = 'Service Approval';
                        if($request->isApprove){
                            $content = 'Dear '.$creator->name.',' . "\n\n" .
                            'Your service has been approved will be ready for order.' . "\n\n" .
                            'Service Details:' . "\n" .
                            'Service ID: ' . $service->id . "\n" .
                            'Service Title: ' . $service->title . "\n" .
                            'Service Description: ' . $service->description . "\n" .
                            'Service Type: ' . $service->service_type . "\n\n" .
                            'Service Requirement: ' . $service->requirement . "\n" .
                            'Service Start Date: ' . $service->start_date . "\n" .
                            'Service End Date: ' . $service->end_date . "\n" .
                            'Status: ' . $masterController->checkMyServiceStatus($service->status) . "\n\n" .
                            'Discount: ' . $service->discount . "%\n\n" .
                            'Price: $' . $service->price . "\n\n" .
                            'Note : This amount will be display without tax included.' . "\n\n" .
                            'Thank you for choosing our platform.';
                        }else{
                            $content = 'Dear '.$creator->name.',' . "\n\n" .
                            'Unfortunately, your service submission did not meet our established criteria or conditions. We appreciate your understanding and encourage you to review our guidelines for future submissions.' . "\n\n" .
                            'Service Details:' . "\n" .
                            'Service ID: ' . $service->id . "\n" .
                            'Service Title: ' . $service->title . "\n" .
                            'Service Description: ' . $service->description . "\n" .
                            'Service Type: ' . $service->service_type . "\n\n" .
                            'Service Requirement: ' . $service->requirement . "\n" .
                            'Service Start Date: ' . $service->start_date . "\n" .
                            'Service End Date: ' . $service->end_date . "\n" .
                            'Status: ' . $masterController->checkMyServiceStatus($service->status) . "\n\n" .
                            'Discount: ' . $service->discount . "%\n\n" .
                            'Price: $' . $service->price . "\n\n" .
                            'Note : This amount will be display without tax included.' . "\n\n" .
                            'Thank you for choosing our platform.';
                        }
                        $emailController->sendTextEmail($creator->email, $subject, $content);
                    }

                    return response()->json([
                        'verified' => true,
                        'status' =>  'success',
                        'msg' => 'The service updated successfully',
                    ]);
                }catch(Exception $e){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
                    ],500);
                }
            }else{
                /**
                 * If the user have no authorization for the action.
                 */
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' => "Oops! Looks like you don't have the right permissions for this. Please contact our support for more detail !",
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
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

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
                    'msg' =>  '',
                    'error_msg' => $validator->errors(),
                ], 400);
            }

            $page = $request->get('page', 1);
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
            if(isset($request->service_type)){
                $result = Service::where('service_type',$request->service_type)->paginate($request->range);
            }else{
                //$result = Service::paginate($request->range);
                $result = Service::paginate($request->range);
            }

            $transformedCollection = $result->getCollection()->transform(function ($item, $key) {
                $attachments = json_decode($item->attachments, true);
                if(isset($attachments)){
                    foreach($attachments as &$attachment){
                        // $attachment = env('APP_URL').$attachment;
                        $attachment = asset('storage/'.$attachment);
                    }
                }
                $item->attachments = $attachments;
                return $item;
            });


            $result->setCollection($transformedCollection);

            return response()->json([
                'verified' => true,
                'status' =>  'success',
                'msg' => 'Enjoy!',
                'error_msg' => "",
                'data'=> $result
            ]);
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  '',
                'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
                    'msg' =>  '',
                    'error_msg' => $validator->errors(),
                ], 400);
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
                    }

                }catch(Exception $e){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' =>  '',
                        'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
                    ]);
                }

                $service->created_by = Auth::user()->id;
                $service->created_at = Carbon::now();
                $service->updated_by = Auth::user()->id;
                $service->updated_at = Carbon::now();
                $service->save();

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Your service created successfully',
                    'error_msg' => '',
                ]);
            }
            /**
             * If the user have no authorization for the action.
             */
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => '',
                'error_msg' => "Oops! Looks like you don't have the right permissions for this. Please contact our support for more detail !",
            ]);
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  '',
                'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
            ]);
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

        if(isset($orderCheck)){
            $masterController = new MasterController();
            $stringStatus = $masterController->checkServiceStatus($orderCheck->order_status);
            $orderCheck->status = $stringStatus;
            return response()->json([
                'verified' => false,
                'status' =>  'warning',
                'msg' => "You're already bought the service and still in progress !",
                'error_msg' => '',
                'data'=>$orderCheck,
                'isReadOnly'=> true
            ]);
        }
        $result = Service::where('id',$id)->increment('view');
        $result = Service::where('id',$id)->first();

        $attachments = json_decode($result->attachments);
        foreach($attachments as &$attachment){
            // $attachment = env('APP_URL').$attachment;
            $attachment = asset('storage/'.$attachment);
        }
        $result->attachments = $attachments;
        return response()->json([
            'verified' => true,
            'status' =>  'success',
            'msg' => '',
            'error_msg' => "",
            'data'=>$result,
            'isReadOnly'=> false
        ]);
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
                    'msg' =>  '',
                    'error_msg' => $validator->errors(),
                ], 400);
            }

            if(Auth::user()->tokenCan('service:update')){
                try{
                    $service = Service::find($id);

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
                    $request->merge(['updated_at' => Carbon::now(),'updated_by'=>Auth::user()->id]);
                    $service->update($request->all());
                }catch(Exception $e){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' =>  '',
                        'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
                    ]);
                }

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Your service updated successfully',
                    'error_msg' => '',
                ]);
            }
            /**
             * If the user have no authorization for the action.
             */
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => '',
                'error_msg' => "Oops! Looks like you don't have the right permissions for this. Please contact our support for more detail !",
            ]);
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  '',
                'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
            ]);
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

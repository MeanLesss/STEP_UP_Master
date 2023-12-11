<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
                            $file->storeAs('public/'.$path, $encryptedNameWithExtension);

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

<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\UserDetail;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceOrderController extends Controller
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
    public function create(Request $request)
    {
        //
        if(Auth::user()->tokenCan('service:purchase')){
            return response()->json([
                'verified' => true,
                'status' =>  'success',
                'msg' => 'Proceed!',
                'error_msg' => "",
            ]);
        }else{
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => '',
                'error_msg' => "Please Login or Create a new account for purchasing!",
            ]);

        }
    }

    /**
     * Confirm agreement before saving data
     */
    public function showAgreement(){
        return response()->json([
            'verified' => true,
            'status' => 'success',
            'msg' => 'Terms and Agreement:

        - Should you cancel the purchase of any service after the freelancer has confirmed the order, a 30% fee will be deducted from your refund to compensate our freelancer.

        - If you cancel before our freelancer confirms the order, you will receive a full refund with no fees deducted.

        - In the event that a freelancer fails to deliver a completed product on time, you have the option to grant them additional time or lodge a complaint with our support team for investigation. Refunds in such cases will be based on the degree of project completion.

        - If a project is not completed and the freelancer is found to have attempted to defraud or deliver a faulty product or service, you will receive a full refund and the freelancer will face penalties.

        - If there is no response from the freelancer within 7 days of purchasing the product, a full refund will be issued. Please confirm the agreement before proceed.',
            'error_msg' => ''
        ]);
    }

    public function confirmAgreement(Request $request){
        if(Auth::user()->tokenCan('service:purchase')){
            if($request->isAgreementAgreed == 1){
                return $this->store($request);
            }else{
                return response()->json([
                    'verified' => true,
                    'status' =>  'cancel',
                    'msg' => '',
                    'error_msg' => "The purchase is cancelled!",
                ]);
            }
        }else{
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => '',
                'error_msg' => "Please Login or Create a new account!",
            ]);

        }
    }

    /**
     * Store a newly created resource in storage.
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
                    'msg' =>  '',
                    'error_msg' => $validator->errors(),
                ], 400);
            }

            if(Auth::user()->tokenCan('service:purchase')){
                try{
                    $userDetail = UserDetail::where('user_id',Auth::user()->id)->first();
                    if(isset($userDetail) && $userDetail->balance <= 0 ){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => '',
                            'error_msg' => 'Insufficiant balance for this purchase. Please refill your balance!',
                        ]);
                    }

                    $service = Service::where('id',$request->service_id)->first();
                    if(!isset($service)){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => '',
                            'error_msg' => 'Service not found! Invalid Service! You can contact our support if it still occur.',
                        ]);
                    }

                    $taxRate = 0.10; // 10% tax
                    $priceWithTax = $service->price * (1 + $taxRate);
                    if($userDetail->balance < $priceWithTax){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' => '',
                            'error_msg' => 'Insufficiant balance for this purchase. Please top up your balance!',
                        ]);
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
                            $path = 'order_uploads/'. Auth::user()->id;
                            $file->storeAs('storage/'.$path, $encryptedNameWithExtension);

                            $filePaths[$originalName] = $path . '/' . $encryptedNameWithExtension;
                        }
                        $serviceOrder->order_attachments = json_encode($filePaths);
                    }

                }catch(Exception $e){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' =>  '',
                        'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
                    ]);
                }
                $serviceOrder->isAgreementAgreed = 1;
                $serviceOrder->order_by = Auth::user()->id;
                $serviceOrder->created_by = Auth::user()->id;
                $serviceOrder->created_at = Carbon::now();
                $serviceOrder->updated_by = Auth::user()->id;
                $serviceOrder->updated_at = Carbon::now();
                $serviceOrder->save();
                $service->increment('service_ordered_count');

                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Order successfully! 🎊 Waiting for confirmation from freelancer! After 1 week ordered without confirmation or cancel by freelancer a fully refund will be issued automatically!',
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

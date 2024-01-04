<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LoginController extends Controller
{
    //use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('login');
    }
    public function web_login(Request $request)
    {
        //
        $response = $this->login($request)->original;
        if($response['verified']){
            session(['user_token' => $response['data']['user_token']]);
            return response()->json($response);
            // return view('layouts.page_template.auth', ['activePage' => 'home','namePage'=>'home']);
            // return view('home', ['activePage' => 'home','namePage'=>'home']);
        }else{
            // return var_dump($response);
            return response()->json($response);
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            try {
                session()->forget('user_token');
                Auth::user()->tokens()->delete();
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' =>  'User logged out',
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  $e->getMessage(),
                ]);
            }
        } else {
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  'No user is authenticated',
            ]);
        }
    }

    public function login(Request $request)
    // public function login($email,$password)
    {
        try{
            $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

            if ($validator->fails()) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'Invalid Credential',
                    // 'error_msg' => $validator->errors(),
                ]);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {

                $check = User::where('email', $request->email)->first();
                if($check){
                    if($check->login_attempt <= 5){
                        $check->update(['login_attempt' => $check->login_attempt + 1]);
                    }else{
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' =>  'Too many attempts, please reset your password!',
                        ]);
                    }
                }
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  'The provided credentials are incorrect.',
                ]);
            }

            Auth::user()->update(['login_attempt' => 0]);
            // return var_dump([2,2,3,3]);
            if(Auth::user()->role == 1000){
                $user_token = Auth::user()->createToken('token',[
                    'service:create',
                    'service:update',
                    'service:delete',
                    'service:cancel',
                    'service:read',
                    'service:view',
                    'service:ban',
                    'serviceOrder:view',
                    'user:status',
                    'service:purchase',
                    'self:update'])->plainTextToken;
            }
            if(Auth::user()->role == 100){
                $user_token = Auth::user()->createToken('token',[
                    'service:create',
                    'service:update',
                    'service:delete',
                    'service:cancel',
                    'service:read',
                    'service:view',
                    'serviceOrder:view',
                    'service:purchase',
                    'free:update'])->plainTextToken;
            }
            if(Auth::user()->role == 101){
                $user_token = Auth::user()->createToken('token',[
                    'service:read',
                    'service:view',
                    'service:cancel',
                    'service:purchase',
                    'serviceOrder:view',
                    'client:update'])->plainTextToken;
            }
            if(Auth::user()->role == 10){
                $user_token = Auth::user()->createToken('token',[
                    'service:read',
                    'service:view',
                    'guest:update' ])->plainTextToken;
            }

            return response()->json([
                'verified' => true,
                'status' =>  'success',
                'msg' => 'Login Successfully',
                'data' =>['user_token' => $user_token],
            ]);
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...') ,
            ]);
        }
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
        $validator = Validator::make($request->all(), [
            'guest' => 'required|boolean',
            'freelancer' => 'required|boolean',
            'name' => 'required_if:guest,false',
            'email' => 'required_if:guest,false|email',
            'password' => 'required_if:guest,false',
            'confirm_password' => 'required_if:guest,false',
            'phone_number' => 'required_if:guest,false',
            'id_number' => 'required_if:guest,false',
            'job_type' => 'required_if:guest,false'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  'Please input all the required fields!',
                // 'error_msg' => $validator->errors(),
            ]);
        }

        try{
            // $userExists = User::where('name', $request->name)
            //     ->orWhere('email', $request->email)
            //     ->exists();
            $user = new User();
            $userDetail = new UserDetail();
            if($request->guest)
            {
                $user->name = $request->name != '' ? $request->name.'_'.Str::random(10) : 'Guest_'.Str::random(10);
                $user->email = $user->name.'@guest.com';
                $user->isGuest = true;
                $user->role = 10;
                $user->password = '';
                $user->created_at = Carbon::now();
                $user->updated_at = Carbon::now();
                $user->save();
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Sign up as guest Successfully',
                    'data' =>[ 'user_token' => $user->createToken('token')->plainTextToken,],
                ]);
            }else{
                $userExists = User::where('email', $request->email)->first();
                if($userExists){
                    if($request->freelancer){
                        if($userExists->role != 100){
                            $userExists->update(['role'=>100]);
                            return response()->json([
                                'verified' => true,
                                'status' =>  'success',
                                'msg' => 'Successfully become a freelaner, let begin the journey!',
                                // 'data' =>['user_token' => $user->createToken('token')->plainTextToken, ],
                            ]);
                        }else{
                            return response()->json([
                                'verified' => false,
                                'status' =>  'error',
                                'msg' => '',
                                'error_msg' => 'You are already a freelancer!',
                                // 'data' =>['user_token' => $user->createToken('token')->plainTextToken, ],
                            ]);
                        }
                    }else{
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' =>  'Sorry try other credential!',
                        ], 200);
                    }
                }
                if($request->password == $request->confirm_password){

                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->isGuest = false;
                    $user->role = $request->freelancer ? 100 : 101;
                    $user->password = Hash::make($request->password);
                    $user->created_at = Carbon::now();
                    $user->updated_at = Carbon::now();
                    $user->save();
                    $userDetail = new UserDetail();
                    $userDetail->user_id = $user->id;
                    $userDetail->phone = $request->phone_number;
                    $userDetail->id_card_no = $request->id_number;
                    $userDetail->job_type = $request->job_type;
                    $userDetail->created_by = $user->id;
                    $userDetail->updated_by = $user->id;
                    $userDetail->created_at = Carbon::now();
                    $userDetail->updated_at = Carbon::now();
                    $userDetail->save();
                    return response()->json([
                        'verified' => true,
                        'status' =>  'success',
                        'msg' => 'Sign up Successfully',
                        'data' =>['user_token' => $user->createToken('token')->plainTextToken, ],
                    ]);
                }else{
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' => 'The password does not match!',
                    ]);
                }
            }
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...'),
            ]);
        }
    }
    public function userUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  'Name or Email Cannot be empty!',
                // 'error_msg' => $validator->errors(),
            ]);
        }

        try{
            if (Auth::check() ) {
                $user = Auth::user();
                if($user->email != $request->email){
                    $userExists = User::where('email', $request->email)->exists();
                    if($userExists){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' =>  'Sorry please try other credential!',
                        ]);
                    }
                }
                if($user->tokenCan('guest:update')){
                    if($request->password == $request->confirm_password){
                        $user->update([
                            'name' => $request->name,
                            'email' => $request->email,
                            'isGuest' => false,
                            'password' => Hash::make($request->password),
                        ]);
                    }
                }else{
                    if($request->password != '' || $request->confirm_password!= ''){
                        if($request->password == $request->confirm_password){
                            $user->update([
                                'name' => $request->name,
                                'email' => $request->email,
                                'password' => Hash::make($request->password),
                            ]);
                        }
                    }else{
                        if($request->password == $request->confirm_password){
                            $user->update([
                                'name' => $request->name,
                                'email' => $request->email,
                            ]);
                        }
                    }
                }

                $userDetail = UserDetail::where('user_id',$user->id)->first();
                if(!isset($userDetail)){
                    $userDetail = new UserDetail();
                }
                $userDetail->user_id = $user->id;
                $userDetail->phone = $request->phone_number;
                $userDetail->id_card_no = $request->id_number;
                $userDetail->job_type = $request->job_type;
                $userDetail->created_by = $user->id;
                $userDetail->updated_by = $user->id;
                $userDetail->created_at = Carbon::now();
                $userDetail->updated_at = Carbon::now();
                $userDetail->save();
                // If the user is authenticated, return the user data
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Update Successfully!',
                    // 'error_msg' => '',
                ]);
            } else {
                // If the user is not authenticated, return a custom message
                return response()->json(['error' => 'Authenticated failed! Please try again!']);
            }
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => Str::limit($e->getMessage(), 150, '...'),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
        // Check if the user is authenticated
        try{

            if (Auth::check()) {
                // If the user is authenticated, return the user data
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'success',
                    'data' =>[ 'user_info' => $request->user(),'user_detail'=> UserDetail::where('user_id',$request->user()->id)->first() ],
                ]);
                //return response()->json(Auth::user(), 200);
            } else {
                // If the user is not authenticated, return a custom message
                return response()->json(['error' => 'Invalid or expired token']);
            }
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  Str::limit($e->getMessage(), 150, '...'),
            ]);
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

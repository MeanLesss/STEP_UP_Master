<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
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
    }

    public function logout()
    {
        if (Auth::check()) {
            try {
                Auth::user()->tokens()->delete();
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' =>  'User logged out',
                    'error_msg' => '',
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  '',
                    'error_msg' => $e->getMessage(),
                ]);
            }
        } else {
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  'No user is authenticated',
                'error_msg' => '',
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
                        'msg' =>  '',
                        'error_msg' => $validator->errors(),
                    ], 400);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' =>  '',
                        'error_msg' => 'The provided credentials are incorrect.',
                    ]);
                }
                // return var_dump([2,2,3,3]);
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Login Successfully',
                    'error_msg' => '',
                    'data' =>['user_token' => Auth::user()->createToken('token')->plainTextToken,],

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
            'name' => 'required_if:guest,false',
            'email' => 'required_if:guest,false|email',
            'password' => 'required_if:guest,false',
            'confirm_password' => 'required_if:guest,false'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' =>  '',
                'error_msg' => $validator->errors(),
            ], 400);
        }

        try{
            // $userExists = User::where('name', $request->name)
            //     ->orWhere('email', $request->email)
            //     ->exists();
            $user = new User();
            if($request->guest)
            {
                $user->name = $request->name != '' ? $request->name.'_'.Str::random(10) : 'Guest_'.Str::random(10);
                $user->email = $user->name.'@guest.com';
                $user->isGuest = true;
                $user->password = '';
                $user->created_at = Carbon::now();
                $user->updated_at = Carbon::now();
                $user->save();
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Sign up as guest Successfully',
                    'error_msg' => '',
                    'data' =>[ 'user_token' => $user->createToken('token')->plainTextToken,],


                ]);
            }else{
                $userExists = User::where('email', $request->email)->exists();

                if($userExists){
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' =>  '',
                        'error_msg' => 'Sorry try other credential!',
                    ], 200);
                }
                if($request->password == $request->confirm_password){

                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->isGuest = false;
                    $user->password = Hash::make($request->password);
                    $user->created_at = Carbon::now();
                    $user->updated_at = Carbon::now();
                    $user->save();
                    return response()->json([
                        'verified' => true,
                        'status' =>  'success',
                        'msg' => 'Sign up Successfully',
                        'error_msg' => '',
                        'data' =>['user_token' => $user->createToken('token')->plainTextToken, ],


                    ]);
                }else{
                    return response()->json([
                        'verified' => false,
                        'status' =>  'error',
                        'msg' => 'Sign up failed!',
                        'error_msg' => 'The password does not match!' ,
                    ]);
                }
            }
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => 'Sign up failed!',
                'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
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
                'msg' =>  '',
                'error_msg' => $validator->errors(),
            ], 400);
        }

        try{
            if (Auth::check()) {
                $user = Auth::user();
                if($user->email != $request->email){
                    $userExists = User::where('email', $request->email)->exists();
                    if($userExists){
                        return response()->json([
                            'verified' => false,
                            'status' =>  'error',
                            'msg' =>  '',
                            'error_msg' => 'Sorry try other credential!',
                        ], 200);
                    }
                }
                if($user->isGuest){
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
                // If the user is authenticated, return the user data
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Update Successfully!',
                    'error_msg' => '',
                ]);
            } else {
                // If the user is not authenticated, return a custom message
                return response()->json(['error' => 'Authenticated failed! Please try again!']);
            }

        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => 'Sign up failed!',
                'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
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
                    'error_msg' => '',
                    'data' =>[ 'user_info' => $request->user() ],


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
                'msg' => '',
                'error_msg' => Str::limit($e->getMessage(), 150, '...'),
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

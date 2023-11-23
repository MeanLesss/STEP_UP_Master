<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'verified' => true,
            'status' =>  'success',
            'msg' =>  'User logged out',
            'error_msg' => '',
        ]);
    }
    public function login(Request $request)
    {
        try{

            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'verified' => false,
                    'status' =>  'error',
                    'msg' =>  '',
                    'error_msg' => 'The provided credentials are incorrect.',
                ]);
            }
            return response()->json([
                'verified' => true,
                'status' =>  'success',
                'msg' => 'Login Successfully',
                'error_msg' => '',
                'user_token' => Auth::user()->createToken('token')->plainTextToken,
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
        //
       /* The code ` ->validate([...])` is used to validate the data received in the ``
       object. It ensures that the data meets certain validation rules before proceeding with the
       rest of the code. */
        $request->validate([
            'guest' => 'required|boolean',
            'name' => 'required_if:guest,false',
            'email' => 'required_if:guest,false|email',
            'password' => 'required_if:guest,false',
        ]);
        try{
            $user = new User();
            if($request->guest)
            {
                $user->name = 'Guest_'.Str::random(10);
                $user->email = $user->name.'@guest.com';
                $user->password = '';
                $user->created_at = Carbon::now();
                $user->updated_at = Carbon::now();
                $user->save();
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Sign up as guest Successfully',
                    'error_msg' => '',
                    'user_token' => $user->createToken('token')->plainTextToken,
                ]);
            }else{
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->created_at = Carbon::now();
                $user->updated_at = Carbon::now();
                $user->save();
                return response()->json([
                    'verified' => true,
                    'status' =>  'success',
                    'msg' => 'Sign up Successfully',
                    'error_msg' => '',
                    'user_token' => $user->createToken('token')->plainTextToken,
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'verified' => false,
                'status' =>  'error',
                'msg' => 'Sign up failed!',
                'error_msg' => Str::limit($e->getMessage(), 150, '...') ,
            ]);
        }
        // $random = Str::random(10);
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
                    'user_info' => $request->user() ,
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

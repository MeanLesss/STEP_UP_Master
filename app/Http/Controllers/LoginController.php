<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            //return var_dump(['The provided credentials are incorrect.']);
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
        $request->validate([
            'guest' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = new User();
        if($request->guest)
        {
            $user->name = 'Guest_'.Str::random(10);
            $user->email = $user->name.'@guest.com';
            $user->password = '';
        }else{
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
        }
        // $random = Str::random(10);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
        return $request->user();
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

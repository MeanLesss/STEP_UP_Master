<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Access\AuthorizationException;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }
    public function sendTextEmail(string $emailSendTo, string $subject, string $content)
    {
        $content = nl2br(e($content));
        $data = ['content' => $content];

        Mail::send('email.NotifyTemplate', $data, function ($message) use ($emailSendTo, $subject) {
            $message->to($emailSendTo)
                    ->subject($subject);
        });
    }
    public function verifyEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Email already verified!', 422);
        }

        $request->user()->sendEmailVerificationNotification();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'verification-link-sent']);
        }

        return back()->with('status', 'verification-link-sent');
    }
    protected function verified(Request $request)
    {
        if ($request->wantsJson()) {
            return view('email.VerifyEmail',[
                'icon'=>'fa-check-circle',
                'title'=>'Success',
                'msg'=>"Email successfully verified! Enjoy (●'◡'●)"
            ],200);
        } else {
            // Return a different view or a redirect here
            return view('email.VerifyEmail',[
                'icon'=>'fa-check-circle',
                'title'=>'Success',
                'msg'=>"Email successfully verified! Enjoy (●'◡'●)"
            ]);
        }
    }
    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (! $user || ! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'verified'=>false,
                'status'=>'error',
                'msg'=>'Email Verify Failed! Try resend confirm email again!'
            ], 401);
        }

        if ($user->hasVerifiedEmail()) {
            //       return response()->json([
            //     'verified'=>false,
            //     'status'=>'error',
            //     'msg'=>'Email Already Verified! You can relax and enjoy༼ つ ◕_◕ ༽つ!'
            // ], 422);
            return view('email.VerifyEmail',[
                    'icon'=>'fa-times-circle',
                    'title'=>'Error',
                    'msg'=>'Email Already Verified! You can relax and enjoy༼ つ ◕_◕ ༽つ!'
                ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        if ($response = $this->verified($request)) {
            return $response;
        }


        return $request->wantsJson()
                        ? new JsonResponse('', 204)
                        : redirect(config('fortify.home').'?verified=1');
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

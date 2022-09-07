<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Mail;

class ForgotPasswordController extends Controller
{
    protected $jwtAuth;
    function __construct( JWTAuth $jwtAuth ) {
        $this->jwtAuth = $jwtAuth;
        $this->middleware('auth:api', ['except' => ['forgot_password', 'update_password']]);
        //
    }

    function forgot_password(Request $request) {
        

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {

            $res = [
                'success' => false,
                // 'message' => __('messages.'.$validator->messages()->first())
                'message' => __('messages.email_is_not_registered_with_us')
            ];
            return response()->json($res);
        }

        $data = $request->all();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => __('messages.email_invalid')]);
        }

        $user = User::where('email', $data['email'])->first();
        if(!$user) {
           
            return response()->json(['success' => false, 'message' => __('messages.email_not_registered')]);
        }

        $otp = $this->generateOtp();
        $details = [
            'title' => __('messages.OTP_for_password_reset'),
            'otp' => $otp
        ];

        $res = User::where('email', $data['email'])->update(['otp' => $otp]);
        \Mail::to($data['email'])->send(new \App\Mail\ForgetPasswordMail($details));

        return response()->json(['success' => true, 'message' => __('messages.email_sent_for_OTP'), 'otp' => $otp]);

    }

    function generateOtp() {
        $otp = rand ( 1000 , 9999 );
        $res = $this->checkOTPUnique($otp);
        if($res) {
            return $otp;
        }   else {
            $this->generateOtp();
        }
    }

    function checkOTPUnique($otp) {
        $res = User::where('otp', $otp)->first();
        if($res) {
            return false;
        }   else {
            return true;
        }
    }

    function update_password(Request $request) {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'otp' => 'required'
        ]);
        // print_r($validator); exit;
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __('messages.'.$validator->messages()->first())
            ];
            return response()->json($res);
        }

        $data = $request->all();
        $res = User::where('otp', $data['otp'])->first();

        if($res) {
            $password = bcrypt($data['password']);
            $res = User::where('otp', $data['otp'])->update(['password' => $password, 'otp' => null]);
            if($res) {
                return response()->json(['success' => true, 'message' => __('messages.password_updated_successfully')]);
            }   else {
                return response()->json(['success' => false, 'message' => __('messages.something_went_wrong')]);
            }
        }   else {
            return response()->json(['success' => false, 'message' => __('messages.invalid_OTP')]);
        }
    }
}

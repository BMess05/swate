<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Validator;
use App\Models\Faqs;

class FaqController extends Controller
{
    protected $jwtAuth;

    function faqs() {
        $questions = Faqs::orderBy('id', 'DESC')->get();
         $res = ['success' => true, 'message' => __('Faqs list'),'data'=>$questions];
         return response()->json($res);
    } 
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;
use App\Models\StorageType;
use App\Models\Goal;
use App\Models\CookingLevel;
use App\Models\DietCategories;
use App\Models\Category;
use App\Models\DeviceToken;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use App\Mail\ResetPasswordMail;
use Mail;
class RegisterController extends Controller
{
    protected $jwtAuth;
    function __construct( JWTAuth $jwtAuth ) {
        $this->jwtAuth = $jwtAuth;
        $this->middleware('auth:api', ['except' => ['register']]);
        //
    }

    function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,{$id},id,deleted_at,NULL',
            'password' => 'required',
            'device_token' => 'required',
            'device_type' => 'required|in:1,2'
        ]);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res);
        }

        $data = $request->all();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => "Email is Invalid"]);
        }


         //goals
         $goals = Goal::select("id", "goal_name")->get();
         $diet_categories = DietCategories::get();
         $storage_types = StorageType::pluck('storage_name');
         $cooking_levels = CookingLevel::select("id", "level_name", "description")->get();
         $ingredient_categories = Category::get()->toArray();
         $units = Unit::select('id','unit')->get();
         // $allergies = Allergy::select("id", "allergy")->get();
         $user_exists = User::withTrashed()->where('email', $data['email'])->first();
        if($user_exists) {
            if($user_exists->deleted_at != NULL) {
                $user = User::withTrashed()->find($user_exists->id);
                $user->deleted_at = NULL;
                $result = $user->save();
                if($result) {
                    $token_save = DeviceToken::updateOrCreate(['device_token' => $data['device_token'], 'device_type' => $data['device_type']], ['user_id' => $user->id]);
                    if(!$token_save) {
                        $res = ['success' => false, 'message' => 'Something went wrong, please try again.'];
                        return response()->json($res);
                    }
                    $token = $this->jwtAuth->fromUser($user);
                    $user_info = $user->toArray();

                    $res = ['success' => true, 'message' => 'Registerd successfully', 'token' => $token, 'user' => $user_info, 'goals' => $goals, 'cooking_levels' => $cooking_levels, 'diet_categories'=>$diet_categories, 'ingredient_categories' => $ingredient_categories,'storage_types'=>$storage_types,'units'=>$units];
                }   else {
                    $res = ['success' => false, 'message' => 'Something went wrong, please try again.'];
                }
                return response()->json($res);
            }
        }

        $user = new User();
        $user->name = $data['first_name'];
        if(isset($data['last_name'])) {
            $user->last_name = $data['last_name'];
        }
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->type = 1;
        $result = $user->save();
        if($result) {
            $token = $this->jwtAuth->fromUser($user);
            $user = User::where('id', $user->id)->first();
            $user_info = $user->toArray();

            $res = ['success' => true, 'message' => 'Registerd successfully', 'token' => $token, 'user' => $user_info, 'goals'=>$goals, 'cooking_levels'=>$cooking_levels, 'diet_categories'=>$diet_categories, 'ingredient_categories' => $ingredient_categories,'storage_types'=>$storage_types,'units'=>$units];


            $token_save = DeviceToken::updateOrCreate(['device_token' => $data['device_token'], 'device_type' => $data['device_type']], ['user_id' => $user->id]);
            if(!$token_save) {
                $res = ['success' => false, 'message' => 'Something went wrong, please try again.'];
                return response()->json($res);
            }

        }   else {
            $res = ['success' => false, 'message' => 'Something went wrong, please try again.'];
        }
        return response()->json($res);
    }

}

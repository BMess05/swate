<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
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
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    protected $jwtAuth;
    public function __construct( JWTAuth $jwtAuth )
    {
        $this->jwtAuth = $jwtAuth;
        $this->middleware('auth:api', ['except' => ['login', 'logout', 'refresh']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
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
            return json_encode(['success' => false, 'message' => 'Email Address is invalid']);
        }

         $goals = Goal::select("id", "goal_name")->get();
         $diet_categories = DietCategories::get();
         $storage_types = StorageType::pluck('storage_name');
         $cooking_levels = CookingLevel::select("id", "level_name", "description")->get();
         $ingredient_categories = Category::get()->toArray();
         $units = Unit::select('id','unit')->get();

        $user = User::where('email', $data['email'])->first();
        if(!$user) {
            return json_encode(['success' => false, 'message' => 'Email not registered']);
        }
        $credentials = $request->only('email', 'password');

        if ($token = $this->guard()->attempt($credentials)) {
            $token = $this->jwtAuth->fromUser($user);
            $token_exists = DeviceToken::where(['device_token' => $data['device_token'], 'device_type' => $data['device_type']])->first();

            $token_save = DeviceToken::updateOrCreate(['device_token' => $data['device_token'], 'device_type' => $data['device_type']], ['user_id' => $user->id]);

            if(!$token_save) {
                $res = ['success' => false, 'message' => 'Something went wrong, please try again.'];
                return response()->json($res);
            }

            $user = auth()->user();

            $res = ['success' => true, 'message' => 'Login successfully.', 'token' => $token, 'user' => $user->toArray(),'goals' => $goals, 'cooking_levels' => $cooking_levels, 'diet_categories'=>$diet_categories, 'ingredient_categories' => $ingredient_categories,'storage_types'=>$storage_types,'units'=>$units];
            return response()->json($res);
        }
        $res = ['success' => false, 'message' => 'Email or Password does not match.'];
        return response()->json($res);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
        // auth()->logout();
        $token = $this->jwtAuth->parseToken();
        $this->jwtAuth->invalidate($token);
        // $this->guard()->logout();
        $data = $request->all();

        $resp = DeviceToken::where('device_token', $data['device_token'])->delete();

        return response()->json(['success'=>true,'message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token =  $this->jwtAuth->refresh();
        $res = [
            'success' => true,
            'message' => 'Token Refreshed',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
        return response()->json($res);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Items;
use App\Models\Inventories;
use App\Models\Receipe;
use App\Models\ReceipeIngredient;
use App\Models\TimeFilters;
use App\Models\DishTypes;
use App\Models\CuisineTypes;
use App\Models\Tags;
use App\Models\DeviceToken;
use App\Models\Category;
use App\Models\DietCategories;
use App\Models\Unit;
use App\Models\ItemsExpireDay;
use App\Imports\IngredientsImport;
use App\Imports\RecipesImport;
use URL;
use App\Models\Goal;
use App\Models\CookingLevel;
use App\Models\StorageType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $jwtAuth;
     public function __construct( JWTAuth $jwtAuth )
    {
        $this->jwtAuth = $jwtAuth;
        $this->middleware('auth:api', ['except' => ['notification','importIngredients','importRecipes']]);
    }


    public function add_user_info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goals' => 'required',
            'diet_category' => 'required|integer', // old param "diet"
            'cooking_level' => 'required|integer',
            'allergies' => 'sometimes',
            'ingredients_donot_like' => 'sometimes',
            'cooking_days' => 'sometimes',
            'breakfast_time' => 'sometimes',
            'lunch_time' => 'sometimes',
            'dinner_time' => 'sometimes',
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __('messages.'.$validator->messages()->first())
            ];
            return response()->json($res);
        }

        $data = $request->all();
        //dd($data);
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        if($user)
        {
            if($user->fill($data)->save()){
                  $res = ['success' => true, 'message' => __('User info added successfully')];
            } else {
                $res = ['success' => false, 'message' => __('Something went wrong')];
            }

        }else{
             $res = ['success' => false, 'message' => __('Invalid user id')];
        }

        return response()->json($res);


    }

    public function getIngredientsByCategory(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'category_id' => 'required'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }

        $items = Items::with(['category','item_expiry_days.storage_type'])->where(['category_id' => $data['category_id']])->get();

        //dd($items);

        if($items)
        {
            $res = ['success' => true, 'message' => __('Ingredients list'),'items'=>$items];
        }else{
             $res = ['success' => false, 'message' => __('Invalid category id'),'items'=>[]];
        }

        return response()->json($res);


    }
    //user inventory
    public function saveUserIngredients(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ingredients' => 'required|array|min:1'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }

        $data = $request->all();


        foreach ($data['ingredients'] as $key => $value) {
            $item_exists = Items::find($value['item_id']);
            if(!$item_exists) {
                continue;
            }

            $inventory=Inventories::where(['item_id'=>$value['item_id'],'user_id'=>\Auth::user()->id])->first();
            $dt_obj = new \DateTime($value['expiry_date']);
            $expiry_date = $dt_obj->format('Y-m-d');
            $ifItemExistForUser = User::find(auth()->user()->id);

            $allergies = $ifItemExistForUser->allergies;
            $ingredient_donotlike = explode(',',$ifItemExistForUser->ingredients_donot_like);
            foreach($allergies as $key=>$allergy){
                if ($allergy['id'] == $value['item_id']) {
                    unset($allergies[$key]);
                    break;
                }
            }
            foreach($ingredient_donotlike as $key=>$dont_like){
                if ($dont_like == $value['item_id']) {
                    unset($ingredient_donotlike[$key]);
                    break;
                }
            }
            $ifItemExistForUser->allergies = implode(',', array_map(function ($entry) {
                return $entry['id'];
            }, $allergies));

            $ifItemExistForUser->ingredients_donot_like = implode(',', array_map(function ($entry) {
                return $entry;
            }, $ingredient_donotlike));
            $ifItemExistForUser->save();

            if(!$inventory){
                $inventory=  Inventories::create([
                    'item_id' => $value['item_id'],
                    'user_id' => auth()->user()->id,
                    'expiry_date' => $expiry_date,
                    'storage_type' => $value['storage_type'],
                    'quantity' => ($value['quantity'])??null,
                ]);
            }else{
                //update
                $inventory->expiry_date=$expiry_date;
                $inventory->storage_type=$value['storage_type'];
                $inventory->quantity=($value['quantity'])??null;
                $inventory=$inventory->save();
            }

        }

        if($inventory){
            $res = ['success' => true, 'message' => __('User ingredients added successfully')];
        }else {
            $res = ['success' => false, 'message' => __('Something went wrong')];
        }
        return response()->json($res);

    }



    public function getFilters(Request $request)
    {

        $time = TimeFilters::select(['id','time_filter_name'])->get()->toArray();
        $dish_type = DishTypes::select(['id','dish_type_name'])->get()->toArray();
        $cuisine = CuisineTypes::select(['id','cuisine_type_name'])->get()->toArray();
        $tags = Tags::select(['id','name'])->get()->toArray();

         $res = ['success' => true, 'message' => __('Filters list'),'time'=>$time,'dish_type'=>$dish_type,'cuisine'=>$cuisine,'tags'=>$tags];

         return response()->json($res);

    }

    public function getMyInventory(Request $request) {
        $td = new \DateTime();
        $today = $td->format('Y-m-d');
        $data['expired_inventories'] = Inventories::select(['id', 'item_id', 'expiry_date', 'storage_type','quantity'])->where('user_id', auth()->user()->id)->where('expiry_date', '<', $today)->get();
        //dd($data);

        $data['fresh_inventories'] = Inventories::select(['id', 'item_id', 'expiry_date', 'storage_type','quantity'])->where('user_id', auth()->user()->id)->where('expiry_date', '>=', $today)->get();
        $data['all_inventories'] = Inventories::select(['id', 'item_id', 'expiry_date', 'storage_type','quantity'])->where('user_id', auth()->user()->id)->get();
        if($data)
        {
            $res = [
                'success' => true,
                'message' => "Inventory list.",
                'data' => $data
            ];
        }else{
            $res = [
                'success' => false,
                'message' => "Inventory list.",
                'data' => $data
            ];
        }
        return response()->json($res);
    }

    public function removeMyInventory(Request $request) {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }

        $data = $request->all();
        foreach($data['items'] as $id) {
            $res = Inventories::where(['item_id'=> $id,'user_id'=>\Auth::user()->id])->delete();
        }

        $res = [
            'success' => true,
            'message' => "Items removed from Inventory."
        ];
        return response()->json($res);
    }

    public function searchIngredients(Request $request){

        $data=$request->all();
        $validator = Validator::make($data, [
            'search' => 'required'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }

        $ingredients = Items::select(['id','item_name','item_image','item_description'])->with('item_expiry_days.storage_type')->where('item_name', 'LIKE', '%'.$data['search'].'%')->get()->toArray();
        //dd($ingredients);

        if($ingredients){
            $res = ['success' => true, 'message' => __('Ingredients list'),'ingredients'=>$ingredients];
        } else {
            $res = ['success' => false, 'message' => __('No ingredients found')];
        }

        return response()->json($res);

    }


    public function change_password(Request $request) {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }
        $data = $request->all();
        $user = User::find(auth()->user()->id);
        // print_r($user->toArray()); exit;
        $result = \Hash::check($data['current_password'], $user->password);
        if(!$result) {
            return response()->json(['success' => false, 'message' => 'Current password does not match']);
        }
        $user->password = bcrypt($data['new_password']);
        if($user->save()) {
            return response()->json(['success' => true, 'message' => 'Password updated successfully']);
        }   else {
            return response()->json(['success' => false, 'message' => 'Something went wrong, Please try again']);
        }
    }

    public function getProfile(Request $request) {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $goals = Goal::select("id", "goal_name")->get();
        $diet_categories = DietCategories::get();
        $storage_types = StorageType::pluck('storage_name');
        $cooking_levels = CookingLevel::select("id", "level_name", "description")->get();
        $ingredient_categories = Category::get()->toArray();
        $units = Unit::select('id','unit')->get();
        if($user) {
            return response()->json(['success' => true, 'message' => 'User details found', 'user' => $user,'goals' => $goals, 'cooking_levels' => $cooking_levels, 'diet_categories'=>$diet_categories, 'ingredient_categories' => $ingredient_categories,'storage_types'=>$storage_types,'units'=>$units]);
        }   else {
            return response()->json(['success' => false, 'message' => 'No details found']);
        }
    }

    public function upload_image(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_picture' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res);
        }

        $file = $request->file('user_picture');
        $name = time() . '-' . $file->getClientOriginalName();
        $path = public_path('/uploads/users');
        if(!\File::exists($path)) {
            \File::makeDirectory($path, 0777, true, true);
        }
        $file_r = $file->move($path, $name);
        $path = url('/uploads/users/'.$name);


        $user = User::find(auth()->user()->id);
        $user->user_picture = $name;
        if($user->save()) {
            $res = ['success' => true, 'message' => 'Image uploaded successfully', 'file_name' => $path];
        }   else {
            $res = ['success' => true, 'message' => 'Something went wrong, Please try again.'];
        }
        return response()->json($res);
    }
    public function update_notification(Request $request){
        $validator = Validator::make($request->all(), [
            'push_notification' => 'sometimes|in:0,1'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res);
        }
        $data = $request->all();

        if(empty($data)) {
            return response()->json(['success' => false, 'message' => 'Please add some values to update']);
        }
        $user = User::find(auth()->user()->id);
        if(isset($data['push_notification'])) {
            $user->push_notification = $data['push_notification'];
        }
        if($user->save()) {
            $res = ['success' => true, 'message' => 'Notification updated successfully'];
        }   else {
            $res = ['success' => true, 'message' => 'Something went wrong, Please try again.'];
        }
        return response()->json($res);
    }
    public function update_profile(Request $request) {

        $data = $request->all();

        if(empty($data)) {
            return response()->json(['success' => false, 'message' => 'Please add some values to update']);
        }
        $user = User::find(auth()->user()->id);
        if(isset($data['diet_category']) && is_numeric($data['diet_category'])) {
            $user->diet_category = $data['diet_category'];
        }
        if(isset($data['allergies'])) {
            $user->allergies = $data['allergies'];
        }else{
            $user->allergies = '';
        }
        if(isset($data['ingredients_donot_like'])) {
            $user->ingredients_donot_like = $data['ingredients_donot_like'];
        }
        else{
            $user->ingredients_donot_like = '';
        }
        if($data['meal_size']) {
            $user->cook_for_people = $data['meal_size'];
        }

        if(isset($data['cooking_days'])) {
            $user->cooking_days = $data['cooking_days'];
        }
        if(isset($data['breakfast_time'])) {
            $user->breakfast_time = $data['breakfast_time'];
        }
        if(isset($data['lunch_time'])) {
            $user->lunch_time = $data['lunch_time'];
        }
        if(isset($data['dinner_time'])) {
            $user->dinner_time = $data['dinner_time'];
        }
        if(isset($data['first_name'])) {
            $user->name = $data['first_name'];
        }
        $user->last_name = $data['last_name'];
        if($user->save()) {
            $res = ['success' => true, 'message' => 'Profile updated successfully'];
        }   else {
            $res = ['success' => true, 'message' => 'Something went wrong, Please try again.'];
        }
        return response()->json($res);

    }

    public function notification(Request $request)
    {

        $data=$request->all();
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        $time = date('H:i');
        //dd($time);
        $time='05:45';
        $td = new \DateTime();
        $today = $td->format('Y-m-d');
        $date_after = date('Y-m-d', strtotime('3 days', strtotime($today)));
        $time_one_min_before = date('H:i', strtotime('-1 minutes', strtotime($time)));
        $time_twenty_nine_min_after = date('H:i', strtotime('29 minutes', strtotime($time)));

        $users=User::whereRaw("find_in_set($dayOfTheWeek , cooking_days)")
                ->where(function($q)  use ($time_one_min_before,$time_twenty_nine_min_after) {
                    $q->whereBetween('breakfast_time',[$time_one_min_before,$time_twenty_nine_min_after])
                      ->orWhereBetween('lunch_time', [$time_one_min_before,$time_twenty_nine_min_after])
                      ->orWhereBetween('dinner_time', [$time_one_min_before,$time_twenty_nine_min_after]);
                })->get()->toArray();
        if(count($users)>0){
            $device_token=[];
            foreach ($users as  $user) {
                $user_id[]=$user['id'];
                foreach ($user['device_token'] as $key => $token) {
                    $device_token[]=$token['device_token'];
                }
            }
            //dd($device_token);
            $items=Inventories::whereBetween('expiry_date',[$today,$date_after])->whereIn('user_id',$user_id)->get()->toArray();
             //dd($items);
            if(count($items)>0){
                $allitems = array_column($items, 'item');
                $item_names = array_column($allitems, 'item_name');
                if(count($item_names)==3){
                    $two_items=array_slice($item_names,0,2,true);
                   // dd($two_items);
                    $message="Items in your inventory ". implode(',', $two_items)." and ".(count($item_names)-2)." more are expiring soon! Use them immediately.";

                }else{
                    $message="Items in your inventory ". implode(',',$item_names)." are expiring soon! Use them immediately.";

                }
                if(count($device_token)>0){
                    $res=$this->sendPushNotification($device_token,'Expiring Soon!',$message,$user['id']);
                    $res = ['success' => true, 'message' =>'Notification sent','result'=>$res];
                }else{
                    $res = ['success' => false, 'message' =>'No device token found'];
                }

            }else{
                $res = ['success' => false, 'message' =>'No expiry items found'];
            }

        }else{
            $res = ['success' => false, 'message' => __('No data found')];
        }

        return response()->json($res);
    }

    function sendPushNotification($fcm_token, $title, $message, $id) {
        $your_project_id_as_key= env('FCM_TOKEN');
        //dd($your_project_id_as_key);
        Log::info($fcm_token);
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = [
        'authorization: key=' . $your_project_id_as_key,
            'content-type: application/json'
        ];
        $finalPostArray = array(
            'registration_ids' => $fcm_token,
            'notification' => array(
                'body' => $message,
                'title' => $title,
                'sound' => "default",
                'badge' => 1,
            ),
            "data"=> array(
                'id' => $id,
                'title' => $title,
                'body' => $message
            )
        );
        $postdata = json_encode($finalPostArray);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function importIngredients(Request $request) {
        if ($request->hasFile('select_file')) {
            $extension = $request->file('select_file')->getClientOriginalExtension();

            if(!in_array($extension, ['xls', 'xlsx','csv'])) {
                $res = ['success' => false, 'message' =>'Only xls or csv files are  allowed'];
            }

            $path = $request->file('select_file')->getRealPath();
        }else{
            $res = ['success' => false, 'message' =>'Please select a file'];
        }

        try {

            $import = new IngredientsImport($request->all());
            Excel::import($import, request()->file('select_file'));
            $rows=$import->rows->toArray();
            $data = [];
            $count = 0;
            foreach ($rows as $key=>$row) {
                $food_group = (isset($row['food_group'])) ? $row['food_group'] :'Produce';

                $category=Category::where('category_name', $food_group)->first();
                if (!$category) {
                    $category= Category::create([
                        'category_name' =>$food_group
                    ]);
                }
                $item=Items::create([
                    'user_id'=>0,
                    'item_name'=>$row['name'],
                    'category_id'=>$category->id,
                    'item_description'=>$row['storage_tips'],
                    'item_image'=>$row['image_url'],

                ]);

                if (isset($row['fridge_expiry']) && $row['fridge_expiry'] !='') {
                    ItemsExpireDay::create([
                        'item_id'=>$item->id,
                        'storage_type'=>1,
                        'expire_days'=>$row['fridge_expiry'],

                    ]);
                }
                if (isset($row['freezer_expiry']) && $row['freezer_expiry'] !='') {
                    ItemsExpireDay::create([
                        'item_id'=>$item->id,
                        'storage_type'=>2,
                        'expire_days'=>$row['freezer_expiry'],

                    ]);
                }
                if (isset($row['pantry_expiry']) && $row['pantry_expiry'] !='') {
                    ItemsExpireDay::create([
                        'item_id'=>$item->id,
                        'storage_type'=>3,
                        'expire_days'=>$row['pantry_expiry'],

                    ]);
                }
                $count++;

            }
            $res = ['success' => true, 'count'=> $count,'message' =>'Items import successfully'];
            // $res = ['success' => true, 'message' =>'Items import successfully'];

        } catch (\Exception $e) {
            $res = ['success' => false, 'message' =>$e->getMessage()];
        }
        return response()->json($res);
    }
    function replace($match){
        $key = trim($match[1]);
        $val = trim($match[2]);

        if($val[0] == '"')
            $val = '"'.addslashes(substr($val, 1, -1)).'"';
        else if($val[0] == "'")
            $val = "'".addslashes(substr($val, 1, -1))."'";

        return $key.": ".$val;
    }



    function importRecipes(Request $request)
    {

        if ($request->hasFile('select_file')) {
            $extension = $request->file('select_file')->getClientOriginalExtension();

            if(!in_array($extension, ['xls', 'xlsx','csv'])) {
                $res = ['success' => false, 'message' =>'Only xls or csv files are allowed'];
            }

            $path = $request->file('select_file')->getRealPath();
        }

        try {

            $import = new RecipesImport($request->all());
            Excel::import($import, request()->file('select_file'));
            $rows=$import->rows->toArray();

            $data = [];


            $count = 0;
            foreach ($rows as $key=>$row){
                $word= 'hr';
                if(strpos( $row['cooking_time'], $word) !== false){
                    $row['cooking_time'] =  $row['cooking_time'];
                } else{
                    $row['cooking_time'] = '0 hr '.$row['cooking_time'];
                }
                preg_match_all('/([\d]+)/', $row['cooking_time'], $match);
                $hr = (isset($match[0][0])) ? $match[0][0] :0;
                $min = (isset($match[0][1])) ? $match[0][1] :0;

                $row['cooking_time'] = ($hr * 60) +  $min;

                $tags = str_replace(array( '[', ']',"'",'"' ), '', $row['tags']);

                $receipe=Receipe::create([
                    'receipe_name'=>$row['recipe_name'],
                    'added_by'=>0,
                    'cooking_time'=>$row['cooking_time'],
                    'tags'=>$tags,
                    'author'=>$row['author'],
                    'author_profile'=>$row['author_profile'],
                    'serving'=>$row['serving'],
                    'directions'=>$row['directions'],
                    'receipe_image'=>$row['image'],
                ]);
                if(isset($row['ingredients']) && !empty($row['ingredients'])){
                    $json_decode= json_decode($row['ingredients'],true);
                }
                foreach ($json_decode as $key => $value) {
                    ReceipeIngredient::create([
                        'receipe_id'=>$receipe->id,
                        'ingredient_name'=> $value['item'],
                        'quantity'=>$value['quantity'],
                        'unit'=>$value['unit'],

                    ]);
                }

                $count++;
                $res = ['success' => true, 'count'=> $count,'message' =>'Receipes import successfully'];
            }

        } catch (\Exception $e) {
            $res = ['success' => false, 'message' =>$e->getMessage()];
        }
        return response()->json($res);
    }


}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Receipe;
use App\Models\Items;
use App\Models\DishTypes;
use App\Models\DietCategories;
use App\Models\CuisineTypes;
use App\Models\TimeFilters;
use App\Models\ReceipeIngredient;
use App\Models\Unit;
use App\Models\Tags;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DataTables;
use DB;
class ReceipeController extends Controller
{
    function script_for_db(){
        $search = '"'; $replace = "";$column = 'tags';
        // Receipe::where('tags', 'LIKE', '%'.$search.'%')
        // ->update([
        //     $column => DB::raw("REPLACE($column,'$search','$replace')") 
        //     ]);

        $query = "UPDATE receipe SET tags = REPLACE($column ,'$search','$replace')";
        DB::statement($query);
   
    }
    function listReceipes(Request $request) {
        if ($request->ajax()) {
           
            $data = Receipe::with(['ingredients', 'ingredients.items','dish','diet','cuisine']);
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('diet_type',function($action){
                return ucwords(str_replace('-', ' ', $action->diet['diet_category_name']));
               
            })
            ->addColumn('dish_type',function($action){
                return ucwords(str_replace('-', ' ', $action->dish['dish_type_name']));
            })
            ->addColumn('cuisine_type',function($action){
                
                return ucwords(str_replace('-', ' ', $action->cuisine['cuisine_type_name']));
            })
            ->addColumn('cooking_time',function($action){
                $hours = floor($action['cooking_time'] / 60);    
                $minutes = ($action['cooking_time'] % 60);
                if($hours<=9){
                    $hours="0".$hours;
                }
                if($minutes<=9){
                    $minutes = "0".$minutes;
                }
                $cooking_time = $hours . ":" .$minutes ;

                return $cooking_time;
            })
        

            ->addColumn('receipe_image',function($action){ 

                $receipe_image = $action["receipe_image"] ?  $action["receipe_image"] : asset("assets/img/thumbnail-default_2.jpg");
                return  '<img src="'.$receipe_image.'" width="100" height="90" class="img img-thumbnail" alt="">';
            })
            ->addColumn('by',function($action){
                
                return ucwords(str_replace('-', ' ', $action->by['name']));
            })
          
            ->addColumn('action', function($row){
                return '<a href="receipe/edit/'.$row->id.'" class="btn btn-info btn-sm"><i class="fas fa-user-edit"></i></a>
                <a onclick="javascript:confirmationDelete($(this));return false;" href="receipe/delete/'.$row->id.'" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
            })
   
            ->rawColumns(['receipe_image','action']) 
            ->make(true);
        }
        return view('admin.receipies.list');
    

        // $receipes = Receipe::with(['ingredients', 'ingredients.items','dish','diet','cuisine'])->orderBy('id', 'DESC')->get()->toArray();
        // $dishTypes = DishTypes::orderBy('id', 'DESC')->get()->toArray();
        // $dietTypes = Diet::orderBy('id', 'DESC')->get()->toArray();
        // $cuisineTypes = CuisineTypes::orderBy('id', 'DESC')->get()->toArray();

        // dd($receipes);

        // return view('admin.receipies.list', compact(['receipes']));
    }

    function addReceipe() {
        $ingredients = Items::get()->toArray();
        $dishTypes = DishTypes::orderBy('id', 'DESC')->get()->toArray();
        $dietTypes = DietCategories::orderBy('id', 'DESC')->get()->toArray();
        $cuisineTypes = CuisineTypes::orderBy('id', 'DESC')->get()->toArray();
        $timeFilters = TimeFilters::orderBy('id', 'DESC')->get()->toArray();
        $units = Unit::orderBy('id', 'DESC')->get()->toArray();
        $tags = Tags::orderBy('id', 'DESC')->get()->toArray();
        return view('admin.receipies.add', compact(['ingredients','dishTypes','dietTypes','cuisineTypes','timeFilters','units','tags']));
    }

    public function saveReceipe(Request $request)
    {
        $user = \Auth::user();
        $data = $request->all();
        // dd($data);

        $validation =Validator::make($data,[
            'receipe_name' => 'required',
            'cooking_time' => ['required'],
            'dish_type' => ['required',Rule::exists('dish_types','id')->where(function ($query) {
                    return $query->where('deleted_at', '=', null);
             })],
            'diet_type' => ['required',Rule::exists('diet_categories','id')->where(function ($query) {
                     return $query->where('deleted_at', '=', null);
            })],
            'cuisine_type' => ['required',Rule::exists('cuisine_types','id')->where(function ($query) {
                     return $query->where('deleted_at', '=', null);
            })],
            'directions' => 'required',
            'receipe_image' => 'required | file',
            'ingredients' =>'required|array|min:1',
            'ingredient_qty' => 'required|array|min:1'
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        }else{


            $stime = explode(':',$data['cooking_time']);
            $data['cooking_time'] = ($stime[0] * 60) +  $stime[1];

            $receipe = new Receipe();
            $receipe->receipe_name = $data['receipe_name'];
            $receipe->cooking_time = $data['cooking_time'];
            // $receipe->receipe_type = $data['receipe_type'];
            $receipe->dish_type = $data['dish_type'];
            $receipe->diet_type = $data['diet_type'];
            $receipe->cuisine_type = $data['cuisine_type'];
            $receipe->directions = $data['directions'];
            $receipe->tags = implode(', ',$data['tags']);
            if ($request->hasFile('receipe_image')) {
                $file = $request->file('receipe_image');
                $name = time() . '-' . $file->getClientOriginalName();
                $path = public_path('/uploads/receipes');
                $file_r = $file->move($path, $name);
                $receipe->receipe_image = $name;
            }

            $receipe->added_by = $user->id;
            if($receipe->save()) {
                
                foreach($data['ingredients'] as $index => $ingre) {
                    $ingredient = new ReceipeIngredient();
                    $ingredient->receipe_id = $receipe->id;
                    $ingredient->ingredient_name = $ingre;
                    $ingredient->quantity = $data['ingredient_qty'][$index];
                    $ingredient->unit = $data['unit'][$index];
                    $ingredient->save();

                }
                return redirect()->route('listReceipes')->with(['status' => 'success', 'message' => 'Receipe Saved Successfully']);
            }   else {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
            }
        }

    }

    function editReceipe($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid receipe id']);
        }
        $receipe1 = Receipe::with(['ingredients'])->where('id', $id)->first();
   
        if(!$receipe1) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid receipe id']);
        }
        $receipe = $receipe1->toArray();
     
        // $ingredients = Items::get()->toArray();
        $ingredients = Items::get()->pluck('item_name','id')->toArray();
         
        $dishTypes = DishTypes::orderBy('id', 'DESC')->get()->toArray();
        $dietTypes = DietCategories::orderBy('id', 'DESC')->get()->toArray();
        $cuisineTypes = CuisineTypes::orderBy('id', 'DESC')->get()->toArray();
        $timeFilters = TimeFilters::orderBy('id', 'DESC')->get()->toArray();
        // $units = Unit::orderBy('id', 'DESC')->get()->toArray();
        $units = Unit::orderBy('id', 'DESC')->get()->pluck('unit','id')->toArray();
        $tags = Tags::orderBy('id', 'DESC')->get()->toArray();
        // echo "<pre>";print_r($receipe);die;

        return view('admin.receipies.edit', compact(['receipe', 'ingredients','dishTypes','dietTypes','cuisineTypes','timeFilters','units','tags']));
    }

    function updateReceipe($id = null, Request $request) {

        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a receipe.']);
        }
        $receipe1 = Receipe::where('id', $id)->first();
        if(!$receipe1) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid receipe id']);
        }
        $validatedData = $request->validate([
            'receipe_name' => 'required',
            // 'cooking_time' =>  ['required',Rule::exists('time_filters','id')->where(function ($query) {
            //                                     return $query->where('deleted_at', '=', null);
            //                                 })],
            'cooking_time' => ['required'],
            'dish_type' => ['required',Rule::exists('dish_types','id')->where(function ($query) {
                    return $query->where('deleted_at', '=', null);
            })],
            'diet_type' => ['required',Rule::exists('diet_categories','id')->where(function ($query) {
                return $query->where('deleted_at', '=', null);
            })],
            'cuisine_type' => ['required',Rule::exists('cuisine_types','id')->where(function ($query) {
                 return $query->where('deleted_at', '=', null);
            })],
            'receipe_image' => 'sometimes|image|dimensions:max-width=300,max-height=300',
            'directions' => 'required',
            'ingredients' =>'required|array|min:1',
            'ingredient_qty' => 'required|array|min:1'
        ]);

        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;

        // dump($data['cooking_time']);


        $stime = explode(':',$data['cooking_time']);
        $data['cooking_time'] = ($stime[0] * 60) +  $stime[1];

        // dd($data['cooking_time']);

        $receipe = Receipe::find($id);
        $receipe->receipe_name = $data['receipe_name'];
        $receipe->cooking_time = $data['cooking_time'];
        // $receipe->receipe_type = $data['receipe_type'];
        $receipe->dish_type = $data['dish_type'];
        $receipe->diet_type = $data['diet_type'];
        $receipe->cuisine_type = $data['cuisine_type'];
        $receipe->directions = $data['directions'];
        $receipe->tags = implode(', ',$data['tags']);

        if ($request->hasFile('receipe_image')) {
            $file = $request->file('receipe_image');
            $name = time() . '-' . $file->getClientOriginalName();
            $path = public_path('/uploads/receipes');
            $file_r = $file->move($path, $name);
            $receipe->receipe_image = $name;

        }

        // dd($receipe);
        if($receipe->save()) {
            $res = ReceipeIngredient::where('receipe_id', $id)->delete();
            foreach($data['ingredients'] as $index => $ingre) {
                $ingredient = new ReceipeIngredient();
                $ingredient->receipe_id = $receipe->id;
                $ingredient->ingredient_name = $ingre;
                $ingredient->quantity = $data['ingredient_qty'][$index];
                $ingredient->unit = $data['unit'][$index];
                $ingredient->save();
            }
            return redirect()->route('listReceipes')->with(['status' => 'success', 'message' => 'Receipe Updated Successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
        }
    }

    function deleteReceipe($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a Receipe.']);
        }
        $item = Receipe::find($id);
        if(!$item) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Receipe id']);
        }
        if(Receipe::where('id', $id)->delete()) {
            $res = ReceipeIngredient::where('receipe_id', $id)->delete();
            return redirect()->back()->with(['status' => 'success', 'message' => 'Receipe deleted successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.']);
        }
    }
}

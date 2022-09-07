<?php

namespace App\Http\Controllers\Admin;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\IngredientsImport;
use App\Models\Items;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ItemsExpireDay;
use App\Models\StorageType;
use DataTables;
class ItemController extends Controller
{
    function listItems(Request $request) {
        if ($request->ajax()) {
            // $data = Items::with(['category'])->orderBy('id', 'DESC');
            $data = Items::with(['category']);
            return Datatables::of($data)
            // ->addIndexColumn()
     
            ->addColumn('category_name',function($action){
                return  $action->category['category_name'];
               
            })
            ->addColumn('item_image',function($action){ 
                
                $item_image = $action["item_image"] ?  $action["item_image"] : asset("assets/img/thumbnail-default_2.jpg");
                return  '<img src="'.$item_image.'" width="100" height="90" class="img img-thumbnail" alt="">';
            })
            
           
            ->addColumn('action', function($row){
                return '<a href="ingredients/edit/'.$row->id.'" class="btn btn-info btn-sm"><i class="fas fa-user-edit"></i></a>
                <a onclick="javascript:confirmationDelete($(this));return false;" href="ingredients/delete/'.$row->id.'" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
            })
            ->rawColumns(['item_image','action'])
            // ->orderColumn('id','item_name $1')
            ->make(true);
        }
        return view('admin.items.list');

        $i=0;

        // $items = Items::with(['category'])->orderBy('id', 'DESC')->get()->toArray();
        //  //echo "<pre>"; print_r($items); exit;
        // return view('admin.items.list', compact(['items','i']));
    }

    function addItem() {
        $categories = Category::get()->toArray();
        $units = Unit::get()->toArray();
        $storage_types = StorageType::get()->toArray();
        return view('admin.items.add', compact(['categories','units','storage_types']));
    }

    public function saveItem(Request $request)
    {

   
        $user = \Auth::user();
        $data = $request->all();

         //dd($data);

        $validation =Validator::make($data,[
            'item_name' => 'required|unique:items,item_name',
            'item_image' => 'required|image|dimensions:max-width=300,max-height=300',
            'category_id' => 'required|exists:categories,id',
            'item_storage_type' => 'required',
            'item_description' => 'required',
            //'expiry_date' => 'required',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        }else{

            $item = new Items();
            $item->user_id = \Auth::guard('web')->user()->id;
            $item->item_name = $data['item_name'];
            $item->category_id = $data['category_id'];
            $item->item_description = $data['item_description'];
            //$item->item_storage_type = $data['item_storage_type'];
            //$item->expiry_date = $data['expiry_date'];
           

            if ($request->hasFile('item_image')) {
                $file = $request->file('item_image');
                $name = time() . '-' . $file->getClientOriginalName();
                $path = public_path('/uploads/items');
                $file_r = $file->move($path, $name);
                $item->item_image = $name;
            }
            if($item->save()) {

                foreach($data['item_storage_type'] as $index => $ingre) {
                    $ingredient = new ItemsExpireDay();
                    $ingredient->item_id = $item->id;
                    $ingredient->storage_type = $data['item_storage_type'][$index];
                    $ingredient->expire_days = $data['expire_days'][$index];
                    $ingredient->save();

                }
                return redirect()->route('listItems')->with(['status' => 'success', 'message' => 'Item Saved Successfully']);
            }   else {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
            }
        }

    }

    function editItem($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid item id']);
        }
        $item = Items::with(['category','item_expiry_days'])->where('id', $id)->first();
        //dd($item);
       
        $categories = Category::get()->toArray();
        $units = Unit::get()->toArray();
        $storage_types = StorageType::get()->toArray();
        if(!$item) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid item id']);
        }
        return view('admin.items.edit', compact(['item', 'categories','units','storage_types']));
    }

    function updateItem($id = null, Request $request) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an item.']);
        }

        $data = $request->all();

        $validation =Validator::make($data,[
            'item_name' => 'required|unique:items,item_name,'.$id,
            'item_image' => 'sometimes|image|dimensions:max-width=300,max-height=300',
            'category_id' => 'required|exists:categories,id',
            'item_storage_type' => 'required',
            'item_description' => 'required',
            //'expiry_date' => 'required',


        ]);

         //echo "<pre>"; print_r($data); exit;

        if ($validation->fails()) {
            return redirect()->back()->withInput()->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        }else{

            $item = Items::find($id);
            if(!$item) {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a valid item.']);
            }
            $item->item_name = $data['item_name'];
            $item->category_id = $data['category_id'];
            $item->item_description = $data['item_description'];
            //$item->expiry_date = $data['expiry_date'];
           

            if ($request->hasFile('item_image')) {
                $file = $request->file('item_image');
                $name = time() . '-' . $file->getClientOriginalName();
                $path = public_path('/uploads/items');
                $file_r = $file->move($path, $name);
                $item->item_image = $name;
            }
            if($item->save()) {
                $res = ItemsExpireDay::where('item_id', $id)->delete();
                foreach($data['item_storage_type'] as $index => $ingre) {
                    $expire = new ItemsExpireDay();
                    $expire->item_id = $item->id;
                    $expire->storage_type = $data['item_storage_type'][$index];
                    $expire->expire_days = $data['expire_days'][$index];
                    $expire->save();

                }

                return redirect()->route('listItems')->with(['status' => 'success', 'message' => 'Item Updated Successfully']);
            }   else {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
            }
        }
    }

    function deleteItem($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an item.']);
        }
        $item = Items::find($id);
        if(!$item) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid item id']);
        }
        if(Items::where('id', $id)->delete()) {
            return redirect()->back()->with(['status' => 'success', 'message' => 'Item deleted successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.']);
        }
    }

    function getUnit(Request $request)
    {
       
       $item = Items::find($request->item_id)->toArray();
       //dd($item['unit']['unit']);
    //    echo "<pre>";print_r($item);die('gg');
       return response()->json($item['unit']['unit']);

    }
    function importIngredients(Request $request) {
        // die('tets');
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
            $message = $count.' Items import successfully';
            $res = ['status' =>'success', 'message' =>$message];
           
        } catch (\Exception $e) {
            $res = ['status' =>'danger', 'message' =>$e->getMessage()];
        }
     
        return redirect()->back()->with($res);
        // return response()->json($res);
    }
}

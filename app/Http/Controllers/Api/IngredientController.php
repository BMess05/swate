<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Items;

class IngredientController extends Controller
{
    protected $jwtAuth;

    function ingredientsCatgeory() {
        $categories = Category::orderBy('id', 'DESC')->get();
        if(count($categories)>0){
        	 $res = ['success' => true, 'message' => __('Categories list'),'data'=>$categories];

        }else{
        	 $res = ['success' => false, 'message' => __('No categories found'),'data'=>$categories];
        }
        
        
         return response()->json($res);
    }
    function getIngredients(Request $request){
        
        $search = $request->search;
        if($search == ''){
            $items = Items::orderby('item_name','asc')->select('id','item_name')->limit(20)->get();
         }else{
            $items = Items::orderby('item_name','asc')->select('id','item_name')->where('item_name', 'like', '%' .$search . '%')->limit(20)->get();
         }   
        $response = array();
        if(count($items)>0){
            $output = '<ul class="dropdown-menu" style="display:block; position:relative;width: 100%;
            padding: 4px 9px;font-size: 14px;color: #8898aa;">';
            foreach($items as $item){
                $output .= '<li><a class="select_suggestion" data-id="'.$request->id.'">'.$item->item_name.'</a></li> ';
            }
            $output .= '</ul>';
            echo $output;

            // foreach($items as $item){
              
            //     $response[] = array(
            //         // "id"=>$item->id,
            //       $item->item_name
            //     );
            //  }
             
        }
      
    
    }

    function searchMultipleIngredients(Request $request){

        $data=$request->all();
        $limit = $request->limit ? $request->limit : 20;
        $searchValues=$data['search'];
       
        if(!empty($searchValues)){
            $ingredients = Items::select(['id','item_name','item_image','item_description','category_id'])->with('item_expiry_days.storage_type')->where(function ($q) use ($searchValues) {
                foreach ($searchValues as $value) {
                    if($value == ""){continue;}
                    $q->orWhere('item_name', 'like', "%{$value}%");
                }
            })->paginate($limit)->toArray();

            if($ingredients['data']){
                $res = ['success' => true, 'message' => __('Ingredients list'),'current_page'=>$ingredients['current_page'],'last_page'=>$ingredients['last_page'],'total_results'=>$ingredients['total'],'ingredients'=>$ingredients['data']];
            } else {
                $res = ['success' => false, 'message' => __('No ingredients found')];
            }
        }else{
            $res = ['success' => false, 'message' => __('No ingredients found')];
        }
        return response()->json($res);

    } 
}

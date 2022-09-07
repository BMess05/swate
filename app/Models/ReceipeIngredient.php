<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Str;
use App\Http\Traits\InflectTrait;

class ReceipeIngredient extends Model
{
    use HasFactory;
    protected $table = 'receipe_ingredients';
    protected $fillable = ['receipe_id', 'ingredient_name', 'quantity','unit'];

    protected $appends = [
            'items',
            'is_avail_ingredient',
            'user_quantity',

    ];

    public function getItemsAttribute(){
         return Items::withTrashed()->select('id','item_name','item_description','item_image')->where('id',$this->ingredient_id )->first();
    }

    public function getUserQuantityAttribute(){
        $data= Inventories::select('quantity')->where('item_id',$this->ingredient_id )->where('user_id',\Auth::id())->first();
        return $data;
    }


    public function getIsAvailIngredientAttribute(){
        $searchValues = explode(" ", $this->ingredient_name);
        // $searchValues = preg_split('/\s+/', $this->ingredient_name, -1, PREG_SPLIT_NO_EMPTY);

        // $data =  Items::select('id')->where('user_id',\Auth::id())->where('item_name',$this->ingredient_name )->first();
     //initial//
        // $data= Inventories::select('id')->where('item_id',$this->ingredient_id)->where('user_id',\Auth::id())->orderBy('id','desc')->first();

        ///////
        //$Inventories= Inventories::select('item_id')->where('user_id',\Auth::id())->orderBy('id','desc')->pluck('item_id');
        $Inventories= Inventories::join('items','inventories.item_id','=','items.id')->where('inventories.user_id',\Auth::id())->pluck('items.item_name');
        if($Inventories){
            $Inventories = $Inventories->toArray();
        }

        $matchedVal = $this->compareArray(array_map('strtolower',$searchValues), array_map('strtolower',$Inventories));
        if($matchedVal){
            return 1;
        }
        return 0;


        // $data = [];
        // foreach($Inventories as $item_id){
        //     // $item_id = 12;
        //     // $searchValues = "%{$this->ingredient_name}%";
        //     // $query = Items::where(function($query) use ($searchValues){
        //     //     $query->where('item_name', 'like', $searchValues);
        //     // })

        //     // ->orwhere('id', $item_id )
        //     // ->value('id');

        //     $query = Items::where('id', $item_id )->where(function($q) use ($searchValues){
        //         foreach ($searchValues as $value) {
        //             $q->orWhere('item_name', 'like', '%'.$value.'%');
        //         }
        //     })->value('id');

        //     // $query = Items::query();
        //     // // $query = $query->where('id', 42);
        //     // $query = $query->orwhere(function ($q) use($searchValues) {
        //     //     foreach ($searchValues as $value) {
        //     //         echo $value;echo '--';
        //     //         // $query= $q->where('id', 42);
        //     //         $query= $q->orWhere('item_name', 'like', '%'.$value.'%');
        //     //     }
        //     //   })
        //     //   ->toSql();

        //     // $query = Items::where('item_name','LIKE', '%'.$this->ingredient_name.'%')->orwhere('id',$item_id )->toSql();
        //     // echo "<pre>";print_r($query);die;
        //     //
        //     if(!empty($query)){
        //         return 1;
        //     }

        //  }
        //  return 0;
        //  echo "<pre>";print_r($data);die;

        // if (isset($data['id'])) {
        //     return 1;
        // }else{
        //     return 0;
        // }
       /* if(isset($data['quantity']))
        {
            $user_quantity=$data['quantity'];
            $receipe_quantity=$this->quantity;
            $cook_for_people=\Auth::user()->cook_for_people;
            $need_quantity=$receipe_quantity*$cook_for_people;
            if($user_quantity<$need_quantity)
            {
                return 0;
            }else{
                return 1;
            }

        }else{
            return 0;
        }*/
    }

    function compareArray($array1, $array2){ // searchValues, Inventories
        $matched = [];
        $plural_array = [];
        $singular_array = [];
        foreach($array1 as $str) {
            $strr = str_replace(',', '', $str);
            $plural_array[] = InflectTrait::pluralize($strr);
            $singular_array[] = InflectTrait::singularize($strr);
        }

        $matched = array_intersect($array2, $array1);
        $matched_plural = array_intersect($array2, $plural_array);
        $matched_singular = array_intersect($array2, $singular_array);
        $all_matched = array_unique(array_merge($matched, $matched_plural, $matched_singular));
        return $all_matched;
    }

    function compareArray_old($array1, $array2){
        $matched = [];
        $srting1 = implode(' ',$array1);
        $srting2 = implode(' ',$array2);
        foreach ($array1 as $value) {
            if(strpos($srting2, $value) !== false){
                $matched[] = $value;
            }
        }
        foreach ($array2 as $value) {
            if(strpos($srting1, $value) !== false){
                $matched[] = $value;
            }
        }
        return $matched;
    }

    public function unit() {
        return $this->hasOne('App\Models\Unit', 'id', 'unit');
    }

    public function receipe() {
        return $this->belongsTo('App\Models\Receipe', 'receipe_id', 'id');
    }

    public function items() {
        return $this->belongsTo('App\Models\Items', 'ingredient_id', 'id');
    }

    public function getUnitAttribute() {
        //dd($columns = $this->getAttributes());

            $data= Unit::find($this->attributes['unit']);
            //return $data->value('unit');
            return $data;



    }

}

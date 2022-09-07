<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DishTypes;



class Receipe extends Model
{
    use HasFactory;
    protected $table = 'receipe';
    protected $fillable = ['receipe_name', 'cooking_time', 'receipe_type', 'directions','receipe_image','dish_type','diet_type','cuisine_type','added_by','tags','author','author_profile','serving'];


    protected $with = [ 'by' ];

     protected $appends = [

            'dish',
            'diet',
            'cuisine',
            'favourite',
            'receipe_image'
    ];

    public function getReceipeImageAttribute() {
        if($this->attributes['receipe_image'] == "") {
            return null;
        }
        if(isset($this->attributes['added_by']) && $this->attributes['added_by'] == 0){
            if($this->attributes['receipe_image'] !='n'){
                return 'http://'.$this->attributes['receipe_image'];
            }

        }else{
            return url('/uploads/receipes/'.$this->attributes['receipe_image']);
        }
    }

      public function getDishAttribute()
    {
       // dd($this->dish_type);
        return DishTypes::select(['id','dish_type_name'])->where('id',$this->dish_type )->first();
    }


      public function getDietAttribute()
    {
        return DietCategories::select(['id','diet_category_name'])->where('id',$this->diet_type )->first();
    }

      public function getCuisineAttribute()
    {
        return CuisineTypes::select(['id','cuisine_type_name'])->where('id',$this->cuisine_type )->first();
    }
      public function getFavouriteAttribute()
    {
        return FavouriteRecipe::select(['recipe_id'])->where(['recipe_id' => $this->id,'user_id'=>auth()->user()->id])->first();
    }

    public function ingredients() {
        return $this->hasMany('App\Models\ReceipeIngredient', 'receipe_id', 'id');
    }


    // public function getDishTypeNameAttribute()
    // {
    //     return DishTypes::whereId($this->receipe_type)->pluck('dish_type_name')[0];

    // }

    public function dish()
    {
        return $this->hasOne('App\Models\DishTypes', 'id', 'dish_type');
    }

    public function diet()
    {
        return $this->hasOne('App\Models\DietCategories', 'id','diet_type' );
    }

    public function cuisine()
    {
        return $this->hasOne('App\Models\CuisineTypes',  'id','cuisine_type');
    }

    public function by(){
        return $this->hasOne('App\Models\User','id','added_by');
    }




}

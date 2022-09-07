<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteRecipe extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','recipe_id'];

     public function receipe() {
        return $this->belongsTo('App\Models\Receipe', 'recipe_id', 'id');
    }

    /* protected $appends = [
            'user_detail'
    ];

     public function getUserDetailAttribute()
    {
        return User::select('id','name','email')->where( 'id',$this->user_id )->first();
    }*/
}

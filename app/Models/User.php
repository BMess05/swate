<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    protected $appends = ['cooking_level', 'diet_category', 'goals', 'allergies', 'donot_like', 'user_picture','device_token'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'goals',
        'diet_category',
        'cooking_level',
        'allergies',
        'cook_for_people',
        'ingredients_donot_like',
        'otp',
        'user_picture',
        'push_notification',
        'cooking_days',
        'breakfast_time',
        'lunch_time',
        'dinner_time',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // public function goals() {

    //     return $this->hasMany('App\Model\Goals', 'goals', 'id');
    // }

    // public function diets() {

    //     return $this->hasMany('App\Model\Diet', 'diet', 'id');
    // }

    public function cooking_level() {

        return $this->belongsTo('App\Models\CookingLevel', 'cooking_level', 'id');
    }

     /*public function inventory() {

        return $this->belongsTo('App\Models\Inventories', 'id', 'user_id');
    }*/
    public function inventory() {
        return $this->hasMany('App\Models\Inventories', 'user_id', 'id');
    }


    public function getCookingLevelAttribute() {
        return CookingLevel::select(['id', 'level_name', 'description'])->find($this->attributes['cooking_level']);
    }

    public function getDietCategoryAttribute() {
        return DietCategories::select(['id', 'diet_category_name', 'diet_category_description'])->find($this->attributes['diet_category']);
    }

    public function getGoalsAttribute() {
        if($this->attributes['goals'] == "") {
            return [];
        }
        $goals_arr = explode(',', $this->attributes['goals']);
        $goals = [];
        foreach($goals_arr as $goal_id) {
            $goal = Goal::select(['id', 'goal_name'])->find($goal_id);
            if($goal) {
                $goals[] = $goal->toArray();
            }
        }
        return $goals;
    }

     /*public function getIngredientsDonotLikeAttribute() {
        if($this->attributes['ingredients_donot_like'] == "") {
            return [];
        }
        $goals_arr = explode(',', $this->attributes['ingredients_donot_like']);
        $goals = [];
        foreach($goals_arr as $goal_id) {
            $goal = Items::select(['id', 'item_name'])->find($goal_id);
            if($goal) {
                $goals[] = $goal->toArray();
            }
        }
        return $goals;
    }*/

    public function getAllergiesAttribute() {
        if($this->attributes['allergies'] == "") {
            return [];
        }
        $allergies_arr = explode(',', $this->attributes['allergies']);
        $allergies = [];
        foreach($allergies_arr as $allergy_id) {
            $item = Items::select(['id', 'item_name', 'item_image'])->find($allergy_id);
            if($item) {
                $allergies[] = $item->toArray();
            }
        }
        return $allergies;
    }

    public function getDonotLikeAttribute() {
        if($this->attributes['ingredients_donot_like'] == "") {
            return [];
        }
        $donot_like_arr = explode(',', $this->attributes['ingredients_donot_like']);
        $items = [];
        foreach($donot_like_arr as $ingredient_id) {
            $item = Items::select(['id', 'item_name', 'item_image'])->find($ingredient_id);
            if($item) {
                $items[] = $item->toArray();
            }
        }
        return $items;
    }

    public function getUserPictureAttribute() {
        if($this->attributes['user_picture'] == "") {
            return null;
        }
        return url('/uploads/users/'.$this->attributes['user_picture']);
    }

    public function getDeviceTokenAttribute() {

        return DeviceToken::select(['id', 'device_type', 'device_token'])->where('user_id',$this->attributes['id'])->get();
    }

    public function device_token() {

        return $this->hasMany('App\Models\DeviceToken', 'user_id', 'id');
     }

}

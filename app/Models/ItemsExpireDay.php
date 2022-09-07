<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsExpireDay extends Model
{
    use HasFactory;

    protected $fillable = ['item_id','storage_type','expire_days'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'storage'
    ];

   /* protected $appends = [
            'storage',
            
            
    ];*/

    /* public function receipe() {
        return $this->belongsTo('App\Models\Receipe', 'recipe_id', 'id');
    }*/

    /* protected $appends = [
            'user_detail'
    ];

   */
   /*   public function getStorageAttribute()
    {
        return StorageType::select('id','storage_name')->where( 'id',$this->storage_type )->first();
    }*/

    public function storage_type() {
        return $this->hasOne('App\Models\StorageType', 'id', 'storage_type');
    }
}

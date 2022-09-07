<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Items extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'items';
    protected $fillable = ['item_name', 'item_image', 'item_type','expiry_date','category_id','user_id','item_description'];

    protected $appends = [
            'item_image',
            
            
    ];

  /*  protected $appends = [
            'category'
    ];

     public function getCategoryAttribute()
    {
        dd($this->category_id);
        return Category::where('id',$this->category_id)->first();
    }
*/
    public function category() {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    public function unit() {
        return $this->hasOne('App\Models\Unit', 'id', 'unit');
    }

   

     public function item_expiry_days() {
        return $this->hasMany('App\Models\ItemsExpireDay', 'item_id', 'id');
    }

    public function inventories() {
        return $this->hasMany('App\Models\Inventories', 'item_id', 'id');
    }

    public function getItemImageAttribute() {

        if($this->attributes['item_image'] == "" || $this->attributes['item_image']=='@') {
            return null;
        }
        // if(isset($this->attributes['user_id']) && $this->attributes['user_id'] == 0){
        //     return url($this->attributes['item_image']);
        // }
        else{
             
            if (!preg_match("~^(?:f|ht)tps?://~i", $this->attributes['item_image'])) {
                return url('/uploads/items/'.$this->attributes['item_image']);
                
            }else{
                return url($this->attributes['item_image']);
            }
         
        }
        
    }

    
}

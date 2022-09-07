<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventories extends Model
{
    use HasFactory;
    protected $table = 'inventories';

    protected $fillable = ['item_id', 'user_id', 'expiry_date', 'storage_type', 'category_id','quantity'];
    protected $appends = [ 'item' ];
    
    public function items() {
        return $this->belongsTo('App\Models\Items', 'item_id', 'id');
    }

    public function getItemAttribute() {
        $items= Items::withTrashed()->select(['item_name', 'item_image', 'item_description','category_id'])->where('id', $this->item_id )->first();
        return $items;
    }
}

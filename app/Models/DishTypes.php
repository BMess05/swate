<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DishTypes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'dish_types';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['dish_type_name'];
}

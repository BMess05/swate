<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DietCategories extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'diet_categories';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['diet_category_name', 'diet_category_description'];


}

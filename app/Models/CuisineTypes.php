<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CuisineTypes;


class CuisineTypes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cuisine_types';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['cuisine_type_name'];
}

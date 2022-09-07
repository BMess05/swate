<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeFilters extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'time_filters';
    protected $fillable = ['time_filter_name','created_at','updated_at','time_filter_value','time_filter_condition'];

    protected $appends = [ ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookingLevel extends Model
{
    use HasFactory;
    protected $table = "cooking_levels";
    protected $fillable = ['level_name', 'description'];

    public function users() {

        return $this->hasMany('App\Models\User', 'cooking_level', 'id');
    }
}

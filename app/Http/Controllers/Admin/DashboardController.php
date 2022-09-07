<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Items;
use App\Models\Receipe;
use App\Models\DietCategories;
use App\Models\CuisineTypes;
use App\Models\DishTypes;
use App\Models\Faqs;
use App\Models\Tags;

class DashboardController extends Controller
{
    function dashboard() { 
        $app_users_count = User::where(['type' => 1])->count();
        $category_count = Category::get()->count();
        $items_count = Items::get()->count();
        $receipe_count = Receipe::get()->count();
        $diet_categories = DietCategories::get()->count();
        $cuisine_types = CuisineTypes::get()->count();
        $dish_types = DishTypes::get()->count();
        $questions = Faqs::get()->count();
        $tags_count = Tags::get()->count();

        return view('admin/dashboard', compact(['app_users_count', 'category_count', 'items_count', 'receipe_count','diet_categories','cuisine_types','dish_types','questions','tags_count']));
    }
}

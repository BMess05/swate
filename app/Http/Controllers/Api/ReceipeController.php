<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Items;
use App\Models\Inventories;
use App\Models\Receipe;
use App\Models\ReceipeIngredient;
use App\Models\TimeFilters;
use App\Models\DishTypes;
use App\Models\CuisineTypes;
use App\Models\FavouriteRecipe;

class ReceipeController extends Controller
{

    protected $jwtAuth;
    function __construct(JWTAuth $jwtAuth)
    {
        $this->jwtAuth = $jwtAuth;
        //dd($this->jwtAuth);
        /*$this->middleware('auth:api', ['except' => ['add_user_info','getIngredientsByCategory']]);*/
    }
    public function getRecipes(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            // 'filter' => 'required',
            'limit' => 'numeric | nullable',
            'page' => 'numeric | nullable',
        ]);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }

        $limit = $request->limit ? $request->limit : 20;

        $user = User::where('id', \Auth::user()->id)->first();
        // $inventories =Inventories::where('user_id',\Auth::user()->id)->get()->toArray();
        // $items_array =Items::where('user_id',\Auth::user()->id)->get()->toArray();
        // echo "<pre>";print_r($items_array);die;
        // $ingredient_name = [];
        // $ingredient_ids = [];
        // foreach ($items_array as $key => $item) {
        //    $ingredient_name[$inventory['item_id']] = $item['item']['item_name'];
        //    $ingredient_ids[] = $inventory['item_id'];
        // }
        //
        // foreach ($ingredient_name as $key => $value) {
        //    echo "<pre>";print_r($ingredient_name);die('tets');
        // }

        $receipe = Receipe::query();
        $receipe = $receipe->with(['ingredients'])->withCount('ingredients')->having('ingredients_count', '>', 0);

        if (isset($data['filter']['dish_type']) && !empty($data['filter']['dish_type'])) {
            $receipe = $receipe->whereIn('dish_type', $data['filter']['dish_type']);
        }

        if (isset($data['filter']['cuisine']) && !empty($data['filter']['cuisine'])) {
            $receipe = $receipe->whereIn('cuisine_type', $data['filter']['cuisine']);
        }
        if (isset($data['filter']['tags']) && !empty($data['filter']['tags'])) {
            $receipe = $receipe->where(function ($query) use ($data) {
                foreach ($data['filter']['tags'] as $term) {
                    $query->orWhere('tags', 'like', '%' . $term . '%');
                };
            });
        }
        // dd($data);
        if (isset($data['filter']['time'])) {
            $time = TimeFilters::where('id', $data['filter']['time'])->first();
            $time_value = $time->time_filter_value;
            $time_condition = $time->time_filter_condition;

            if ($time_condition == 1) {
                $receipe = $receipe->where('cooking_time', '<', $time_value);
            } elseif ($time_condition == 2) {
                $receipe = $receipe->where('cooking_time', '>', $time_value);
            } else {
                $receipe = $receipe->where('cooking_time', '=', $time_value);
            }
        }

        $ingredient_name = [];
        foreach ($user['allergies'] as $key => $allergy) {
            $ingredient_name[$allergy['id']] = $allergy['item_name'];
        }

        $ingredient_donotlike = explode(',', $user->ingredients_donot_like);
        foreach ($ingredient_donotlike as $key => $ingredient_id) {
            $item = Items::select(['id', 'item_name', 'item_image'])->find($ingredient_id);
            if ($item) {
                $ingredient_name[$item['id']] = $item['item_name'];
            }
        }

        if (isset($data['not_include']) && !empty($data['not_include'])) {
            $receipes = $receipe->whereNotIn('id', $data['not_include']);
        }

        $receipes = $receipe->inRandomOrder();

        if (!empty($ingredient_name)) {
            $receipes = $receipe->with(['ingredients'])
                ->whereDoesntHave('ingredients', function ($query) use ($ingredient_name) {
                    $query->where(function ($q) use ($ingredient_name) {
                        foreach ($ingredient_name as $key => $value) {
                            $q->orWhere('ingredient_name', 'LIKE', '%' . $value . '%');
                        }
                    });
                })
                ->paginate($limit);
        }   else {
            $receipes = $receipe->paginate($limit);
        }


        // Removed below section because allergic items should be removed with or  without filter as well

        // if (!isset($data['filter']) || empty($data['filter'])) {
        //     if (!empty($ingredient_name)) {
        //         $receipes = $receipe->with(['ingredients'])
        //             ->whereDoesntHave('ingredients', function ($query) use ($ingredient_name) {
        //                 $query->where(function ($q) use ($ingredient_name) {
        //                     foreach ($ingredient_name as $key => $value) {
        //                         $q->orWhere('ingredient_name', 'LIKE', '%' . $value . '%');
        //                     }
        //                 });
        //             })
        //             ->paginate($limit);
        //     } else {
        //         $receipes = $receipe->paginate($limit)->withCount('ingredients')->having('ingredients_count', '>', 0);
        //     }
        // } else {
        //     $receipes = $receipe->paginate($limit);
        // }






        //  $receipes=$receipe->whereHas('ingredients', function ($query) use ($user) {
        //     $query->whereNotIn('id', explode(",",$user->ingredients_donot_like))->whereNotIn('id', $user['allergies']);
        // })->paginate($limit);

        $receipes = $receipes->toArray();

        $res = $this->get_receipe_data($receipes);
        return response()->json($res);
    }



    //must be removed
    public function getRecipesByIngredients(Request $request)
    {

        $data = $request->all();


        $validator = Validator::make($data, [
            'ingredients' => 'required',
            'limit' => 'numeric | nullable',
            'page' => 'numeric | nullable',
        ]);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }

        $limit = $request->limit ? $request->limit : 50;

        $receipes = Receipe::with(['ingredients'])
            ->whereHas('ingredients', function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    foreach ($data['ingredients'] as $value) {
                        $q->orWhere('ingredient_name', 'like', '%' . $value . '%');
                    }
                });
            })->paginate($limit);
        // $receipes = Receipe::with(['ingredients'])
        //     ->whereHas('ingredients', function ($query) use ($data) {
        //        foreach ($data['ingredients'] as $value) {
        //            $query->orWhere('ingredient_name', 'like', '%'.$value.'%');
        //         }
        // // })->paginate($limit);
        //  })->toSql();
        // echo "<pre>";print_r($receipes);die;
        $receipes = $receipes->toArray();
        //dd($receipes);

        $res = $this->get_receipe_data($receipes);

        return response()->json($res);
    }

    function get_receipe_data($receipes)
    {

        if ($receipes['data']) {

            foreach ($receipes['data'] as $key => $receipe) {
                $ingredients = [];
                $mis_ingredient_count = 0;
                foreach ($receipe['ingredients'] as $key => $ingredient) {
                    if ($ingredient['is_avail_ingredient'] == 0) { //means ingredient is missing
                        $mis_ingredient_count = $mis_ingredient_count + 1;
                    }
                    $ingredients[] = [
                        'ingredient_id' => $ingredient['id'],
                        'ingredient_name' => $ingredient['ingredient_name'],
                        'quantity' => $ingredient['quantity'],
                        'unit' => (isset($ingredient['unit'])) ? $ingredient['unit'] : new \StdClass(),
                        'is_available' => $ingredient['is_avail_ingredient']
                    ];
                }

                $receipedata[] = [
                    'receipe_id' => $receipe['id'],
                    'receipe_name' => $receipe['receipe_name'],
                    'cooking_time' => $receipe['cooking_time'],
                    'directions' => $receipe['directions'],
                    'receipe_image' => $receipe['receipe_image'],
                    'dish' => (isset($receipe['dish'])) ? $receipe['dish']['dish_type_name'] : '',
                    'diet' => (isset($receipe['diet'])) ? $receipe['diet']['diet_category_name'] : '',
                    'cuisine' => (isset($receipe['cuisine'])) ? $receipe['cuisine']['cuisine_type_name'] : '',
                    'is_favourite' => ($receipe['favourite'] != null) ? 1 : 0,
                    'tags' => $receipe['tags'],
                    'ingredients' => $ingredients,
                    'missing_ingredient_count' => $mis_ingredient_count,
                    'ingredient_count' => count($ingredients)
                ];
            }

            $receipedata = collect($receipedata)->sortBy('missing_ingredient_count')->values();

            //  echo "<pre>";print_r($receipedata);die;
            // if($receipes['data']){
            if (isset($ingredients) && !empty($ingredients)) {
                $res = ['success' => true, 'message' => __('Receipes list'), 'current_page' => $receipes['current_page'], 'last_page' => $receipes['last_page'], 'total_results' => $receipes['total'], 'recipes' => $receipedata];
            } else {
                $res = ['success' => false, 'message' => __('No recipes found'), 'current_page' => $receipes['current_page'], 'last_page' => $receipes['last_page'], 'total_results' => $receipes['total'], 'recipes' => []];
            }
        } else {
            $res = ['success' => false, 'message' => __('No recipes found'), 'current_page' => $receipes['current_page'], 'last_page' => $receipes['last_page'], 'total_results' => $receipes['total'], 'recipes' => []];
        }
        return $res;
    }
    public function sortAvailIngredient($a, $b)
    {

        if (strtotime($a['missing_ingredient_count']) == strtotime($b['missing_ingredient_count'])) return 0;
        return (strtotime($a['missing_ingredient_count']) < strtotime($b['missing_ingredient_count'])) ? -1 : 1;
    }
    //search recepie by text(recepie name)
    public function searchRecipes(Request $request)
    {

        $data = $request->all();
        $limit = $request->limit ? $request->limit : 20;
        if (isset($data['search'])) {
            $receipe = Receipe::with(['ingredients'])->withCount('ingredients')->where('receipe_name', 'LIKE', '%' . $data['search'] . '%')->having('ingredients_count', '>', 0);
            // $receipe = Receipe::with(['ingredients','ingredients.items.item_expiry_days.storage_type'])->where('receipe_name','LIKE', '%'.$data['search'].'%');

            $receipe = $receipe->paginate($limit);
            $receipes = $receipe->toArray();
            $res = $this->get_receipe_data($receipes);
        } else {
            $res = ['success' => false, 'message' => __('No recipes found'), 'recipes' => []];
        }

        return response()->json($res);
        //dd($data);

    }

    public function favouriteRecipe(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'favourite' => 'required',
            'recipe_id' => 'required'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }

        $data['user_id'] = \Auth::user()->id;

        if ($data['favourite'] == 1) {
            //add
            $receipe = Receipe::find($data['recipe_id']);
            if ($receipe) {
                $favourite_receipe = FavouriteRecipe::where(['recipe_id' => $data['recipe_id'], 'user_id' => \Auth::user()->id])->first();
                if (!$favourite_receipe) {
                    unset($data['favourite']);
                    $favourite = FavouriteRecipe::create($data);
                    $res = ['success' => true, 'message' => __('Recipe added to your favourite')];
                } else {
                    $res = ['success' => false, 'message' => __('Recipe already in your favourite.')];
                }
            } else {
                $res = ['success' => false, 'message' => __('Invalid recipe id')];
            }
        } else {
            //delete
            $favourite_receipe = FavouriteRecipe::where(['recipe_id' => $data['recipe_id'], 'user_id' => \Auth::user()->id])->first();
            if ($favourite_receipe) {
                $favourite_receipe->delete();
                $res = ['success' => true, 'message' => __('Recipe removed from your favourite')];
            } else {
                $res = ['success' => false, 'message' => __('Receipe not in your favourite')];
            }
        }

        return response()->json($res);
    }

    public function getFavouriteRecipes(Request $request)
    {
        $data = $request->all();
        $user_id = \Auth::user()->id;
        if (!$user_id) {
            $res = ['success' => false, 'message' => __('Invalid token')];
            return response()->json($res);
        }
        if (isset($data['limit'])) {
            $favourite_receipe = FavouriteRecipe::with(['receipe' => function ($query) {
                $query->withCount('ingredients')->having('ingredients_count', '>', 0);
            }, 'receipe.ingredients'])->where('user_id', $user_id)->paginate($data['limit']);
            $favourite_receipe = $favourite_receipe->toArray();

            //dd($favourite_receipe=$favourite_receipe['data']);
        } else {
            $favourite_receipe = FavouriteRecipe::with(['receipe' => function ($query) {
                $query->withCount('ingredients')->having('ingredients_count', '>', 0);
            }, 'receipe.ingredients'])->where('user_id', $user_id)->paginate();
            $favourite_receipe = $favourite_receipe->toArray();
        }

        if ($favourite_receipe && $favourite_receipe['data']) {

            foreach ($favourite_receipe['data'] as $key => $receipe) {

                $ingredients = [];
                if(isset($receipe['receipe']['ingredients'])) {
                    foreach ($receipe['receipe']['ingredients'] as $key => $ingredient) {

                        $ingredients[] = [
                            'ingredient_id' => $ingredient['id'],
                            'ingredient_name' => $ingredient['ingredient_name'],

                            // 'ingredient_name'=>$ingredient['items']['item_name'],
                            // 'ingredient_description'=>$ingredient['items']['item_description'],
                            //'ingredient_storage_type'=>$ingredient['items']['item_storage_type'],
                            'quantity' => $ingredient['quantity'],
                            //'unit'=>$ingredient['items']['unit'],
                            'is_available' => $ingredient['is_avail_ingredient'],
                            //'user_quantity'=>$ingredient['user_quantity']['quantity'],

                        ];

                    }

                    $receipedata[] = [
                        'receipe_id' => (int)$receipe['recipe_id'],
                        'receipe_name' => $receipe['receipe']['receipe_name'],
                        'cooking_time' => $receipe['receipe']['cooking_time'],
                        'directions' => $receipe['receipe']['directions'],
                        'receipe_image' => $receipe['receipe']['receipe_image'],
                        'dish' => $receipe['receipe']['dish']['dish_type_name'] ?? '',
                        'diet' => $receipe['receipe']['diet']['diet_category_name'] ?? '',
                        'cuisine' => $receipe['receipe']['cuisine']['cuisine_type_name'] ?? '',
                        'is_favourite' => ($receipe['receipe']['favourite'] != null) ? 1 : 0,
                        'tags' => $receipe['receipe']['tags'],
                        'ingredients' => $ingredients,
                    ];
                }
            }
            //$res = ['success' => true, 'message' => __('Receipes list'),'total_results'=>count($receipedata),'recipes'=>$receipedata];
            $res = ['success' => true, 'message' => __('Receipes list'), 'current_page' => $favourite_receipe['current_page'], 'last_page' => $favourite_receipe['last_page'], 'total_results' => $favourite_receipe['total'], 'recipes' => $receipedata];
        } else {
            $res = ['success' => false, 'message' => __('No recipes found'), 'current_page' => $favourite_receipe['current_page'], 'last_page' => $favourite_receipe['last_page'], 'total_results' => $favourite_receipe['total'], 'recipes' => []];
        }

        return response()->json($res);
    }

    function RawQuery(Request $request)
    {

        $data = $request->all();

        $validator = Validator::make($data, [
            // 'filter' => 'required',
            'limit' => 'numeric | nullable',
            'page' => 'numeric | nullable',
        ]);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => __($validator->messages()->first())
            ];
            return response()->json($res);
        }

        $limit = $request->limit ? $request->limit : 20;

        $user = User::where('id', \Auth::user()->id)->first();
        //dd($user['ingredients_donot_like']);

        $receipe = Receipe::query();
        $receipe = $receipe->with(['ingredients']);
        if (isset($data['filter']['dish_type'])) {
            $receipe = $receipe->whereIn('dish_type', $data['filter']['dish_type']);
        }

        if (isset($data['filter']['cuisine'])) {
            $receipe = $receipe->whereIn('cuisine_type', $data['filter']['cuisine']);
        }

        if (isset($data['filter']['time'])) {
            $time = TimeFilters::where('id', $data['filter']['time'])->first();
            $time_value = $time->time_filter_value;
            $time_condition = $time->time_filter_condition;

            if ($time_condition == 1) {
                $receipe = $receipe->where('cooking_time', '<', $time_value);
            } elseif ($time_condition == 2) {
                $receipe = $receipe->where('cooking_time', '>', $time_value);
            } else {
                $receipe = $receipe->where('cooking_time', '=', $time_value);
            }
        }
        $receipes = $receipe->whereHas('ingredients', function ($query) use ($user) {
            $query->whereNotIn('ingredient_id', explode(",", $user->ingredients_donot_like))->whereNotIn('ingredient_id', $user['allergies']);
        })->whereRaw(
            '(
                    select A.id,
                       CASE WHEN B.item_id IS NOT NULL
                       THEN 1
                       ELSE 0
                       END
                       as status

                    from items A
                    left join inventories B
                    on A.id = B.item_id order by status asc
                )'
        )


            ->paginate($limit);

        $receipes = $receipes->toArray();
        dd($receipes);
        $results =
            \DB::select(
                \DB::raw("
                    select A.id,
                       CASE WHEN B.item_id IS NOT NULL
                       THEN 1
                       ELSE 0
                       END
                       as status

                    from items A
                    left join inventories B
                    on A.id = B.item_id order by status asc
                  ")
            );

        // dd($results);

        foreach ($results as $result) {
            echo $result->status . "__________| " . $result->status . "<br />";
        }
    }
}

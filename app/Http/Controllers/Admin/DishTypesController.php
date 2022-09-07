<?php

namespace App\Http\Controllers\Admin;

use App\Models\DishTypes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Imports\DistTypesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DishTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dishTypes = DishTypes::orderBy('id', 'DESC')
            ->get()
            ->toArray();
        return view('admin.dishtypes.list', compact(['dishTypes']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.dishtypes.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();

        $params = $request->all();
        $validation = Validator::make($params, [
            'dish_type_name' => [
                'required',
                Rule::unique('dish_types')->where(function ($query) {
                    return $query->where('deleted_at', '=', null);
                }),
            ],
        ]);

        if ($validation->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        } else {
            $dishType = DishTypes::create($params);
            if ($dishType->id) {
                $dishTypes = DishTypes::orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return view('admin.dishtypes.list', compact(['dishTypes']));
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Something went wrong!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Models\DishTypes  $dishTypes
     * @return \Illuminate\Http\Response
     */
    public function show(DishTypes $dishTypes)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Models\DishTypes  $dishTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $category_id, DishTypes $dishTypes)
    {
        // $id = base64_decode($category_id);
        $dishType = $dishTypes::whereId($category_id)->first();
        // dd($dishType);
        return view('admin.dishtypes.edit', compact(['dishType']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\DishTypes  $dishTypes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $category_id, DishTypes $dishTypes)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();

        $params = $request->all();
        $validation = Validator::make($params, [
            'dish_type_name' => [
                'required',
                Rule::unique('dish_types')->where(function ($query) use ($category_id) {
                    return $query->where('id', '!=', $category_id)->where('deleted_at', '=', null);
                }),
            ],
        ]);

        if ($validation->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        } else {
            $dishType = DishTypes::whereId($category_id)->update(['dish_type_name' => $params['dish_type_name']]);
            if ($dishType) {
                $dishTypes = DishTypes::orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return view('admin.dishtypes.list', compact(['dishTypes']));
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Something went wrong!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Models\DishTypes  $dishTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DishTypes $dishTypes)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();

        $dishType = $dishTypes::whereId($id);
        try {
            $dishType = $dishTypes::whereId($id);
            $dishTypes = DishTypes::orderBy('id', 'DESC')
                ->get()
                ->toArray();

            if ($dishType->exists()) {
                $dishType = $dishType->delete();
                if ($dishType) {
                    return redirect()
                        ->route('listDishTypes')
                        ->with(['status' => 'true', 'message' => 'Category deleted successfully.', 'dishTypes' => $dishTypes]);
                } else {
                    return redirect()
                        ->route('listDishTypes')
                        ->with(['status' => 'false', 'message' => 'Something Went wrong.', 'dishTypes' => $dishTypes]);
                }
            } else {
                return redirect()
                    ->route('listDishTypes')
                    ->with(['status' => 'false', 'message' => 'Category not found.', 'dishTypes' => $dishTypes]);
            }
        } catch (Exception $e) {
            return redirect()
                ->route('listDishTypes')
                ->with(['status' => 'false', 'message' => 'Something Went wrong.', 'dishTypes' => $dishTypes]);
        }
    }

    /**
     * Import Dish Types from Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if ($request->hasFile('select_file')) {
            $extension = $request->file('select_file')->getClientOriginalExtension();

            if (!in_array($extension, ['xls', 'xlsx', 'csv'])) {
                $res = ['success' => false, 'message' => 'Only xls or csv files are  allowed'];
            }

            $path = $request->file('select_file')->getRealPath();
        } else {
            $res = ['success' => false, 'message' => 'Please select a file'];
        }

        try {
            $import = new DistTypesImport($request->all());
            Excel::import($import, request()->file('select_file'));
            $rows = $import->rows->toArray();
            $data = [];
            $count = 0;
            foreach ($rows as $row) {
                $validator = \Validator::make($row, [
                    'dish_type_name' => [
                        'required',
                        Rule::unique('dish_types')->where(function ($query) {
                            return $query->where('deleted_at', '=', null);
                        }),
                    ],
                ]);
                if (!$validator->fails()) {
                    if (!$this->myArrayContainsWord($data, $row['dish_type_name'])) {
                        array_push($data, ["dish_type_name" => $row['dish_type_name']]);
                        $count++;
                    }
                }
            }
            DishTypes::insert($data);
            $message = $count . ' Items imported';
            $res = ['status' => 'success', 'message' => $message];
        } catch (\Exception $e) {
            $res = ['status' => 'danger', 'message' => $e->getMessage()];
        }

        return redirect()
            ->back()
            ->with($res);
    }
    function myArrayContainsWord(array $myArray, $word)
    {
        if (count($myArray) > 0) {
            foreach ($myArray as $element) {
                if ($element['dish_type_name'] == $word) {
                    return true;
                }
            }
        }

        return false;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DietCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Imports\CommonImport;
use Maatwebsite\Excel\Facades\Excel;

class DietCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dietCategories = DietCategories::orderBy('id', 'DESC')
            ->get()
            ->toArray();
        return view('admin.dietcategories.list', compact(['dietCategories']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.dietcategories.add');
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
        // dd($user,1);

        $params = $request->all();
        $validation = Validator::make($params, [
            'diet_category_name' => [
                'required',
                Rule::unique('diet_categories')->where(function ($query) {
                    return $query->where('deleted_at', '=', null);
                }),
            ],
            'diet_category_description' => 'required',
        ]);

        if ($validation->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        } else {
            $dietCategory = DietCategories::create($params);
            if ($dietCategory->id) {
                $dietCategories = DietCategories::orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return view('admin.dietcategories.list', compact(['dietCategories']));
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Somthing went wrong!');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Models\DietCategories  $dietCategories
     * @return \Illuminate\Http\Response
     */
    public function show(DietCategories $dietCategories)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Models\DietCategories  $dietCategories
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $category_id, DietCategories $dietCategories)
    {
        // $id = base64_decode($category_id);
        $dietCategory = $dietCategories::whereId($category_id)->first();
        // dd($dietCategory);
        return view('admin.dietcategories.edit', compact(['dietCategory']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\DietCategories  $dietCategories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $category_id, DietCategories $dietCategories)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();

        $params = $request->all();
        $validation = Validator::make($params, [
            'diet_category_name' => [
                'required',
                Rule::unique('diet_categories')->where(function ($query) use ($category_id) {
                    return $query->where('id', '!=', $category_id)->where('deleted_at', '=', null);
                }),
            ],
            'diet_category_description' => 'required',
        ]);

        // dd($category_id);
        if ($validation->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        } else {
            $dietCategory = DietCategories::whereId($category_id)->update(['diet_category_name' => $params['diet_category_name'], 'diet_category_description' => $params['diet_category_description']]);
            if ($dietCategory) {
                $dietCategories = DietCategories::orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return view('admin.dietcategories.list', compact(['dietCategories']));
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
     * @param  \App\Models\Models\DietCategories  $dietCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DietCategories $dietCategories)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();

        $dietCategory = $dietCategories::whereId($id);
        try {
            $dietCategory = $dietCategories::whereId($id);
            $dietCategories = DietCategories::orderBy('id', 'DESC')
                ->get()
                ->toArray();

            if ($dietCategory->exists()) {
                $dietCategory = $dietCategory->delete();
                if ($dietCategory) {
                    return redirect()
                        ->route('listDietCategories')
                        ->with(['status' => 'true', 'message' => 'Category deleted successfully.', 'dietCategories' => $dietCategories]);
                } else {
                    return redirect()
                        ->route('listDietCategories')
                        ->with(['status' => 'false', 'message' => 'Something Went wrong.', 'dietCategories' => $dietCategories]);
                }
            } else {
                return redirect()
                    ->route('listDietCategories')
                    ->with(['status' => 'false', 'message' => 'Category not found.', 'dietCategories' => $dietCategories]);
            }
        } catch (Exception $e) {
            return redirect()
                ->route('listDietCategories')
                ->with(['status' => 'false', 'message' => 'Something Went wrong.', 'dietCategories' => $dietCategories]);
        }
    }

    /**
     * Import Cuisine Types from Excel file.
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
            $import = new CommonImport($request->all());
            Excel::import($import, request()->file('select_file'));
            $rows = $import->rows->toArray();
            $data = [];
            $count = 0;
            foreach ($rows as $row) {
                $validator = \Validator::make($row, [
                    'diet_category_name' => [
                        'required',
                        Rule::unique('diet_categories')->where(function ($query) {
                            return $query->where('deleted_at', '=', null);
                        }),
                    ],
                    'diet_category_description' => 'required',
                ]);
                if (!$validator->fails()) {
                    if (!$this->myArrayContainsWord($data, $row['diet_category_name'])) {
                        array_push($data, ["diet_category_name" => $row['diet_category_name'], "diet_category_description" => $row['diet_category_description']]);
                        $count++;
                    }
                }
            }
            DietCategories::insert($data);
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
                if ($element['diet_category_name'] == $word) {
                    return true;
                }
            }
        }

        return false;
    }
}

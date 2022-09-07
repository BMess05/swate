<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuisineTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Imports\CommonImport;
use Maatwebsite\Excel\Facades\Excel;

class CuisineTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cuisineTypes = CuisineTypes::orderBy('id', 'DESC')
            ->get()
            ->toArray();
        return view('admin.cuisinetypes.list', compact(['cuisineTypes']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cuisinetypes.add');
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
            'cuisine_type_name' => [
                'required',
                Rule::unique('cuisine_types')->where(function ($query) {
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
            $cuisineType = CuisineTypes::create($params);
            if ($cuisineType->id) {
                $cuisineTypes = CuisineTypes::orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return view('admin.cuisinetypes.list', compact(['cuisineTypes']));
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
     * @param  \App\Models\Models\CuisineTypes  $cuisineTypes
     * @return \Illuminate\Http\Response
     */
    public function show(CuisineTypes $cuisineTypes)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Models\CuisineTypes  $cuisineTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $category_id, CuisineTypes $cuisineTypes)
    {
        // $id = base64_decode($category_id);
        $cuisineType = $cuisineTypes::whereId($category_id)->first();
        // dd($cuisineType);
        return view('admin.cuisinetypes.edit', compact(['cuisineType']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\CuisineTypes  $cuisineTypes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $category_id, CuisineTypes $cuisineTypes)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();

        $params = $request->all();
        $validation = Validator::make($params, [
            'cuisine_type_name' => [
                'required',
                Rule::unique('cuisine_types')->where(function ($query) use ($category_id) {
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
            $cuisineType = CuisineTypes::whereId($category_id)->update(['cuisine_type_name' => $params['cuisine_type_name']]);
            if ($cuisineType) {
                $cuisineTypes = CuisineTypes::orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return view('admin.cuisinetypes.list', compact(['cuisineTypes']));
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
     * @param  \App\Models\Models\CuisineTypes  $cuisineTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, CuisineTypes $cuisineTypes)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();

        $cuisineType = $cuisineTypes::whereId($id);
        try {
            $cuisineType = $cuisineTypes::whereId($id);
            $cuisineTypes = CuisineTypes::orderBy('id', 'DESC')
                ->get()
                ->toArray();

            if ($cuisineType->exists()) {
                $cuisineType = $cuisineType->delete();
                if ($cuisineType) {
                    return redirect()
                        ->route('listCuisineTypes')
                        ->with(['status' => 'true', 'message' => 'Category deleted successfully.', 'cuisineTypes' => $cuisineTypes]);
                } else {
                    return redirect()
                        ->route('listCuisineTypes')
                        ->with(['status' => 'false', 'message' => 'Something Went wrong.', 'cuisineTypes' => $cuisineTypes]);
                }
            } else {
                return redirect()
                    ->route('listCuisineTypes')
                    ->with(['status' => 'false', 'message' => 'Category not found.', 'cuisineTypes' => $cuisineTypes]);
            }
        } catch (Exception $e) {
            return redirect()
                ->route('listCuisineTypes')
                ->with(['status' => 'false', 'message' => 'Something Went wrong.', 'cuisineTypes' => $cuisineTypes]);
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
                    'cuisine_type_name' => [
                        'required',
                        Rule::unique('cuisine_types')->where(function ($query) {
                            return $query->where('deleted_at', '=', null);
                        }),
                    ],
                ]);
                if (!$validator->fails()) {
                    if (!$this->myArrayContainsWord($data, $row['cuisine_type_name'])) {
                        array_push($data, ["cuisine_type_name" => $row['cuisine_type_name']]);
                        $count++;
                    }
                }
            }
            CuisineTypes::insert($data);
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
                if ($element['cuisine_type_name'] == $word) {
                    return true;
                }
            }
        }

        return false;
    }
}

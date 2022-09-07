<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Unit;

class UnitController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::orderBy('id', 'DESC')->get();
        return view('admin.units.list', compact(['units']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.units.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $params = $request->all();
        $validatedData = $request->validate([
             'unit' => "required|unique:units"
        ]);

        $unit = Unit::create($params);
        if( $unit->id ){
             return redirect()->route('listUnits')->with(['status' => 'success', 'message' => 'Unit Saved Successfully']);
        }else{
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();  
        }          
        
    }

   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Models\units  $units
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id )
    {
        $unit = Unit::whereId($id)->first();
        return view('admin.units.edit', compact(['unit']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\units  $units
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
        $params = $request->all();
        $validation = Validator::make($params,[
            'unit' => ['required', Rule::unique('units')->ignore($id)],

        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        }else{
            
            $unit = Unit::findOrFail($id);
            if($unit->fill($params)->save()){

                 return redirect()->route('listUnits')->with(['status' => 'success', 'message' => 'Unit Update Successfully']);
            }else{
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();  
            }       
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Models\units  $units
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Unit $unit)
    {
        $unit = Unit::findOrFail($id);
        if($unit->delete())
        { 
        	return redirect()->route('listUnits')->with(['status' => 'true', 'message' => 'Unit deleted successfully.']);
        }else{
        	return redirect()->route('listunits')->with(['status' => 'false', 'message' => 'Something Went wrong.']);
        }
       
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TimeFiltersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timeFilters = TimeFilters::orderBy('id', 'DESC')->get()->toArray();
        return view('admin.timefilters.list', compact(['timeFilters']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.timefilters.add');
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
        $validation = Validator::make($params,[
            'time_filter_name' => ['required',Rule::unique('time_filters')->where(function ($query) {
                                                return $query->where('deleted_at', '=', null);
                                            })
                                        ],
            'time_filter_condition' => "required",
            'time_filter_value' => "required",
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        }else{
            
            $timeFilter = TimeFilters::create($params);
            if( $timeFilter->id ){
                $timeFilters = TimeFilters::orderBy('id', 'DESC')->get()->toArray();
                return view('admin.timefilters.list',compact(['timeFilters']));
            }else{
                return redirect()->back()->withInput()->with('error','Something went wrong!');   
            }          
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Models\TimeFilters  $timeFilters
     * @return \Illuminate\Http\Response
     */
    public function show( TimeFilters $timeFilters )
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Models\TimeFilters  $timeFilters
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $category_id, TimeFilters $timeFilters )
    {
        // $id = base64_decode($category_id);
        $timeFilter = $timeFilters::whereId($category_id)->first();
        // dd($timeFilter);
        return view('admin.timefilters.edit', compact(['timeFilter']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\TimeFilters  $timeFilters
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $category_id,  TimeFilters $timeFilters)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();

        $params = $request->all();
        $validation = Validator::make($params,[
            'time_filter_name' => ['required',Rule::unique('time_filters')->where(function ($query) use($category_id){
                                                return $query->where('id','!=',$category_id)->where('deleted_at', '=', null);
                                            })
                                        ],
            'time_filter_condition' => "required",
            'time_filter_value' => "required",
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->with(['status' => 'false', 'message' => $validation->messages()->first()]);
        }else{
            
            $timeFilter = TimeFilters::whereId($category_id)->update(['time_filter_name'=>$params['time_filter_name'],'time_filter_value'=>$params['time_filter_value'],'time_filter_condition'=>$params['time_filter_condition' ]]  ) ;
            if( $timeFilter ){
                $timeFilters = TimeFilters::orderBy('id', 'DESC')->get()->toArray();
                return view('admin.timefilters.list',compact(['timeFilters']));
            }else{
                return redirect()->back()->withInput()->with('error','Something went wrong!');
            }          
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Models\TimeFilters  $timeFilters
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, TimeFilters $timeFilters)
    {
        $user = \Auth::user();
        $is_admin = \Auth::guard('web')->check();
        
        $timeFilter = $timeFilters::whereId($id);
        try{
             
            $timeFilter = $timeFilters::whereId($id);
            $timeFilters = TimeFilters::orderBy('id', 'DESC')->get()->toArray();

            if( $timeFilter->exists() ){
                $timeFilter = $timeFilter->delete();
                if($timeFilter){
                    return redirect()->route('listTimeFilters')->with(['status' => 'true', 'message' => 'Category deleted successfully.','timeFilters'=>$timeFilters]);
                }else{
                    return redirect()->route('listTimeFilters')->with(['status' => 'false', 'message' => 'Something Went wrong.','timeFilters'=>$timeFilters]);
                }
            }
            else{                
                return redirect()->route('listTimeFilters')->with(['status' => 'false', 'message' => 'Category not found.','timeFilters'=>$timeFilters]);
            }
        }catch(Exception $e){
            return redirect()->route('listTimeFilters')->with(['status' => 'false', 'message' => 'Something Went wrong.','timeFilters'=>$timeFilters]);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tags;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TagsImport;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tags::orderBy('id', 'DESC')->get()->toArray();
        return view('admin.tags.list', compact(['tags']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tags.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:tags,name'
        ]);
        
        $data = $request->all();
        
        $tag = new Tags();
        $tag->name = $data['name'];
        $tag->status = 1;
        if($tag->save()) {
            return redirect()->route('listTags')->with(['status' => 'success', 'message' => 'Tag Saved Successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
        }
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid tag id']);
        }
        $tag = Tags::find($id);
        if(!$tag) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid tag id']);
        }
        return view('admin.tags.edit', compact(['tag']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Tag not found']);
        }
        $validatedData = $request->validate([
            'name' => 'required|unique:tags,name,'.$id
        ]);
        
        $tag = Tags::find($id);
        if(!$tag) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid tag id']);
        }
        $data = $request->all();
        $tag->name = $data['name']; 
        if($tag->save()) {
            return redirect()->route('listTags')->with(['status' => 'success', 'message' => 'Tag updated successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a tag.']);
        }
        $tag = Tags::find($id);
        if(!$tag) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid tag id']);
        }
        if(Tags::where('id', $id)->delete()) {
            return redirect()->route('listTags')->with(['status' => 'success', 'message' => 'Tag deleted successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.']);
        }
    }

    /**
     * Import Tags from Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if ($request->hasFile('select_file')) {
            $extension = $request->file('select_file')->getClientOriginalExtension();
          
            if(!in_array($extension, ['xls', 'xlsx','csv'])) {
                $res = ['success' => false, 'message' =>'Only xls or csv files are  allowed'];
            }

            $path = $request->file('select_file')->getRealPath();
        }else{
            $res = ['success' => false, 'message' =>'Please select a file'];
        }

        try {
            $import = new TagsImport($request->all());
            Excel::import($import, request()->file('select_file'));
            $rows=$import->rows->toArray();
            $data = [];
            $count = 0;
            foreach ($rows as $row) {
                $validator = \Validator::make($row, [
                    'tag_name' => 'required|unique:tags,name',
                ]);
                if(!$validator->fails()) {
                    if (!$this->myArrayContainsWord($data, $row['tag_name'])) {
                        array_push($data, ["name"=>$row['tag_name'], "status"=>1]); 
                        $count++;  
                    }
                }
            }
            Tags::insert($data);
            $message = $count.' Items import successfully';
            $res = ['status' =>'success', 'message' =>$message];
        } catch (\Exception $e) {
            $res = ['status' =>'danger', 'message' =>$e->getMessage()];
        }
     
        return redirect()->back()->with($res);
    }
    function myArrayContainsWord(array $myArray, $word) {
        if(count($myArray) > 0){
            foreach ($myArray as $element) {
                if ($element['name'] == $word) {
                    return true;
                }
            }
        }
        
        return false;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $tags = [];
        $tagQuery = Tags::query();
        if(isset($request->search) != '' ){
            $tagQuery->where('name','LIKE',"%{$request->search}%");
        }
        $alltags = $tagQuery->select('id','name as text','name as slug')->orderBy('id', 'DESC')->get();
        if($alltags){
            $tags = $alltags->toArray();
        }
        return $tags;
    }
}

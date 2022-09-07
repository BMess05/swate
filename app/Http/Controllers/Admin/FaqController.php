<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faqs;
class FaqController extends Controller
{
    function index() {
        $questions = Faqs::orderBy('id','DESC')->get();
        //dd($questions);
        return view('admin.faq.list', compact(['questions']));
    } 

    function create() {
        return view('admin.faq.add');
    }

    function store(Request $request) { 
        $validatedData = $request->validate([
            'question' => 'required',
            'answer' => 'required'
        ]);
        
        $data = $request->all();
        
        $faq = new Faqs();
        $faq->question = $data['question'];
        $faq->answer = $data['answer'];
        if($faq->save()) {
            return redirect()->route('listFaqs')->with(['status' => 'success', 'message' => 'Faq Saved Successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
        }
    
    }

    function edit($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid faq id']);
        }
        $faq = Faqs::where('id', $id)->first();
        if(!$faq) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid id']);
        }
        return view('admin.faq.edit', compact(['faq']));
    }

    function update($id = null, Request $request) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a question.']);
        }
        $validatedData = $request->validate([
            'question' => 'required',
            'answer' => 'required'
            //'answer' => 'required|unique:faqs,answer,'.$id
        ]);
        
        $faq = Faqs::find($id);
        if(!$faq) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid faq id']);
        }
        $data = $request->all();
        $faq->question = $data['question']; 
        $faq->answer = $data['answer']; 
        if($faq->save()) {
            return redirect()->route('listFaqs')->with(['status' => 'success', 'message' => 'Faq updated successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
         }
    }

    function destroy($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a category.']);
        }
        $category = Faqs::find($id);
        if(!$category) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid faq id']);
        }
        if(Faqs::where('id', $id)->delete()) {
            return redirect()->route('listFaqs')->with(['status' => 'success', 'message' => 'Faq deleted successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.']);
        }
    }
}

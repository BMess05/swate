<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    function listCategories() {
        $categories = Category::orderBy('id', 'DESC')->get()->toArray();
        return view('admin.categories.list', compact(['categories']));
    } 

    function addCategory() {
        return view('admin.categories.add');
    }

    function saveCategory(Request $request) { 
        $validatedData = $request->validate([
            'category_name' => 'required|unique:categories,category_name'
        ]);
        
        $data = $request->all();
        
        $category = new Category();
        $category->category_name = $data['category_name'];
        if($category->save()) {
            return redirect()->route('listCategories')->with(['status' => 'success', 'message' => 'Category Saved Successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
        }
    
    }

    function editCategory($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid category id']);
        }
        $category = Category::where('id', $id)->first();
        if(!$category) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid category id']);
        }
        return view('admin.categories.edit', compact(['category']));
    }

    function updateCategory($id = null, Request $request) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a user.']);
        }
        $validatedData = $request->validate([
            'category_name' => 'required|unique:categories,category_name,'.$id
        ]);
        
        $category = Category::find($id);
        if(!$category) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid category id']);
        }
        $data = $request->all();
        $category->category_name = $data['category_name']; 
        if($category->save()) {
            return redirect()->route('listCategories')->with(['status' => 'success', 'message' => 'Category updated successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
        }
    }

    function deleteCategory($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a category.']);
        }
        $category = Category::find($id);
        if(!$category) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid category id']);
        }
        if(Category::where('id', $id)->delete()) {
            return redirect()->route('listCategories')->with(['status' => 'success', 'message' => 'Category deleted successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.']);
        }
    }
}

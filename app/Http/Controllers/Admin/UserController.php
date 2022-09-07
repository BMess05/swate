<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ReportReason;
use App\Models\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
class UserController extends Controller
{
    function listUsers($filter = null) {
        $users = User::where(['type' => 1])->orderBy('id', 'DESC')->get();
        return view('admin.users.list', compact(['users', 'filter']));
    } 

    function addUser() {
        return view('admin.users.add');
    }

    function saveUser(Request $request) { 
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        $data = $request->all();
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Email Address is invalid'])->withInput();
        }
        // echo "<pre>"; print_r($data); exit;
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->type = 1;
        if($user->save()) {
            return redirect('users')->with(['status' => 'success', 'message' => 'User Saved Successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
        }
    
    }

    function editUser($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid user id']);
        }
        $user = User::where('id', $id)->first();
        if(!$user) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid user id']);
        }
        return view('admin.users.edit', compact(['user']));
    }

    function updateUser($id = null, Request $request) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a user.']);
        }
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'sometimes|confirmed'
        ]);
        
        $data = $request->all();
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Email Address is invalid'])->withInput();
        }
        $user = User::find($id);
        if(!$user) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid user id']);
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        if($data['password'] != "") {
            $user->password = bcrypt($data['password']);
        }

        if($user->save()) {
            return redirect()->route('users')->with(['status' => 'success', 'message' => 'User updated successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.'])->withInput();
        }
    }

    function deleteUser($id = null) {
        if($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a user.']);
        }
        
        if(User::where('id', $id)->delete()) {
            return redirect()->back()->with(['status' => 'success', 'message' => 'User deleted successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something Went wrong.']);
        }
    }

    function userProfile($id = null) {
        if($id == null) { 
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid user id']);
        }
        $user = User::where('id', $id)->first();
        // $logs = Log::where('user_id', $id)->orderBy('id', 'DESC')->get();
        if(!$user) { 
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid user id']);
        }
        // echo "<pre>"; print_r($user->toArray()); exit;
        return view('admin.users.profile', compact(['user']));
    }


    function exportUsers($filter = null) {
        return Excel::download(new UsersExport($filter), 'users_'.time().'.csv');
    }
}

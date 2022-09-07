<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receipe;
use App\Models\Tags;
class IndependentController extends Controller
{
    public function index(Request $request)
    {
        $receipe =  Receipe::pluck('tags')->toArray();
        $tags = [];
        $data = [];
        foreach ($receipe as $value) {
            $tags = array_merge($tags,explode(', ',$value));
        }
        $uniquetags = array_unique($tags);
        foreach ($uniquetags as $key => $tag) {
            $validator = \Validator::make(["tag_name"=>$tag], [
                'tag_name' => 'required|unique:tags,name',
            ]);
            if(!$validator->fails() && $tag != '') {
                $data[] = ["name"=>$tag];
            }
        }
        Tags::insert($data);
    }
}
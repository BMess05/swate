<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,WithHeadings
{
     /**
    * @return \Illuminate\Support\Collection
    */
    protected $filter;

    function __construct($filter) {
        $this->filter = $filter;
    }
    public function headings(): array {
        return [
            "First Name",
            "Last Name",
            "Email",
            "Goals",
            "Diet",
            "Cooking level",
            "Allergies",
            "People count"
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
    */
    public function collection() {
       
        $users = User::select('name', 'last_name','email', 'goals', 'diet', 'cooking_level', 'allergies', 'people_count')->where('type','!=',0)->orderBy('id', 'DESC')->get();

        // dd($users->toArray());

        foreach($users as $k => $user) {
            // if($user->type == 0) {
            //     $users[$k]->type = "Admin";
            // }else{
            //     $users[$k]->type = "User";
            // }

            if($user->goals == 1) {
                $users[$k]->goals = "Save money";
            }   elseif($user->goals == 2) {
                $users[$k]->goals = "Save the planet";
            }   elseif($user->goals == 3) {
                $users[$k]->goals = "Learn to cook";
            }   else {
                $users[$k]->goals = "Discover new recipes";
            }

            if($user->diet == 1) {
                $users[$k]->diet = "Classic";
            }   elseif($user->diet == 2) {
                $users[$k]->diet = "Flexitarian";
            }   elseif($user->diet == 3) {
                $users[$k]->diet = "Pescetarian";
            }   elseif($user->diet == 4) {
                $users[$k]->diet = "Vegan";
            }   else {
                $users[$k]->diet = "Paleo";
            }

            if($user->cooking_level == 1) {
                $users[$k]->cooking_level = "Beginner";
            }   elseif($user->cooking_level == 2) {
                $users[$k]->cooking_level = "Intermediate";
            }   else {
                $users[$k]->cooking_level = "Advanced";
            }

            if($user->allergies == 1) {
                $users[$k]->allergies = "None";
            } else{
                $users[$k]->allergies = "None";
            }

            if($user->people_count > 0 ) {
                $users[$k]->people_count = $user->people_count;
            } else{
                $users[$k]->people_count ='0';
            }
        }       

       
        return collect($users);
    }
}

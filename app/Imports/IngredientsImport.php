<?php

namespace App\Imports;

use App\Models\Items;
use App\Models\ItemsExpireDay;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class IngredientsImport  implements ToCollection,WithHeadingRow
{

    protected $expenses;

    function __construct($expenses) {
        $this->expenses = $expenses;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public $rows;
    public function collection(Collection $rows)
    {
        
     try {
          $this->rows = $rows;
        } catch (\Exception $e) {
            
          return redirect()->back()->with('error' , $e->getMessage());
        }
    }
}

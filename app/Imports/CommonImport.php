<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CommonImport  implements ToCollection,WithHeadingRow
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

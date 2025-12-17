<?php

namespace App\Http\Controllers;
use App\Models\Branchcode;
use App\Exports\DepartmentExport;
use App\Exports\BranchExport;
use App\Exports\BudgetCodeExport;
use App\Exports\GlExport;
use App\Exports\TaxExport;
use App\Exports\SupplierExport;
use App\Exports\GroupExport;
use App\Exports\GroupSpesialExport;
use App\Exports\ProductExport;
use App\Exports\SegmentExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function departmentExport(Request $request){
        
        return Excel::download(new DepartmentExport, 'Department.xlsx');
    }
    public function branchExport(){
        return Excel::download(new BranchExport, 'Branch.xlsx');
    }
    public function budgetCodeExport(){
        return Excel::download(new BudgetCodeExport, 'BudgetCode.xlsx');
    }
    public function budgetGl(){
        return Excel::download(new GlExport, 'GL code.xlsx');
    }
    public function budgetTax (){
        return Excel::download(new TaxExport, 'Tax code.xlsx');
    }
    public function budgetSupplier(){
        return Excel::download(new SupplierExport, 'Supplier.xlsx');
    }
    public function budgetGroup(){
        return Excel::download(new GroupExport, 'Group User.xlsx');
    }
    public function budgetSpecial(){
        return Excel::download(new GroupSpesialExport, 'Special Group.xlsx');
    }
    public function budgetProduct(){
        return Excel::download(new ProductExport, 'Product Code.xlsx');
    }
    public function budgetSegment(){
        return Excel::download(new SegmentExport, 'Segment Code.xlsx');
    }
}

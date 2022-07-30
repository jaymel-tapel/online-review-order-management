<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    
    public function search($keyword)
    {
        $filterData = Company::where('name','LIKE','%'.$keyword.'%')->orderByDesc('updated_at')
        ->get();
        return $filterData;
    }
}

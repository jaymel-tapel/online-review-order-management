<?php

namespace App\Http\Controllers;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function search($keyword)
    {
        $filterData = Seller::where('email','LIKE','%'.$keyword.'%')
                      ->get();
        return $filterData;
    }
}

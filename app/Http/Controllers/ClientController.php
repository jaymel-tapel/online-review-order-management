<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function search($keyword)
    {
        $filterData = Client::where('email','LIKE','%'.$keyword.'%')
                      ->get();
        return $filterData;
    }
}

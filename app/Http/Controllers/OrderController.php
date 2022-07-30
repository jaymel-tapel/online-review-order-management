<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Company;
use App\Models\Client;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $filterBy = $request->query('filter');
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');

        if($date_from == null) {
            $date_from = (new \DateTime())->modify('-1 day');
        } else {
            $date_from = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $date_from);
        }

        if($date_to == null) {
            $date_to = new \DateTime();
        } else {
            $date_to = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $date_to);
        }

        switch($filterBy) {
            case 'order_id':
            return Order::whereBetween(DB::raw('DATE(created_at)'), array($date_from, $date_to))
                ->where('id','LIKE','%'.$keyword.'%')->with('company','client','seller')->orderByDesc('updated_at')->get();
            break;

            case 'company':
            return Order::whereBetween(DB::raw('DATE(created_at)'), array($date_from, $date_to))
                ->whereHas('company', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%'.$keyword.'%');
                })->with('company','client','seller')->orderByDesc('updated_at')->get();
            break;

            case 'order_status':
            return Order::whereBetween(DB::raw('DATE(created_at)'), array($date_from, $date_to))
                ->where('order_status','=', $keyword )->with('company','client','seller')->orderByDesc('updated_at')->get();
            break;

            case 'payment_status':
                return Order::whereBetween(DB::raw('DATE(created_at)'), array($date_from, $date_to))
                ->where('order_status','=', $keyword )->with('company','client','seller')->orderByDesc('updated_at')->get();
            break;

            default: 
            return  Order::whereBetween(DB::raw('DATE(created_at)'), array($date_from, $date_to)
                )->with('company','client','seller')->orderByDesc('updated_at')->get();
        }
    }
    
    public function create(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $client = Client::where('email','=', $request->client_email)->first();
            if ($client == null) {
                $client = new Client;
                $client->name = strtoupper($request->client_name);
                $client->email = strtoupper($request->client_email);
                $client->save();
            }

            $seller = Seller::where('email','=', $request->seller_email)->first();
            if ($seller == null) {
                $seller = new Seller;
                $seller->name = strtoupper($request->seller_name);
                $seller->email = strtoupper($request->seller_email);
                $seller->save();
            }

            $company = Company::where('name','=', $request->company_name)->first();
            if ($company == null) {
                $company = new Company;
                $company->name = strtoupper($request->company_name);
                $company->url = strtoupper($request->company_url);
                $company->save();
            }

            $newOrder = new Order;
            $newOrder->client_id =$client->id;
            $newOrder->seller_id =$seller->id;
            $newOrder->company_id =$company->id;
            $newOrder->number_of_reviews = $request->number_of_reviews;
            $newOrder->unit_cost = $request->unit_cost;
            $newOrder->total_price = $request->number_of_reviews * $request->unit_cost;
            $newOrder->reviewers = $request->reviewers;
            $newOrder->remarks = $request->remarks;
            $newOrder->order_status = 0;
            $newOrder->payment_status = 0;
            
            if($request->order_date != null) {
                $newOrder->created_at = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z',$request->order_date);
            }
            
            $newOrder->save();
            DB::commit();
            return $newOrder;
        });
    }

    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $updatedOrder = Order::find($id);
            if($updatedOrder == null) {
                return response()->json(['message' => 'Not Found!'], 404);
            }

            $client = Client::where('email','=', $request->client_email)->first();
            if ($client == null) {
                $client = new Client;
                $client->name = strtoupper($request->client_name);
                $client->email = strtoupper($request->client_email);
                $client->save();
            } else {
                Client::where('email', $request->client_email)->update([
                    'name' =>  strtoupper($request->client_name)
                ]);
            }

            $seller = Seller::where('email','=', $request->seller_email)->first();
            if ($seller == null) {
                $seller = new Seller;
                $seller->name = strtoupper($request->seller_name);
                $seller->email = strtoupper($request->seller_email);
                $seller->save();
            } else {
                Seller::where('email', $request->seller_email)->update([
                    'name' =>  strtoupper($request->seller_name)
                ]);
            }

            $company = Company::where('name','=', $request->company_name)->first();
            if ($company == null) {
                $company = new Company;
                $company->name = strtoupper($request->company_name);
                $company->url = strtoupper($request->company_url);
                $company->save();
            } else {
                Company::where('name', '=', $request->company_name)->update([
                    'created_at' =>  \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z',$request->order_date)
                ]);
            }
            
            
            Order::where('id', $id)->update([
            'client_id' => $client->id,
            'seller_id' => $seller->id,
            'company_id' => $company->id,
            'number_of_reviews' =>  $request->number_of_reviews,
            'unit_cost' =>  $request->unit_cost,
            'total_price' =>  $request->number_of_reviews * $request->unit_cost,
            'reviewers' =>  $request->reviewers,
            'remarks' =>  $request->remarks,
            'order_status' =>  $request->order_status,
            'payment_status' =>  $request->payment_status,
            'payment_status' =>  $request->payment_status,
            ]);

            
            if($request->order_date != null) {
                Order::where('id', $id)->update([
                'created_at' =>  \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z',$request->order_date)
                ]);
            }

            return true;
        });
    }

    public function update_order_status(Request $request, $id)
    {
        Order::where('id', $id)->update([
        'order_status' =>   $request->order_status
        ]);
        
        return true;
    }


    public function update_payment_status(Request $request, $id)
    {
        Order::where('id', $id)->update([
        'payment_status' =>   $request->payment_status
        ]);
        
        return true;
    }


    public function delete(Request $request, $id)
    {
        $Order = Order::findOrFail($id);
        $Order->delete();
        return 204;
    }
}

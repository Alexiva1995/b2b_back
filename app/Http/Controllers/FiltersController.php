<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class FiltersController extends Controller
{
    public function filtersOrderAdmin(request $request)
    {
        $filter = $request->dataFilter;

        $query = Order::with('user');

        if(is_numeric($filter)){
            $query->where('id', $filter);
        }
        if(!is_numeric($filter)){
            $query->whereHas('user', function ($q) use ($filter) {
                $q->whereRaw("CONCAT(`name`,' ',`last_name`) LIKE ?",['%'.$filter.'%']);
            });
        }

        /* $query->when(isset($filter['user_id']), function ($q) use ($filter) {
            $q->where('user_id', $filter['user_id']);
        });

        $query->when(isset($filter['name']), function ($q) use ($filter) {
            $q->whereHas('user', function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter['name'] . '%');
            });
        }); */
        $orders = $query->get();
        $data = array();
        foreach ($orders as $order) {
           
            $object = [
                'id' => $order->id,
                'user_id' => $order->user->id,
                'user_username' => $order->user->user_name,
                'user_email' => $order->user->email,
                'program' => $order->packagesB2B->product_name,
               // 'phase' => $phase ?? "",
               // 'account' => $order->packageMembership->account,
                'status' => $order->status,
                'hash_id' => $order->hash, // Hash::make($order->id)
                'amount' => $order->amount,
                'sponsor_id' => $order->user->sponsor->id,
                'sponsor_username' => $order->user->sponsor->user_name,
                'sponsor_email' => $order->user->sponsor->email,
                'hashLink' => $order->coinpaymentTransaction->checkout_url ?? "",
                'date' => $order->created_at->format('Y-m-d')
            ];
            array_push($data, $object);
        }


        return response()->json(['status' => 'success', 'data' => $data, 200]);
    }

    public function filtersProductAdmin(request $request)
    {
        $filter = $request->dataToProduct;

        $query = Product::query();

        foreach ([
            'name',
            'user_id',
            'country',
            'document_id',
            'postal_code',
            'phone_number',
            'status',
            'state',
            'street',
            'department',
            'created_at',
            'updated_at',
        ] as $field) {
            $value = $filter[$field] ?? null;

            if ($value !== null) {
                if (in_array($field, ['name', 'street', 'department'])) {
                    $query->where($field, 'like', '%' . $value . '%');
                } elseif (in_array($field, ['created_at', 'updated_at'])) {
                    $query->whereDate($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        $data = $query->get();

        return response()->json($data, 200);
    }
}

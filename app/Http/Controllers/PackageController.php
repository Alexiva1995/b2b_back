<?php

namespace App\Http\Controllers;

use App\Models\Invesment;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use App\Services\CoinpaymentsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class PackageController extends Controller
{
    protected $CoinpaymentsService;

    public function __construct(CoinpaymentsService $CoinpaymentsService)
    {
        $this->CoinpaymentsService = $CoinpaymentsService;
    }

    public function getPackages()
    {
        $packages = Package::all();
        $user = JWTAuth::parseToken()->authenticate();

        foreach ($packages as $package) {
            $package->investment = Invesment::where([['user_id', $user->id], ['package_id', $package->id], ['status', '<=', 1]])->first();
            $serie = 0;
            $package->amountP = number_format(0, 2, ',', '.');
            $current = number_format(0, 2, ',', '.');
            $max_gain = number_format(0, 2, ',', '.');
            if (!is_null($package->investment)) {
                $serie = number_format(($package->investment->gain * 100) / $package->investment->max_gain, 2);
                $current = number_format($package->investment->gain, 2, ',', '.');
                $max_gain = number_format($package->investment->max_gain, 2, ',', '.');
                $package->amountP = number_format($package->investment->invested, 2, ',', '.');
                $package->investment->time = Carbon::parse($package->investment->created_at)->diffInDays($package->investment->expiration_date);
            }
            $package->series = [$serie];
            $package->chartOptions = [
                'chart' => [
                    'height' => "10em",
                    'type' => "radialBar",
                    'foreColor' => '#FFF',
                ],
                'plotOptions' => [
                    'radialBar' => [
                        'hollow' => [
                            'size' => "75%",
                        ]
                    ]
                ],
                'labels' => ["$current/$max_gain"],
            ];
        };
        return $packages;
    }

    public function purchasedInvestment(Request $request)
    {
        //return $request->all();
        DB::beginTransaction();
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $package = Package::find($request->packageId);
            $date = Carbon::now();

            $order = new Order();
            $order->user_id = $user->id;
            $order->amount = $request->amount;
            $order->status = '0';
            $order->type = 'inicio';
            $order->package_id = $request->packageId;
            $order->save();

            $investment = new Invesment();
            $investment->user_id = $user->id;
            $investment->package_id = $request->packageId;
            $investment->order_id = $order->id;
            $investment->capital = $request->amount;
            $investment->invested = $request->amount;
            $investment->expiration_date = $date->addMonths($package->investment_time)->format('Y-m-d');
            $investment->max_gain = ($request->amount * ($package->gain / 100)) + $request->amount;
            $investment->status = 0;
            $investment->gain = 0;
            $investment->save();

            $response = $this->CoinpaymentsService->create_transaction($request->amount, $package, $request, $order, $user);
            if ($response['status'] == 'error') {
                Log::error($response);
                throw new Exception("Error processing purchase", 400);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error al comprar -' . $th->getMessage());
            Log::error($th);
            return response()->json($th->getMessage(), 400);
        }
    }

    public function checkOrder(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $order = $user->orders()->where([['status', '0']])->first();
        if (is_null($order)) {
            return response()->json(['data' => null], 200);
        }
        $datapayment = $order->coinpaymentTransaction;
        return response()->json(['data' => $datapayment], 200);
    }

    public function getActiveInvestments(Request $request)
    {
        $filter = $request->get('name');

        $query = Invesment::where('status', 1)->with('user');

        if(!is_null($filter)){
            $query->whereHas('user', function ($q) use ($filter) {
                $q->WhereRaw("email LIKE ?", ['%'.$filter.'%']);
            });
        }
        $investors = User::whereHas('investments', function (Builder $query) {
            $query->where('status', 1);
        })
        ->count();
        $investments = $query->get();
        $i = 1;
        foreach ($investments as $investment) {
            $investment->time_remaining = Carbon::parse($investment->created_at)->diffInDays($investment->expiration_date);
            $investment->count = $i;
            $i++;
            if(isset($package[$investment->package_id])){
                $package[$investment->package_id] += $investment->gain;
            } else {
                $package[$investment->package_id] = $investment->gain;
            }
        }
            $total = 0;
            if(isset($package[1])){
                $total += $package[1];
            }
            if(isset($package[2])){
                $total += $package[2];
            }
            if(isset($package[3])){
                $total += $package[3];
            }

           // Log::alert($investments);
        return response()->json($data =[
            'investments' => $investments ,
            'basic' => number_format($package[1], 2) ?? 0,
            'advanced' => number_format($package[2], 2) ?? 0,
            'expert' => number_format($package[3], 2) ?? 0,
            'total' => number_format($total, 2),
            'countInverstors' => $investors,
            'countInvestments' => $investments->count(),
        ]);
    }

    public function getCompleteInvestments(Request $request)
    {
        $filter = $request->get('name');

        $query = Invesment::where('status', 2)->with('user');

        if(!is_null($filter)){
            $query->whereHas('user', function ($q) use ($filter) {
                $q->WhereRaw("email LIKE ?", ['%'.$filter.'%']);
            });
        }
        $investors = User::whereHas('investments', function (Builder $query) {
            $query->where('status', 1);
        })
        ->count();
        $investments = $query->get();
        $i=0;
        foreach ($investments as $investment) {
            $investment->time_remaining = Carbon::parse($investment->created_at)->diffInDays($investment->expiration_date);
            $investment->count = $i;
            $i++;
            if(isset($package[$investment->package_id])){
                $package[$investment->package_id] += $investment->gain;
            } else {
                $package[$investment->package_id] = $investment->gain;
            }
        }
            $total = 0;
            if(isset($package[1])){
                $total += $package[1];
            }
            elseif(isset($package[2])){
                $total += $package[2];
            }
            elseif(isset($package[3])){
                $total += $package[3];
            }
            return response()->json($data =[
                'investments' => $investments ,
                'basic' => number_format($package[1], 2) ?? 0,
                'advanced' => number_format($package[2], 2) ?? 0,
                'expert' => number_format($package[3], 2) ?? 0,
                'total' => number_format($total, 2),
                'countInverstors' => $investors,
                'countInvestments' => $investments->count(),
            ]);
    }
}

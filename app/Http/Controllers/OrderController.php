<?php

namespace App\Http\Controllers;

use App\Models\Invesment;
use App\Models\MarketPurchased;
use App\Models\Order;
use App\Models\Package;
use App\Models\Project;
use App\Models\ReferalLink;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Services\BonusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
	{
		$this->orderRepository = $orderRepository;
	}
    public function getOrdersAdmin(Request $request)
    {

        $filter = $request->get('dataFilter');

        $orders = Order::with(['user','project','packageMembership'])
        ->filter($filter)
        ->orderBy('id', 'DESC')
        ->get();

        $data = array();
        foreach ($orders as $order) {
            if (isset($order->project)) {
                $phase = ($order->project->phase2 == null && $order->project->phase1 == null)
                ? ""
                : (($order->project->phase2 != null)
                ? "Phase 2"
                : "Phase 1");
            }

            $object = [
                'id' => $order->id,
                'user_id' => $order->user->id,
                'user_username' => $order->user->name,
                'user_email' => $order->user->email,
                'program' => $order->cyborg_id ? $order->packagesB2B->product_name : "Mining ".$order->package->package,
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

        return response()->json(['status' => 'success', 'data' => $data, 201]);
    }
    public function getOrdersDownload() {
        $orders = $this->orderRepository->getOrders();

		foreach($orders as $order)
		{
			if (isset($order->project)) {
				$phase = ($order->project->phase2 == null && $order->project->phase1 == null)
				? ''
				: (($order->project->phase2 == null)
				? 'Phase 1'
				: 'Phase 2');
			}

			$data[] = [
				'id' => $order->id,
                'date' => $order->created_at->format('Y-m-d'),
                'user_name' => strtolower(explode(" ", $order->user->name)[0]." ".explode(" ", $order->user->last_name)[0]),
                'program' => $order->packageMembership->getTypeName(),
                'status' => $order->getStatus(),
                'hash_id' => $order->hash,
                'amount' => $order->amount,
			];
		}
		return response()->json($data, 200);
    }
    public function filterOrders(Request $request)
    {
        $query = Order::with('user');
        $params = false;

        if ($request->has('hash') && $request->hash !== null) {
            $query->where('hash', $request->hash);
            $params = true;
        }

        if ($request->has('email') && $request->email !== null) {
            $email = $request->email;
            $query->whereHas('user', function($q) use($email){
                $q->where('email', $email);
            });
            $params = true;
        }

        $orders = $query->get();

        $data = [];

        if(!$orders || !$params) {
            return response()->json($data, 200);
        }
        foreach ($orders as $order) {
            if (isset($order->project)) {
                $phase = ($order->project->phase2 == null && $order->project->phase1 == null)
                ? ""
                : (($order->project->phase2 != null)
                ? "Phase 2"
                : "Phase 1");
            }

            $object = [
                'id' => $order->id,
                'user_id' => $order->user->id,
                'user_username' => $order->user->user_name,
                'user_email' => $order->user->email,
                'program' => $order->packageMembership->getTypeName(),
                'phase' => $phase ?? "",
                'account' => $order->packageMembership->account,
                'status' => $order->status,
                'hash_id' => $order->hash, // Hash::make($order->id)
                'amount' => $order->amount,
                'sponsor_id' => $order->user->sponsor->id,
                'sponsor_username' => $order->user->sponsor->user_name,
                'sponsor_email' => $order->user->sponsor->email,
                'date' => $order->created_at->format('Y-m-d')
            ];

            array_push($data, $object);
        }
        return response()->json($data, 200);
    }

    public function processOrderApproved($order)
    {
        if(!is_null($order->cyborg_id)) $this->matrixApproved($order);
        if(!is_null($order->package_id)) $this->investmentApproved($order);
    }
    public function processOrderCanceled($order)
    {
        if(!is_null($order->package_id)) $this->investmentCanceled($order);
    }

    public function investmentCanceled($order)
    {
        $investment = Invesment::where([['order_id', $order->id]])->first();
        $investment-> status = 3;
        $investment->save();
    }


    public function investmentApproved($order)
    {
       $date = Carbon::now();
       $investment = Invesment::where('order_id', $order->id)->first();
       $investment->status = 1;
       $package = Package::find($investment->package_id);
       $investment->expiration_date = $date->addMonths($package->investment_time)->format('Y-m-d');
       $investment->save();
    }

    private function matrixApproved($order)
    {
        $code = $this->generateCode();

        $referal = [
            'user_id' => $order->user_id,
            'link_code' => $code,
            'cyborg_id' => $order->cyborg_id,
            'right' => 0,
            'left' => 0,
        ];
        $user = User::find($order->user_id);
        if($user->status == '0'){
            $user->status = '1';
            $user->save();

            $bonusService = new BonusService;
            $bonusService->generateFirstComission(20,$user, $order, $buyer = $user, $level = 2, $user->id);
        }

        ReferalLink::create($referal);
        MarketPurchased::create(['user_id' => $order->user_id, 'cyborg_id' => $order->cyborg_id, 'order_id' => $order->id]);

    }
    private function generateCode()
    {
        $code = Str::random(6);
        if(!ReferalLink::where('link_code', $code)->exists()){
            return $code;
        }
        $this->generateCode();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\Inversion;
use App\Models\Ticket;
use App\Models\PackageMembership;
use App\Models\MarketPurchased;
use App\Models\User;
use App\Models\WalletComission;
use App\Repositories\OrderRepository;
use App\Repositories\TicketRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
	protected $ticketRepository;
	protected $orderRepository;

	public function __construct(TicketRepository $ticketRepository, OrderRepository $orderRepository)
	{
		$this->ticketRepository = $ticketRepository;
		$this->orderRepository = $orderRepository;
	}

	public function getLast10SupportTickets()
	{
		$tickets = $this->ticketRepository->getTicketsByQuantity(10);

		$data = [];

		foreach ($tickets as $ticket) {
			$data[] = [
				'id' => $ticket->id,
				'user_id' => $ticket->user_id,
				'status' => $ticket->status,
				'subject' => $ticket->subject,
				'categories' => $ticket->categories,
				'user' => [
					'name' => ucwords(strtolower($ticket->user->name)),
					'last_name' => ucwords(strtolower($ticket->user->last_name)),
					'user_name' => strtolower($ticket->user->user_name),
					'email' => strtolower($ticket->user->email),
					'profile_picture' => $ticket->user->profile_picture,
				]
			];
		}

		return response()->json($data, 200);
	}
	public function getTicketsAdmin()
	{
		$tickets = Ticket::where('status', 0)->with('user')->with('messages')->orderBy('updated_at', 'desc')->take(10)->get();
		return response()->json($tickets, 200);
	}

	public function mostRequestedPackages()
	{
		$evaluations = Order::whereHas('packageMembership', function ($q) {
			$q->where('type', 1);
		})->count();


		$fast = Order::whereHas('packageMembership', function ($q) {
			$q->where('type', 2);
		})->count();

		$accelerated = Order::whereHas('packageMembership', function ($q) {
			$q->where('type', 2);
		})->count();

		$one_hundred = $evaluations + $fast + $accelerated;

		$data = [
			'evaluations' => $this->calculatePercent($one_hundred, $evaluations),
			'fast' => $this->calculatePercent($one_hundred, $fast),
			'accelerated' => $this->calculatePercent($one_hundred, $accelerated)
		];

		return response()->json($data, 200);
	}

	private function calculatePercent(int $one_hundred, int $amount)
	{
		return ($amount * 100) / $one_hundred;
	}

	public function getLast10Orders()
	{
		$orders = $this->orderRepository->getOrdersByQuantity(10);

		$data = [];
		foreach ($orders as $order) {
			if (isset($order->project)) {
				$phase = ($order->project->phase2 == null && $order->project->phase1 == null)
					? ''
					: (($order->project->phase2 == null)
						? 'Phase 1'
						: 'Phase 2');
			}

			$data[] = [
				'id' => $order->id,
				'date' => $order->created_at,
				'status' => $order->status,
				'program' => [
					'name' => $order->packageMembership->getTypeName(),
					'account' => $order->packageMembership->account,
					'phase' => $phase ?? '',
				],
				'user' => [
					'id' => $order->user_id,
					'name' => ucwords(strtolower($order->user->name)),
					'last_name' => ucwords(strtolower($order->user->last_name)),
					'user_name' => strtolower($order->user->user_name),
					'email' => strtolower($order->user->email),
					'profile_picture' => $order->user->profile_picture,
				]
			];
		}
		return response()->json($data, 200);
	}

	public function getOrders()
	{
		$orders = $this->orderRepository->getOrders();

		foreach ($orders as $order) {
			$data[] = [
				'id' => $order->id,
				'user_id' => $order->user->id,
				'user_name' => strtolower(explode(" ", $order->user->name)[0] . " " . explode(" ", $order->user->last_name)[0]),
				'status' => $order->status,
				'description' => $order->packagesB2B->package,
				'hash_id' => $order->hash,
				'amount' => round($order->amount, 2),
				'date' => $order->created_at->format('Y-m-d'),
				'update_date' => $order->updated_at	->format('Y-m-d')
			];
		}
		return response()->json($data, 200);
	}

    public function sumOrderPaid()
    {
        $order = Order::where('status', '1')->get();

        $data = $order->sum('amount');

        return response()->json($data, 200);
    }

    public function sumComissionPaid()
    {
        $order = WalletComission::where('status', '2')->get();

        $data = $order->sum('amount_retired');

        return response()->json($data, 200);
    }

    public function gainWeekly()
    {
        // Obtener los datos de la tabla 'ordenes' ordenados por fecha de creación
        $ordenes = Order::where('status', '1')->orderBy('created_at')->get();

        // Crear un arreglo para almacenar los datos de la gráfica
        $data = [];

        // Iterar sobre los registros de la tabla 'ordenes'
        foreach ($ordenes as $orden) {
            $diaSemana = $orden->created_at->format('D');
            $ganancias = $orden->amount;

            // Agregar los datos al arreglo de la gráfica
            $data[$diaSemana] = $ganancias;
        }

        // Devolver los datos de la gráfica como respuesta JSON
        return response()->json($data, 200);
    }

	public function getReferralCount(User $user, $level = 1, $maxLevel = 4, $parentSide = null, $matrix = null): int
{
    $referralCount = 0;

    if ($level <= $maxLevel) {
        // Obtener las matrices compradas por el usuario autenticado
        $purchasedMatrices = MarketPurchased::where('user_id', $user->id);

        // Verificar si se proporcionó un valor válido para $matrix y filtrar las matrices por ese valor
        if ($matrix !== null) {
            $purchasedMatrices->where('cyborg_id', $matrix);
        }

        $purchasedMatrices = $purchasedMatrices->pluck('id');

        // Filtrar los usuarios que tienen el campo 'father_cyborg_purchased_id' igual al 'cyborg_id' de las matrices compradas
        $usersWithPurchasedMatrices = User::whereIn('father_cyborg_purchased_id', $purchasedMatrices)->get();

        // Contar los referidos del usuario actual en el lado izquierdo (binary_side = 'L')
        $leftReferralCount = $usersWithPurchasedMatrices
            ->where('binary_side', 'L')
            ->count();

        // Contar los referidos del usuario actual en el lado derecho (binary_side = 'R')
        $rightReferralCount = $usersWithPurchasedMatrices
            ->where('binary_side', 'R')
            ->count();

        // Sumar los referidos de ambos lados para obtener el total de referidos directos del usuario
        $referralCount = $leftReferralCount + $rightReferralCount;

        // Recorrer los referidos y obtener la cantidad de sus referidos recursivamente
        foreach ($usersWithPurchasedMatrices as $referral) {
            $subReferralCount = $this->getReferralCount(User::find($referral->id), $level + 1, $maxLevel, $referral->binary_side, $matrix);
            $referralCount += $subReferralCount;
        }
    }

    return $referralCount;
}


	public function topFiveUsers()
{
    // Obtener todos los usuarios de la base de datos
    $users = User::all();

    // Crear una colección para almacenar los resultados
    $userList = collect();

    // Recorrer todos los usuarios y obtener la cantidad de referidos y la matriz de cada uno
    foreach ($users as $user) {
        // Obtener la cantidad de referidos del usuario actual
        $referralCount = $this->getReferralCount($user);

        // Obtener la matriz en la que se encuentra el usuario
        $matrix = $user->matrix_type;

        // Agregar el usuario y sus datos a la colección de resultados
        $userList->push([
            'name' => $user->name,
            'referral_count' => $referralCount,
            'matrix' => $matrix,
        ]);
    }

    // Ordenar la colección en función del número de referidos (de mayor a menor)
    $userList = $userList->sortByDesc('referral_count');

    // Tomar los primeros cinco usuarios de la lista (los cinco con más referidos)
    $topFiveUsers = $userList->take(5);

    return $topFiveUsers;
	}


    public function mountMatrix()
    {

        $matrix = WalletComission::get();
        $matrixTotalAmount = $matrix->sum('amount');
        $totalAmountMatrix20 = $matrix->where('type', '1')->sum('amount');
        $totalAmountMatrix200 = $matrix->where('type', '2')->sum('amount');
        $totalAmountMatrix2000 = $matrix->where('type', '3')->sum('amount');

        $data = array(
            'matrixTotalAmount'     => $matrixTotalAmount,
            'totalAmountMatrix20'   => $totalAmountMatrix20,
            'totalAmountMatrix200'  => $totalAmountMatrix200,
            'totalAmountMatrix2000' => $totalAmountMatrix2000
        );

        return response()->json($data, 200);
    }

    public function totalEarnigs()
    {
        $inversion = PackageMembership::all();
        $data = $inversion->sum('amount');

        return response()->json($data, 200);
    }

	public function countUserForMatrix()
	{
		$inversions = Inversion::get();
        $userCount = $inversions->sum('amount');
        $userMatrix20 = $inversions->where('type', '0')->sum('amount');
        $userMatrix200 = $inversions->where('type', '1')->sum('amount');
        $userMatrix2000 = $inversions->where('type', '2')->sum('amount');

        $data = array(
            'userCount'     => $userCount,
            'userMatrix20'   => $userMatrix20,
            'userMatrix200'  => $userMatrix200,
            'userMatrix2000' => $userMatrix2000
        );

        return response()->json($data, 200);
	}

    public function countOrderAndCommision()
    {
        // Obtener el conteo de órdenes por mes
        $orders = DB::table('orders')
        ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
        ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
        ->get();

        // Obtener el conteo de comisiones por mes
        $commissions = DB::table('wallets_commissions')
        ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
        ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
        ->get();

        // Combinar los resultados en un objeto JSON
        $data = [
            'orders' => $orders,
            'commissions' => $commissions
        ];

        return response()->json($data);
    }

}

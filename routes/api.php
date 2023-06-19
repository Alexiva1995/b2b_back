<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\FutswapTransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ReportsController;
use App\Http\Middleware\AdminRoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PackageMembershipController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\TreController;
use App\Http\Controllers\DocumentController;
use App\Models\Order;
use App\Models\User;
use App\Services\BonusService;
use App\Services\PagueloFacilService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::post('/paguelo', function(){
//     $pagueloService = new PagueloFacilService();
//     $user = User::find(2);
//     $order = Order::find(1);
//     $result = $pagueloService->makeTransaction($user->id, $order->id);
//     return response()->json(['url' => $result], 201);
// });

Route::controller(AuthController::class)->group(function ($router) {
    Route::post('register', 'register');
    Route::get('get-prefixes', 'getPrefixes');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('update-password', 'updatePassword');
    Route::post('verify-email', 'verifyEmail');
    Route::post('send-email-verification-code', 'sendVerificationCode');
    Route::get('get-sponsor-name/{identifier}', 'getSponsorName');
    Route::get('auth/user', 'getAuthUser');
});
Route::controller(LandingController::class)->group(function ($router) {
    Route::post('contact-us', 'contactUs');
    Route::post('subscription', 'subscription');
    Route::post('download-terms-conditions', 'downloadTermsAndConditions');
    Route::get('mails-terms-conditions', 'mailsTermsAndConditions');
});

Route::post('paguelo-facil-hook', [ReportsController::class, 'updateOrders']);

Route::middleware('jwt')->group(function () {

    // ADMINvalidateCoupon
    Route::middleware([AdminRoleMiddleware::class])->group(function () {

        Route::controller(AdminDashboardController::class)->group(function ($router) {
            Route::get('get-last-ten-tickets', 'getLast10SupportTickets');
            Route::get('get-last-ten-orders', 'getLast10Orders');
            Route::get('get-orders', 'getOrders');
            Route::get('get-tickets-admin', 'getTicketsAdmin');
            Route::get('most-requested-packages', 'mostRequestedPackages');
        });

        Route::controller(TicketsController::class)->group(function ($router) {
            Route::get('ticket-edit-admin/{id}', 'editAdmin');
            Route::post('ticket-update-admin/{id}', 'updateAdmin');
            Route::get('ticket-list', 'listTickets');
            Route::post('ticket-list-admin', 'listAdmin');
            Route::get('ticket-show-admin/{id}', 'showAdmin');
        });
        Route::controller(UserController::class)->group(function ($router) {
            Route::get('get-users', 'getUsers');
            Route::get('find-user/{user_id}', 'findUser');
            Route::get('get-users-download', 'getUsersDownload');
            //Ruta DashboardUser B2B obtener Balance del usuario
            Route::get('get-user-balance', 'getUserBalance');
            //Fin
            //Ruta Dashboard User B2B obtener bonos matrix del user
            Route::get('get-user-bonus-user','getUserBonus');
            //Fin
            Route::post('update-user-affiliate', 'updateUserAffiliate');
            Route::post('toggle-user-can-buy-fast', 'toggleUserCanBuyFast');
            Route::get('get-users-wallet-list', 'getUsersWalletsList');
            Route::post('get-filter-users-wallet-list', 'getFilterUsersWalletsList');
            Route::post('filter-users-wallet-list', 'filterUsersWalletsList');
            Route::post('filter-users-list', 'filterUsersList');
            
        });
        Route::controller(UserController::class)->group(function ($router) {
            Route::get('audit-user-wallets', 'auditUserWallets');
            Route::get('audit-user-profile', 'auditUserProfile');
            Route::get('audit-user-dashboard', 'auditUserDashboard');
        });
        Route::controller(PackageMembershipController::class)->group(
            function ($router) {
                Route::get('/projects-admin', 'GetProjectsAdmin');
                Route::get('/get-packages-list', 'GetPackagesList');
                Route::post('filter-admin-reports', 'filterAdminReports');
                Route::post('add-order-to-user', 'addOrderToUser');
                Route::get('/project-admin/{id}', 'GetProjectAdmin');
                Route::post('/formulary/create', 'formularyCreate');
                Route::put('/formulary/update', 'formularyUpdate');
                Route::post('/update-project-status', 'updateStatusProject');
            }
        );


        Route::controller(ReportsController::class)->group(function ($router) {
            Route::get('reports/comisions', 'commision');
            Route::get('reports/refund', 'refund');
            Route::post('filter/reports/comisions', 'filterComissionList');
            Route::get('reports/liquidactions', 'liquidaction');
            Route::get('reports/coupons', 'coupons');
        });
        Route::controller(KycController::class)->group(function ($router) {
            Route::get('kyc-list', 'admin');
            Route::post('kyc-filter-list', 'filterKycList');
            Route::post('kyc-update', 'updateStatus');
        });

        Route::controller(WalletController::class)->group(function ($router) {
            Route::get('/devolutions-admin', 'devolutionsAdmin');
            Route::get('/get-refunds-download', 'getRefundsDownload');
            Route::get('/get-comissions-download', 'getComissionsDownload');
        });

        Route::controller(OrderController::class)->group(function ($router) {
            // Route::get('get-orders', 'getOrdersAdmin');
            Route::post('filter-orders', 'filterOrders');
            Route::get('get-orders-download', 'getOrdersDownload');
        });

        Route::controller(DocumentController::class)->group(function ($router) {
            Route::get('documents-list', 'index');
            Route::post('documents-store', 'store');
            Route::post('documents-delete', 'destroy');
            Route::post('documents-download', 'download');
        });
        Route::controller(FutswapTransactionController::class)->group(
            function ($router) {
                Route::post('/token-auth', 'saveTokenAuth');
            }
        );
    });

    // USER
    Route::controller(TreController::class)->group(function () {
        Route::get('/red-unilevel/{user_id}', 'index');
    });
    Route::controller(WithdrawalController::class)->group(function () {
        Route::get('get-withdrawals', 'getWithdrawals');
        Route::get('get-withdrawals-download', 'getWithdrawalsDownload');
    });
    Route::controller(CouponController::class)->group(
        function ($router) {
            Route::get('/coupon/check', 'checkUserCouponActive');
            Route::post('/coupon/create', 'create');
            Route::post('/coupon/validate', 'validateCoupon');
        }
    );
    Route::controller(TicketsController::class)->group(function ($router) {
        Route::get('get-tickets', 'getTickets');
        Route::post('create-ticket', 'createTicket');
        Route::put('close-ticket', 'closeTicket');
        Route::post('create-message', 'createMessage');

        Route::get('edit-ticket/{id}', 'editTicket');
        Route::post('ticket-update-user/{id}', 'updateUser');
        Route::get('ticket-show-user/{id}', 'showUser');
    });

    Route::controller(ReportsController::class)->group(function ($router) {
        Route::get('reports/comisions', 'commision');
        Route::get('reports/liquidactions', 'liquidaction');
        Route::get('reports/coupons', 'coupons');
    });
    Route::controller(AuthController::class)->group(function ($router) {
        Route::get('test', 'test');
        Route::post('logout', 'logout');
        Route::post('verify_token', 'verifyToken');
    });

    Route::controller(UserController::class)->group(function ($router) {
        Route::get('/user-profile', 'getUser');
        Route::get('/user', 'getUser');
        Route::get('/countries', 'GetCountry');
        Route::post('/change/data', 'ChangeData');
        Route::post('/email/check', 'CheckCodeToChangeEmail');
        Route::post('/change/password', 'ChangePassword');
        Route::post('/send/code', 'SendSecurityCode');
        Route::get('/get-mt-users', 'getMT5UserList');
        Route::get('/get-mt-account', 'getMT5User');
        Route::get('/get-mt-summary', 'getMTSummary');
        Route::post('/create-mt-user', 'createMT5User');
    });
    Route::apiResource('users', UserController::class);

    Route::controller(WalletController::class)->group(function ($router) {
        Route::post('add-balance-to-user', 'addBalanceToUser');
        Route::get('get-refunds', 'getRefunds');
        Route::get('/refunds-list/{id}', 'refundsList');
        Route::get('/get-wallet-comissions-list', 'getWallets');
        Route::get('/get-total-available', 'getTotalAvailable');
        Route::get('/get-total-directs', 'getTotalDirects');
        Route::get('/check-wallet-user', 'checkWalletUser');
    });

    Route::controller(PackageMembershipController::class)->group(
        function ($router) {
            Route::get('/packages-memberships/{email}', 'GetPackageMemberships');
            Route::post('/buy-membership', 'BuyPackage');
        }
    );
    Route::controller(DocumentController::class)->group(function ($router) {
        Route::get('documents-list', 'index');
        Route::post('documents-download', 'download');
    });
    // Rutas retiros
    Route::controller(FutswapTransactionController::class)->group(
        function ($router) {
            Route::post('/guard-code', 'saveWallet');
            Route::post('/save-wallet', 'saveWallet');
            Route::get('/generate-code', 'generateCode');
            Route::post('/liquidactions-store', 'procesarLiquidacion');
        }
    );

    Route::controller(KycController::class)->group(function ($router) {
        Route::post('kyc-request', 'store');
    });

    Route::controller(DashboardController::class)->group(function ($router) {
        Route::get('get-user-audit', 'getUser');
        Route::get('get-wallet-balance', 'getWalletBalance');
        Route::get('get-user-programs', 'getUserPrograms');
        Route::get('get-user-orders', 'getUserOrders');
        Route::get('get-user-refunds', 'getUserRefunds');
        Route::get('get-most-download-doc', 'getMostDownloadDoc');
    });
});

// Rutas Futswap
Route::middleware('futswap')->group(function () {
    Route::post('/payment/confirmation', [FutswapTransactionController::class, 'paymentConfirmation']);
    Route::post('/payment/withdrawal', [FutswapTransactionController::class, 'withdrawalConfirmation']);
    Route::post('/verify/wallet', [FutswapTransactionController::class, 'verify_wallet']);
});

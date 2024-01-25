<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AmazonController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryLearningsController;
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
use App\Http\Controllers\FiltersController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\MassMessageController;
use App\Http\Controllers\PackageController;
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
    Route::get('/check-matrix/{code}', 'checkMatrix');
    Route::get('create-comision/{id}', 'createComission');
    Route::get('/check-matrix/{code}/{side}', 'checkMatrix');
    Route::post('/first-purchase', 'firstPurchase');
    Route::post('/data-payment', 'getDataPayment');
    Route::post('check-order', 'checkOrder');
    Route::post('mark-complete', 'paymentCompleted');
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

        Route::controller(AmazonController::class)->group(function ($router) {
            Route::get('amazon/all-active-invest', 'getAllActiveInvestment');
            Route::post('amazon/category', 'storeCategory');
            Route::post('amazon/lot/{category}', 'storeLot');
            Route::post('amazon/pay', 'payYield');
            Route::delete('amazon/lot/{lot}/delete', 'deleteLot');
            Route::delete('amazon/category/{category}/delete', 'deleteCategory');
            Route::delete('amazon/lot/product/{id}', 'deleteProduct');
            Route::post('amazon/lot/{id}/update', 'updateLot');

        });

        Route::controller(MassMessageController::class)->group(function ($router) {
            Route::get('massMessages', 'index');
            Route::get('massMessage/{id}', 'getMessage');
            Route::post('massMessage/create', 'store');
            Route::post('massMessage/read', 'readMessage');
            Route::put('massMessage/{id}', 'updateMessage');
            Route::delete('massMessage/{id}', 'destroyMessage');
        });

        Route::controller(PackageController::class)->group(function ($router) {
            Route::get('get-packages', 'getPackages');
            Route::get('packages/actives', 'getActiveInvestments');
            Route::get('packages/complete', 'getCompleteInvestments');
        });

        Route::controller(MarketController::class)->group(function ($router) {
            Route::get('/cyborg/{id?}', 'getAllCyborgs');
            Route::get('/get-cyborgs/{email?}', 'getAllCyborgs2');
            Route::post('/cyborg/purchase-manual', 'purchaseManualCyborg');
            Route::post('/check-order-market', 'checkOrderMarket');
        });

        Route::controller(CategoryLearningsController::class)->group(function ($router) {
            Route::get('get-all-category/{type}', 'getAll');
            Route::get('get-category/{id}', 'getAll');
            Route::post('create-category', 'store');
            Route::post('update-category', 'update');
            Route::delete('delete-category/{id}', 'destroy');
        });

        Route::controller(AdminDashboardController::class)->group(function ($router) {
            Route::get('get-last-ten-tickets', 'getLast10SupportTickets');
            Route::get('/order/paid', 'sumOrderPaid');
            Route::get('get-last-ten-orders', 'getLast10Orders');
            Route::get('get-tickets-admin', 'getTicketsAdmin');
            Route::get('most-requested-packages', 'mostRequestedPackages');

            //rutas dashboard admin b2b
            Route::get('/order/paid', 'sumOrderPaid');
            Route::get('get/orders', 'getOrders');
            Route::get('/comission/paid', 'sumComissionPaid');
            Route::get('/gain/weekly', 'gainWeekly');
            Route::get('/top/users', 'topFiveUsers');
            Route::get('/amount/matrix', 'mountMatrix');
            Route::get('/amount/earnings', 'totalEarnings');
            Route::get('/count/user/matrix', 'countUserForMatrix');
            Route::get('/count/order/and/commision', 'countOrderAndCommision');
            Route::get('/user/matrices', 'userMatrices');
            //fin

        });

        Route::controller(FiltersController::class)->group(function ($router) {
            Route::post('filter/order/admin', 'filtersOrderAdmin');
            Route::post('filter/product/admin', 'filtersProductAdmin');
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
            Route::get('find-user-matrix/{cyborg}/{user_id}', 'findUserMatrix');
            Route::get('get-users-download', 'getUsersDownload');
            Route::post('update-user-affiliate', 'updateUserAffiliate');
            Route::post('toggle-user-can-buy-fast', 'toggleUserCanBuyFast');
            Route::get('get-users-wallet-list', 'getUsersWalletsList');
            Route::post('get-filter-users-wallet-list', 'getFilterUsersWalletsList');
            Route::post('filter-users-wallet-list', 'filterUsersWalletsList');
            Route::post('filter-users-list', 'filterUsersList');
            Route::post('admin/change/password/{id?}', 'ChangePassword');
            Route::post('create-user', 'createUser');
            Route::get('get-cyborg/{email}', 'getCyborg');
            Route::post('activate-user', 'activateUser');
            Route::post('delete-user', 'userDelete');
            Route::post('delete-matrix', 'userDeleteMatrix');
        });

        Route::controller(UserController::class)->group(function ($router) {
            Route::get('audit-user-wallets', 'auditUserWallets');
            Route::get('audit-user-profile/{id?}', 'auditUserProfile');
            Route::get('audit-user-wallet/{id?}', 'auditUserWallet');
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
            //ruta de liquidacion admin b2b
            Route::get('reports/liquidactions', 'liquidaction');
            // ruta de liquidacion de admin pendiente b2b
            Route::get('reports/liquidactions/pending', 'liquidactionPending');

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
            //b2b ordenes admin
            Route::get('get/orders', 'getOrdersAdmin');
            //
            Route::post('filter-orders', 'filterOrders');
            Route::get('get-orders-download', 'getOrdersDownload');
        });

        Route::controller(DocumentController::class)->group(function ($router) {
            Route::get('documents-list', 'index');
            Route::post('documents-store', 'store');
            Route::post('documents-delete', 'destroy');
            Route::post('documents-download', 'download');
        });
        Route::controller(LearningController::class)->group(function ($router) {
            Route::get('learnings-all', 'learnings');
            Route::get('learnings/{type}/{category}', 'learningsType');
            Route::post('documents-store', 'documentStore');
            Route::post('video-store', 'videoStore');
            Route::post('link-store', 'linkStore');
            Route::post('delete-learning', 'deleteLearning');
        });
        Route::controller(FutswapTransactionController::class)->group(
            function ($router) {
                Route::post('/token-auth', 'saveTokenAuth');
            }
        );
        Route::controller(WithdrawalController::class)->group(function () {
            Route::post('withdrawal-update', 'withdrawalUpdate');
        });
    });

    // USER
    Route::controller(AmazonController::class)->group(function ($router) {
        Route::get('amazon/userInvests', 'getInvestUser');
        Route::get('amazon', 'getCategories');
        Route::get('amazon/category/{type}', 'getLotsType');
        Route::get('amazon/lot/{lot}/products', 'getProducts');
        Route::post('amazon/invest', 'purchasedInvestment');
        Route::post('amazon/check-order', 'checkOrder');
        Route::post('amazon/invest/cancel', 'canceleInvestment');
    });

    Route::controller(MassMessageController::class)->group(function ($router) {
        Route::get('massMessages', 'index');
        Route::get('massMessage/{id}', 'getMessage');
        Route::post('massMessage/read', 'readMessage');
    });
    Route::controller(PackageController::class)->group(function ($router) {
        Route::get('get-packages', 'getPackages');
        Route::post('purchased', 'purchasedInvestment');
        Route::post('mining/check-order', 'checkOrder');
    });

    Route::controller(CategoryLearningsController::class)->group(function ($router) {
        Route::get('get-all-category/{type}', 'getAll');
        Route::get('get-category/{id}', 'getAll');
    });

    //Rutas producto B2B
    Route::controller(ProductController::class)->group(function ($router) {
        Route::post('products/shipping', 'storeShippingData');
        Route::get('products/list ', 'listUsersProductData');
        Route::get('products/user', 'listUserData');
        Route::put('/products/{id}', 'updateProductStatus');
    });
    //Fin

    Route::controller(TreController::class)->group(function () {
        Route::get('/red-unilevel/{user_id}', 'index');
    });

    //Ruta de retiros B2B
    Route::controller(WithdrawalController::class)->group(function () {
        Route::get('/get/withdrawals/{id?}', 'getWithdrawals');
        Route::get('/get/user/code', 'generateCode');
        Route::post('/save/user/wallet', 'saveWallet');
        Route::post('/withdrawal/process/user', 'processWithdrawal');
        Route::get('get/withdrawals/download', 'getWithdrawalsDownload');
    });
    //Fin

    Route::controller(CouponController::class)->group(
        function ($router) {
            Route::get('/coupon/check', 'checkUserCouponActive');
            Route::post('/coupon/create', 'create');
            Route::post('/coupon/validate', 'validateCoupon');
        }
    );
    // Route::controller(TicketsController::class)->group(function ($router) {
    //     Route::get('get-tickets', 'getTickets');
    //     Route::post('create-ticket', 'createTicket');
    //     Route::put('close-ticket', 'closeTicket');
    //     Route::post('create-message', 'createMessage');

    //     Route::get('edit-ticket/{id}', 'editTicket');
    //     Route::post('ticket-update-user/{id}', 'updateUser');
    //     Route::get('ticket-show-user/{id}', 'showUser');
    // });

    Route::controller(ReportsController::class)->group(function ($router) {
        Route::get('reports/comisions', 'commision');
        Route::get('reports/liquidactions', 'liquidaction');
        Route::get('reports/coupons', 'coupons');
        // ruta de liquidacion user
        Route::get('reports/liquidactions/user/{id?}', 'LiquidacionUser');
    });
    Route::controller(AuthController::class)->group(function ($router) {
        Route::get('test', 'test');
        Route::post('logout', 'logout');
        Route::post('verify_token', 'verifyToken');
    });

    Route::controller(UserController::class)->group(function ($router) {
        Route::get('/user-profile/{id?}', 'getUser');
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
        Route::get('/get-referal_links', 'getReferalLinks');
        Route::post('check-status-user/', 'checkStatus');

        //Ruta Ordenes User B2B
        Route::get('get/user/order/list/{id?}', 'userOrder');
        //

        //Ruta DashboardUser B2B obtener Balance del usuario
        Route::get('get/user/balance/{id?}', 'getUserBalance');
        //Fin

        //Ruta DashboardUser B2B obtener Retiros del usuario
        Route::get('get/user/withdrawals', 'getAllWithdrawals');
        //Fin

        //Ruta Dashboard User B2B obtener bonos matrix del user
        Route::get('get/user/bonus/{id?}', 'getUserBonus');
        //Fin

        //Ruta Dashboard User B2B para obtener plan del user
        Route::get('get/user/matrix/data/{id?}', 'myBestMatrixData');
        //Fin

        //Ruta Dashboard User B2B comisiones mensuales
        Route::get('get/monthly/commissions/{id?}', 'getMonthlyCommissions');
        //Fin

        //Ruta Dashboard User B2B ganancias mensuales
        Route::get('get/monthly/earnings/{id?}', 'getMonthlyEarnings');
        //Fin

        //Ruta Dashboard User B2B ordenes mensuales
        Route::get('get/monthly/orders/{id?}', 'getMonthlyOrders');
        //Fin

        //Ruta Dashboard User B2B ultimos 10 retiros
        Route::get('get/monthly/last/withdrawals/{id?}', 'getLast10Withdrawals');
        //Fin

        //Ruta Dashboard User B2B ultimos 10 retiros
        Route::get('get/user/orders', 'getUserOrders');
        //Fin

        //Ruta Matrix User B2B
        Route::get('get/user/cyborg/{cyborg_id}/matrix/{type?}/{id?}', 'showReferrals');

        //Fin

        //Ruta Lista Matrix User B2B
        Route::get('get/user/list/matrix/{matrix}', 'listReferrals');
        //Fin

    });
    Route::apiResource('users', UserController::class);

    //Ruta B2B para obtener datos para Cyborg y compra de Cyborg
    Route::controller(MarketController::class)->group(function ($router) {
        Route::get('/cyborg/{id?}', 'getAllCyborgs');
        Route::post('/cyborg/purchase', 'purchaseCyborg');
        Route::post('/check-order-market', 'checkOrderMarket');
    });
    //Fin
    Route::controller(WalletController::class)->group(function ($router) {
        Route::post('add-balance-to-user', 'addBalanceToUser');
        Route::get('get-refunds', 'getRefunds');
        Route::get('/refunds-list/{id}', 'refundsList');
        //ruta comision user b2b
        Route::get('/wallet/comissions/list/user/{id?}', 'getWallets');
        Route::get('/wallet/comissions/list/admin', 'getWalletsAdmin');
        //fin

        //Ruta Wallet b2b
        Route::get('/wallet/Data/list/user/{id?}', 'walletUserDataList');
        Route::get('/wallet/Data/list/admin', 'walletAdminDataList');
        Route::get('/wallet/Data/user/gain/{id?}', 'getMonthlyGain');
        Route::get('/wallet/Data/user/charts/{id?}', 'getChartData');
        //


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
    Route::controller(LearningController::class)->group(function ($router) {
        Route::get('learnings-all', 'learnings');
        Route::get('learnings/{type}/{category}', 'learningsType');
        Route::get('learnings-videos', 'videos');
        Route::get('learnings-links', 'links');
        Route::get('learnings-documents', 'documents');
        Route::post('download-learning', 'download');
    });
});

// Rutas Futswap
Route::middleware('futswap')->group(function () {
    Route::post('/payment/confirmation', [FutswapTransactionController::class, 'paymentConfirmation']);
    Route::post('/payment/withdrawal', [FutswapTransactionController::class, 'withdrawalConfirmation']);
    Route::post('/verify/wallet', [FutswapTransactionController::class, 'verify_wallet']);
});

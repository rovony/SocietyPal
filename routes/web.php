<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OwnerController;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AmenitiesController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminPaypalController;
use App\Http\Controllers\AdminStripeController;
use App\Http\Controllers\AssetsIssueController;
use App\Http\Controllers\BookAmenityController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\AdminPayfastController;
use App\Http\Controllers\CustomModuleController;
use App\Http\Controllers\AdminPaystackController;
use App\Http\Controllers\AdminRazorPayController;
use App\Http\Controllers\GlobalSettingController;
use App\Http\Controllers\PaypalWebhookController;
use App\Http\Controllers\CommonAreaBillController;
use App\Http\Controllers\OfflineRequestController;
use App\Http\Controllers\SocietySettingController;
use App\Http\Controllers\TowerManagmentController;
use App\Http\Controllers\FloorManagementController;
use App\Http\Controllers\AdminFlutterwaveController;
use App\Http\Controllers\AssetsManagementController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\TenantManagementController;
use App\Http\Controllers\AssetsMaintenanceController;
use App\Http\Controllers\ParkingManagementController;
use App\Http\Controllers\ServiceClockInOutController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\VisitorManagementController;
use App\Http\Controllers\AdminStripeWebhookController;
use App\Http\Controllers\UtilityBillManagementController;
use App\Http\Controllers\SocietyForumController;
use App\Http\Middleware\SuperAdmin;
use App\Http\Middleware\DisableFrontend;
use App\Http\Controllers\SuperAdmin\PaypalController;
use App\Http\Controllers\SuperAdmin\FlutterwaveController;
use App\Http\Controllers\SuperAdmin\StripeWebhookController;
use App\Http\Controllers\SuperAdmin\PaystackWebhookController;
use App\Http\Controllers\SuperAdmin\RazorpayWebhookController;
use App\Http\Controllers\SuperAdmin\FlutterwaveWebhookController;
use App\Http\Controllers\OnboardingStepController;
use App\Http\Controllers\CustomWebPageController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SuperAdmin\PaystackController;
use App\Http\Controllers\SuperadminSettingController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SocietyController;
use App\Http\Controllers\LandingSiteController;
use App\Http\Controllers\SuperAdmin\PayfastController;
use App\Http\Controllers\SuperAdmin\PayFastWebhookController;

Route::get('/manifest.json', [HomeController::class, 'manifest'])->name('manifest');

Route::middleware([LocaleMiddleware::class])->group(function () {

    Route::get('/', [HomeController::class, 'landing'])->name('home')->middleware(DisableFrontend::class);
    Route::get('/society-signup', [HomeController::class, 'signup'])->name('society_signup');
    Route::get('/customer-logout', [HomeController::class, 'customerLogout'])->name('customer_logout');

});

// Stripe webhook
Route::post('/webhook/verify-webhook/{hash}', [AdminStripeWebhookController::class, 'verifyStripeWebhook'])->name('stripe.webhook');
Route::post('/webhook/paypal-webhook/{hash}', [PaypalWebhookController::class, 'handleGatewayWebhook'])->name('paypal.webhook');
Route::post('/webhook/razorpay-webhook/{hash}', [AdminRazorPayController::class, 'handleGatewayWebhook'])->name('razorpay.webhook');
Route::post('/webhook/flutter-webhook/{hash}', [AdminFlutterwaveController::class, 'handleGatewayWebhook'])->name('flutterwave.webhook');
Route::match(['get', 'post'], '/success', [AdminFlutterwaveController::class, 'paymentMainSuccess'])->name('flutterwave.success');
Route::match(['get', 'post'], '/failed', [AdminFlutterwaveController::class, 'paymentFailed'])->name('flutterwave.failed');
Route::match(['get', 'post'], '/success', [AdminPaystackController::class, 'paymentMainSuccess'])->name('paystack.success');
Route::post('/webhook/paystack-webhook/{hash}', [AdminPaystackController::class, 'handleGatewayWebhook'])->name('paystack.webhook');
Route::match(['get', 'post'], '/failed', [AdminPaystackController::class, 'paymentFailed'])->name('paystack.failed');

Route::match(['get', 'post'], '/payfast/success', [AdminPayfastController::class, 'paymentMainSuccess'])->name('payfast.success');
Route::match(['get', 'post'], '/payfast/failed', [AdminPayfastController::class, 'paymentFailed'])->name('payfast.failed');
Route::post('/webhook/notify/{company}/{reference}', [AdminPayfastController::class, 'payfastNotify'])->name('payfast.notify');

Route::view('/society-register', 'auth.register')->name('auth.register');

Route::post('/webhook/billing-verify-webhook/{hash}', [StripeWebhookController::class, 'verifyStripeWebhook'])->name('billing.verify-webhook');
Route::post('/webhook/save-razorpay-webhook/{hash}', [RazorpayWebhookController::class, 'saveInvoices'])->name('billing.save_razorpay-webhook');
Route::post('/webhook/save-flutterwave-webhook/{hash}', [FlutterwaveWebhookController::class, 'handleWebhook'])->name('billing.save-flutterwave-webhook');
Route::post('/webhook/save-paystack-webhook/{hash}', [PaystackWebhookController::class, 'saveInvoices'])->name('billing.save-paystack-webhook');
Route::post('payfast-notification/{id}', [PayFastWebhookController::class, 'saveInvoice'])->name('payfast-notification');

Route::post('save-paypal-webhook/{hash}', [PaypalController::class, 'verifyBillingIPN'])->name('billing.save_paypal-webhook');

Route::middleware(['auth', config('jetstream.auth_session'), 'verified', LocaleMiddleware::class])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('account_unverified', [DashboardController::class, 'accountUnverified'])->name('account_unverified');

    Route::get('onboarding-steps', [OnboardingStepController::class, 'index'])->name('onboarding_steps');

    Route::resource('towers', TowerManagmentController::class);

    Route::resource('floors', FloorManagementController::class);

    Route::resource('apartments', ApartmentController::class);

    Route::resource('parkings', ParkingManagementController::class);

    Route::post('/subscribe', [PushNotificationController::class, 'subscribe']);

    Route::resource('utilityBills', UtilityBillManagementController::class);
    Route::get('utilityBills/print/{id}', [UtilityBillManagementController::class, 'printOrder'])->name('utilitybills.print');
    Route::get('page/{slug}', [CustomWebPageController::class, 'index'])->name('customMenu');

    Route::resource('common-area-bill', CommonAreaBillController::class);
    Route::get('common-area-bill/print/{id}', [CommonAreaBillController::class, 'printOrder'])->name('commonAreaBill.print');

    Route::resource('book-amenity', BookAmenityController::class);

    Route::resource('amenities', AmenitiesController::class);
    Route::get('amenities/print/{id}', [AmenitiesController::class, 'printOrder'])->name('amenities.print');

    Route::resource('assets', AssetsManagementController::class);

    Route::resource('service-management', ServiceManagementController::class);

    Route::resource('service-type', ServiceTypeController::class);

    Route::resource('service-clock-in-out', ServiceClockInOutController::class);

    Route::resource('visitors', VisitorManagementController::class);
    Route::get('visitors/print/{id}', [VisitorManagementController::class, 'printOrder'])->name('visitors.print');
    Route::get('/visitors/approval/{visitor}', [VisitorManagementController::class, 'visitorApproval'])->name('visitors.approval');

    Route::resource('settings', SocietySettingController::class);

    Route::resource('tenants', TenantManagementController::class)->only(['index', 'show']);

    Route::resource('rents', RentController::class);

    Route::resource('users', UserController::class);

    Route::resource('notices', NoticeBoardController::class);

    Route::resource('events', EventController::class);

    Route::resource('owners', OwnerController::class);

    Route::resource('asset-maintenance', AssetsMaintenanceController::class);
    Route::resource('asset-issue', AssetsIssueController::class);


    Route::resource('tickets', TicketController::class);

    Route::resource('society-forum', SocietyForumController::class);

    Route::resource('events', EventController::class);
    Route::post('/calendar/events', [EventController::class, 'loadEvents'])->name('calendar.events');

    Route::resource('maintenance', MaintenanceController::class);
    Route::get('maintenance-offline-plan', [OfflineRequestController::class, 'offlinePlanRequests'])->name('offline.offline-plan-request');

    Route::post('stripe/maintenance-payment', [AdminStripeController::class, 'maintenancePayment'])->name('stripe.maintenance_payment');
    Route::get('/stripe/success-callback', [AdminStripeController::class, 'success'])->name('stripe.success');
    Route::get('paypal/success', [AdminPaypalController::class, 'success'])->name('paypal.success');
    Route::get('paypal/cancel', [AdminPaypalController::class, 'cancel'])->name('paypal.cancel');

    Route::prefix('reports')->group(function () {
        Route::view('maintenance-report', 'reports.maintenance')->name('reports.maintenance');
        Route::view('financial-report', 'reports.financial')->name('reports.financial');

    });

    Route::view('billing/upgrade-plan', 'plans.index')->name('pricing.plan');

    Route::post('stripe/license-payment', [StripeController::class, 'licensePayment'])->name('stripe.license_payment');
    Route::get('/stripe/license-success-callback', [StripeController::class, 'licenseSuccess'])->name('stripe.license_success');

    Route::post('/flutterwave/initiate-payment', [FlutterwaveController::class, 'initiatePayment'])->name('flutterwave.initiate-payment');
    Route::get('/flutterwave/callback', [FlutterwaveController::class, 'paymentCallback'])->name('flutterwave.callback');

    Route::post('/paypal/initiate-payment', [PaypalController::class, 'initiatePayment'])->name('paypal.initiate-payment');
    Route::get('billing/paypal-recurring', [PaypalController::class, 'payWithPaypalRecurrring'])->name('billing.paypal-recurring');
    Route::get('/paypal/lifetime/success', [PaypalController::class, 'paypalLifetimeSuccess'])->name('paypal.lifetime.success');

    Route::post('/paystack/initiate-payment', [PaystackController::class, 'initiatePaystackPayment'])->name('paystack.initiate-payment');
    Route::get('/paystack/callback', [PaystackController::class, 'handleGatewayCallback'])->name('paystack.callback');

    Route::post('/payfast/initiate-payment', [PayfastController::class, 'initiatePayfastPayment'])->name('payfast.initiate-payment');
    Route::get('billing/payfast-success', [PayFastController::class, 'payFastPaymentSuccess'])->name('billing.payfast-success');
    Route::get('billing/payfast-cancel', [PayFastController::class, 'payFastPaymentCancel'])->name('billing.payfast-cancel');


});


Route::middleware(['auth', config('jetstream.auth_session'), 'verified', SuperAdmin::class, LocaleMiddleware::class])->group(function () {

    Route::name('superadmin.')->group(function () {
        Route::get('super-admin-dashboard', [DashboardController::class, 'superadmin'])->name('dashboard');

        Route::resource('superadmin-settings', SuperadminSettingController::class);

        Route::resource('society', SocietyController::class);

        Route::resource('packages', PackageController::class);

        Route::resource('invoices', BillingController::class);

        Route::get('offline-plan', [BillingController::class, 'offlinePlanRequests'])->name('offline-plan-request');

        Route::post('app-update/deleteFile', [GlobalSettingController::class, 'deleteFile'])->name('app-update.deleteFile');
        Route::resource('app-update', GlobalSettingController::class);
        Route::post('custom-modules/verify-purchase', [CustomModuleController::class, 'verifyingModulePurchase'])->name('custom-modules.verify_purchase');
        Route::resource('custom-modules', CustomModuleController::class)->except(['update']);
        Route::put('custom-modules/{custom_module}', [CustomModuleController::class, 'update'])->withoutMiddleware('csrf')->name('custom-modules.update');

        Route::resource('landing-sites', LandingSiteController::class);

    });

});
Route::view('offline', 'offline');

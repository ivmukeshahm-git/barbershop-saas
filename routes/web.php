<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Platform\SuperAdminController;
use App\Http\Controllers\Saas\SignupController;
use App\Http\Controllers\Website\TrackingController;
use App\Http\Controllers\Website\WebsiteController;
use App\Mail\welcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    return 'All cache cleared';
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'All storage cleared';
});

Route::get('/mail', function () {
    $email = "mukeshsthr690@gmail.com";
    $emailData = [
        'customer_name' => "mukesh",
        'service' => "Demo",
        'staff' => "Mk",
        'date' => "12-12-2025",
        'slot_start' => "10:10",
        'slot_end' => "10:10",
        'email' => "mukeshsthr690@gmail.com",
        'subject' => 'Appointment Confirmation - Rosenthal Barbershop',
    ];
    dd(Mail::to($email)->send(new WelcomeMail($emailData)));
});

/*
|--------------------------------------------------------------------------
| Public booking / website
|--------------------------------------------------------------------------
*/
Route::get('book', [WebsiteController::class, 'index'])->name('book');
Route::post('get-staff-service', [WebsiteController::class, 'get_staff_service'])->name('get-staff-service');
Route::post('get-slot-by-staff', [WebsiteController::class, 'get_slot_by_staff'])->name('get-slot-by-staff');
Route::post('get-appointment-details', [WebsiteController::class, 'get_appointment_details'])->name('get-appointment-details');
Route::post('submit-appointment-details', [WebsiteController::class, 'submit_appointment_details'])->name('submit-appointment-details');

Route::get('track-booking', [TrackingController::class, 'index'])->name('track-booking');
Route::post('track-booking/search', [TrackingController::class, 'search'])->name('track-booking.search');
Route::get('track-booking/{code}', [TrackingController::class, 'show'])->name('track-booking.show');

/*
|--------------------------------------------------------------------------
| SaaS: sell admin panel (home → pricing → shop signup)
|--------------------------------------------------------------------------
*/
Route::get('/', [SignupController::class, 'home'])->name('saas.home');
Route::get('/pricing', [SignupController::class, 'pricing'])->name('saas.pricing');
Route::get('/get-started', [SignupController::class, 'create'])->name('saas.signup');
Route::post('/get-started', [SignupController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('saas.signup.store');
Route::get('/get-started/check-slug', [SignupController::class, 'checkSlug'])->name('saas.check-slug');
Route::get('/get-started/success/{shop}', [SignupController::class, 'success'])->name('saas.success');

/*
|--------------------------------------------------------------------------
| Platform super-admin
|--------------------------------------------------------------------------
*/
Route::prefix('super-admin')->name('platform.')->group(function () {
    Route::get('login', [SuperAdminController::class, 'showLogin'])->name('login');
    Route::post('login', [SuperAdminController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.submit');

    Route::middleware('platform.admin')->group(function () {
        Route::post('logout', [SuperAdminController::class, 'logout'])->name('logout');
        Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('shops', [SuperAdminController::class, 'shops'])->name('shops');
        Route::get('shops/data', [SuperAdminController::class, 'shopsData'])->name('shops.data');
        Route::get('shops/{shop}/plan-history', [SuperAdminController::class, 'shopPlanHistory'])->name('shops.history');
        Route::patch('shops/{shop}/status', [SuperAdminController::class, 'updateShopStatus'])->name('shops.status');
        Route::get('payments', [SuperAdminController::class, 'payments'])->name('payments');
        Route::get('payments/data', [SuperAdminController::class, 'paymentsData'])->name('payments.data');
        Route::get('payments/{subscription}/receipt', [SuperAdminController::class, 'receipt'])->name('payments.receipt');
        Route::get('plans', [SuperAdminController::class, 'plans'])->name('plans');
        Route::patch('plans/{plan}', [SuperAdminController::class, 'updatePlan'])->name('plans.update');
        Route::get('website', [SuperAdminController::class, 'website'])->name('website');
        Route::put('website', [SuperAdminController::class, 'updateWebsite'])->name('website.update');
    });
});

Route::get('/admin-login', [SignupController::class, 'findShop'])->name('saas.find-shop');
Route::post('/admin-login', [SignupController::class, 'resolveShop'])
    ->middleware('throttle:20,1')
    ->name('saas.find-shop.resolve');

// Legacy /admin entry → shop finder (or shop panel if already logged in)
Route::get('/admin', function () {
    if (Auth::check() && Auth::user()->shop_id) {
        $shop = \App\Models\SaasShop::find(Auth::user()->shop_id);
        if ($shop) {
            return redirect()->to($shop->adminHomeUrl());
        }
    }
    return redirect()->route('saas.find-shop');
});
Route::get('/admin/login', function () {
    return redirect()->route('saas.find-shop');
});
Route::any('/admin/{any}', function () {
    if (Auth::check() && Auth::user()->shop_id) {
        $shop = \App\Models\SaasShop::find(Auth::user()->shop_id);
        if ($shop) {
            $path = request()->path(); // admin/...
            $rest = preg_replace('#^admin/?#', '', $path);
            return redirect('/' . $shop->slug . '/admin/' . ltrim($rest, '/'));
        }
    }
    return redirect()->route('saas.find-shop')
        ->with('error', 'Open your shop admin using your shop URL.');
})->where('any', '.*');

/*
|--------------------------------------------------------------------------
| Shop-tenant admin: /{shop}/admin/...
|--------------------------------------------------------------------------
*/
Route::prefix('{shop}')
    ->middleware(['resolve.shop'])
    ->group(function () {
        Route::get('admin/login', [AuthController::class, 'index'])->name('login');
        Route::post('admin/post-login', [AuthController::class, 'postLogin'])->name('post-login');
        Route::post('admin/logout', [AuthController::class, 'logout'])->name('logout');

        Route::prefix('admin/forgot-password')->name('admin.forgot-password')->group(function () {
            Route::get('/', [ForgotPasswordController::class, 'showForgotForm'])->name('');
            Route::post('/', [ForgotPasswordController::class, 'sendOtp'])
                ->middleware('throttle:forgot-password')
                ->name('.submit');

            Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyOtp'])->name('.verify-otp');
            Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])
                ->middleware('throttle:forgot-password-otp')
                ->name('.verify-otp.submit');
            Route::post('/resend-otp', [ForgotPasswordController::class, 'resendOtp'])
                ->middleware('throttle:forgot-password-resend')
                ->name('.resend-otp');

            Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('.reset');
            Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
                ->middleware('throttle:forgot-password')
                ->name('.update');
        });

        Route::middleware(['checklogin', 'shop.user', 'shop.subscription'])
            ->prefix('admin')
            ->as('admin.')
            ->group(base_path('routes/admin.php'));
    });

/*
|--------------------------------------------------------------------------
| Marketing website pages (demo shop public site)
|--------------------------------------------------------------------------
*/
Route::redirect('/home', '/')->name('home');

Route::get('/about', function () {
    $gallary = \App\Models\Gallery::all();
    return view('website.about', compact('gallary'));
})->name('about');

Route::get('/services', function () {
    $servicesCategory = \App\Models\ServiceCategory::query()
        ->with(['services' => function ($query) {
            $query->orderBy('service_name');
        }])
        ->orderBy('id')
        ->get()
        ->filter(function ($category) {
            return $category->services->isNotEmpty();
        })
        ->values();

    return view('website.services', compact('servicesCategory'));
})->name('services');

Route::get('/barbers', function () {
    $barebers = \App\Models\User::barbers()
        ->forCurrentShop()
        ->where('active_status', 1)
        ->whereRaw('LOWER(TRIM(name)) NOT LIKE ?', ['first available%'])
        ->orderBy('name')
        ->get()
        ->map(function ($barber) {
            $serviceIds = \App\Models\StaffService::idsForStaff($barber->id);
            $barber->expertise = \App\Models\Service::query()
                ->whereIn('id', $serviceIds)
                ->where('status', 1)
                ->orderBy('service_name')
                ->pluck('service_name');

            return $barber;
        });

    return view('website.barbers', compact('barebers'));
})->name('barbers');

Route::redirect('/gallery', '/about#gallery')->name('gallery');

Route::get('/contact', function () {
    $slot = \App\Models\Slot::where('staff_id', 0)->get();
    return view('website.contact', compact('slot'));
})->name('contact');
Route::post('/contact', [WebsiteController::class, 'sendContactMessage'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

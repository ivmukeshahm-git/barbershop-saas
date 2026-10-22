<?php

namespace App\Providers;

use App\Models\Gst;
use App\Models\PlatformSetting;
use App\Models\Setting;
use App\Support\CountryCurrency;
use App\Support\CurrentShop;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $helpers = app_path('helpers.php');
        if (is_file($helpers)) {
            require_once $helpers;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('saas.layout', function ($view) {
            $settings = Schema::hasTable('platform_settings')
                ? PlatformSetting::values()
                : [];

            $view->with('platformSiteSettings', $settings);
        });

        View::composer('*', function ($view) {
            try {
                $shop = CurrentShop::get();
                $currencyCode = ($shop && !empty($shop->currency))
                    ? strtoupper((string) $shop->currency)
                    : 'INR';
                $view->with('shopCurrency', $currencyCode);
                $view->with('shopCurrencySymbol', CountryCurrency::symbol($currencyCode));

                if (Schema::hasTable('tbl_setting')) {
                    $siteSetting = CurrentShop::id()
                        ? Setting::query()->first()
                        : Setting::withoutShopScope()->orderBy('id')->first();
                    $view->with('siteSetting', $siteSetting);
                } else {
                    $view->with('siteSetting', null);
                }

                if (Schema::hasTable('tbl_gst')) {
                    $view->with('defaultGst', Gst::where('default_status', 1)->first());
                } else {
                    $view->with('defaultGst', null);
                }
            } catch (\Throwable $e) {
                $view->with('siteSetting', null);
                $view->with('defaultGst', null);
                $view->with('shopCurrency', 'INR');
                $view->with('shopCurrencySymbol', CountryCurrency::symbol('INR'));
            }
        });
    }
}

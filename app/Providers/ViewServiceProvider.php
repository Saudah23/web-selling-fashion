<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\SystemSetting;
use App\Models\Province;
use App\Models\City;
use App\Models\District;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share global data to all views
        View::composer('*', function ($view) {
            // Categories for navigation
            $globalCategories = Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();

            // System settings for footer
            $systemSettings = [
                'app_name' => SystemSetting::get('app_name', 'Fashion Marketplace'),
                'app_description' => SystemSetting::get('app_description', 'Premium fashion marketplace for modern clothing'),
                'contact_email' => SystemSetting::get('contact_email'),
                'contact_phone' => SystemSetting::get('contact_phone'),
                'business_hours' => SystemSetting::get('business_hours'),
            ];

            // Get complete address from location models
            $shippingAddress = null;
            $provinceId = SystemSetting::get('shipping_origin_province_id');
            $cityId = SystemSetting::get('shipping_origin_city_id');
            $districtId = SystemSetting::get('shipping_origin_district_id');
            $streetAddress = SystemSetting::get('shipping_origin_address');

            if ($provinceId && $cityId && $districtId) {
                $province = Province::find($provinceId);
                $city = City::find($cityId);
                $district = District::find($districtId);

                if ($province && $city && $district) {
                    $shippingAddress = [
                        'street' => $streetAddress ?: 'Jl. Raya Marketplace No. 123',
                        'district' => $district->name,
                        'city' => $city->name,
                        'province' => $province->name,
                        'full_address' => ($streetAddress ?: 'Jl. Raya Marketplace No. 123') .
                                       ', ' . $district->name .
                                       ', ' . $city->name .
                                       ', ' . $province->name
                    ];
                }
            }

            // Fallback address if no location data
            if (!$shippingAddress) {
                $shippingAddress = [
                    'street' => 'Jl. Raya Marketplace No. 123',
                    'district' => 'Banjarmasin Tengah',
                    'city' => 'Banjarmasin',
                    'province' => 'Kalimantan Selatan',
                    'full_address' => 'Jl. Raya Marketplace No. 123, Banjarmasin Tengah, Banjarmasin, Kalimantan Selatan'
                ];
            }

            $view->with([
                'globalCategories' => $globalCategories,
                'systemSettings' => $systemSettings,
                'shippingAddress' => $shippingAddress
            ]);
        });
    }
}

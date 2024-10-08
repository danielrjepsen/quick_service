<?php

namespace App\Http\Middleware;

use App\Models\Organisation;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $data = [
            'stripe_publishable_Key' => env('STRIPE_KEY', ""),
            'fallbackLocale' => app()->getFallbackLocale(),
            'locale' => app()->getLocale()
        ];
        if (Tenant::checkCurrent()) {
            $organisation = Organisation::first();

            $data = array_merge([
                'organisation' => $organisation,
                'trial' => $organisation->onTrial(),
                'trial_days' => Carbon::parse($organisation->trial_ends_at)->diffInDays(now()),
            ], $data);
        }
        return array_merge(parent::share($request), $data);
    }
}

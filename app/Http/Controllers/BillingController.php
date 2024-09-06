<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function show()
    {
        $organisation = Organisation::first();
        $usersCount = 0;
        if (Tenant::checkCurrent()) {
            $usersCount = User::where('tenant_id', Tenant::current()->id)->count();
        }
        $plans = [
            [
                'id' => 1,
                'price' => null,
                'name' => 'Free',
                'description' => 'All features, when doing less than 500 services each month',
            ],
            [
                'id' => 'price_1ID21kFHzIACECPxmTCf1UTp',
                'yearly' => 'price_1IHPTXFHzIACECPxxv74WN6r',
                'price' => 249,
                'name' => 'Professional',
                'description' => 'All features unlocked!',
            ],
        ];
        return Inertia::render('Billing', [
            'intent' => $organisation->createSetupIntent(),
            'user_count' => $usersCount + 5,
            'free_monthly' => 500,
            'plans' => $plans,
            'services_this_month' => Service::count(),
            'stripe_key' => config('cashier.key'),
        ]);
    }

    public function process(Request $request)
    {
        $organisation = Organisation::first();

        $subscription = $organisation->newSubscription('professional', $request->plan_id)
            ->quantity(User::where('tenant_id', Tenant::current()->id)->count() + 5);
        if ($request->promo_code) {
            $subscription->withPromotionCode($request->promo_code);
        }
        $subscription->create($request->payment_method);

        $organisation->updateStripeCustomer([
            'email' => $request->billing_email,
        ]);

        $organisation->trial_ends_at = null;
        $organisation->save();

        return redirect('dashboard');
    }
}

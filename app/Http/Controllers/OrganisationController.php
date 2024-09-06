<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganisationController extends Controller
{
    public function updateBilling(Request $request)
    {
        $organisation = Organisation::first();
        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'billing_email' => ['nullable', 'email', 'max:255'],
            'billing_info' => ['nullable', 'string'],
        ])->validateWithBag('updateOrganisation');

        $organisation->fill([
            'name' => $input['name'],
            'billing_email' => $input['billing_email'],
            'billing_info' => $input['billing_info'],
        ])->save();

        return back(303);
    }

    public function updateTwoFactor(Request $request)
    {
        $organisation = Organisation::first();
        $input = $request->all();

        Validator::make($input, [
            'require2FA' => ['required', 'boolean']
        ])->validateWithBag('updateTwoFactor');

        $organisation->require2FA = $input['require2FA'];
        $organisation->save();

        return back(303);
    }
}

<?php

namespace App\Http\Responses;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Illuminate\Support\Facades\Auth;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $tenant_id = Auth::user()->tenant_id;
        $domain = Tenant::findOrFail($tenant_id)->domain;

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : Inertia::location(request()->getScheme() . '://' . $domain);
    }
}

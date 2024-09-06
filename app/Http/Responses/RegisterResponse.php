<?php

namespace App\Http\Responses;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $tenant_id = User::where('email', $request->email)->first()->tenant_id;
        $domain = Tenant::findOrFail($tenant_id)->domain;
        return $request->wantsJson()
        ? new JsonResponse('', 201)
        : Inertia::location(request()->getScheme() . '://' . $domain);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\TeamInvitation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Dashboard');
    }

    public function teamInvitationsRegister(Request $request, TeamInvitation $invitation)
    {
        $organisation = Organisation::first();
        return Inertia::render('Auth/Register', ['invitation' => $invitation, 'organisation' => $organisation->name]);
    }

    public function settings(Request $request)
    {
        $user = Auth::user();
        $team = $user->currentTeam;

        $team->load(
            [
                'owner',
                'teamInvitations',
                'users'
            ]);

        foreach ($team['users'] as $user) {
            $user['two_factor_enabled'] = ! is_null($user['two_factor_secret']);
        }

        function getAllUsers() {
            $allOrganisationUsers = User::where('tenant_id', Tenant::current()->id)->get();
            foreach ($allOrganisationUsers as $user) {
                $user['two_factor_enabled'] = ! is_null($user['two_factor_secret']);
            }
            return $allOrganisationUsers;
        }

        return Inertia::render('Settings',
            [
                'organisation' => Organisation::first(),
                'team' => $team,
                'availableRoles' => array_values(Jetstream::$roles),
                'permissions' => [
                    'canAddTeamMembers' => Gate::check('addTeamMember', $team),
                    'canDeleteTeam' => Gate::check('delete', $team),
                    'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                    'canUpdateTeam' => Gate::check('update', $team),
                ],
                'allOrganisationUsers' => Inertia::lazy(fn () => getAllUsers())
            ]

        );
    }
}

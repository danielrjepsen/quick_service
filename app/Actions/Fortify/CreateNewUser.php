<?php

namespace App\Actions\Fortify;

use App\Models\Organisation;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:landlord.users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        if (isset($input['invitation']) && $input['invitation'] !== null) {

            Validator::make($input, [
                'email' => ['exists:tenant.team_invitations']
            ], ['email.exists' => __('We were unable to find a invitation with this email address.')]
            )->validate();

            $invitation = TeamInvitation::findOrFail($input['invitation']);
            $tenant = Tenant::current();

            return DB::transaction(function () use ($input, $tenant, $invitation) {
                User::create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => Hash::make($input['password']),
                    'tenant_id' => $tenant->id,
                    'current_team_id' => $invitation->team_id,
                    'role' => $invitation->role
                ]);

                app(AddsTeamMembers::class)->add(
                    $invitation->team->owner,
                    $invitation->team,
                    $invitation->email,
                    $invitation->role
                );
                $invitation->delete();
            });
        }

        $slug = Str::of($input['organisation'])->slug('-');
        $input2 = [
            'organisation' => (String) $slug,
        ];
        Validator::make($input2, [
            'organisation' => ['required', 'string', 'max:255', 'unique:landlord.tenants,name'],
        ])->validate();

        return DB::transaction(function () use ($input, $slug) {
            $tenant = new Tenant();
            $tenant->name = $slug;
            $tenant->domain = $slug . '.' . request()->getHost();
            $tenant->database = Str::of($input['organisation'])->slug('_');
            $tenant->save();

            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'tenant_id' => $tenant->id,
                'role' => 'admin'
            ]), function (User $user) use ($input) {
                $this->createTeam($user, $input['organisation']);
                $organisation = new Organisation();
                $organisation->name = $input['organisation'];
                $organisation->save();
            });
        });
    }

    /**
     * Create a personal team for the user.
     *
     * @param  \App\Models\User  $user
     * @param  String  $organisation
     *
     * @return void
     */
    protected function createTeam(User $user, String $organisation)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => $organisation,
            'personal_team' => true,
        ]));
    }
}

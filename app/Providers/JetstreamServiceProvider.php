<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamMemberRole;
use App\Actions\Jetstream\UpdateTeamName;
use App\Http\Responses\LogoutResponse;
use App\Http\Responses\RegisterResponse;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Http\Responses\LogoutResponse as FortifyLogoutResponse;
use Laravel\Jetstream\Actions\UpdateTeamMemberRole as JetUpdateTeamMemberRole;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        $this->app->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );

        $this->app->singleton(
            \Laravel\Fortify\Contracts\TwoFactorLoginResponse::class,
            \App\Http\Responses\TwoFactorLoginResponse::class
        );

        $this->app->singleton(
            \Laravel\Jetstream\TeamInvitation::class,
            \App\Models\TeamInvitation::class
        );
        $this->app->singleton(
            \Laravel\Jetstream\Http\Controllers\TeamInvitationController::class,
            \App\Http\Controllers\Jetstream\TeamInvitationController::class
        );
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
        $this->app->singleton(JetUpdateTeamMemberRole::class, UpdateTeamMemberRole::class);
        $this->app->singleton(FortifyLogoutResponse::class, LogoutResponse::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', __('Administrator'), [
            'create',
            'read',
            'update',
            'delete',
        ])->description(__('Administrator users can perform any action.'));

        Jetstream::role('editor', __('Editor'), [
            'read',
            'create',
            'update',
        ])->description(__('Editor users have the ability to read, create, and update.'));
    }
}

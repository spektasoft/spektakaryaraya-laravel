<?php

namespace App\Policies;

use App\Models\MonitoredSite;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MonitoredSitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MonitoredSite $monitoredSite): bool
    {
        if ($user->isNot($monitoredSite->creator) && ! $this->viewAll($user)) {
            return false;
        }

        return $user->can('view_monitored::site');
    }

    /**
     * Determine whether the user can view all models.
     */
    public function viewAll(User $user): bool
    {
        return $user->can('view_all_monitored::site');
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_monitored::site');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_monitored::site');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MonitoredSite $monitoredSite): bool
    {
        if ($user->isNot($monitoredSite->creator) && ! $this->viewAll($user)) {
            return false;
        }

        return $user->can('update_monitored::site');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MonitoredSite $monitoredSite): bool
    {
        if ($monitoredSite->isReferenced()) {
            return false;
        }

        if ($user->isNot($monitoredSite->creator) && ! $this->viewAll($user)) {
            return false;
        }

        return $user->can('delete_monitored::site');
    }
}

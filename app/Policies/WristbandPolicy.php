<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wristband;
use Illuminate\Auth\Access\HandlesAuthorization;

class WristbandPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_wristband');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Wristband $wristband): bool
    {
        return $user->can('view_wristband');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_wristband');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Wristband $wristband): bool
    {
        return $user->can('update_wristband');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wristband $wristband): bool
    {
        return $user->can('delete_wristband');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_wristband');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Wristband $wristband): bool
    {
        return $user->can('force_delete_wristband');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_wristband');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Wristband $wristband): bool
    {
        return $user->can('restore_wristband');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_wristband');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Wristband $wristband): bool
    {
        return $user->can('replicate_wristband');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_wristband');
    }
}

<?php

namespace App\Policies;

use App\Models\Wristband;
use App\Models\Ticket;
use App\Models\User;

class WristbandPolicy
{
    /**
     * Determine if the user can exchange a ticket for wristband
     */
    public function exchange(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('wristband_exchange_officer')) {
            // Officer can exchange for any event
            // In production, you might want to restrict to assigned events
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can validate a wristband for entry
     */
    public function validate(User $user, Wristband $wristband): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('wristband_validator')) {
            // Validator can validate for any event
            // In production, you might want to restrict to assigned events
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can reissue a wristband
     */
    public function reissue(User $user, Wristband $wristband): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('wristband_exchange_officer')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can view wristband details
     */
    public function view(User $user, Wristband $wristband): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('client')) {
            return $user->client_id === $wristband->ticket->order->event->client_id;
        }

        return false;
    }
}

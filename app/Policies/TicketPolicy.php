<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine if the user can view any tickets
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'client']);
    }

    /**
     * Determine if the user can view the ticket
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('client')) {
            return $user->client_id === $ticket->order->event->client_id;
        }

        return false;
    }

    /**
     * Tickets are generated automatically, not created manually
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine if the user can update the ticket
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can delete the ticket
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('super_admin');
    }
}

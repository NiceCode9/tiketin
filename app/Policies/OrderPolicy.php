<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view any orders
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'client']);
    }

    /**
     * Determine if the user can view the order
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('client')) {
            return $user->client_id === $order->event->client_id;
        }

        return false;
    }

    /**
     * Orders are created by consumers, not admin users
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine if the user can update the order
     */
    public function update(User $user, Order $order): bool
    {
        // Only super admin can update orders (e.g., manual refunds)
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can delete the order
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('super_admin');
    }
}

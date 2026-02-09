<?php

namespace App\Policies;

use App\Models\PromoCode;
use App\Models\User;

class PromoCodePolicy
{
    /**
     * Determine if the user can view any promo codes
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'client']);
    }

    /**
     * Determine if the user can view the promo code
     */
    public function view(User $user, PromoCode $promoCode): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('client')) {
            return $user->client_id === $promoCode->event->client_id;
        }

        return false;
    }

    /**
     * Determine if the user can create promo codes
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'client']);
    }

    /**
     * Determine if the user can update the promo code
     */
    public function update(User $user, PromoCode $promoCode): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('client')) {
            return $user->client_id === $promoCode->event->client_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the promo code
     */
    public function delete(User $user, PromoCode $promoCode): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($user->hasRole('client')) {
            return $user->client_id === $promoCode->event->client_id;
        }

        return false;
    }
}

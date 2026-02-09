<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClientScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Only apply if user is authenticated and has client role
        if (auth()->check() && auth()->user()->hasRole('client')) {
            $builder->whereHas('event.client', function ($query) {
                $query->where('client_id', auth()->user()->client_id);
            });
        }
        
        // For models that directly have client_id
        if ($model->getTable() === 'events') {
            if (auth()->check() && auth()->user()->hasRole('client')) {
                $builder->where('client_id', auth()->user()->client_id);
            }
        }
    }
}

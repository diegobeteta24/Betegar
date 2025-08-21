<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    public function view(User $user, WorkOrder $order): bool
    {
        return $user->hasRole('admin') || $order->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, WorkOrder $order): bool
    {
        return $user->hasRole('admin') || $order->user_id === $user->id;
    }

    public function delete(User $user, WorkOrder $order): bool
    {
        return $user->hasRole('admin');
    }
}

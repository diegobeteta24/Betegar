<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FundTransfer;

class FundTransferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, FundTransfer $transfer): bool
    {
        return $user->hasRole('admin');
    }
}

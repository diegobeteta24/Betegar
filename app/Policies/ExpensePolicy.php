<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Expense;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('technician');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('technician');
    }
}

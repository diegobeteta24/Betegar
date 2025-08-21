<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TechnicianSession;

class TechnicianSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}

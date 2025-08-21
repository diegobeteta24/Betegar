<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TechnicianSessionLocation;

class TechnicianSessionLocationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}

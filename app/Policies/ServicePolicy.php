<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ServicePolicy
{
    public function viewAny(User $user): bool { return $user->can('service.view'); }
    public function view(User $user, Product $service): bool { return $user->can('service.view'); }
    public function create(User $user): bool { return $user->can('service.create'); }
    public function update(User $user, Product $service): bool { return $user->can('service.update'); }
    public function delete(User $user, Product $service): bool { return $user->can('service.delete'); }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\WorkOrder;
use App\Policies\WorkOrderPolicy;
use App\Models\Expense;
use App\Policies\ExpensePolicy;
use App\Models\FundTransfer;
use App\Policies\FundTransferPolicy;
use App\Models\TechnicianSession;
use App\Policies\TechnicianSessionPolicy;
use App\Models\TechnicianSessionLocation;
use App\Policies\TechnicianSessionLocationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        WorkOrder::class => WorkOrderPolicy::class,
    Expense::class   => ExpensePolicy::class,
    FundTransfer::class => FundTransferPolicy::class,
    TechnicianSession::class => TechnicianSessionPolicy::class,
    TechnicianSessionLocation::class => TechnicianSessionLocationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

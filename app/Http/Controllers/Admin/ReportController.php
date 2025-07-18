<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function topProducts()
    {
        return view('admin.reports.top-products');
    }

    public function topCustomers()
    {
        return view('admin.reports.top-customers');
    }

    public function lowStock()
    {
        return view('admin.reports.low-stock');
    }
}

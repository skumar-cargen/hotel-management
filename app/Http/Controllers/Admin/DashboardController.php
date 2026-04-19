<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index()
    {
        $data = $this->dashboardService->getAllDashboardData();

        return view('admin.dashboard', $data);
    }
}

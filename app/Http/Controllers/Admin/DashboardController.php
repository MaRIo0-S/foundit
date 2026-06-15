<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => $this->dashboard->stats(),
            'itemsByCategory' => $this->dashboard->itemsByCategory(),
            'itemsByLocation' => $this->dashboard->itemsByLocation(),
            'recentItems' => $this->dashboard->recentItems(),
            'pendingClaims' => $this->dashboard->pendingClaims(),
            'recentActivity' => $this->dashboard->recentActivity(),
            'declarationsTrend' => $this->dashboard->declarationsTrend(),
            'staleItems' => $this->dashboard->staleItems(),
        ]);
    }
}

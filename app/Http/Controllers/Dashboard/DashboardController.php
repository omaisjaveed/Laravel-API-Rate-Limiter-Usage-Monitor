<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetrics;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetrics $metrics,
    ) {}

    public function overview(): View
    {
        $stats = $this->metrics->overview();

        return view('dashboard.overview', $stats);
    }
}

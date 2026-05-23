<?php

namespace App\Http\Controllers;

use App\Services\DashboardStatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $filter = request('filter', 'this_month');
        $userId = auth()->id();

        $stats = new DashboardStatsService($userId, $filter);

        return view('dashboard.index', [
            'greeting' => $stats->getGreeting(),
            'financialMessage' => $stats->getFinancialMessage(),
            'summaryCards' => $stats->getSummaryCards(),
            'cashflowData' => $stats->getCashflowData(),
            'expensesByCategory' => $stats->getExpensesByCategory(),
            'accounts' => $stats->getAccounts(),
            'budgetsStatus' => $stats->getBudgetsStatus(),
            'goalsProgress' => $stats->getGoalsProgress(),
            'debtsOverview' => $stats->getDebtsOverview(),
            'recentOperations' => $stats->getRecentOperations(),
            'insights' => $stats->getInsights(),
            'currentFilter' => $filter,
        ]);
    }
}
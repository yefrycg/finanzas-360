<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Goal;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_dashboard_loads_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
    }

    public function test_dashboard_shows_greeting(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('greeting');
        $response->assertViewHas('financialMessage');
    }

    public function test_dashboard_shows_summary_cards(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('summaryCards');
        $this->assertIsArray($response->viewData('summaryCards'));
    }

    public function test_dashboard_shows_cashflow_chart_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('cashflowData');
        $cashflow = $response->viewData('cashflowData');
        $this->assertArrayHasKey('labels', $cashflow);
        $this->assertArrayHasKey('income', $cashflow);
        $this->assertArrayHasKey('expenses', $cashflow);
    }

    public function test_dashboard_shows_expenses_by_category(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('expensesByCategory');
        $expenses = $response->viewData('expensesByCategory');
        $this->assertArrayHasKey('categories', $expenses);
        $this->assertArrayHasKey('total', $expenses);
    }

    public function test_dashboard_shows_accounts_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('accounts');
        $accounts = $response->viewData('accounts');
        $this->assertArrayHasKey('accounts', $accounts);
        $this->assertArrayHasKey('total_balance', $accounts);
    }

    public function test_dashboard_shows_budgets_status(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('budgetsStatus');
    }

    public function test_dashboard_shows_goals_progress(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('goalsProgress');
    }

    public function test_dashboard_shows_debts_overview(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('debtsOverview');
        $debts = $response->viewData('debtsOverview');
        $this->assertArrayHasKey('total_debt', $debts);
        $this->assertArrayHasKey('paid_amount', $debts);
        $this->assertArrayHasKey('remaining_debt', $debts);
        $this->assertArrayHasKey('paid_debts', $debts);
        $this->assertArrayHasKey('pending_debts', $debts);
        $this->assertArrayHasKey('recent_debts', $debts);
    }

    public function test_dashboard_shows_recent_operations(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('recentOperations');
    }

    public function test_dashboard_shows_insights(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewHas('insights');
    }

    public function test_dashboard_filters_by_user_data_only(): void
    {
        $user2 = User::factory()->create();
        Account::factory()->create(['user_id' => $this->user->id, 'name' => 'User1 Account', 'current_balance' => 1000]);
        Account::factory()->create(['user_id' => $user2->id, 'name' => 'User2 Account', 'current_balance' => 500]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $accounts = $response->viewData('accounts')['accounts'];
        $this->assertEquals(1, $accounts->count());
        $this->assertEquals('User1 Account', $accounts->first()['name']);
    }

    public function test_dashboard_calculates_correct_total_balance(): void
    {
        Account::factory()->create(['user_id' => $this->user->id, 'current_balance' => 1000]);
        Account::factory()->create(['user_id' => $this->user->id, 'current_balance' => 2000]);
        Account::factory()->create(['user_id' => $this->user->id, 'current_balance' => 500]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $summary = $response->viewData('summaryCards');
        $this->assertEquals(3500.00, $summary['total_balance']['value']);
    }

    public function test_dashboard_calculates_income_and_expenses(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'income', 'color' => '#22c55e', 'icon' => 'fas fa-dollar']);
        $account = Account::factory()->create(['user_id' => $this->user->id, 'current_balance' => 10000]);

        Operation::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id, 'account_id' => $account->id, 'type' => 'income', 'amount' => 5000, 'date_time' => now()]);
        Operation::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id, 'account_id' => $account->id, 'type' => 'expense', 'amount' => 1500, 'date_time' => now()]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $summary = $response->viewData('summaryCards');
        $this->assertEquals(5000.00, $summary['monthly_income']['value']);
        $this->assertEquals(1500.00, $summary['monthly_expenses']['value']);
        $this->assertEquals(3500.00, $summary['net_savings']['value']);
    }

    public function test_dashboard_counts_active_budgets(): void
    {
        Budget::factory()->create(['user_id' => $this->user->id]);
        Budget::factory()->create(['user_id' => $this->user->id]);
        Budget::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $summary = $response->viewData('summaryCards');
        $this->assertEquals(3, $summary['active_budgets']['value']);
    }

    public function test_dashboard_counts_pending_goals(): void
    {
        $cat1 = Category::factory()->create(['user_id' => $this->user->id]);
        $cat2 = Category::factory()->create(['user_id' => $this->user->id]);
        $cat3 = Category::factory()->create(['user_id' => $this->user->id]);

        Goal::factory()->create(['user_id' => $this->user->id, 'category_id' => $cat1->id, 'current_amount' => 0, 'target_amount' => 1000]);
        Goal::factory()->create(['user_id' => $this->user->id, 'category_id' => $cat2->id, 'current_amount' => 500, 'target_amount' => 1000]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $summary = $response->viewData('summaryCards');
        $this->assertEquals(2, $summary['active_goals']['value']);
    }

    public function test_dashboard_counts_pending_debts(): void
    {
        $debt1 = Debt::factory()->create(['user_id' => $this->user->id, 'paid_amount' => 0, 'total_amount' => 1000]);
        $debt2 = Debt::factory()->create(['user_id' => $this->user->id, 'paid_amount' => 500, 'total_amount' => 1000]);
        $debt3 = Debt::factory()->create(['user_id' => $this->user->id, 'paid_amount' => 1000, 'total_amount' => 1000]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $summary = $response->viewData('summaryCards');
        $this->assertEquals(2, $summary['pending_debts']['value']);
    }

    public function test_dashboard_supports_filter_parameter(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index', ['filter' => 'this_year']));
        $response->assertStatus(200);
        $response->assertViewHas('currentFilter', 'this_year');
    }

    public function test_dashboard_cashflow_shows_six_months(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $cashflow = $response->viewData('cashflowData');
        $this->assertCount(6, $cashflow['labels']);
        $this->assertCount(6, $cashflow['income']);
        $this->assertCount(6, $cashflow['expenses']);
    }

    public function test_dashboard_debts_overview_calculates_totals(): void
    {
        Debt::factory()->create(['user_id' => $this->user->id, 'total_amount' => 1000, 'paid_amount' => 400, 'status' => 'no_paid']);
        Debt::factory()->create(['user_id' => $this->user->id, 'total_amount' => 500, 'paid_amount' => 500, 'status' => 'paid']);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $debts = $response->viewData('debtsOverview');
        $this->assertEquals(1500.00, $debts['total_debt']);
        $this->assertEquals(900.00, $debts['paid_amount']);
        $this->assertEquals(600.00, $debts['remaining_debt']);
        $this->assertEquals(1, $debts['paid_debts']);
        $this->assertEquals(1, $debts['pending_debts']);
    }

    public function test_dashboard_recent_operations_structure(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id, 'color' => '#6b7280', 'icon' => 'fas fa-circle']);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        Operation::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id, 'account_id' => $account->id, 'type' => 'income', 'amount' => 100, 'date_time' => now()]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $ops = $response->viewData('recentOperations');
        $op = $ops->first();
        $this->assertArrayHasKey('id', $op);
        $this->assertArrayHasKey('amount', $op);
        $this->assertArrayHasKey('type', $op);
        $this->assertArrayHasKey('category', $op);
        $this->assertArrayHasKey('account', $op);
    }

    public function test_dashboard_insights_generate_with_data(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-fire']);
        $account = Account::factory()->create(['user_id' => $this->user->id, 'current_balance' => 10000]);

        Operation::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id, 'account_id' => $account->id, 'type' => 'expense', 'amount' => 200, 'date_time' => now()]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $insights = $response->viewData('insights');
        $this->assertIsArray($insights);
    }

    public function test_dashboard_expenses_by_category_groups_correctly(): void
    {
        $cat1 = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense', 'color' => '#22c55e', 'icon' => 'fas fa-apple']);
        $cat2 = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense', 'color' => '#3b82f6', 'icon' => 'fas fa-car']);
        $account = Account::factory()->create(['user_id' => $this->user->id, 'current_balance' => 10000]);

        Operation::factory()->create(['user_id' => $this->user->id, 'category_id' => $cat1->id, 'account_id' => $account->id, 'type' => 'expense', 'amount' => 200, 'date_time' => now()->startOfMonth()]);
        Operation::factory()->create(['user_id' => $this->user->id, 'category_id' => $cat1->id, 'account_id' => $account->id, 'type' => 'expense', 'amount' => 100, 'date_time' => now()->startOfMonth()]);
        Operation::factory()->create(['user_id' => $this->user->id, 'category_id' => $cat2->id, 'account_id' => $account->id, 'type' => 'expense', 'amount' => 150, 'date_time' => now()->startOfMonth()]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $expenses = $response->viewData('expensesByCategory');
        $this->assertEquals(2, $expenses['categories']->count());
        $this->assertEquals(450.00, $expenses['total']);
    }

    public function test_dashboard_budgets_status_calculates_spent(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense', 'color' => '#9333ea', 'icon' => 'fas fa-star']);
        $budget = Budget::factory()->create(['user_id' => $this->user->id, 'limit_amount' => 500, 'period' => 'monthly']);
        $budget->categories()->attach($category->id);
        $account = Account::factory()->create(['user_id' => $this->user->id, 'current_balance' => 10000]);

        Operation::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id, 'account_id' => $account->id, 'type' => 'expense', 'amount' => 200, 'date_time' => now()->startOfMonth()]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $budgets = $response->viewData('budgetsStatus');
        $this->assertEquals(200.00, $budgets->first()['spent_amount']);
        $this->assertEquals(300.00, $budgets->first()['remaining']);
        $this->assertEquals(40.0, $budgets->first()['percentage']);
    }

    public function test_dashboard_goals_progress_calculates_correctly(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        Goal::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id, 'target_amount' => 1000, 'current_amount' => 350]);

        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $goals = $response->viewData('goalsProgress');
        $this->assertEquals(350.00, $goals->first()['current_amount']);
        $this->assertEquals(1000.00, $goals->first()['target_amount']);
        $this->assertEquals(650.00, $goals->first()['remaining_amount']);
        $this->assertEquals(35.0, $goals->first()['progress']);
    }

    public function test_dashboard_returns_401_for_unauthenticated(): void
    {
        $response = $this->get(route('dashboard.index'));
        $response->assertRedirect('/login');
    }

    public function test_greeting_is_valid(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.index'));
        $response->assertStatus(200);
        $greeting = $response->viewData('greeting');
        $this->assertContains($greeting, ['Buenos días', 'Buenas tardes', 'Buenas noches']);
    }
}
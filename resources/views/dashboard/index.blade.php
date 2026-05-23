@extends('layouts.dashboard')

@push('styles')
  <style>
    .stat-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .chart-container {
      position: relative;
      min-height: 300px;
    }
    .account-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .account-card:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.1);
    }
    .progress-bar-animated {
      transition: width 1s ease-in-out;
    }
    .insight-card {
      transition: border-color 0.2s ease;
    }
    .insight-card:hover {
      border-color: #3b82f6;
    }
    .filter-btn.active {
      background-color: #2563eb;
      color: white;
    }
    @media (max-width: 768px) {
      .chart-container {
        min-height: 250px;
      }
    }
  </style>
@endpush

@section('content')
  {{-- Header Section --}}
  <div class="mb-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
            <i class="fas fa-chart-pie text-white text-lg"></i>
          </div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
            {{ $greeting }}, {{ auth()->user()->name }} <span class="inline-block"></span>
          </h1>
        </div>
        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium mt-1">{{ $financialMessage }}</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @foreach (['today' => 'Hoy', 'this_week' => 'Esta Semana', 'this_month' => 'Este Mes', 'this_year' => 'Este Año'] as $filterKey => $filterLabel)
          <a href="{{ route('dashboard.index', ['filter' => $filterKey]) }}"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
              {{ $currentFilter === $filterKey
                  ? 'bg-primary-700 text-white shadow-md shadow-primary-700/30'
                  : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
            {{ $filterLabel }}
          </a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Summary Cards Section --}}
  <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-6">
    @foreach ($summaryCards as $key => $card)
      @php
        $icons = [
            'total_balance' => ['icon' => 'fa-solid fa-wallet', 'gradient' => 'from-blue-500 to-blue-600'],
            'monthly_income' => ['icon' => 'fa-solid fa-arrow-trend-up', 'gradient' => 'from-green-500 to-emerald-600'],
            'monthly_expenses' => ['icon' => 'fa-solid fa-arrow-trend-down', 'gradient' => 'from-red-500 to-rose-600'],
            'net_savings' => ['icon' => 'fa-solid fa-piggy-bank', 'gradient' => $card['positive'] ? 'from-emerald-500 to-teal-600' : 'from-orange-500 to-amber-600'],
            'active_budgets' => ['icon' => 'fa-solid fa-chart-pie', 'gradient' => 'from-purple-500 to-violet-600'],
            'active_goals' => ['icon' => 'fa-solid fa-bullseye', 'gradient' => 'from-cyan-500 to-sky-600'],
            'pending_debts' => ['icon' => 'fa-solid fa-credit-card', 'gradient' => 'from-orange-500 to-red-600'],
            'total_accounts' => ['icon' => 'fa-solid fa-building-columns', 'gradient' => 'from-indigo-500 to-blue-600'],
        ];
        $iconData = $icons[$key] ?? ['icon' => 'fa-solid fa-circle', 'gradient' => 'from-gray-500 to-gray-600'];
      @endphp
      <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $card['label'] }}</p>
            <p class="text-xl md:text-2xl font-bold mt-1 {{ $card['positive'] ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400' }}">
              @if (in_array($key, ['total_balance', 'monthly_income', 'monthly_expenses', 'net_savings']))
                ${{ number_format($card['value'], 2) }}
              @else
                {{ $card['value'] }}
              @endif
            </p>
          </div>
          <div class="w-10 h-10 rounded-lg bg-linear-to-br {{ $iconData['gradient'] }} flex items-center justify-center shadow-md">
            <i class="{{ $iconData['icon'] }} text-white text-sm"></i>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Cashflow Chart & Expenses by Category --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Cashflow Chart --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Ingresos vs Gastos</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Resumen de los últimos 6 meses</p>
        </div>
        <div class="flex items-center gap-4 text-sm">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-500"></span>
            <span class="text-gray-600 dark:text-gray-400">Ingresos</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            <span class="text-gray-600 dark:text-gray-400">Gastos</span>
          </div>
        </div>
      </div>
      <div class="chart-container">
        <canvas id="cashflowChart"></canvas>
      </div>
    </div>

    {{-- Expenses by Category --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
      <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Gastos por Categoría</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Donde va tu dinero</p>
      </div>
      <div class="chart-container flex items-center justify-center">
        <canvas id="expensesChart"></canvas>
      </div>
      @if ($expensesByCategory['categories']->count() > 0)
        <div class="mt-4 space-y-2 max-h-40 overflow-y-auto">
          @foreach ($expensesByCategory['categories']->take(5) as $cat)
            <div class="flex items-center justify-between text-sm">
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $cat['color'] }}"></span>
                <span class="text-gray-700 dark:text-gray-300 truncate">{{ $cat['name'] }}</span>
              </div>
              <span class="text-gray-500 dark:text-gray-400">{{ $cat['percentage'] }}%</span>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- Accounts Overview --}}
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Resumen de Cuentas</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Tus cuentas financieras</p>
      </div>
      <a href="{{ route('dashboard.accounts.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
        Ver todas <i class="fas fa-arrow-right ml-1"></i>
      </a>
    </div>

    @if ($accounts['accounts']->count() > 0)
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        @foreach ($accounts['accounts'] as $account)
          <div class="account-card bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $account['color'] }}20">
                <i class="{{ $account['icon'] }} text-base" style="color: {{ $account['color'] }}"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $account['name'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $account['type_label'] }}</p>
              </div>
            </div>
            <p class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($account['current_balance'], 2) }}</p>
          </div>
        @endforeach
      </div>
      <div class="chart-container h-48">
        <canvas id="accountsChart"></canvas>
      </div>
    @else
      <div class="text-center py-8">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
          <i class="fas fa-wallet text-gray-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 dark:text-gray-400">No hay cuentas aún</p>
        <a href="{{ route('dashboard.accounts.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium mt-1 inline-block">Crea tu primera cuenta</a>
      </div>
    @endif
  </div>

  {{-- Budgets & Goals --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Budgets Status --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Estado de Presupuestos</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Controla tus límites de gasto</p>
        </div>
        <a href="{{ route('dashboard.budgets.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
          Ver todos <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>

      @if ($budgetsStatus->count() > 0)
        <div class="space-y-4">
          @foreach ($budgetsStatus as $budget)
            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $budget['name'] }}</span>
                  @if ($budget['is_exceeded'])
                    <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 rounded-full">Excedido</span>
                  @else
                    <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded-full">Activo</span>
                  @endif
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $budget['period'] }}</span>
              </div>
              <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                <span>${{ number_format($budget['spent_amount'], 2) }} gastado</span>
                <span>${{ number_format($budget['remaining'], 2) }} restante</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                <div class="h-2 rounded-full progress-bar-animated {{ $budget['is_exceeded'] ? 'bg-red-500' : 'bg-primary-600' }}"
                  style="width: {{ min($budget['percentage'], 100) }}%"></div>
              </div>
              <div class="flex items-center gap-2 mt-2">
                @foreach ($budget['categories']->take(3) as $cat)
                  <span class="inline-flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400">
                    <i class="{{ $cat['icon'] }}" style="color: {{ $cat['color'] }}; font-size: 10px;"></i>
                    {{ $cat['name'] }}
                  </span>
                @endforeach
                @if ($budget['categories']->count() > 3)
                  <span class="text-xs text-gray-400">+{{ $budget['categories']->count() - 3 }}</span>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-6">
          <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-chart-pie text-gray-400 text-xl"></i>
          </div>
          <p class="text-gray-500 dark:text-gray-400 text-sm">Sin presupuestos activos</p>
        </div>
      @endif
    </div>

    {{-- Goals Progress --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Progreso de Metas</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Sigue tus metas financieras</p>
        </div>
        <a href="{{ route('dashboard.goals.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
          Ver todas <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>

      @if ($goalsProgress->count() > 0)
        <div class="space-y-4">
          @foreach ($goalsProgress as $goal)
            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $goal['name'] }}</span>
                  @if ($goal['category'])
                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-600 rounded-full text-gray-600 dark:text-gray-300">
                      <i class="{{ $goal['category']['icon'] }}" style="color: {{ $goal['category']['color'] }}; font-size: 10px;"></i>
                      {{ $goal['category']['name'] }}
                    </span>
                  @endif
                </div>
                <span class="text-xs font-medium text-primary-600 dark:text-primary-400">{{ $goal['progress'] }}%</span>
              </div>
              <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                <span>${{ number_format($goal['current_amount'], 2) }}</span>
                <span>${{ number_format($goal['target_amount'], 2) }}</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                <div class="h-2 rounded-full progress-bar-animated bg-linear-to-r from-primary-500 to-primary-600"
                  style="width: {{ min($goal['progress'], 100) }}%"></div>
              </div>
              @if ($goal['due_date'])
                <p class="text-xs text-gray-400 mt-2">Vence: {{ $goal['due_date'] }}</p>
              @endif
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-6">
          <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-bullseye text-gray-400 text-xl"></i>
          </div>
          <p class="text-gray-500 dark:text-gray-400 text-sm">Sin metas activas</p>
        </div>
      @endif
    </div>
  </div>

  {{-- Debts Overview --}}
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Resumen de Deudas</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Controla tus deudas y pagos</p>
      </div>
      <a href="{{ route('dashboard.debts.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
        Ver todas <i class="fas fa-arrow-right ml-1"></i>
      </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 text-center">
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($debtsOverview['total_debt'], 2) }}</p>
        <p class="text-xs text-red-600/70 dark:text-red-400/70 mt-1">Deuda Total</p>
      </div>
      <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-center">
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($debtsOverview['paid_amount'], 2) }}</p>
        <p class="text-xs text-green-600/70 dark:text-green-400/70 mt-1">Monto Pagado</p>
      </div>
      <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-4 text-center">
        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">${{ number_format($debtsOverview['remaining_debt'], 2) }}</p>
        <p class="text-xs text-orange-600/70 dark:text-orange-400/70 mt-1">Restante</p>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
          <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $debtsOverview['paid_debts'] }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pagadas</p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
          <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $debtsOverview['pending_debts'] }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pendientes</p>
        </div>
      </div>
    </div>

    @if ($debtsOverview['recent_debts']->count() > 0)
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="chart-container h-48">
          <canvas id="debtsChart"></canvas>
        </div>
        <div class="space-y-3">
          @foreach ($debtsOverview['recent_debts'] as $debt)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $debt['lender'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${{ number_format($debt['remaining'], 2) }} restante</p>
              </div>
              <div class="text-right">
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $debt['progress'] }}%</span>
                <div class="w-20 bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mt-1">
                  <div class="h-1.5 rounded-full bg-orange-500" style="width: {{ $debt['progress'] }}%"></div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @else
      <div class="text-center py-6">
        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
          <i class="fas fa-check-circle text-green-400 text-xl"></i>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Sin deudas para rastrear</p>
      </div>
    @endif
  </div>

  {{-- Recent Operations --}}
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Operaciones Recientes</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Tus últimas transacciones</p>
      </div>
      <a href="{{ route('dashboard.operations.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
        Ver todas <i class="fas fa-arrow-right ml-1"></i>
      </a>
    </div>

    @if ($recentOperations->count() > 0)
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th class="px-4 py-3">Categoría</th>
              <th class="px-4 py-3">Cuenta</th>
              <th class="px-4 py-3">Tipo</th>
              <th class="px-4 py-3">Monto</th>
              <th class="px-4 py-3">Fecha</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($recentOperations as $op)
              <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: {{ $op['category']['color'] }}20">
                      <i class="{{ $op['category']['icon'] }} text-sm" style="color: {{ $op['category']['color'] }}"></i>
                    </span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $op['category']['name'] }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center" style="background-color: {{ $op['account']['color'] }}20">
                      <i class="{{ $op['account']['icon'] }} text-xs" style="color: {{ $op['account']['color'] }}"></i>
                    </span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $op['account']['name'] }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $op['type'] === 'income' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                    {{ $op['type'] === 'income' ? 'Ingreso' : 'Gasto' }}
                  </span>
                </td>
                <td class="px-4 py-3 font-medium {{ $op['type'] === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                  {{ $op['type'] === 'income' ? '+' : '-' }}${{ number_format($op['amount'], 2) }}
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $op['date_time'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-center py-8">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
          <i class="fas fa-receipt text-gray-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 dark:text-gray-400">Sin operaciones aún</p>
        <a href="{{ route('dashboard.operations.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium mt-1 inline-block">Registra tu primera operación</a>
      </div>
    @endif
  </div>

  {{-- Insights Section --}}
  @if (count($insights) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
      <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 rounded-lg bg-linear-to-r from-amber-400 to-orange-500 flex items-center justify-center">
          <i class="fas fa-lightbulb text-white text-sm"></i>
        </div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Perspectivas Financieras</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($insights as $insight)
          <div class="insight-card flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
              @if ($insight['type'] === 'success') bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400
              @elseif ($insight['type'] === 'warning') bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400
              @elseif ($insight['type'] === 'danger') bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400
              @else bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 @endif">
              <i class="{{ $insight['icon'] }} text-sm"></i>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $insight['message'] }}</p>
          </div>
        @endforeach
      </div>
    </div>
  @endif
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const isDark = document.documentElement.classList.contains('dark');
      const textColor = isDark ? '#e5e7eb' : '#99a1af';
      const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';

      Chart.defaults.color = textColor;
      Chart.defaults.borderColor = gridColor;

      // Cashflow Chart
      const cashflowCtx = document.getElementById('cashflowChart');
      if (cashflowCtx) {
        new Chart(cashflowCtx, {
          type: 'line',
          data: {
            labels: @json($cashflowData['labels']),
            datasets: [
              {
                label: 'Ingresos',
                data: @json($cashflowData['income']),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#22c55e',
              },
              {
                label: 'Gastos',
                data: @json($cashflowData['expenses']),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#ef4444',
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                  label: function(context) {
                    return context.dataset.label + ': $' + context.raw.toLocaleString('es-ES', { minimumFractionDigits: 2 });
                  }
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return '$' + value.toLocaleString('es-ES');
                  }
                },
                grid: { color: gridColor }
              },
              x: {
                grid: { display: false }
              }
            },
            interaction: {
              mode: 'nearest',
              axis: 'x',
              intersect: false
            }
          }
        });
      }

      // Expenses by Category Chart
      const expensesCtx = document.getElementById('expensesChart');
      if (expensesCtx) {
        const categoryData = @json($expensesByCategory['categories']);
        if (categoryData.length > 0) {
          new Chart(expensesCtx, {
            type: 'doughnut',
            data: {
              labels: categoryData.map(c => c.name),
              datasets: [{
                data: categoryData.map(c => c.amount),
                backgroundColor: categoryData.map(c => c.color),
                borderWidth: 0,
                hoverOffset: 8,
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              cutout: '65%',
              plugins: {
                legend: { display: false },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return context.label + ': $' + context.raw.toLocaleString('es-ES', { minimumFractionDigits: 2 });
                    }
                  }
                }
              }
            }
          });
        } else {
          expensesCtx.parentElement.innerHTML = '<div class="text-center text-gray-400 dark:text-gray-500 py-8"><i class="fas fa-chart-pie text-4xl mb-2"></i><p class="text-sm">Sin datos de gastos</p></div>';
        }
      }

      // Accounts Chart
      const accountsCtx = document.getElementById('accountsChart');
      if (accountsCtx) {
        const accountsData = @json($accounts['accounts']);
        if (accountsData.length > 0) {
          new Chart(accountsCtx, {
            type: 'bar',
            data: {
              labels: accountsData.map(a => a.name),
              datasets: [{
                label: 'Saldo',
                data: accountsData.map(a => a.current_balance),
                backgroundColor: accountsData.map(a => a.color + 'cc'),
                borderColor: accountsData.map(a => a.color),
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return '$' + context.raw.toLocaleString('es-ES', { minimumFractionDigits: 2 });
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  ticks: {
                    callback: function(value) {
                      return '$' + value.toLocaleString('es-ES');
                    }
                  },
                  grid: { color: gridColor }
                },
                x: {
                  grid: { display: false }
                }
              }
            }
          });
        }
      }

      // Debts Chart
      const debtsCtx = document.getElementById('debtsChart');
      if (debtsCtx) {
        const debtsData = @json($debtsOverview['recent_debts']);
        if (debtsData.length > 0) {
          new Chart(debtsCtx, {
            type: 'bar',
            data: {
              labels: debtsData.map(d => d.lender),
              datasets: [
                {
                  label: 'Pagado',
                  data: debtsData.map(d => d.paid_amount),
                  backgroundColor: '#22c55eaa',
                  borderColor: '#22c55e',
                  borderWidth: 1,
                  borderRadius: 4,
                  borderSkipped: false,
                },
                {
                  label: 'Restante',
                  data: debtsData.map(d => d.remaining),
                  backgroundColor: '#f97316aa',
                  borderColor: '#f97316',
                  borderWidth: 1,
                  borderRadius: 4,
                  borderSkipped: false,
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'top',
                  labels: { boxWidth: 12, padding: 8, font: { size: 11 } }
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      return context.dataset.label + ': $' + context.raw.toLocaleString('es-ES', { minimumFractionDigits: 2 });
                    }
                  }
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  stacked: true,
                  ticks: {
                    callback: function(value) {
                      return '$' + value.toLocaleString('es-ES');
                    }
                  },
                  grid: { color: gridColor }
                },
                x: {
                  stacked: true,
                  grid: { display: false }
                }
              }
            }
          });
        }
      }
    });
  </script>
@endpush
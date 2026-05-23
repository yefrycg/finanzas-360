@extends('layouts.dashboard')

@push('styles')
  <style>
    table i[class*="fa-"] {
      display: inline-block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
  </style>
@endpush

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Presupuestos</h1>
    <p class="text-gray-500 dark:text-gray-400">Planifica y controla tu presupuesto por período</p>
  </div>

  <section class="bg-gray-50 dark:bg-gray-900 antialiased">
    <div class="mx-auto max-w-7xl">
      <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-visible">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
          <div class="w-full md:w-auto flex flex-col md:flex-row items-center gap-3">
            <div class="relative w-full md:w-64">
              <form action="{{ route('dashboard.budgets.index') }}" method="get" class="flex items-center">
                @if (request('category_id'))
                  <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                @if (request('period'))
                  <input type="hidden" name="period" value="{{ request('period') }}">
                @endif
                @if (request('status'))
                  <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative w-full">
                  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                      viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input type="text" name="search" id="simple-search" value="{{ request('search') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Buscar presupuestos">
                </div>
              </form>
            </div>

            <div class="flex items-center space-x-3 w-full md:w-auto">
              <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown"
                data-dropdown-placement="bottom-start"
                class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                type="button">
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 mr-2 text-gray-400"
                  viewbox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                    clip-rule="evenodd" />
                </svg>
                Filtrar
                <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <path clip-rule="evenodd" fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                </svg>
              </button>

              <div id="filterDropdown" class="z-10 hidden w-80 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                <form action="{{ route('dashboard.budgets.index') }}" method="get">
                  @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                  @endif
                  <div class="space-y-3">
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Categoría</label>
                      <select name="category_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">Todas</option>
                        @foreach ($categories as $category)
                          <option value="{{ $category->id }}"
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Período</label>
                      <select name="period"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">Todos</option>
                        <option value="daily" {{ request('period') === 'daily' ? 'selected' : '' }}>Diario</option>
                        <option value="weekly" {{ request('period') === 'weekly' ? 'selected' : '' }}>Semanal</option>
                        <option value="monthly" {{ request('period') === 'monthly' ? 'selected' : '' }}>Mensual</option>
                        <option value="annually" {{ request('period') === 'annually' ? 'selected' : '' }}>Anual</option>
                      </select>
                    </div>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                      <select name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="exceeded" {{ request('status') === 'exceeded' ? 'selected' : '' }}>Excedido
                        </option>
                      </select>
                    </div>
                    <button type="submit"
                      class="w-full text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700">Aplicar</button>
                    <a href="{{ route('dashboard.budgets.index') }}"
                      class="w-full text-center text-gray-600 border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-600 mt-2 block">Reestablecer</a>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div
            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 shrink-0">
            <button type="button" data-modal-target="createBudgetModal" data-modal-toggle="createBudgetModal"
              class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
              <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path clip-rule="evenodd" fill-rule="evenodd"
                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
              </svg>
              Agregar Presupuesto
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 px-4 py-3 border-t dark:border-gray-700">
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total presupuestado</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalLimit, 2) }}</p>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total gastado</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalSpent, 2) }}</p>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Presupuestos excedidos</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exceededCount }} / {{ $totalBudgets }}</p>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Presupuesto restante global</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalRemaining, 2) }}</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>
                <th scope="col" class="px-4 py-3">Nombre</th>
                <th scope="col" class="px-4 py-3">Categorías</th>
                <th scope="col" class="px-4 py-3">Límite</th>
                <th scope="col" class="px-4 py-3">Gastado</th>
                <th scope="col" class="px-4 py-3">Restante</th>
                <th scope="col" class="px-4 py-3">Progreso</th>
                <th scope="col" class="px-4 py-3">Período</th>
                <th scope="col" class="px-4 py-3">Estado</th>
                <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
              </tr>
            </thead>
            <tbody>
              @php
                $periodLabels = [
                    'daily' => 'Diario',
                    'weekly' => 'Semanal',
                    'monthly' => 'Mensual',
                    'annually' => 'Anual',
                ];
              @endphp

              @forelse ($budgets as $budget)
                <tr class="border-b dark:border-gray-700">
                  <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $budget->name }}
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-2">
                      @foreach ($budget->categories as $category)
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                          style="background-color: {{ $category->color }}20;">
                          <i class="{{ $category->icon }}" style="color: {{ $category->color }};"></i>
                          <span class="text-gray-900 dark:text-white">{{ $category->name }}</span>
                        </span>
                      @endforeach
                    </div>
                  </td>
                  <td class="px-4 py-3">${{ number_format((float) $budget->limit_amount, 2) }}</td>
                  <td class="px-4 py-3">${{ number_format((float) $budget->spent_amount, 2) }}</td>
                  <td class="px-4 py-3">${{ number_format((float) $budget->remaining_amount, 2) }}</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                      <div class="w-24 h-2 bg-gray-200 rounded-full dark:bg-gray-600">
                        <div
                          class="h-2 rounded-full {{ $budget->status === 'exceeded' ? 'bg-red-600' : 'bg-primary-600' }}"
                          style="width: {{ min((float) $budget->progress, 100) }}%"></div>
                      </div>
                      <span class="text-xs">{{ number_format((float) $budget->progress, 0) }}%</span>
                    </div>
                  </td>
                  <td class="px-4 py-3">{{ $periodLabels[$budget->period] ?? $budget->period }}</td>
                  <td class="px-4 py-3">
                    @if ($budget->status === 'exceeded')
                      <span
                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Excedido</span>
                    @else
                      <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Activo</span>
                    @endif
                  </td>
                  <td class="px-4 py-3 flex items-center justify-end overflow-visible">
                    <button id="budget-{{ $budget->id }}-dropdown-button"
                      data-dropdown-toggle="budget-{{ $budget->id }}-dropdown"
                      class="inline-flex items-center text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5 text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                      type="button">
                      <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                      </svg>
                    </button>
                    <div id="budget-{{ $budget->id }}-dropdown"
                      class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                      <ul class="py-1 text-sm" aria-labelledby="budget-{{ $budget->id }}-dropdown-button">
                        <li>
                          <button type="button" data-modal-target="updateBudgetModal"
                            data-modal-toggle="updateBudgetModal" data-id="{{ $budget->id }}"
                            data-name="{{ $budget->name }}" data-period="{{ $budget->period }}"
                            data-limit-amount="{{ (float) $budget->limit_amount }}"
                            data-category-ids="{{ $budget->categories->pluck('id')->implode(',') }}"
                            class="flex w-full items-center py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-gray-700 dark:text-gray-200">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 20 20"
                              fill="currentColor" aria-hidden="true">
                              <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                              <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                            </svg>
                            Editar
                          </button>
                        </li>
                        <li>
                          @php
                            $budgetCategoriesPayload = $budget->categories
                                ->map(function ($c) {
                                    return [
                                        'id' => $c->id,
                                        'name' => $c->name,
                                        'color' => $c->color,
                                        'icon' => $c->icon,
                                    ];
                                })
                                ->values()
                                ->all();
                          @endphp
                          <button type="button" data-modal-target="readBudgetModal"
                            data-modal-toggle="readBudgetModal" data-name="{{ $budget->name }}"
                            data-period="{{ $budget->period }}"
                            data-limit-amount="{{ (float) $budget->limit_amount }}"
                            data-spent-amount="{{ (float) $budget->spent_amount }}"
                            data-remaining-amount="{{ (float) $budget->remaining_amount }}"
                            data-progress="{{ (float) $budget->progress }}" data-status="{{ $budget->status }}"
                            data-categories='@json($budgetCategoriesPayload)'
                            class="flex w-full items-center py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-gray-700 dark:text-gray-200">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 20 20"
                              fill="currentColor" aria-hidden="true">
                              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                              <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Ver
                          </button>
                        </li>
                        <li>
                          <button type="button" data-modal-target="deleteBudgetModal"
                            data-modal-toggle="deleteBudgetModal" data-id="{{ $budget->id }}"
                            data-name="{{ $budget->name }}"
                            class="flex w-full items-center py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 20 20"
                              fill="currentColor" aria-hidden="true">
                              <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                            </svg>
                            Eliminar
                          </button>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No se encontraron
                    presupuestos.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
          aria-label="Table navigation">
          <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
            Mostrando
            <span
              class="font-semibold text-gray-900 dark:text-white">{{ $budgets->firstItem() ?? 0 }}-{{ $budgets->lastItem() ?? 0 }}</span>
            de
            <span class="font-semibold text-gray-900 dark:text-white">{{ $budgets->total() }}</span>
          </span>
          {{ $budgets->links() }}
        </nav>
      </div>
    </div>
  </section>

  <div id="createBudgetModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div
        class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Crear Presupuesto</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-target="createBudgetModal" data-modal-toggle="createBudgetModal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Cerrar</span>
          </button>
        </div>
        <form action="{{ route('dashboard.budgets.store') }}" method="POST">
          @csrf
          <div class="grid gap-4 mb-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
              <input type="text" name="name" id="name" value="{{ old('name') }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Ej: Entretenimiento" required>
              @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="period" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Período</label>
              <select name="period" id="period"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                required>
                <option value="">Seleccionar período</option>
                <option value="daily" {{ old('period') === 'daily' ? 'selected' : '' }}>Diario</option>
                <option value="weekly" {{ old('period') === 'weekly' ? 'selected' : '' }}>Semanal</option>
                <option value="monthly" {{ old('period') === 'monthly' ? 'selected' : '' }}>Mensual</option>
                <option value="annually" {{ old('period') === 'annually' ? 'selected' : '' }}>Anual</option>
              </select>
              @error('period')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="limit_amount"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Límite</label>
              <input type="number" name="limit_amount" id="limit_amount" step="0.01" min="0"
                value="{{ old('limit_amount') }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                placeholder="0.00" required>
              @error('limit_amount')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
            </div>
            <div class="sm:col-span-2">
              <label for="categories" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categorías
                (gastos)</label>
              <select name="categories[]" id="categories" multiple
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}"
                    {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
              @error('categories')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
              @error('categories.*')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Puedes seleccionar múltiples categorías.</p>
            </div>
          </div>
          <button type="submit"
            class="text-white inline-flex items-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
            <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd" />
            </svg>
            Crear Presupuesto
          </button>
        </form>
      </div>
    </div>
  </div>

  <div id="updateBudgetModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div
        class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actualizar Presupuesto</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-toggle="updateBudgetModal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Cerrar</span>
          </button>
        </div>
        <form id="updateForm" action="" method="POST">
          @csrf
          @method('PUT')
          <div class="grid gap-4 mb-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label for="update-name"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
              <input type="text" name="name" id="update-name"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                required>
            </div>
            <div>
              <label for="update-period"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Período</label>
              <select name="period" id="update-period"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                required>
                <option value="daily">Diario</option>
                <option value="weekly">Semanal</option>
                <option value="monthly">Mensual</option>
                <option value="annually">Anual</option>
              </select>
            </div>
            <div>
              <label for="update-limit-amount"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Límite</label>
              <input type="number" name="limit_amount" id="update-limit-amount" step="0.01" min="0"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                required>
            </div>
            <div class="sm:col-span-2">
              <label for="update-categories"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categorías (gastos)</label>
              <select name="categories[]" id="update-categories" multiple
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                required>
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <button type="submit"
              class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="readBudgetModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-xl max-h-full">
      <div
        class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between mb-4 rounded-t sm:mb-5">
          <div class="text-lg text-gray-900 md:text-xl dark:text-white">
            <h3 class="font-semibold" id="read-name"></h3>
          </div>
          <div>
            <button type="button"
              class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex dark:hover:bg-gray-600 dark:hover:text-white"
              data-modal-toggle="readBudgetModal">
              <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd" />
              </svg>
              <span class="sr-only">Cerrar</span>
            </button>
          </div>
        </div>
        <dl>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Categorías</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">
            <div id="read-categories" class="flex flex-wrap gap-2"></div>
          </dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Límite</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-limit"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Gastado</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-spent"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Restante</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-remaining"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Progreso</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">
            <div class="flex items-center gap-2">
              <div class="w-full h-2 bg-gray-200 rounded-full dark:bg-gray-600">
                <div id="read-progress-bar" class="h-2 rounded-full bg-primary-600" style="width: 0%"></div>
              </div>
              <span class="text-xs" id="read-progress"></span>
            </div>
          </dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Período</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-period"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Estado</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-status"></dd>
        </dl>
        <div class="flex justify-between items-center">
          <button type="button"
            class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
            data-modal-toggle="readBudgetModal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div id="deleteBudgetModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex justify-center items-center w-full md:inset-0 h-full max-h-full overflow-y-auto">
    <div class="absolute inset-0 bg-gray-900/50" onclick="closeModal('deleteBudgetModal')"></div>
    <div
      class="relative p-4 w-full max-w-md max-h-full bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5 my-4">
      <button type="button"
        class="text-gray-400 absolute top-2.5 right-2.5 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
        data-modal-toggle="deleteBudgetModal">
        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd"
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd" />
        </svg>
        <span class="sr-only">Cerrar</span>
      </button>
      <svg class="text-gray-400 dark:text-gray-500 w-11 h-11 mb-3.5 mx-auto" aria-hidden="true" fill="currentColor"
        viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
          clip-rule="evenodd" />
      </svg>
      <p class="mb-4 text-gray-500 dark:text-gray-300">¿Estás seguro de que quieres eliminar "<span
          id="delete-budget-name"></span>"?</p>
      <div class="flex justify-center items-center space-x-4">
        <button data-modal-toggle="deleteBudgetModal" type="button"
          class="py-2 px-3 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No,
          cancelar</button>
        <form id="deleteForm" method="POST" class="inline">
          @csrf
          @method('DELETE')
          <button type="submit"
            class="py-2 px-3 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">Sí,
            eliminar</button>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        function closeModal(modalId) {
          const modal = document.getElementById(modalId);
          if (modal) {
            modal.classList.add('hidden');
          }
        }

        window.closeModal = closeModal;

        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
          modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.classList.contains('bg-gray-900/50')) {
              modal.classList.add('hidden');
            }
          });
        });

        const updateUrlTemplate = @js(route('dashboard.budgets.update', ['budget' => '__BUDGET__']));
        const deleteUrlTemplate = @js(route('dashboard.budgets.destroy', ['budget' => '__BUDGET__']));

        const periodLabels = {
          daily: 'Diario',
          weekly: 'Semanal',
          monthly: 'Mensual',
          annually: 'Anual'
        };

        const statusLabels = {
          active: 'Activo',
          exceeded: 'Excedido'
        };

        document.querySelectorAll('[data-modal-target="updateBudgetModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const period = this.dataset.period;
            const limitAmount = this.dataset.limitAmount;
            const categoryIds = (this.dataset.categoryIds || '').split(',').filter(Boolean);

            document.getElementById('updateForm').action = updateUrlTemplate.replace('__BUDGET__', id);
            document.getElementById('update-name').value = name;
            document.getElementById('update-period').value = period;
            document.getElementById('update-limit-amount').value = limitAmount;

            const categoriesSelect = document.getElementById('update-categories');
            if (categoriesSelect) {
              Array.from(categoriesSelect.options).forEach(option => {
                option.selected = categoryIds.includes(option.value);
              });
            }
          });
        });

        document.querySelectorAll('[data-modal-target="readBudgetModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const name = this.dataset.name;
            const period = this.dataset.period;
            const limitAmount = parseFloat(this.dataset.limitAmount || '0');
            const spentAmount = parseFloat(this.dataset.spentAmount || '0');
            const remainingAmount = parseFloat(this.dataset.remainingAmount || '0');
            const progress = parseFloat(this.dataset.progress || '0');
            const status = this.dataset.status;
            const categories = JSON.parse(this.dataset.categories || '[]');

            document.getElementById('read-name').textContent = name;
            document.getElementById('read-period').textContent = periodLabels[period] || period;
            document.getElementById('read-limit').textContent = '$' + limitAmount.toFixed(2);
            document.getElementById('read-spent').textContent = '$' + spentAmount.toFixed(2);
            document.getElementById('read-remaining').textContent = '$' + remainingAmount.toFixed(2);
            document.getElementById('read-progress').textContent = Math.round(progress) + '%';
            document.getElementById('read-status').textContent = statusLabels[status] || status;

            const progressBar = document.getElementById('read-progress-bar');
            if (progressBar) {
              progressBar.style.width = Math.min(progress, 100) + '%';
              progressBar.classList.toggle('bg-red-600', status === 'exceeded');
              progressBar.classList.toggle('bg-primary-600', status !== 'exceeded');
            }

            const categoriesContainer = document.getElementById('read-categories');
            if (categoriesContainer) {
              categoriesContainer.innerHTML = '';
              categories.forEach(category => {
                const badge = document.createElement('span');
                badge.className =
                  'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium';
                badge.style.backgroundColor = (category.color || '#999999') + '20';

                const icon = document.createElement('i');
                icon.className = category.icon || '';
                icon.style.color = category.color || '#999999';

                const text = document.createElement('span');
                text.className = 'text-gray-900 dark:text-white';
                text.textContent = category.name || '';

                badge.appendChild(icon);
                badge.appendChild(text);
                categoriesContainer.appendChild(badge);
              });
            }
          });
        });

        document.querySelectorAll('[data-modal-target="deleteBudgetModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('delete-budget-name').textContent = name;
            document.getElementById('deleteForm').action = deleteUrlTemplate.replace('__BUDGET__', id);
          });
        });
      });
    </script>
  @endpush
@endsection

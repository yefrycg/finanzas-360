@extends('layouts.dashboard')

@push('styles')
  <style>
    input[type="datetime-local"]::-webkit-calendar-picker-indicator {
      filter: invert(100%);
    }
    table i[class*="fa-"] {
      display: inline-block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
  </style>
@endpush

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Operaciones</h1>
    <p class="text-gray-500 dark:text-gray-400">Registra y visualiza tus transacciones</p>
  </div>

  <section class="bg-gray-50 dark:bg-gray-900 antialiased">
    <div class="mx-auto max-w-7xl">
      <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-visible">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
          <div class="w-full md:w-auto flex flex-col md:flex-row items-center gap-3">
            <div class="relative w-full md:w-64">
              <form action="{{ route('dashboard.operations.index') }}" method="get" class="flex items-center">
                @if (request('type'))
                  <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                @if (request('account_id'))
                  <input type="hidden" name="account_id" value="{{ request('account_id') }}">
                @endif
                @if (request('category_id'))
                  <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                @if (request('date_from'))
                  <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                @endif
                @if (request('date_to'))
                  <input type="hidden" name="date_to" value="{{ request('date_to') }}">
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
                    placeholder="Buscar en notas, categorías, cuentas">
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
                <form action="{{ route('dashboard.operations.index') }}" method="get">
                  @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                  @endif
                  <div class="space-y-3">
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Type</label>
                      <select name="type"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">Todas</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Ingreso</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Gasto</option>
                      </select>
                    </div>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Account</label>
                      <select name="account_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">Todas</option>
                        @foreach ($accounts as $account)
                          <option value="{{ $account->id }}"
                            {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div>
                      <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                      <select name="category_id" id="filter-category-id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">Todas</option>
                        @foreach ($categories as $category)
                          <option value="{{ $category->id }}"
                            {{ request('category_id') == $category->id ? 'selected' : '' }}
                            data-type="{{ $category->type }}">{{ $category->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                      <div>
                        <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Desde</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                      </div>
                      <div>
                        <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Hasta</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                      </div>
                    </div>
                    <button type="submit"
                      class="w-full text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700">Aplicar</button>
                    <a href="{{ route('dashboard.operations.index') }}"
                      class="w-full text-center text-gray-600 border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-600 mt-2 block">Reestablecer</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div
            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 shrink-0">
            <button type="button" data-modal-target="createOperationModal" data-modal-toggle="createOperationModal"
              class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
              <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path clip-rule="evenodd" fill-rule="evenodd"
                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
              </svg>
              Agregar Operación
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 px-4 py-3 border-t dark:border-gray-700">
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total ingresos</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($totalIncome, 2) }}</p>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total gastos</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($totalExpense, 2) }}</p>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Balance neto</p>
            <p class="text-2xl font-bold {{ $totalIncome - $totalExpense >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">${{ number_format($totalIncome - $totalExpense, 2) }}</p>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total operaciones</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalOperations }}</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>
                <th scope="col" class="px-4 py-3">Categoría</th>
                <th scope="col" class="px-4 py-3">Cuenta</th>
                <th scope="col" class="px-4 py-3">Tipo</th>
                <th scope="col" class="px-4 py-3">Monto</th>
                <th scope="col" class="px-4 py-3">Fecha y Hora</th>
                <th scope="col" class="px-4 py-3">Nota</th>
                <th scope="col" class="px-4 py-3">
                  <span class="sr-only">Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody>
              @forelse ($operations as $operation)
                <tr class="border-b dark:border-gray-700">
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                      <span class="w-8 h-8 rounded-full flex items-center justify-center"
                        style="background-color: {{ $operation->category->color }}20; display: inline-flex !important;">
                        <i class="{{ $operation->category->icon }} text-base"
                          style="color: {{ $operation->category->color }}; display: inline-block !important;"></i>
                      </span>
                      <span class="font-medium text-gray-900 dark:text-white">{{ $operation->category->name }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                      <span class="w-8 h-8 rounded-full flex items-center justify-center"
                        style="background-color: {{ $operation->account->color }}20; display: inline-flex !important;">
                        <i class="{{ $operation->account->icon }} text-base"
                          style="color: {{ $operation->account->color }}; display: inline-block !important;"></i>
                      </span>
                      <span class="font-medium text-gray-900 dark:text-white">{{ $operation->account->name }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    @if ($operation->type === 'income')
                      <span
                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Ingreso</span>
                    @else
                      <span
                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Gasto</span>
                    @endif
                  </td>
                  <td
                    class="px-4 py-3 font-medium {{ $operation->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $operation->type === 'income' ? '+' : '-' }}${{ number_format($operation->amount, 2) }}
                  </td>
                  <td class="px-4 py-3">
                    {{ \Carbon\Carbon::parse($operation->date_time)->format('d/m/Y H:i') }}
                  </td>
                  <td class="px-4 py-3">
                    <span class="text-gray-500 dark:text-gray-400 truncate max-w-xs block">
                      {{ $operation->note ?? '—' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 flex items-center justify-end overflow-visible">
                    <button id="operation-{{ $operation->id }}-dropdown-button"
                      data-dropdown-toggle="operation-{{ $operation->id }}-dropdown"
                      class="inline-flex items-center text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5 text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                      type="button">
                      <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                      </svg>
                    </button>
                    <div id="operation-{{ $operation->id }}-dropdown"
                      class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                      <ul class="py-1 text-sm" aria-labelledby="operation-{{ $operation->id }}-dropdown-button">
                        <li>
                          <button type="button" data-modal-target="updateOperationModal"
                            data-modal-toggle="updateOperationModal"
                            data-id="{{ $operation->id }}"
                            data-amount="{{ $operation->amount }}"
                            data-date-time="{{ $operation->date_time->format('Y-m-d\TH:i') }}"
                            data-type="{{ $operation->type }}"
                            data-category-id="{{ $operation->category_id }}"
                            data-account-id="{{ $operation->account_id }}"
                            data-note="{{ $operation->note ?? '' }}"
                            data-category-name="{{ $operation->category->name }}"
                            data-category-color="{{ $operation->category->color }}"
                            data-category-icon="{{ $operation->category->icon }}"
                            data-account-name="{{ $operation->account->name }}"
                            data-account-color="{{ $operation->account->color }}"
                            data-account-icon="{{ $operation->account->icon }}"
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
                          <button type="button" data-modal-target="readOperationModal"
                            data-modal-toggle="readOperationModal"
                            data-id="{{ $operation->id }}"
                            data-amount="{{ $operation->amount }}"
                            data-type="{{ $operation->type }}"
                            data-category-name="{{ $operation->category->name }}"
                            data-category-color="{{ $operation->category->color }}"
                            data-category-icon="{{ $operation->category->icon }}"
                            data-account-name="{{ $operation->account->name }}"
                            data-account-color="{{ $operation->account->color }}"
                            data-account-icon="{{ $operation->account->icon }}"
                            data-date-time="{{ $operation->date_time->toIso8601String() }}"
                            data-note="{{ $operation->note ?? '' }}"
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
                          <button type="button" data-modal-target="deleteOperationModal"
                            data-modal-toggle="deleteOperationModal"
                            data-id="{{ $operation->id }}"
                            data-name="{{ $operation->category->name }} - @if($operation->type === 'income')+@else-@endif${{ number_format($operation->amount, 2) }}"
                            data-amount="{{ $operation->amount }}"
                            data-type="{{ $operation->type }}"
                            class="flex w-full items-center py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 text-red-500 dark:hover:text-red-400">
                            <svg class="w-4 h-4 mr-2" viewbox="0 0 14 15" fill="none"
                              xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                              <path fill-rule="evenodd" clip-rule="evenodd" fill="currentColor"
                                d="M6.09922 0.300781C5.93212 0.30087 5.76835 0.347476 5.62625 0.435378C5.48414 0.523281 5.36931 0.649009 5.29462 0.798481L4.64302 2.10078H1.59922C1.36052 2.10078 1.13161 2.1956 0.962823 2.36439C0.79404 2.53317 0.699219 2.76209 0.699219 3.00078C0.699219 3.23948 0.79404 3.46839 0.962823 3.63718C1.13161 3.80596 1.36052 3.90078 1.59922 3.90078V12.9008C1.59922 13.3782 1.78886 13.836 2.12643 14.1736C2.46399 14.5111 2.92183 14.7008 3.39922 14.7008H10.5992C11.0766 14.7008 11.5344 14.5111 11.872 14.1736C12.2096 13.836 12.3992 13.3782 12.3992 12.9008V3.90078C12.6379 3.90078 12.8668 3.80596 13.0356 3.63718C13.2044 3.46839 13.2992 3.23948 13.2992 3.00078C13.2992 2.76209 13.2044 2.53317 13.0356 2.36439C12.8668 2.1956 12.6379 2.10078 12.3992 2.10078H9.35542L8.70382 0.798481C8.62913 0.649009 8.5143 0.523281 8.37219 0.435378C8.23009 0.347476 8.06631 0.30087 7.89922 0.300781H6.09922ZM4.29922 5.70078C4.29922 5.46209 4.39404 5.23317 4.56282 5.06439C4.73161 4.8956 4.96052 4.80078 5.19922 4.80078C5.43791 4.80078 5.66683 4.8956 5.83561 5.06439C6.0044 5.23317 6.09922 5.46209 6.09922 5.70078V11.1008C6.09922 11.3395 6.0044 11.5684 5.83561 11.7372C5.66683 11.906 5.43791 12.0008 5.19922 12.0008C4.96052 12.0008 4.73161 11.906 4.56282 11.7372C4.39404 11.5684 4.29922 11.3395 4.29922 11.1008V5.70078ZM8.79922 4.80078C8.56052 4.80078 8.33161 4.8956 8.16282 5.06439C7.99404 5.23317 7.89922 5.46209 7.89922 5.70078V11.1008C7.89922 11.3395 7.99404 11.5684 8.16282 11.7372C8.33161 11.906 8.56052 12.0008 8.79922 12.0008C9.03791 12.0008 9.26683 11.906 9.43561 11.7372C9.6044 11.5684 9.69922 11.3395 9.69922 11.1008V5.70078C9.69922 5.46209 9.6044 5.23317 9.43561 5.06439C9.26683 4.8956 9.03791 4.80078 8.79922 4.80078Z" />
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
                  <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No se encontraron operaciones.</td>
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
              class="font-semibold text-gray-900 dark:text-white">{{ $operations->firstItem() ?? 0 }}-{{ $operations->lastItem() ?? 0 }}</span>
            de
            <span class="font-semibold text-gray-900 dark:text-white">{{ $operations->total() }}</span>
          </span>
          {{ $operations->links() }}
        </nav>
      </div>
    </div>
  </section>

  <!-- Create modal -->
  <div id="createOperationModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div
        class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Crear Operación</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-target="createOperationModal" data-modal-toggle="createOperationModal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Cerrar</span>
          </button>
        </div>
        <form action="{{ route('dashboard.operations.store') }}" method="POST">
          @csrf
          <div class="grid gap-4 mb-4 sm:grid-cols-2">
            <div>
              <label for="amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Monto</label>
              <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                value="{{ old('amount') }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="0.00" required>
              @error('amount')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="date_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha y Hora</label>
              <input type="datetime-local" name="date_time" id="date_time" value="{{ old('date_time') }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
              @error('date_time')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
              <select name="type" id="type"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                <option value="">Seleccionar tipo</option>
                <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Ingreso</option>
                <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Gasto</option>
              </select>
              @error('type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="account_id"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cuenta</label>
              <select name="account_id" id="account_id"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                <option value="">Seleccionar cuenta</option>
                @foreach ($accounts as $account)
                  <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                    {{ $account->name }}</option>
                @endforeach
              </select>
              @error('account_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>
            <div class="sm:col-span-2">
              <label for="category_id"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoría</label>
              <select name="category_id" id="category_id"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                <option value="">Seleccionar categoría</option>
              </select>
              @error('category_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
              @enderror
            </div>
            <div class="sm:col-span-2">
              <label for="note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nota</label>
              <textarea name="note" id="note" rows="3"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Descripción opcional">{{ old('note') }}</textarea>
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
            Crear Operación
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Update modal -->
  <div id="updateOperationModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div
        class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actualizar Operación</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-target="updateOperationModal" data-modal-toggle="updateOperationModal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Cerrar</span>
          </button>
        </div>
        <form id="updateOperationForm" action="" method="POST">
          @csrf
          @method('PUT')
          <div class="grid gap-4 mb-4 sm:grid-cols-2">
            <div>
              <label for="update-amount"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Monto</label>
              <input type="number" name="amount" id="update-amount" step="0.01" min="0.01"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="0.00" required>
            </div>
            <div>
              <label for="update-date-time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha y Hora</label>
              <input type="datetime-local" name="date_time" id="update-date-time"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
            </div>
            <div>
              <label for="update-type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
              <select name="type" id="update-type"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-500 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                <option value="income">Ingreso</option>
                <option value="expense">Gasto</option>
              </select>
            </div>
            <div>
              <label for="update-account-id"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cuenta</label>
              <select name="account_id" id="update-account-id"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-500 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                @foreach ($accounts as $account)
                  <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="sm:col-span-2">
              <label for="update-category-id"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoría</label>
              <select name="category_id" id="update-category-id"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-500 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
              </select>
            </div>
            <div class="sm:col-span-2">
              <label for="update-note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nota</label>
              <textarea name="note" id="update-note" rows="3"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Descripción opcional"></textarea>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <button type="submit"
              class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Actualizar</button>
            <button type="button" data-modal-target="updateOperationModal" data-modal-toggle="updateOperationModal"
              class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Read modal -->
  <div id="readOperationModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-xl max-h-full">
      <div
        class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ver Operación</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-target="readOperationModal" data-modal-toggle="readOperationModal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Cerrar</span>
          </button>
        </div>
        <dl>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Tipo</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-type"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Monto</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400 text-lg" id="read-amount"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Categoría</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-category"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Cuenta</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-account"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Fecha y Hora</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-date-time"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Nota</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-note"></dd>
        </dl>
        <div class="flex justify-end items-center pt-4 border-t dark:border-gray-600">
          <button type="button" data-modal-toggle="readOperationModal"
            class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
@endsection

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

      const allCategories = @json($categories->toArray());
      const updateUrlTemplate = @json(route('dashboard.operations.update', ['operation' => '__OPERATION__']));
      const deleteUrlTemplate = @json(route('dashboard.operations.destroy', ['operation' => '__OPERATION__']));

      const typeSelect = document.getElementById('type');
      const categorySelect = document.getElementById('category_id');
      const updateTypeSelect = document.getElementById('update-type');
      const updateCategorySelect = document.getElementById('update-category-id');
      const filterTypeSelect = document.querySelector('select[name="type"]');
      const filterCategorySelect = document.getElementById('filter-category-id');

      function filterCategories(type, targetSelect, selectedId = null) {
        targetSelect.innerHTML = '<option value="">Seleccionar categoría</option>';
        const filtered = allCategories.filter(c => c.type === type);
        filtered.forEach(cat => {
          const option = document.createElement('option');
          option.value = cat.id;
          option.textContent = cat.name;
          if (selectedId && cat.id == selectedId) {
            option.selected = true;
          }
          targetSelect.appendChild(option);
        });
      }

      function filterCategoryDropdown() {
        if (!filterTypeSelect || !filterCategorySelect) return;
        const selectedType = filterTypeSelect.value;
        const selectedCategory = filterCategorySelect.value;
        filterCategorySelect.innerHTML = '<option value="">Todas</option>';
        const filtered = allCategories.filter(c => !selectedType || c.type === selectedType);
        filtered.forEach(cat => {
          const option = document.createElement('option');
          option.value = cat.id;
          option.textContent = cat.name;
          if (cat.id == selectedCategory) {
            option.selected = true;
          }
          filterCategorySelect.appendChild(option);
        });
      }

      if (filterTypeSelect && filterCategorySelect) {
        filterTypeSelect.addEventListener('change', filterCategoryDropdown);
        filterCategoryDropdown();
      }

      if (typeSelect && categorySelect) {
        typeSelect.addEventListener('change', function() {
          filterCategories(this.value, categorySelect);
        });
      }

      if (updateTypeSelect && updateCategorySelect) {
        updateTypeSelect.addEventListener('change', function() {
          filterCategories(this.value, updateCategorySelect);
        });
      }

      document.querySelectorAll('[data-modal-target="updateOperationModal"][data-modal-toggle]').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.dataset.id;
          document.getElementById('updateOperationForm').action = updateUrlTemplate.replace('__OPERATION__', id);
          document.getElementById('update-amount').value = this.dataset.amount;
          document.getElementById('update-date-time').value = this.dataset.dateTime;
          document.getElementById('update-type').value = this.dataset.type;
          document.getElementById('update-note').value = this.dataset.note;
          document.getElementById('update-account-id').value = this.dataset.accountId;
          filterCategories(this.dataset.type, updateCategorySelect, this.dataset.categoryId);
        });
      });

      document.querySelectorAll('[data-modal-target="readOperationModal"][data-modal-toggle]').forEach(button => {
        button.addEventListener('click', function() {
          const type = this.dataset.type;
          const amount = parseFloat(this.dataset.amount).toFixed(2);
          document.getElementById('read-type').textContent = type === 'income' ? 'Ingreso' : 'Gasto';
          document.getElementById('read-type').className = type === 'income'
            ? 'mb-4 font-light text-green-600 sm:mb-5 dark:text-green-400'
            : 'mb-4 font-light text-red-600 sm:mb-5 dark:text-red-400';
          document.getElementById('read-amount').textContent = (type === 'income' ? '+' : '-') + '$' + amount;
          document.getElementById('read-amount').className = type === 'income'
            ? 'mb-4 font-light text-green-600 sm:mb-5 dark:text-green-400 text-lg font-bold'
            : 'mb-4 font-light text-red-600 sm:mb-5 dark:text-red-400 text-lg font-bold';
          document.getElementById('read-category').innerHTML = `<span class="inline-flex items-center gap-2"><span class="w-6 h-6 rounded-full flex items-center justify-center" style="background-color: ${this.dataset.categoryColor}20"><i class="${this.dataset.categoryIcon}" style="color: ${this.dataset.categoryColor}"></i></span>${this.dataset.categoryName}</span>`;
          document.getElementById('read-account').innerHTML = `<span class="inline-flex items-center gap-2"><span class="w-6 h-6 rounded-full flex items-center justify-center" style="background-color: ${this.dataset.accountColor}20"><i class="${this.dataset.accountIcon}" style="color: ${this.dataset.accountColor}"></i></span>${this.dataset.accountName}</span>`;
          const date = new Date(this.dataset.dateTime);
          document.getElementById('read-date-time').textContent = date.toLocaleString('en-GB');
          document.getElementById('read-note').textContent = this.dataset.note || '—';
        });
      });

      document.querySelectorAll('[data-modal-target="deleteOperationModal"][data-modal-toggle]').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.dataset.id;
          const name = this.dataset.name;
          document.getElementById('deleteOperationForm').action = deleteUrlTemplate.replace('__OPERATION__', id);
          document.getElementById('delete-operation-name').textContent = name;
        });
      });
    });
  </script>
@endpush

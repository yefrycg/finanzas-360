@extends('layouts.dashboard')

@push('styles')
  <style>
    input[type="date"]::-webkit-calendar-picker-indicator {
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
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Cuentas</h1>
    <p class="text-gray-500 dark:text-gray-400">Administra tus cuentas bancarias y de efectivo</p>
  </div>

  <section class="bg-gray-50 dark:bg-gray-900 antialiased">
    <div class="mx-auto max-w-7xl">
      <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-visible">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
          <div class="w-full md:w-auto flex flex-col md:flex-row items-center gap-3">
            <div class="relative w-full md:w-64">
              <form action="{{ route('dashboard.accounts.index') }}" method="get" class="flex items-center">
                @if (request('type'))
                  <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <label for="simple-search" class="sr-only">Buscar</label>
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
                    placeholder="Buscar cuentas">
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
              <div id="filterDropdown" class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Tipo</h6>
                <form action="{{ route('dashboard.accounts.index') }}" method="get">
                  @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                  @endif
                  <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                    <li class="flex items-center">
                      <input id="type-all" type="radio" name="type" value=""
                        {{ !request('type') ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        onchange="this.closest('form').submit()">
                      <label for="type-all" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Todos los
                        tipos</label>
                    </li>
                    <li class="flex items-center">
                      <input id="type-general" type="radio" name="type" value="general_account"
                        {{ request('type') === 'general_account' ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        onchange="this.closest('form').submit()">
                      <label for="type-general"
                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">General</label>
                    </li>
                    <li class="flex items-center">
                      <input id="type-cash" type="radio" name="type" value="cash"
                        {{ request('type') === 'cash' ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        onchange="this.closest('form').submit()">
                      <label for="type-cash"
                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Efectivo</label>
                    </li>
                    <li class="flex items-center">
                      <input id="type-checking" type="radio" name="type" value="checking_account"
                        {{ request('type') === 'checking_account' ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        onchange="this.closest('form').submit()">
                      <label for="type-checking"
                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Cuenta Corriente</label>
                    </li>
                    <li class="flex items-center">
                      <input id="type-credit" type="radio" name="type" value="credit_card"
                        {{ request('type') === 'credit_card' ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        onchange="this.closest('form').submit()">
                      <label for="type-credit"
                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tarjeta de Crédito</label>
                    </li>
                    <li class="flex items-center">
                      <input id="type-savings" type="radio" name="type" value="savings_account"
                        {{ request('type') === 'savings_account' ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        onchange="this.closest('form').submit()">
                      <label for="type-savings"
                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Ahorros</label>
                    </li>
                  </ul>
                </form>
              </div>
            </div>
          </div>
          <div
            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 shrink-0">
            <button type="button" data-modal-target="createAccountModal" data-modal-toggle="createAccountModal"
              class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
              <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path clip-rule="evenodd" fill-rule="evenodd"
                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
              </svg>
              Agregar Cuenta
            </button>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-3 border-t dark:border-gray-700">
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total en cuentas</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalBalance, 2) }}</p>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Número de cuentas</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $accounts->total() }}</p>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>
                <th scope="col" class="px-4 py-3">Nombre</th>
                <th scope="col" class="px-4 py-3">Tipo</th>
                <th scope="col" class="px-4 py-3">Saldo</th>
                <th scope="col" class="px-4 py-3">Icono</th>
                <th scope="col" class="px-4 py-3">
                  <span class="sr-only">Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody>
              @forelse ($accounts as $account)
                <tr class="border-b dark:border-gray-700">
                  <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $account->name }}
                  </td>
                  <td class="px-4 py-3">
                    @php
                      $typeClasses = [
                        'general_account' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'cash' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'checking_account' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                        'credit_card' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                        'savings_account' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                      ];
                    @endphp
                    <span class="text-xs font-medium px-2.5 py-0.5 rounded {{ $typeClasses[$account->type] ?? '' }}">
                      {{ \App\Models\Account::getTypeLabel($account->type) }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    ${{ number_format($account->current_balance, 2) }}
                  </td>
                  <td class="px-4 py-3">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: {{ $account->color }}20; display: inline-flex !important;">
                      <i class="{{ $account->icon }} text-base" style="color: {{ $account->color }}; display: inline-block !important;"></i>
                    </span>
                  </td>
                  <td class="px-4 py-3 flex items-center justify-end overflow-visible">
                    <button id="account-{{ $account->id }}-dropdown-button"
                      data-dropdown-toggle="account-{{ $account->id }}-dropdown"
                      class="inline-flex items-center text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5 text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                      type="button">
                      <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                      </svg>
                    </button>
                    <div id="account-{{ $account->id }}-dropdown"
                      class="hidden fixed z-50 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                      <ul class="py-1 text-sm" aria-labelledby="account-{{ $account->id }}-dropdown-button">
                        <li>
                          <button type="button" data-modal-target="updateAccountModal"
                            data-modal-toggle="updateAccountModal" data-id="{{ $account->id }}"
                            data-name="{{ $account->name }}" data-type="{{ $account->type }}"
                            data-current-balance="{{ $account->current_balance }}"
                            data-credit-limit="{{ $account->credit_limit }}"
                            data-color="{{ $account->color }}" data-icon="{{ $account->icon }}"
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
                          <button type="button" data-modal-target="readAccountModal"
                            data-modal-toggle="readAccountModal" data-id="{{ $account->id }}"
                            data-name="{{ $account->name }}" data-type="{{ $account->type }}"
                            data-current-balance="{{ $account->current_balance }}"
                            data-credit-limit="{{ $account->credit_limit }}"
                            data-color="{{ $account->color }}" data-icon="{{ $account->icon }}"
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
                          <button type="button" data-modal-target="deleteAccountModal"
                            data-modal-toggle="deleteAccountModal" data-id="{{ $account->id }}"
                            data-name="{{ $account->name }}"
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
                  <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No se encontraron cuentas.</td>
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
              class="font-semibold text-gray-900 dark:text-white">{{ $accounts->firstItem() ?? 0 }}-{{ $accounts->lastItem() ?? 0 }}</span>
            de
            <span class="font-semibold text-gray-900 dark:text-white">{{ $accounts->total() }}</span>
          </span>
          {{ $accounts->links() }}
        </nav>
      </div>
    </div>
  </section>

  <!-- Create modal -->
  <div id="createAccountModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Crear Cuenta</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-target="createAccountModal" data-modal-toggle="createAccountModal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Cerrar</span>
          </button>
        </div>
        <form action="{{ route('dashboard.accounts.store') }}" method="POST">
          @csrf
          <div class="grid gap-4 mb-4 sm:grid-cols-2">
            <div>
              <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
              <input type="text" name="name" id="name" value="{{ old('name') }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Nombre de la cuenta" required>
              @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
              <select name="type" id="type"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                <option value="">Seleccionar tipo</option>
                <option value="general_account" {{ old('type') === 'general_account' ? 'selected' : '' }}>General</option>
                <option value="cash" {{ old('type') === 'cash' ? 'selected' : '' }}>Efectivo</option>
                <option value="checking_account" {{ old('type') === 'checking_account' ? 'selected' : '' }}>Cuenta Corriente</option>
                <option value="credit_card" {{ old('type') === 'credit_card' ? 'selected' : '' }}>Tarjeta de Crédito</option>
                <option value="savings_account" {{ old('type') === 'savings_account' ? 'selected' : '' }}>Ahorros</option>
              </select>
              @error('type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="current_balance"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Saldo Actual</label>
              <input type="number" name="current_balance" id="current_balance" step="0.01" min="0"
                value="{{ old('current_balance', 0) }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="0.00" required>
              @error('current_balance')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="credit_limit"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Límite de Crédito</label>
              <input type="number" name="credit_limit" id="credit_limit" step="0.01" min="0"
                value="{{ old('credit_limit') }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 opacity-50"
                placeholder="0.00" disabled>
              @error('credit_limit')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
              @enderror
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
            Crear Cuenta
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Update modal -->
  <div id="updateAccountModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actualizar Cuenta</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-toggle="updateAccountModal">
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
            <div>
              <label for="update-name"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
              <input type="text" name="name" id="update-name"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Nombre de la cuenta" required>
            </div>
            <div>
              <label for="update-type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
              <select name="type" id="update-type"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                <option value="general_account">General</option>
                <option value="cash">Efectivo</option>
                <option value="checking_account">Cuenta Corriente</option>
                <option value="credit_card">Tarjeta de Crédito</option>
                <option value="savings_account">Ahorros</option>
              </select>
            </div>
            <div>
              <label for="update-current-balance"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Saldo Actual</label>
              <input type="number" name="current_balance" id="update-current-balance" step="0.01" min="0"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="0.00" required>
            </div>
            <div>
              <label for="update-credit-limit"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Límite de Crédito</label>
              <input type="number" name="credit_limit" id="update-credit-limit" step="0.01" min="0"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 opacity-50"
                placeholder="0.00" disabled>
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

  <!-- Read modal -->
  <div id="readAccountModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-xl max-h-full">
      <div class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between mb-4 rounded-t sm:mb-5">
          <div class="text-lg text-gray-900 md:text-xl dark:text-white">
            <h3 class="font-semibold" id="read-name"></h3>
          </div>
          <div>
            <button type="button"
              class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex dark:hover:bg-gray-600 dark:hover:text-white"
              data-modal-toggle="readAccountModal">
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
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Tipo</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-type"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Saldo Actual</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-current-balance"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Límite de Crédito</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-credit-limit"></dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Color</dt>
          <dd class="mb-4 font-light flex items-center text-gray-500 sm:mb-5 dark:text-gray-400">
            <span class="w-6 h-6 rounded-full inline-block mr-2" id="read-color"></span>
            <span id="read-color-text"></span>
          </dd>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Icono</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400"><i id="read-icon" class="text-2xl"></i>
          </dd>
        </dl>
        <div class="flex justify-between items-center">
          <button type="button"
            class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
            data-modal-toggle="readAccountModal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete modal -->
  <div id="deleteAccountModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex justify-center items-center w-full md:inset-0 h-full max-h-full overflow-y-auto">
    <div class="absolute inset-0 bg-gray-900/50" onclick="closeModal('deleteAccountModal')"></div>
    <div class="relative p-4 w-full max-w-md max-h-full bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5 my-4">
      <button type="button"
        class="text-gray-400 absolute top-2.5 right-2.5 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
        data-modal-toggle="deleteAccountModal">
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
          id="delete-account-name"></span>"?</p>
      <div class="flex justify-center items-center space-x-4">
        <button data-modal-toggle="deleteAccountModal" type="button"
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

        // Close modal on backdrop click
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
          modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.classList.contains('bg-gray-900/50')) {
              modal.classList.add('hidden');
            }
          });
        });

        const typeSelect = document.getElementById('type');
        const creditLimitInput = document.getElementById('credit_limit');

        const updateTypeSelect = document.getElementById('update-type');
        const updateCreditLimitInput = document.getElementById('update-credit-limit');

        function handleTypeChange(typeSelect, creditLimitInput) {
          if (typeSelect.value === 'credit_card') {
            creditLimitInput.disabled = false;
            creditLimitInput.parentElement.classList.remove('opacity-50');
          } else {
            creditLimitInput.disabled = true;
            creditLimitInput.value = '';
            creditLimitInput.parentElement.classList.add('opacity-50');
          }
        }

        if (typeSelect && creditLimitInput) {
          typeSelect.addEventListener('change', function() {
            handleTypeChange(typeSelect, creditLimitInput);
          });
          handleTypeChange(typeSelect, creditLimitInput);
        }

        if (updateTypeSelect && updateCreditLimitInput) {
          updateTypeSelect.addEventListener('change', function() {
            handleTypeChange(updateTypeSelect, updateCreditLimitInput);
          });
        }

        const updateUrlTemplate = @js(route('dashboard.accounts.update', ['account' => '__ACCOUNT__']));
        const deleteUrlTemplate = @js(route('dashboard.accounts.destroy', ['account' => '__ACCOUNT__']));

        const accountTypeLabels = {
          'general_account': 'General',
          'cash': 'Efectivo',
          'checking_account': 'Cuenta Corriente',
          'credit_card': 'Tarjeta de Crédito',
          'savings_account': 'Ahorros'
        };

        document.querySelectorAll('[data-modal-target="updateAccountModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const type = this.dataset.type;
            const currentBalance = this.dataset.currentBalance;
            const creditLimit = this.dataset.creditLimit;

            document.getElementById('updateForm').action = updateUrlTemplate.replace('__ACCOUNT__', id);
            document.getElementById('update-name').value = name;
            document.getElementById('update-type').value = type;
            document.getElementById('update-current-balance').value = currentBalance;
            document.getElementById('update-credit-limit').value = creditLimit || '';

            handleTypeChange(updateTypeSelect, updateCreditLimitInput);
          });
        });

        document.querySelectorAll('[data-modal-target="readAccountModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const name = this.dataset.name;
            const type = this.dataset.type;
            const currentBalance = this.dataset.currentBalance;
            const creditLimit = this.dataset.creditLimit;
            const color = this.dataset.color;
            const icon = this.dataset.icon;

            document.getElementById('read-name').textContent = name;
            document.getElementById('read-type').textContent = accountTypeLabels[type] || type;
            document.getElementById('read-current-balance').textContent = '$' + parseFloat(currentBalance).toFixed(2);
            document.getElementById('read-credit-limit').textContent = creditLimit ? '$' + parseFloat(creditLimit).toFixed(2) : '—';
            document.getElementById('read-color').style.backgroundColor = color;
            document.getElementById('read-color-text').textContent = color;
            document.getElementById('read-icon').className = icon + ' text-2xl';
          });
        });

        document.querySelectorAll('[data-modal-target="deleteAccountModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('delete-account-name').textContent = name;
            document.getElementById('deleteForm').action = deleteUrlTemplate.replace('__ACCOUNT__', id);
          });
        });
      });
    </script>
  @endpush
@endsection

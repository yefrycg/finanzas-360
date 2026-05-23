@extends('layouts.dashboard')

@push('styles')
  <style>
    input[type="datetime-local"]::-webkit-calendar-picker-indicator,
    input[type="date"]::-webkit-calendar-picker-indicator {
      filter: invert(100%);
    }
    table i[class*="fa-"] {
      display: inline-block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
    #icon-suggestions,
    #update-icon-suggestions {
      color: #ffffff;
    }
    #icon-suggestions i,
    #icon-suggestions span,
    #update-icon-suggestions i,
    #update-icon-suggestions span {
      color: #ffffff !important;
    }
    #icon-suggestions div,
    #update-icon-suggestions div {
      color: #ffffff;
    }
    #icon-suggestions div:hover,
    #update-icon-suggestions div:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
  </style>
@endpush

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Categorías</h1>
    <p class="text-gray-500 dark:text-gray-400">Gestiona las categorías de tus transacciones</p>
  </div>

  <section class="bg-gray-50 dark:bg-gray-900 antialiased">
    <div class="mx-auto max-w-7xl">
      <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-visible">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
          <div class="w-full md:w-auto flex flex-col md:flex-row items-center gap-3">
            <div class="relative w-full md:w-64">
              <form action="{{ route('dashboard.categories.index') }}" method="get" class="flex items-center">
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
                    placeholder="Buscar categorías">
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
                <form action="{{ route('dashboard.categories.index') }}" method="get">
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
                      <input id="type-income" type="radio" name="type" value="income"
                        {{ request('type') === 'income' ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        onchange="this.closest('form').submit()">
                      <label for="type-income"
                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Ingreso</label>
                    </li>
                    <li class="flex items-center">
                      <input id="type-expense" type="radio" name="type" value="expense"
                        {{ request('type') === 'expense' ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-100 border-gray-300 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        onchange="this.closest('form').submit()">
                      <label for="type-expense"
                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Gasto</label>
                    </li>
                  </ul>
                </form>
              </div>
            </div>
          </div>
          <div
            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 shrink-0">
            <button type="button" data-modal-target="createCategoryModal" data-modal-toggle="createCategoryModal"
              class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
              <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path clip-rule="evenodd" fill-rule="evenodd"
                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
              </svg>
              Agregar Categoría
            </button>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-3 border-t dark:border-gray-700">
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Categorías de ingreso</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $incomeCount }}</p>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Categorías de gasto</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $expenseCount }}</p>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>
                <th scope="col" class="px-4 py-3">Nombre</th>
                <th scope="col" class="px-4 py-3">Tipo</th>
                <th scope="col" class="px-4 py-3">Icono</th>
                <th scope="col" class="px-4 py-3">
                  <span class="sr-only">Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody>
              @forelse ($categories as $category)
                <tr class="border-b dark:border-gray-700">
                  <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $category->name }}
                  </td>
                  <td class="px-4 py-3">
                    @if ($category->type === 'income')
                      <span
                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Ingreso</span>
                    @else
                      <span
                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Gasto</span>
                    @endif
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: {{ $category->color }}20">
                      <i class="{{ $category->icon }} text-base" style="color: {{ $category->color }}"></i>
                    </span>
                  </td>
                  <td class="px-4 py-3 flex items-center justify-end overflow-visible">
                    <button id="category-{{ $category->id }}-dropdown-button"
                      data-dropdown-toggle="category-{{ $category->id }}-dropdown"
                      class="inline-flex items-center text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5 text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                      type="button">
                      <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                      </svg>
                    </button>
                    <div id="category-{{ $category->id }}-dropdown"
                      class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                      <ul class="py-1 text-sm" aria-labelledby="category-{{ $category->id }}-dropdown-button">
                        <li>
                          <button type="button" data-modal-target="updateCategoryModal"
                            data-modal-toggle="updateCategoryModal" data-id="{{ $category->id }}"
                            data-name="{{ $category->name }}" data-type="{{ $category->type }}"
                            data-color="{{ $category->color }}" data-icon="{{ $category->icon }}"
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
                          <button type="button" data-modal-target="readCategoryModal"
                            data-modal-toggle="readCategoryModal" data-id="{{ $category->id }}"
                            data-name="{{ $category->name }}" data-type="{{ $category->type }}"
                            data-color="{{ $category->color }}" data-icon="{{ $category->icon }}"
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
                          <button type="button" data-modal-target="deleteCategoryModal"
                            data-modal-toggle="deleteCategoryModal" data-id="{{ $category->id }}"
                            data-name="{{ $category->name }}"
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
                  <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No se encontraron
                    categorías.</td>
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
              class="font-semibold text-gray-900 dark:text-white">{{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }}</span>
            de
            <span class="font-semibold text-gray-900 dark:text-white">{{ $categories->total() }}</span>
          </span>
          {{ $categories->links() }}
        </nav>
      </div>
    </div>
  </section>

  <!-- Create modal -->
  <div id="createCategoryModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div
        class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Crear Categoría</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-target="createCategoryModal" data-modal-toggle="createCategoryModal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <form action="{{ route('dashboard.categories.store') }}" method="POST">
          @csrf
          <div class="grid gap-4 mb-4 sm:grid-cols-2">
            <div>
              <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
              <input type="text" name="name" id="name"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Nombre de la categoría" required>
            </div>
            <div>
              <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
              <select name="type" id="type"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                <option value="">Seleccionar tipo</option>
                <option value="income">Ingreso</option>
                <option value="expense">Gasto</option>
              </select>
            </div>
            <div>
              <label for="color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color</label>
              <div class="flex items-center gap-3">
                <input type="color" id="color-picker" value="#3B82F6"
                  class="h-12 w-12 p-1 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-primary-500 dark:border-gray-600 dark:hover:border-primary-500"
                  oninput="document.getElementById('color-input').value = this.value">
                <input type="text" name="color" id="color-input" value="#3B82F6"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                  placeholder="#3B82F6" required>
              </div>
            </div>
            <div>
              <label for="icon-search"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Icono</label>
              <div class="relative">
                <input type="text" id="icon-search" placeholder="Buscar icono..."
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                  autocomplete="off">
                <input type="hidden" name="icon" id="selected-icon" value="">
                <div id="icon-suggestions"
                  class="hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-700 dark:border-gray-600 max-h-60 overflow-y-auto">
                </div>
                <div id="selected-icon-container" class="mt-2 flex items-center gap-2 hidden">
                  <span class="text-sm text-gray-500 dark:text-gray-400">Seleccionado:</span>
                  <span
                    class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 text-primary-700 rounded dark:bg-primary-900 dark:text-primary-300">
                    <i id="selected-icon-preview" class=""></i>
                    <span id="selected-icon-name"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" id="createCategorySubmitBtn"
            class="text-white inline-flex items-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
            <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd" />
            </svg>
            Crear Categoría
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Update modal -->
  <div id="updateCategoryModal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div
        class="relative p-4 bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5">
        <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actualizar Categoría</h3>
          <button type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-toggle="updateCategoryModal">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
            <span class="sr-only">Close modal</span>
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
                placeholder="Nombre de la categoría" required>
            </div>
            <div>
              <label for="update-type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
              <select name="type" id="update-type"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                required>
                <option value="income">Ingreso</option>
                <option value="expense">Gasto</option>
              </select>
            </div>
            <div>
              <label for="update-color-picker"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Color</label>
              <div class="flex items-center gap-3">
                <input type="color" id="update-color-picker"
                  class="h-12 w-12 p-1 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-primary-500 dark:border-gray-600 dark:hover:border-primary-500"
                  oninput="document.getElementById('update-color').value = this.value">
                <input type="text" name="color" id="update-color"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                  placeholder="#3B82F6" required>
              </div>
            </div>
            <div>
              <label for="update-icon-search"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Icono</label>
              <div class="relative">
                <input type="text" id="update-icon-search" placeholder="Buscar icono..."
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                  autocomplete="off">
                <input type="hidden" name="icon" id="update-selected-icon" value="">
                <div id="update-icon-suggestions"
                  class="hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-700 dark:border-gray-600 max-h-60 overflow-y-auto">
                </div>
                <div class="mt-2 flex items-center gap-2">
                  <span class="text-sm text-gray-500 dark:text-gray-400">Seleccionado:</span>
                  <span id="update-selected-badge"
                    class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 text-primary-700 rounded dark:bg-primary-900 dark:text-primary-300">
                    <i id="update-selected-icon-preview" class=""></i>
                    <span id="update-selected-icon-name"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <button type="submit" id="updateCategorySubmitBtn"
              class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Read modal -->
  <div id="readCategoryModal" tabindex="-1" aria-hidden="true"
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
              data-modal-toggle="readCategoryModal">
              <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd" />
              </svg>
              <span class="sr-only">Close modal</span>
            </button>
          </div>
        </div>
        <dl>
          <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">Tipo</dt>
          <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400" id="read-type"></dd>
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
            data-modal-toggle="readCategoryModal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete modal -->
  <div id="deleteCategoryModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex justify-center items-center w-full md:inset-0 h-full max-h-full overflow-y-auto">
    <div class="absolute inset-0 bg-gray-900/50" onclick="closeModal('deleteCategoryModal')"></div>
    <div class="relative p-4 w-full max-w-md max-h-full bg-white rounded-lg shadow border border-gray-200 dark:border-gray-600 dark:bg-gray-800 sm:p-5 my-4">
      <button type="button"
        class="text-gray-400 absolute top-2.5 right-2.5 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
        data-modal-toggle="deleteCategoryModal">
        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd"
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd" />
        </svg>
        <span class="sr-only">Close modal</span>
      </button>
      <svg class="text-gray-400 dark:text-gray-500 w-11 h-11 mb-3.5 mx-auto" aria-hidden="true" fill="currentColor"
        viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
          clip-rule="evenodd" />
      </svg>
      <p class="mb-4 text-gray-500 dark:text-gray-300">¿Estás seguro de que quieres eliminar "<span
          id="delete-category-name"></span>"?</p>
      <div class="flex justify-center items-center space-x-4">
        <button data-modal-toggle="deleteCategoryModal" type="button"
          class="py-2 px-3 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No,
          cancelar</button>
        <form id="deleteForm" method="POST" class="inline">
          @csrf
          @method('DELETE')
          <button type="submit" id="deleteCategorySubmitBtn"
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

        // Modal toggle using Flowbite
        const modalToggleButtons = document.querySelectorAll('[data-modal-toggle]');
        modalToggleButtons.forEach(button => {
          button.addEventListener('click', function() {
            const modalId = this.dataset.modalToggle;
            const modal = document.getElementById(modalId);
            if (modal) {
              if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
              } else {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
              }
            }
          });
        });

        // Close modal on backdrop click
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
          modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.classList.contains('bg-gray-900/50')) {
              modal.classList.add('hidden');
              document.body.classList.remove('overflow-hidden');
            }
          });
        });

        const allIcons = @js(config('categories.icons', []));

        const updateUrlTemplate = @js(route('dashboard.categories.update', ['category' => '__CATEGORY__']));
        const deleteUrlTemplate = @js(route('dashboard.categories.destroy', ['category' => '__CATEGORY__']));

        // Icon search - Create modal
        const iconSearch = document.getElementById('icon-search');
        const iconSuggestions = document.getElementById('icon-suggestions');
        const selectedIconInput = document.getElementById('selected-icon');
        const selectedIconContainer = document.getElementById('selected-icon-container');
        const selectedIconPreview = document.getElementById('selected-icon-preview');
        const selectedIconName = document.getElementById('selected-icon-name');

        if (iconSearch && iconSuggestions) {
          iconSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            iconSuggestions.innerHTML = '';

            if (searchTerm.length < 1) {
              iconSuggestions.classList.add('hidden');
              return;
            }

            const filtered = allIcons.filter(icon => icon[1].toLowerCase().includes(searchTerm)).slice(0, 5);

            if (filtered.length === 0) {
              iconSuggestions.classList.add('hidden');
              return;
            }

            filtered.forEach(icon => {
              const div = document.createElement('div');
              div.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2';
              div.innerHTML = '<i class="' + icon[0] + ' w-5"></i><span>' + icon[1] + '</span>';
              div.addEventListener('click', function() {
                selectedIconInput.value = icon[0];
                iconSearch.value = icon[1];
                iconSuggestions.classList.add('hidden');
                selectedIconContainer.classList.remove('hidden');
                selectedIconPreview.className = icon[0];
                selectedIconName.textContent = icon[1];
              });
              iconSuggestions.appendChild(div);
            });

            iconSuggestions.classList.remove('hidden');
          });

          document.addEventListener('click', function(e) {
            if (!iconSearch.contains(e.target) && !iconSuggestions.contains(e.target)) {
              iconSuggestions.classList.add('hidden');
            }
          });
        }

        // Icon search - Update modal
        const updateIconSearch = document.getElementById('update-icon-search');
        const updateIconSuggestions = document.getElementById('update-icon-suggestions');
        const updateSelectedIconInput = document.getElementById('update-selected-icon');
        const updateSelectedIconPreview = document.getElementById('update-selected-icon-preview');
        const updateSelectedIconName = document.getElementById('update-selected-icon-name');

        if (updateIconSearch && updateIconSuggestions) {
          updateIconSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            updateIconSuggestions.innerHTML = '';

            if (searchTerm.length < 1) {
              updateIconSuggestions.classList.add('hidden');
              return;
            }

            const filtered = allIcons.filter(icon => icon[1].toLowerCase().includes(searchTerm)).slice(0, 5);

            if (filtered.length === 0) {
              updateIconSuggestions.classList.add('hidden');
              return;
            }

            filtered.forEach(icon => {
              const div = document.createElement('div');
              div.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2';
              div.innerHTML = '<i class="' + icon[0] + ' w-5"></i><span>' + icon[1] + '</span>';
              div.addEventListener('click', function() {
                updateSelectedIconInput.value = icon[0];
                updateIconSearch.value = icon[1];
                updateIconSuggestions.classList.add('hidden');
                updateSelectedIconPreview.className = icon[0];
                updateSelectedIconName.textContent = icon[1];
              });
              updateIconSuggestions.appendChild(div);
            });

            updateIconSuggestions.classList.remove('hidden');
          });

          document.addEventListener('click', function(e) {
            if (!updateIconSearch.contains(e.target) && !updateIconSuggestions.contains(e.target)) {
              updateIconSuggestions.classList.add('hidden');
            }
          });
        }

        // Update modal - load existing data
        document.querySelectorAll('[data-modal-target="updateCategoryModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const type = this.dataset.type;
            const color = this.dataset.color;
            const icon = this.dataset.icon;

            document.getElementById('updateForm').action = updateUrlTemplate.replace('__CATEGORY__', id);
            document.getElementById('update-name').value = name;
            document.getElementById('update-type').value = type;
            document.getElementById('update-color').value = color;
            document.getElementById('update-color-picker').value = color;

            // Find icon name
            const iconData = allIcons.find(i => i[0] === icon) || [icon, icon.split(' ').pop()];
            updateSelectedIconInput.value = icon;
            updateIconSearch.value = iconData[1];
            updateSelectedIconPreview.className = icon;
            updateSelectedIconName.textContent = iconData[1];
          });
        });

        // Read modal
        document.querySelectorAll('[data-modal-target="readCategoryModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const name = this.dataset.name;
            const type = this.dataset.type;
            const color = this.dataset.color;
            const icon = this.dataset.icon;

            document.getElementById('read-name').textContent = name;
            document.getElementById('read-type').textContent = type === 'income' ? 'Ingreso' : 'Gasto';
            document.getElementById('read-color').style.backgroundColor = color;
            document.getElementById('read-color-text').textContent = color;
            document.getElementById('read-icon').className = icon + ' text-2xl';
          });
        });

        // Delete modal
        document.querySelectorAll('[data-modal-target="deleteCategoryModal"][data-modal-toggle]').forEach(button => {
          button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('delete-category-name').textContent = name;
            document.getElementById('deleteForm').action = deleteUrlTemplate.replace('__CATEGORY__', id);
          });
        });
      });
    </script>
  @endpush
@endsection

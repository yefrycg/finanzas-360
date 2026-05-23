@php
  $menuItems = [
      [
          'label' => 'Panel de control',
          'icon' => '<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>',
          'route' => 'dashboard.index',
      ],
      [
          'separator' => true,
      ],
      [
          'label' => 'Categorías',
          'icon' => '<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>',
          'route' => 'dashboard.categories.index',
      ],
      [
          'label' => 'Cuentas',
          'icon' => '<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 011-1.732V10.5a2 2 0 112 0v1.268A2 2 0 016 10zM4 16a2 2 0 001.732-1H14a2 2 0 001.732-1H4z" clip-rule="evenodd"></path></svg>',
          'route' => 'dashboard.accounts.index',
      ],
      [
          'label' => 'Operaciones',
          'icon' => '<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path></svg>',
          'route' => 'dashboard.operations.index',
      ],
      [
          'label' => 'Metas',
          'icon' => '<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12zm0-10a1 1 0 011 1v3.586l2.707 2.707a1 1 0 01-1.414 1.414l-3-3A1 1 0 019 10V8a1 1 0 011-1z" clip-rule="evenodd"></path></svg>',
          'route' => 'dashboard.goals.index',
      ],
      [
          'label' => 'Deudas',
          'icon' => '<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
          'route' => 'dashboard.debts.index',
      ],
      [
          'label' => 'Presupuestos',
          'icon' => '<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v2a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v2a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd"></path></svg>',
          'route' => 'dashboard.budgets.index',
      ],
  ];
@endphp

<aside
  class="fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
  aria-label="Sidenav" id="drawer-navigation">
  <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
    <ul class="space-y-2">
      @foreach ($menuItems as $item)
        @if (isset($item['separator']))
          </ul>
          <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
        @else
          @php
            $isActive = request()->routeIs($item['route']);
          @endphp
          <li>
            <a href="{{ route($item['route']) }}"
              class="flex items-center p-2 text-base font-medium rounded-lg transition duration-75 group {{ $isActive ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
              <span class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ $isActive ? 'text-gray-900 dark:text-white' : '' }}">
                {!! $item['icon'] !!}
              </span>
              <span class="ml-3">{{ $item['label'] }}</span>
            </a>
          </li>
        @endif
      @endforeach
    </ul>
  </div>
</aside>
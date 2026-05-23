<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-full px-5 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gray-200/70 disabled:pointer-events-none disabled:opacity-60 dark:text-gray-200 dark:hover:bg-gray-900 dark:hover:text-white dark:focus-visible:ring-gray-800/80']) }}>
    {{ $slot }}
</button>

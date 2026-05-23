<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full bg-red-600 px-5 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200 ease-out hover:-translate-y-0.5 hover:bg-red-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-red-200/70 active:translate-y-0 disabled:pointer-events-none disabled:opacity-60 dark:focus-visible:ring-red-900/40']) }}>
    {{ $slot }}
</button>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full bg-primary-700 px-5 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200 ease-out hover:-translate-y-0.5 hover:bg-primary-800 hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary-300/60 active:translate-y-0 disabled:pointer-events-none disabled:opacity-60 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus-visible:ring-primary-900/60']) }}>
    {{ $slot }}
</button>

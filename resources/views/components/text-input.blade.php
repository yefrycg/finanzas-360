@props(['disabled' => false])

<input
	@disabled($disabled)
	{{
		$attributes->merge([
			'class' => 'block w-full rounded-xl border border-gray-200 bg-white/80 p-3 text-sm text-gray-900 shadow-sm transition-all duration-200 focus:border-primary-400 focus:ring-4 focus:ring-primary-200/60 focus:outline-none disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-800 dark:bg-gray-950/40 dark:text-white dark:placeholder-gray-500 dark:focus:border-primary-600 dark:focus:ring-primary-900/40',
		])
	}}
>

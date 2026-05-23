@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-2 block text-sm font-medium text-gray-900 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label>

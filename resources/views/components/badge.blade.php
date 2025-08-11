<span @class([
    'text-xs font-medium px-2.5 py-0.5 rounded',
    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => $type === 'success',
    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => $type === 'warning',
    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $type === 'danger',
    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' => $type === 'theme',
    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' => $type === 'secondary',
])>
    {{ $slot }}
</span>
<li>
    <a href="{{ $link }}" wire:navigate.hover
       @class(['flex items-center p-2 text-base text-gray-900 rounded-xl hover:bg-gray-200 group dark:text-gray-200 dark:hover:bg-gray-700', 'hover:text-gray-800 text-white font-bold bg-gray-700' => $active])>
       {!! $icon !!}
       <span class="ltr:ml-3 rtl:mr-3" sidebar-toggle-item>{{ $name }}</span>
        @if(isset($badge) && $badge > 0)
            <span class="ml-3 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                {{ $badge }}
            </span>
        @endif
    </a>
</li>

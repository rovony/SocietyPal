 {{-- New Section --}}
 <div>

    @if ($customMenu)
        <div id="{{ Str::slug($customMenu->menu_name) }}"> </div>

        <div>
            <div class="px-4 mx-auto max-w-7xl lg:px-8 lg:py-5">
                <ul class="flex flex-col gap-4">
                </ul>

                <div id="{{ Str::slug($customMenu->menu_name) }}"
                    class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
                    <!-- Title -->
                    <div class="mx-auto mb-8 text-center lg:mb-14">
                        <h2 class="text-3xl font-bold text-gray-800 lg:text-4xl dark:text-neutral-200">
                            {{ $customMenu->menu_name }}
                        </h2>
                    </div>
                    <!-- End Title -->

                    <!-- Content -->
                    <div class="text-gray-600 dark:text-neutral-400">
                        {!! $customMenu->menu_content !!}
                    </div>
                    <!-- End Content -->
                </div>
            </div>
        </div>
    @endif
 </div>



    {{-- End New Section --}}

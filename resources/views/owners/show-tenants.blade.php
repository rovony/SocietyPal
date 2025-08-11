<div class="flex flex-col mb-12">
    <div class="overflow-x-auto ">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                #
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('app.name')
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.user.email')
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.user.phone')
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.user.status')
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.apartmentNumber')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                        @forelse ($tenants as $tenant)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='tenant-{{ $tenant->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $loop->index+1 }}
                            </td>

                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                <a href="{{ route('tenants.show', $tenant->id) }}"
                                    class="text-base font-semibold hover:underline dark:text-black-400">
                                {{$tenant->user->name}}
                                </a>
                            </td>

                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $tenant->user->email ?? '--' }}
                            </td>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $tenant->user->phone_number ?? '--' }}
                            </td>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                <div class="flex items-center">
                                    @if(isset($tenant->status) && $tenant->status === 'current_resident')
                                        @lang('modules.tenant.currentResident')
                                    @elseif(isset($tenant->status))
                                        {{ ucfirst($tenant->status) }}
                                    @else
                                        --
                                    @endif
                                </div>
                            </td>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $tenant->apartments->pluck('apartment_number')->implode(', ') }}
                            </td>
                        
                        </tr>
                        @empty
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="dark:text-gray-400" colspan="12">
                                <div class="py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    @lang('messages.noTenantFound')
                                </div>
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
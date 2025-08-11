<div class="px-4 pt-6 xl:gap-4 dark:bg-gray-900">
    <h3 class="text-lg font-bold dark:text-white mb-4">@lang('modules.user.familyMembers')</h3>

    <div class="col-span-full">
        <div class="grid lg:grid-cols-1 lg:gap-6 mb-4">
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center space-x-4">
                    <div class="w-1/2">
                        <div>
                            <x-label for="familyMemberName" value="{{ __('modules.user.addFamilyMember') }}" required/>
                            <x-input id="familyMemberName" class="block mt-1 w-full" type="text" wire:model='familyMemberName' autocomplete="off"/>
                            <x-input-error for="familyMemberName" class="mt-2" />
                        </div>
                    </div>
            
                    <div class="flex items-center mt-4">
                        <x-button wire:click="addFamilyMember">@lang('modules.user.add')</x-button>
                    </div>
                </div>
                <div class="w-full mt-3">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    #
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.user.familyMemberName')
                                </th>
    
                                <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>
    
                            @forelse($familyMembers as $index => $familyMember)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='family-member-{{ $familyMember['id'] . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $loop->index+1 }}
                                </td>

                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $familyMember['name'] }}     
                                </td>
    
                                <td class="p-4 space-x-2 whitespace-nowrap text-right">
                                    <x-danger-button  wire:click="removeFamilyMember({{ $familyMember['id'] }})">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </x-danger-button>
                                </td>
                            </tr>
                            @empty
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="dark:text-gray-400" colspan="12">
                                    <div class="py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                        @lang('modules.user.noFamilyMembers')
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
</div>
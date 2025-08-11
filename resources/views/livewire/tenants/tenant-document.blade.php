<div class="p-6 bg-white dark:bg-gray-900 rounded-lg shadow-md" id="documents-content" role="tabpanel" aria-labelledby="documents-tab">
    <h3 class="text-lg font-bold dark:text-white mb-4">@lang('modules.tenant.tenantDocuments')</h3>
    <div class="col-span-full">
        <div class="grid lg:grid-cols-1 lg:gap-6 mb-4">
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-start space-x-4">
                    <div class="w-1/2">
                        <x-label for="documentName" value="{{ __('modules.tenant.addDocument') }}" required/>
                        <x-input id="documentName" class="block mt-1 w-full" type="text" wire:model='documentName' autocomplete="off"/>
                        <x-input-error for="documentName" class="mt-2" />
                    </div>
                    <div class="w-1/2">
                        <x-label for="documentFile" value="{{ __('modules.user.uploadDocument') }}" required/>
                        <input class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 text-slate-500 mt-1" type="file" wire:model="documentFile">
                        <x-input-error for="documentFile" class="mt-2" />
                    </div>
                </div>
                <div class="flex w-full pb-4 space-x-4 mt-6">
                    <x-button wire:click="addDocument">@lang('modules.tenant.add')</x-button>
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
                                    @lang('modules.user.documentName')
                                </th>

                                <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                            @forelse($documents as $document)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='document-{{ $document['id'] . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $loop->index+1 }}
                                </td>

                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $document['filename'] }}
                                </td>

                                <td class="p-4 space-x-2 whitespace-nowrap text-right">
                                    <a href="{{ asset_url_local_s3($document['hashname']) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 4.5C6.75 4.5 3 12 3 12s3.75 7.5 9 7.5 9-7.5 9-7.5-3.75-7.5-9-7.5zM12 16.5c-2.25 0-4.5-1.5-4.5-4.5s2.25-4.5 4.5-4.5 4.5 1.5 4.5 4.5-2.25 4.5-4.5 4.5zM12 10.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                                        </svg>
                                    </a>
                                    <a href="{{ asset_url_local_s3($document['hashname']) }}" download class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                            <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </a>
                                    <x-danger-button  wire:click="removeDocument({{ $document['id'] }})">
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
                                        @lang('modules.user.noUserDocuments')
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
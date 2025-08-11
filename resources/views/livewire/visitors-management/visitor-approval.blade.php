<div>
    <div class="max-w-3xl mx-auto py-4 px-4">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            
            {{-- Header --}}
            <div class="text-white justify-center bg-skin-base hover:bg-skin-base/[.8] sm:w-auto dark:bg-skin-base dark:hover:bg-skin-base/[.8] font-semibold rounded-lg text-sm px-5 py-2.5 text-center">
                <h2 class="text-lg font-semibold">@lang('modules.visitorManagement.visitorApproval')</h2>
                <span class="text-sm text-blue-200">@lang('modules.visitorManagement.visitorId'){{ $visitor->id }}</span>
            </div>
    
            {{-- Body --}}
            <div class="p-6">
    
                {{-- Avatar --}}
                <div class="flex flex-col items-center mb-6">
                    <x-avatar-image 
                        :src="$visitor->visitor_photo_url" 
                        :alt="$visitor->visitor_name" 
                        :name="$visitor->visitor_name"
                        class="w-28 h-28 rounded-full shadow-md mb-2"
                    />
                </div>

                <div>
                    <div class="px-4 xl:gap-4 dark:bg-gray-900">
                        <div class="col-span-full">
                            <div class="grid grid-cols-1 justify-center mb-4">
                                <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800 max-w-xl w-full">
                                    <div class="items-center flex sm:space-x-4 space-x-4">
                                        <div class="space-y-3">
                                            <dl class="flex flex-col sm:flex-row gap-1">
                                                <dt class="min-w-40">
                                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.visitorManagement.visitorMobile')</span>
                                                </dt>
                                                <dd>
                                                    <ul>
                                                        <li class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                                            {{ $visitor->phone_number ?? '--' }}
                                                        </li>
                                                    </ul>
                                                </dd>
                                            </dl>
            
                                            <dl class="flex flex-col sm:flex-row gap-1">
                                                <dt class="min-w-40">
                                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.visitorManagement.visitorAddress')</span>
                                                </dt>
                                                <dd>
                                                    <ul>
                                                        <li
                                                            class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                                            {{ $visitor->address }}
                                                        </li>
                                                    </ul>
                                                </dd>
                                            </dl>
            
                                            <dl class="flex flex-col sm:flex-row gap-1">
                                                <dt class="min-w-40">
                                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.visitorManagement.dateOfVisit')</span>
                                                </dt>
                                                <dd>
                                                    <ul>
                                                        <li
                                                            class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                                            {{ $visitor->date_of_visit
                                                                ? \Carbon\Carbon::parse($visitor->date_of_visit)->timezone(timezone())->format('d F Y')
                                                                : 'N/A' }}
                                                        </li>
                                                    </ul>
                                                </dd>
                                            </dl>

                                            <dl class="flex flex-col sm:flex-row gap-1">
                                                <dt class="min-w-40">
                                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.visitorManagement.inTime')</span>
                                                </dt>
                                                <dd>
                                                    <ul>
                                                        <li
                                                            class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                                            {{ $visitor->in_time
                                                                ? \Carbon\Carbon::parse($visitor->in_time)->timezone(timezone())->format('h:i A')
                                                                : 'N/A' }}
                                                        </li>
                                                    </ul>
                                                </dd>
                                            </dl>
                                            <dl class="flex flex-col sm:flex-row gap-1">
                                                <dt class="min-w-40">
                                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.societyApartmentNumber')</span>
                                                </dt>
                                                <dd>
                                                    <ul>
                                                        <li
                                                            class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                                            {{ $visitor->apartment->apartment_number ?? 'N/A' }}
                                                        </li>
                                                    </ul>
                                                </dd>
                                            </dl>
            
                                        </div>
                                    </div>  
                                  
                                </div>  
                                
                            </div>
                        </div>
                    </div>
                </div>
    
                {{-- Approval Buttons --}}
                @if ($visitor->status === 'pending')
                    <div class="flex justify-center gap-4">
                        <button 
                            wire:click="approve"
                            class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-md transition duration-200"
                        >
                            <i class="bi bi-check-circle mr-1"></i> @lang('app.allow')
                        </button>
        
                        <button 
                            wire:click="deny"
                            class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-md transition duration-200"
                        >
                            <i class="bi bi-x-circle mr-1"></i>  @lang('app.deny')
                        </button>
                    </div>
                @else
                    <div class="flex justify-center mt-4">
                        <span class="px-4 py-1 text-sm font-semibold rounded-full 
                            @if($visitor->status === 'allowed') bg-green-100 text-green-800 
                            @elseif($visitor->status === 'not_allowed') bg-red-100 text-red-800 
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $visitor->status === 'not_allowed' ? 'Denied' : ucwords(str_replace('_', ' ', $visitor->status)) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

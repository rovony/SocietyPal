<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header with Stats -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    @lang('menu.openAndPendingTickets')
                </h2>
            </div>
            @if($tickets->count() > 0)
                <div class="flex space-x-2">
                    <span class="px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded-full">
                        {{ $tickets->where('status', 'pending')->count() }} @lang('modules.tickets.pending')
                    </span>
                    <span class="px-3 py-1 text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 rounded-full">
                        {{ $tickets->where('status', 'open')->count() }} @lang('modules.tickets.open')
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Tickets List with Custom Scrollbar -->
    <div class="max-h-[320px] overflow-y-auto overflow-x-hidden p-4
                [&::-webkit-scrollbar]:w-1.5
                [&::-webkit-scrollbar-track]:rounded-lg
                [&::-webkit-scrollbar-thumb]:rounded-lg
                [&::-webkit-scrollbar-track]:bg-gray-100
                dark:[&::-webkit-scrollbar-track]:bg-gray-700
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-thumb]:bg-gray-500
                hover:[&::-webkit-scrollbar-thumb]:bg-gray-400
                dark:hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
        
        <div class="space-y-3">
            @forelse ($tickets as $ticket)
                <a href="{{ route('tickets.show', $ticket->id) }}" 
                   class="block relative group">
                    <!-- Content -->
                    <div class="relative p-4 bg-white dark:bg-gray-800 rounded-lg border 
                              {{ $ticket->status === 'pending' ? 'border-gray-100 dark:border-gray-700 hover:border-yellow-200 dark:hover:border-yellow-700' : 
                                 'border-gray-100 dark:border-gray-700 hover:border-red-200 dark:hover:border-red-700' }}
                              transition-all duration-300 ease-in-out hover:shadow-lg">
                        
                        <div class="grid grid-cols-3 items-center">
                            <!-- Column 1: User Info -->
                            <div class="flex items-center space-x-3">
                                <!-- Ticket Icon -->
                                <div class="p-2 rounded-lg
                                    {{ $ticket->status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/30' : 
                                       'bg-red-50 dark:bg-red-900/30' }}">
                                    <svg class="w-5 h-5 
                                        {{ $ticket->status === 'pending' ? 'text-yellow-500 dark:text-yellow-400' : 
                                           'text-red-500 dark:text-red-400' }}"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $ticket->ticket_number }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $ticket->user->name ?? '--' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Column 2: Agent -->
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $ticket->agent->name ?? '--' }}
                                </span>
                            </div>

                            <!-- Column 3: Status -->
                            <div class="flex items-center justify-end">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                           {{ $ticket->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 
                                              'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                                 <!-- View Details -->
                                 <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span class="{{ $ticket->status === 'pending' ? 'text-yellow-600 dark:text-yellow-300' : 
                                                   'text-red-600 dark:text-red-300' }} text-sm flex items-center">
                                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full 
                                bg-gray-100 dark:bg-gray-700 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">
                        @lang('messages.noTicketFound')
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>

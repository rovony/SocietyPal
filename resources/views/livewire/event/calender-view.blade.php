<div class="items-center justify-between block p-4 bg-white sm:flex dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
    <div id="calendar-container" wire:ignore>
        <div id="calendar"></div>
    </div>
    </div>

    <x-right-modal wire:model.live="showEventDetailModal">
        <x-slot name="title">
            {{ __("modules.event.viewEvent") }}
        </x-slot>

        <x-slot name="content">
            @if ($showEvent)
                @livewire('forms.showEvent', ['event' => $showEvent], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEventDetailModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.js"></script>
        <script>
            let calendar; // define globally

            document.addEventListener('DOMContentLoaded', function () {
                let calendarEl = document.getElementById('calendar');

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: function (fetchInfo, successCallback, failureCallback) {
                        fetch("{{ route('calendar.events') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                start: fetchInfo.startStr,
                                end: fetchInfo.endStr,
                            })
                        })
                        .then(res => res.json())
                        .then(data => successCallback(data))
                        .catch(err => failureCallback(err));
                    },
                    eventContent: function(arg) {
                        const time = arg.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        const title = arg.event.title;

                        return {
                            html: `<div style="line-height:1.2">
                                        <strong>${time}</strong><br>
                                        <span>${title}</span>
                                </div>`
                        };
                    },
                    eventClick: function(info) {
                        Livewire.dispatch('showEventDetail', { id: info.event.id });
                    }
                });

                calendar.render();
            });

            // 👇 Render calendar again when the tab is shown

        </script>

        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.css" rel="stylesheet" />
    @endpush
</div>

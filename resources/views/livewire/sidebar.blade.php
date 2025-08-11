<div>
    <aside id="sidebar"
        class="fixed top-0 z-20 flex flex-col flex-shrink-0 hidden w-64 h-full pt-16 font-normal duration-75 ltr:left-0 rtl:right-0 lg:flex transition-width menu-collapsed:hidden"
        aria-label="Sidebar">
        <div
            class="relative flex flex-col flex-1 min-h-0 pt-0 border-gray-200 bg-gray-50 ltr:border-r rtl:border-l dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto mb-16 [&::-webkit-scrollbar]:w-1.5
                [&::-webkit-scrollbar-track]:rounded-xl
                [&::-webkit-scrollbar-thumb]:rounded-xl
                [&::-webkit-scrollbar-track]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-gray-600
                [&::-webkit-scrollbar-thumb]:bg-skin-base/[.5]
                hover:[&::-webkit-scrollbar-thumb]:bg-skin-base/[.8]">
                <div
                    class="flex-1 px-3 space-y-1 divide-y divide-gray-200 bg-gray-50 dark:bg-gray-800 dark:divide-gray-700">
                    <ul class="pb-2 space-y-2">

                    @if (!role_permissions())
                        @php
                        role_permissions();
                        @endphp
                    @endif

                    @livewire('sidebar-menu-item', ['name' => __('menu.dashboard'), 'icon' => 'dashboard', 'link' => route('dashboard'), 'active' => request()->routeIs('dashboard')])

                    @if (user_can('Show User') || isRole() == 'Manager' || isRole() == 'Guard')
                        @if($this->hasModule('User') && $this->hasSocietyModule('User'))
                            @livewire('sidebar-menu-item', ['name' => __('menu.users'), 'icon' => 'users', 'link' => route('users.index'), 'active' => request()->routeIs('users.index') || request()->routeIs('users.show')])
                        @endif
                    @endif

                    @if (user_can('Show Owner') || isRole() == 'Owner' || isRole() == 'Tenant')
                        @if($this->hasModule('Owner')  && $this->hasSocietyModule('Owner'))
                            @livewire('sidebar-menu-item', ['name' => __('menu.owners'), 'icon' => 'owners', 'link' => route('owners.index'), 'active' => request()->routeIs('owners.index') || request()->routeIs('owners.show')])
                        @endif
                    @endif

                    @if (((user_can('Show Tenant') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Tenant')  && $this->hasSocietyModule('Tenant')) || ((user_can('Show Rent') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Rent')  && $this->hasSocietyModule('Rent')))
                        <x-sidebar-dropdown-menu :name='__("menu.tenantManagement")' icon='tenants' :active='request()->routeIs(["tenants.*" , "rents.*"])'>
                            @if($this->hasModule('Tenant')  && $this->hasSocietyModule('Tenant'))
                                @if (user_can('Show Tenant') || isRole() == 'Owner' || isRole() == 'Tenant')
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.tenantManagement'), 'link' => route('tenants.index'), 'active' => request()->routeIs('tenants.index') || request()->routeIs('tenants.show')])
                                @endif
                            @endif

                            @if (user_can('Show Rent') || isRole() == 'Owner' || isRole() == 'Tenant')
                                @if($this->hasModule('Rent') && $this->hasSocietyModule('Rent'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.rentManagement'), 'link' => route('rents.index'), 'active' => request()->routeIs('rents.index')])
                                @endif
                            @endif
                        </x-sidebar-dropdown-menu>
                    @endif

                    @if (
                        (user_can('Show Tower') && $this->hasModule('Tower')  && $this->hasSocietyModule('Tower')) || 
                        (user_can('Show Floor') && $this->hasModule('Floor') && $this->hasSocietyModule('Floor')) || 
                        ((user_can('Show Apartment') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Apartment') && $this->hasSocietyModule('Apartment')) || 
                        ((user_can('Show Parking') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard') && $this->hasModule('Parking') && $this->hasSocietyModule('Parking'))
                    )                        
                        <x-sidebar-dropdown-menu :name='__("menu.apartmentManagement")' icon='apartments' :active='request()->routeIs(["towers.*" , "floors.*" , "apartments.*" , "parkings.*"])'>
                            @if(user_can('Show Tower'))
                                @if($this->hasModule('Tower') && $this->hasSocietyModule('Tower'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.towerManagment'), 'link' => route('towers.index'), 'active' => request()->routeIs('towers.index') || request()->routeIs('towers.show')])
                                @endif
                            @endif

                            @if(user_can('Show Floor'))
                                @if($this->hasModule('Floor') && $this->hasSocietyModule('Floor'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.floorManagment'), 'link' => route('floors.index'), 'active' => request()->routeIs('floors.index')|| request()->routeIs('floors.show')])
                                @endif
                            @endif

                            @if (user_can('Show Apartment')|| isRole() == 'Owner' || isRole() == 'Tenant')
                                @if($this->hasModule('Apartment') && $this->hasSocietyModule('Apartment'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.apartmentManagement'), 'link' => route('apartments.index'), 'active' => request()->routeIs('apartments.index')|| request()->routeIs('apartments.show')])
                                @endif
                            @endif

                            @if(user_can('Show Parking') || isRole() == 'Owner' || isRole() == 'Tenant'|| isRole() == 'Guard')
                                @if($this->hasModule('Parking') && $this->hasSocietyModule('Parking'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.parkingManagement'), 'link' => route('parkings.index'), 'active' => request()->routeIs('parkings.index')])
                                @endif
                            @endif
                        </x-sidebar-dropdown-menu>
                    @endif

                    @if (((user_can('Show Amenities') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Amenities') && $this->hasSocietyModule('Amenities')) || ((user_can('Show Book Amenity') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Book Amenity') && $this->hasSocietyModule('Book Amenity')))
                        <x-sidebar-dropdown-menu :name='__("menu.amenitiesManagement")' icon='amenities' :active='request()->routeIs(["amenities.*" , "book-amenity.*"])'>
                            @if(user_can('Show Amenities') || isRole() == 'Owner' || isRole() == 'Tenant')
                                @if($this->hasModule('Amenities') && $this->hasSocietyModule('Amenities'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.amenitiesManagement'), 'link' => route('amenities.index'), 'active' => request()->routeIs('amenities.index')])
                                @endif
                            @endif

                            @if (user_can('Show Book Amenity') || isRole() == 'Owner' || isRole() == 'Tenant')
                                @if($this->hasModule('Book Amenity') && $this->hasSocietyModule('Book Amenity'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.bookAmenity'), 'link' => route('book-amenity.index'), 'active' => request()->routeIs('book-amenity.index')])
                                @endif
                            @endif
                        </x-sidebar-dropdown-menu>
                    @endif

                    @if (
                        ((user_can('Show Utility Bills') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Utility Bills') && $this->hasSocietyModule('Utility Bills')) || 
                        (user_can('Show Common Area Bills') && $this->hasModule('Common Area Bills') && $this->hasSocietyModule('Common Area Bills')) || 
                        ((user_can('Show Maintenance') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Maintenance') && $this->hasSocietyModule('Maintenance'))
                    )                        
                        <x-sidebar-dropdown-menu :name='__("menu.bills")' icon='bills' :active='request()->routeIs(["utilityBills.*" , "common-area-bill.*" , "maintenance.*"])'>
                            @if(user_can('Show Utility Bills')|| isRole() == 'Owner' || isRole() == 'Tenant')
                                @if($this->hasModule('Utility Bills') && $this->hasSocietyModule('Utility Bills'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.utilityBillsManagement'), 'link' => route('utilityBills.index'), 'active' => request()->routeIs('utilityBills.index')])
                                @endif
                            @endif

                            @if(user_can('Show Common Area Bills'))
                                @if($this->hasModule('Common Area Bills') && $this->hasSocietyModule('Common Area Bills'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.commonAreaBill'), 'link' => route('common-area-bill.index'), 'active' => request()->routeIs('common-area-bill.index')])
                                @endif
                            @endif

                            @if(user_can('Show Maintenance') || isRole() == 'Owner' || isRole() == 'Tenant')
                                @if($this->hasModule('Maintenance') && $this->hasSocietyModule('Maintenance'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.maintenanceManagement'), 'link' => route('maintenance.index'), 'active' => request()->routeIs('maintenance.index') || request()->routeIs('maintenance.show')])
                                @endif
                            @endif
                        </x-sidebar-dropdown-menu>
                    @endif

                    @if (isRole() == 'Admin')
                        @livewire('sidebar-menu-item', ['name' => __('menu.offlineRequest'), 'icon' => 'maintenance_offline_plan_request', 'link' => route('offline.offline-plan-request'), 'active' => request()->routeIs('offline.offline-plan-request'), 'badge' => \App\Models\MaintenanceApartment::where('paid_status', 'payment_requested')
                            ->whereHas('maintenanceManagement', function ($query) {
                                $query->where('society_id', user()->society_id);
                            })
                            ->count()])
                    @endif
                    
                    @if ((user_can('Show Assets') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Assets')  && $this->hasSocietyModule('Assets'))
                        <x-sidebar-dropdown-menu :name='__("menu.assetsManagement")' icon='assets' :active='request()->routeIs(["assets.*", "asset-maintenance.*", "asset-issue.*"])'>
                            @if(user_can('Show Assets') || isRole() == 'Owner' || isRole() == 'Tenant')
                                @if($this->hasModule('Assets'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.assetsManagement'), 'link' => route('assets.index'), 'active' => request()->routeIs('assets.index')|| request()->routeIs('assets.show')])
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.maintenanceSchedule'), 'link' => route('asset-maintenance.index'), 'active' => request()->routeIs('asset-maintenance.index')])
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.issueReport'), 'link' => route('asset-issue.index'), 'active' => request()->routeIs('asset-issue.index')])
                                @endif
                            @endif
                        </x-sidebar-dropdown-menu>
                    @endif

                    @if(user_can('Show Tickets') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
                        @if($this->hasModule('Tickets') && $this->hasSocietyModule('Tickets'))
                            @livewire('sidebar-menu-item', ['name' => __('menu.ticketManagement'), 'icon' => 'tickets', 'link' => route('tickets.index'), 'active' => request()->routeIs('tickets.index') || request()->routeIs('tickets.show')])
                        @endif
                    @endif

                    @if (user_can('Show Notice Board') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
                        @if($this->hasModule('Notice Board') && $this->hasSocietyModule('Notice Board'))
                            @livewire('sidebar-menu-item', ['name' => __('menu.noticeBoard'), 'icon' => 'notice_board', 'link' => route('notices.index'), 'active' => request()->routeIs('notices.index')])
                        @endif
                    @endif

                    @if (
                        ((user_can('Show Service Provider') || isRole() == 'Owner' || isRole() == 'Tenant') && $this->hasModule('Service Provider') && $this->hasSocietyModule('Service Provider')) || 
                        ((user_can('Show Service Time Logging') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard') && $this->hasModule('Service Time Logging') && $this->hasSocietyModule('Service Time Logging'))
                    )
                        <x-sidebar-dropdown-menu :name='__("menu.serviceManagement")' icon='service_management' :active='request()->routeIs(["service-management.*" , "service-type.*" , "service-clock-in-out.*"])'>
                            @if(user_can('Show Service Provider') || isRole() == 'Owner' || isRole() == 'Tenant')
                                @if($this->hasModule('Service Provider') && $this->hasSocietyModule('Service Provider'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.serviceManagement'), 'link' => route('service-management.index'), 'active' => request()->routeIs('service-management.index')])
                                @endif
                            @endif

                            @if(user_can('Show Service Time Logging') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
                                @if($this->hasModule('Service Time Logging') && $this->hasSocietyModule('Service Time Logging'))
                                    @livewire('sidebar-dropdown-menu', ['name' => __('menu.serviceClockInOut'), 'link' => route('service-clock-in-out.index'), 'active' => request()->routeIs('service-clock-in-out.index')])
                                @endif
                            @endif

                        </x-sidebar-dropdown-menu>
                    @endif

                    @if (user_can('Show Visitors')|| isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
                        @if($this->hasModule('Visitors') && $this->hasSocietyModule('Visitors'))
                            @livewire('sidebar-menu-item', ['name' => __('menu.visitorsManagement'), 'icon' => 'visitor_management', 'link' => route('visitors.index'), 'active' => request()->routeIs('visitors.index') || request()->routeIs('visitors.approval')])
                        @endif
                    @endif

                    @if (user_can('Show Event') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
                        @if($this->hasModule('Event') && $this->hasSocietyModule('Event'))
                            @livewire('sidebar-menu-item', ['name' => __('menu.events'), 'icon' => 'event', 'link' => route('events.index'), 'active' => request()->routeIs('events.index')])
                        @endif
                    @endif

                    @if (user_can('Show Forum') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
                        @if($this->hasModule('Forum') && $this->hasSocietyModule('Forum'))
                            @livewire('sidebar-menu-item', ['name' => __('menu.societyForum'), 'icon' => 'society_forum', 'link' => route('society-forum.index'), 'active' => request()->routeIs('society-forum.index') || request()->routeIs('society-forum.show')])
                        @endif
                    @endif

                    @if (isRole() == 'Admin' || isRole() == 'Owner' || isRole() == 'Tenant')
                        <x-sidebar-dropdown-menu :name='__("menu.report")' icon='reports' :active='request()->routeIs(["reports.maintenance", "reports.financial"])'>
                            @if($this->hasModule('Maintenance'))
                                @livewire('sidebar-dropdown-menu', ['name' => __('menu.maintenanceReport'), 'link' => route('reports.maintenance'), 'active' => request()->routeIs('reports.maintenance')])
                            @endif
                            @livewire('sidebar-dropdown-menu', ['name' => __('menu.financialReport'), 'link' => route('reports.financial'), 'active' => request()->routeIs('reports.financial')])
                        </x-sidebar-dropdown-menu>
                    @endif

                    @foreach (custom_module_plugins() as $item)
                        @includeIf(strtolower($item) . '::sections.sidebar')
                    @endforeach
                    
                    @if (user_can('Manage Settings'))
                        @livewire('sidebar-menu-item', ['name' => __('menu.settings'), 'icon' => 'settings', 'link' => route('settings.index'), 'active' => request()->routeIs('settings.index')])
                    @endif
                    </ul>

                </div>
            </div>

        </div>
    </aside>

    <div class="fixed inset-0 z-10 hidden bg-gray-900/50 dark:bg-gray-900/90" id="sidebarBackdrop"></div>


</div>

<div class="grid lg:grid-cols-3">
    <style>
        .onboarding-steps .button-cancel {
            display: none
        }
    </style>


    <div class="bg-gray-100 lg:h-screen p-4 sm:flex items-center dark:bg-gray-800 dark:border-gray-700">

        <section class="py-8  md:py-16 px-6">
            <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
                <div class="mx-auto max-w-3xl space-y-6 sm:space-y-8">

                    <div class="mb-4">
                        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('app.hello')
                            {{ user()->name }}!</h1>
                    </div>

                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        @lang('modules.onboarding.completeSteps')</h2>


                    <ol class="relative border-s border-gray-200 dark:border-gray-700 space-y-8">


                        <li class="ms-6">
                            <span
                                class="absolute -start-2.5 flex h-5 w-5 items-center justify-center rounded-full bg-green-700 ring-8 ring-white dark:bg-green-800 dark:ring-gray-900">
                                <svg class="h-3 w-3 text-green-50 dark:text-green-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                </svg>
                            </span>
                            <h3 class="mb-1.5 text-base font-semibold leading-none text-gray-900 dark:text-white">
                                @lang('modules.onboarding.addSocietyHeading')</h3>
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400 tracking-wide">
                                @lang('modules.onboarding.addSocietyInfo')</p>
                        </li>

                        <li class="ms-6">
                            <span @class(['absolute -start-2.5 flex h-5 w-5 items-center justify-center rounded-full
                                ring-8 ring-white dark:ring-gray-900', 'bg-gray-100 dark:bg-gray-800'=>
                                !$onboardingSteps->add_tower_completed, 'bg-green-700 dark:bg-green-800' =>
                                $onboardingSteps->add_tower_completed])>
                                <svg aria-hidden="true" @class(['h-3 w-3', 'text-gray-500 dark:text-gray-400'=>
                                    !$onboardingSteps->add_tower_completed, 'text-green-50 dark:text-green-400' =>
                                    $onboardingSteps->add_tower_completed])
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                </svg>
                            </span>
                            <h3 @class(["mb-1.5 text-base font-semibold leading-none text-gray-900 dark:text-white flex
                                justify-between"])>@lang('modules.onboarding.addTowerHeading')

                                @if ($onboardingSteps->add_tower_completed)
                                <a href="javascript:;" wire:click="showAddTowerForm"
                                    class="text-gray-900 bg-gray-300 hover:bg-gray-800 hover:text-white focus:ring-4 focus:ring-gray-300 font-medium rounded-md text-xs px-2.5 py-1 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800 dark:text-gray-100">@lang('app.addMore')</a>
                                @endif
                            </h3>
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400 tracking-wide">
                                @lang('modules.onboarding.addTowerInfo')</p>
                        </li>

                        <li class="ms-6">
                            <span @class(['absolute -start-2.5 flex h-5 w-5 items-center justify-center rounded-full
                                ring-8 ring-white dark:ring-gray-900', 'bg-gray-100 dark:bg-gray-800'=>
                                !$onboardingSteps->add_floor_completed, 'bg-green-700 dark:bg-green-800' =>
                                $onboardingSteps->add_floor_completed])>
                                <svg aria-hidden="true" @class(['h-3 w-3', 'text-gray-500 dark:text-gray-400'=>
                                    !$onboardingSteps->add_floor_completed, 'text-green-50 dark:text-green-400' =>
                                    $onboardingSteps->add_floor_completed])
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                </svg>
                            </span>
                            <h3 @class(["mb-1.5 text-base font-semibold leading-none text-gray-900 dark:text-white flex
                                justify-between"])>@lang('modules.onboarding.addFloorHeading')
                                @if ($onboardingSteps->add_floor_completed)
                                <a href="javascript:;" wire:click="showAddFloorForm"
                                    class="text-gray-900 bg-gray-300 hover:bg-gray-800 hover:text-white focus:ring-4 focus:ring-gray-300 font-medium rounded-md text-xs px-2.5 py-1 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800 dark:text-gray-100">@lang('app.addMore')</a>
                                @endif
                            </h3>
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400 tracking-wide">
                                @lang('modules.onboarding.addFloorInfo')</p>
                        </li>

                        <li class="ms-6">
                            <span @class(['absolute -start-2.5 flex h-5 w-5 items-center justify-center rounded-full
                                ring-8 ring-white dark:ring-gray-900', 'bg-gray-100 dark:bg-gray-800'=>
                                !$onboardingSteps->add_apartment_completed, 'bg-green-700 dark:bg-green-800' =>
                                $onboardingSteps->add_apartment_completed])>
                                <svg aria-hidden="true" @class(['h-3 w-3', 'text-gray-500 dark:text-gray-400'=>
                                    !$onboardingSteps->add_apartment_completed, 'text-green-50 dark:text-green-400' =>
                                    $onboardingSteps->add_apartment_completed])
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                </svg>
                            </span>
                            <h3 @class(["mb-1.5 text-base font-semibold leading-none text-gray-900 dark:text-white flex
                                justify-between"])>@lang('modules.onboarding.addApartmentHeading')
                                @if ($onboardingSteps->add_apartment_completed)
                                <a href="javascript:;" wire:click="showAddApartmentForm"
                                    class="text-gray-900 bg-gray-300 hover:bg-gray-800 hover:text-white focus:ring-4 focus:ring-gray-300 font-medium rounded-md text-xs px-2.5 py-1 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800 dark:text-gray-100">@lang('app.addMore')</a>
                                @endif
                            </h3>
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400 tracking-wide">
                                @lang('modules.onboarding.addApartmentInfo')</p>
                        </li>

                        <li class="ms-6">
                            <span @class(['absolute -start-2.5 flex h-5 w-5 items-center justify-center rounded-full
                                ring-8 ring-white dark:ring-gray-900', 'bg-gray-100 dark:bg-gray-800'=>
                                !$onboardingSteps->add_parking_completed, 'bg-green-700 dark:bg-green-800' =>
                                $onboardingSteps->add_parking_completed])>
                                <svg aria-hidden="true" @class(['h-3 w-3', 'text-gray-500 dark:text-gray-400'=>
                                    !$onboardingSteps->add_parking_completed, 'text-green-50 dark:text-green-400' =>
                                    $onboardingSteps->add_parking_completed])
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                </svg>
                            </span>
                            <h3 @class(["mb-1.5 text-base font-semibold leading-none text-gray-900 dark:text-white flex
                                justify-between"])>@lang('modules.onboarding.addParkingHeading')
                                @if ($onboardingSteps->add_parking_completed)
                                <a href="javascript:;" wire:click="showAddParkingForm"
                                    class="text-gray-900 bg-gray-300 hover:bg-gray-800 hover:text-white focus:ring-4 focus:ring-gray-300 font-medium rounded-md text-xs px-2.5 py-1 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800 dark:text-gray-100">@lang('app.addMore')</a>
                                @endif
                            </h3>
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400 tracking-wide">
                                @lang('modules.onboarding.addParkingInfo')</p>
                        </li>


                    </ol>

                </div>
            </div>
        </section>

    </div>

    <div class="col-span-2 px-8 lg:px-16 flex items-center w-full onboarding-steps">

        @if (!$onboardingSteps->add_tower_completed || $showAddTower)
        <div class="space-y-4 w-full">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __("modules.settings.addTower") }}
            </div>

            @livewire('forms.addTower')
        </div>
        @elseif (!$onboardingSteps->add_floor_completed || $showAddFloor)
        <div class="space-y-4 w-full">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __("modules.settings.addFloor") }}
            </div>

            @livewire('forms.addFloor')
        </div>
        @elseif (!$onboardingSteps->add_apartment_completed || $showAddApartment)
        <div class="space-y-4 w-full">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __("modules.settings.addApartmentManagement") }}
            </div>

            @livewire('forms.addApartmentManagment')
        </div>
        @elseif (!$onboardingSteps->add_parking_completed || $showAddParking)
        <div class="space-y-4 w-full">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __("modules.settings.addParking") }}
            </div>

            @livewire('forms.addParking')
        </div>
        @else
        <div class="space-y-4 w-full">
            <div
                class="group flex flex-col h-full bg-white border border-gray-200 shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70 max-w-md mx-auto">
                <div class="h-52 flex flex-col justify-center items-center bg-skin-base rounded-t-xl">
                    <svg class="size-28 transition duration-75 text-gray-50 dark:text-gray-900 dark:group-hover:text-white" viewBox="0 -0.5 25 25" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9.35 19.0001C9.35 19.4143 9.68579 19.7501 10.1 19.7501C10.5142 19.7501 10.85 19.4143 10.85 19.0001H9.35ZM10.1 16.7691L9.35055 16.7404C9.35018 16.75 9.35 16.7595 9.35 16.7691H10.1ZM12.5 14.5391L12.4736 15.2886C12.4912 15.2892 12.5088 15.2892 12.5264 15.2886L12.5 14.5391ZM14.9 16.7691H15.65C15.65 16.7595 15.6498 16.75 15.6495 16.7404L14.9 16.7691ZM14.15 19.0001C14.15 19.4143 14.4858 19.7501 14.9 19.7501C15.3142 19.7501 15.65 19.4143 15.65 19.0001H14.15ZM10.1 18.2501C9.68579 18.2501 9.35 18.5859 9.35 19.0001C9.35 19.4143 9.68579 19.7501 10.1 19.7501V18.2501ZM14.9 19.7501C15.3142 19.7501 15.65 19.4143 15.65 19.0001C15.65 18.5859 15.3142 18.2501 14.9 18.2501V19.7501ZM10.1 19.7501C10.5142 19.7501 10.85 19.4143 10.85 19.0001C10.85 18.5859 10.5142 18.2501 10.1 18.2501V19.7501ZM9.5 19.0001V18.2501C9.4912 18.2501 9.4824 18.2502 9.4736 18.2505L9.5 19.0001ZM5.9 15.6541H5.15C5.15 15.6635 5.15018 15.673 5.15054 15.6825L5.9 15.6541ZM6.65 8.94807C6.65 8.53386 6.31421 8.19807 5.9 8.19807C5.48579 8.19807 5.15 8.53386 5.15 8.94807H6.65ZM3.0788 9.95652C2.73607 10.1891 2.64682 10.6555 2.87944 10.9983C3.11207 11.341 3.57848 11.4302 3.9212 11.1976L3.0788 9.95652ZM6.3212 9.56863C6.66393 9.336 6.75318 8.86959 6.52056 8.52687C6.28793 8.18415 5.82152 8.09489 5.4788 8.32752L6.3212 9.56863ZM5.47883 8.3275C5.13609 8.5601 5.04682 9.02651 5.27942 9.36924C5.51203 9.71198 5.97844 9.80125 6.32117 9.56865L5.47883 8.3275ZM11.116 5.40807L10.7091 4.77804C10.7043 4.78114 10.6995 4.78429 10.6948 4.7875L11.116 5.40807ZM13.884 5.40807L14.3052 4.7875C14.3005 4.78429 14.2957 4.78114 14.2909 4.77804L13.884 5.40807ZM18.6788 9.56865C19.0216 9.80125 19.488 9.71198 19.7206 9.36924C19.9532 9.02651 19.8639 8.5601 19.5212 8.3275L18.6788 9.56865ZM14.9 18.2501C14.4858 18.2501 14.15 18.5859 14.15 19.0001C14.15 19.4143 14.4858 19.7501 14.9 19.7501V18.2501ZM15.5 19.0001L15.5264 18.2505C15.5176 18.2502 15.5088 18.2501 15.5 18.2501V19.0001ZM19.1 15.6541L19.8495 15.6825C19.8498 15.673 19.85 15.6635 19.85 15.6541L19.1 15.6541ZM19.85 8.94807C19.85 8.53386 19.5142 8.19807 19.1 8.19807C18.6858 8.19807 18.35 8.53386 18.35 8.94807H19.85ZM21.079 11.1967C21.4218 11.4293 21.8882 11.3399 22.1207 10.9971C22.3532 10.6543 22.2638 10.1879 21.921 9.9554L21.079 11.1967ZM19.521 8.3274C19.1782 8.09487 18.7119 8.18426 18.4793 8.52705C18.2468 8.86984 18.3362 9.33622 18.679 9.56875L19.521 8.3274ZM10.85 19.0001V16.7691H9.35V19.0001H10.85ZM10.8495 16.7977C10.8825 15.9331 11.6089 15.2581 12.4736 15.2886L12.5264 13.7895C10.8355 13.73 9.41513 15.0497 9.35055 16.7404L10.8495 16.7977ZM12.5264 15.2886C13.3911 15.2581 14.1175 15.9331 14.1505 16.7977L15.6495 16.7404C15.5849 15.0497 14.1645 13.73 12.4736 13.7895L12.5264 15.2886ZM14.15 16.7691V19.0001H15.65V16.7691H14.15ZM10.1 19.7501H14.9V18.2501H10.1V19.7501ZM10.1 18.2501H9.5V19.7501H10.1V18.2501ZM9.4736 18.2505C7.96966 18.3035 6.70648 17.1294 6.64946 15.6257L5.15054 15.6825C5.23888 18.0125 7.19612 19.8317 9.5264 19.7496L9.4736 18.2505ZM6.65 15.6541V8.94807H5.15V15.6541H6.65ZM3.9212 11.1976L6.3212 9.56863L5.4788 8.32752L3.0788 9.95652L3.9212 11.1976ZM6.32117 9.56865L11.5372 6.02865L10.6948 4.7875L5.47883 8.3275L6.32117 9.56865ZM11.5229 6.0381C12.1177 5.65397 12.8823 5.65397 13.4771 6.0381L14.2909 4.77804C13.2008 4.07399 11.7992 4.07399 10.7091 4.77804L11.5229 6.0381ZM13.4628 6.02865L18.6788 9.56865L19.5212 8.3275L14.3052 4.7875L13.4628 6.02865ZM14.9 19.7501H15.5V18.2501H14.9V19.7501ZM15.4736 19.7496C17.8039 19.8317 19.7611 18.0125 19.8495 15.6825L18.3505 15.6257C18.2935 17.1294 17.0303 18.3035 15.5264 18.2505L15.4736 19.7496ZM19.85 15.6541V8.94807H18.35V15.6541H19.85ZM21.921 9.9554L19.521 8.3274L18.679 9.56875L21.079 11.1967L21.921 9.9554Z" fill="currentColor"></path> </g></svg>
                </div>
                <div class="p-4 md:p-6">

                    <h3 class="text-xl font-semibold text-gray-800 dark:text-neutral-300 dark:hover:text-white">
                        @lang('modules.onboarding.completedHeading')
                    </h3>
                    <p class="mt-3 text-gray-500 dark:text-neutral-500">
                        @lang('modules.onboarding.completedInfo')
                    </p>
                </div>
                <div
                    class="mt-auto flex border-t border-gray-200 divide-x divide-gray-200 dark:border-neutral-700 dark:divide-neutral-700">
                    <a class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-base font-semibold rounded-es-xl bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                        href="{{ route('dashboard') }}" wire:navigate>
                        @lang('modules.onboarding.goToDashboard')
                    </a>

                </div>
            </div>
            <!-- End Card -->
        </div>
        @endif

    </div>



</div>
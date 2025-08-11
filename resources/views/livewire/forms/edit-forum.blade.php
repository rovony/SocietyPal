<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.js"></script>

    <form wire:submit.prevent="submitForm">
        @csrf

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="title" value="{{ __('modules.forum.title') }}" required/>
                    <x-input id="title" class="block mt-1 w-full" type="text" wire:model.live='title' />
                    <x-input-error for="title" class="mt-2" />
                </div>

                <div>
                    <x-label for="category_id" value="{{ __('modules.forum.category') }}" required/>
                    <x-select id="category_id" wire:model.live="category_id" class="mt-1 w-full">
                        <option value="">@lang('Select Category')</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="category_id" class="mt-2" />
                </div>
            </div>

            <div>
                <x-label for="description" value="{{ __('modules.forum.description') }}" />
                <input id="description" name="description" wire:model="description" type="hidden" />

                <div wire:ignore class="border border-gray-400 rounded-md mt-2 dark:border-gray-600 dark:bg-gray-300 pt-2">
                    <trix-editor
                        class="trix-content text-sm dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600"
                        x-on:trix-change="$wire.description = $event.target.value"
                        x-ref="trixEditor"
                        data-gramm="false"
                        x-init="$refs.trixEditor.editor.loadHTML(@js($description))">
                    </trix-editor>
                </div>
                <x-input-error for="description" class="mt-2" />
            </div>


            <div>
                <x-label for="files" value="{{ __('modules.forum.fileUploads') }}" />
                <input 
                    class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 text-slate-500 mt-1"
                    type="file" 
                    wire:model="files" 
                    multiple
                    accept="image/png,image/gif,image/jpeg,image/webp,application/pdf"/>
            
                <x-input-error for="files" class="mt-2" />
                <x-input-error for="files.*" class="mt-2" />
            
                @if (!empty($existingFiles))
                    <div class="mt-3 space-y-2">
                        <p class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('modules.forum.existingFiles') }}:</p>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($existingFiles as $file)
                                @php
                                    $fileUrl = asset_url_local_s3(\App\Models\ForumFile::FILE_PATH . '/' . $file->file);
                                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file->file);
                                @endphp

                                <div class="relative w-16 h-16 border rounded overflow-hidden shadow-sm group">
                                    @if ($isImage)
                                        <img src="{{ $fileUrl }}" class="object-cover w-full h-full" alt="File preview">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full bg-gray-100 text-[10px] text-center px-1 text-gray-600">
                                            <a href="{{ $fileUrl }}" target="_blank" class="underline break-words">
                                                {{ \Illuminate\Support\Str::limit(basename($file->file), 20) }}
                                            </a>
                                        </div>
                                    @endif

                                    <button type="button"
                                        class="absolute top-0.5 right-0.5 bg-red-600 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] hover:bg-red-700 transition"
                                        wire:click="deleteFile({{ $file->id }})"
                                        title="Delete File">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <x-label for="discussion_type" value="{{ __('modules.forum.discussionType') }}" required/>
                <x-select id="discussion_type" wire:model.live="discussion_type" class="mt-1 w-full">
                    <option value="">@lang('modules.forum.selectType')</option>
                    <option value="private">@lang('Private')</option>
                    <option value="public">@lang('Public')</option>
                </x-select>
                <x-input-error for="discussion_type" class="mt-2" />
            </div>

            @if($discussion_type === 'private')
                <div class="space-y-4">
                    <x-label value="{{ __('modules.forum.selectType') }}" />
                    <div class="flex space-x-4">
                        <label class="flex-1 flex items-center p-3 rounded-lg bg-white border border-gray-200 cursor-pointer hover:bg-gray-50">
                            <input type="radio" wire:model.live="userRoleWise" value="role-wise" name="userRoleWise"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <div class="ml-3">
                                <span class="block text-sm font-medium">@lang('app.role')</span>
                            </div>
                        </label>

                        <label class="flex-1 flex items-center p-3 rounded-lg bg-white border border-gray-200 cursor-pointer hover:bg-gray-50">
                            <input type="radio" wire:model.live="userRoleWise" value="user-wise" name="userRoleWise"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <div class="ml-3">
                                <span class="block text-sm font-medium">@lang('app.user')</span>
                            </div>
                        </label>
                    </div>

                    @if ($userRoleWise === 'role-wise')
                        <div>
                            <x-label for="selectedRoles" :value="__('modules.user.role')" required/>
                            <div class="grid grid-cols-2 gap-4 mt-2">
                                @foreach ($roles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedRoles" value="{{ $role->id }}" class="mr-2">
                                        <span>{{ $role->display_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error for="selectedRoles" class="mt-2" />
                        </div>
                    @endif

                    @if ($userRoleWise === 'user-wise')
                        <div>
                            <x-label for="selectedUserNames" :value="__('app.name')" />
                            <div x-data="{ isOpen: @entangle('isOpen'), selectedUserNames: @entangle('selectedUserNames') }" @click.away="isOpen = false">
                                <div class="flex items-center space-x-2">
                                    <div class="relative flex-1">
                                        <div @click="isOpen = !isOpen" class="p-2 bg-gray-100 border rounded cursor-pointer">
                                            @lang('modules.settings.selectUser')
                                        </div>

                                        <ul x-show="isOpen" x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                            @foreach ($users as $user)
                                                <li @click="$wire.toggleSelectType({ id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })"
                                                    wire:key="{{ $loop->index }}"
                                                    class="relative py-2 pl-3 pr-9 cursor-pointer hover:bg-gray-100"
                                                    :class="{ 'bg-gray-100': selectedUserNames.includes({{ $user->id }}) }"
                                                    role="option">
                                                    <div class="flex items-center">
                                                        <span class="block ml-3 truncate">{{ $user->name }}</span>
                                                        <span x-show="selectedUserNames.includes({{ $user->id }})" class="absolute inset-y-0 right-0 flex items-center pr-4 text-black" x-cloak>
                                                            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <span class="text-sm text-gray-500">@lang('modules.settings.selectUser')</span>
                                    <span class="text-sm text-gray-900">
                                        {{ implode(', ', $users->whereIn('id', $selectedUserNames)->pluck('name')->toArray()) }}
                                    </span>
                                </div>

                                <x-input-error for="selectedUserNames" class="mt-2" />
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex justify-end space-x-4 pt-4">
                <x-button type="submit">@lang('app.update')</x-button>
                <x-button-cancel wire:click="$dispatch('hideEditForum')">@lang('app.cancel')</x-button-cancel>
            </div>
        </div>
    </form>
</div>
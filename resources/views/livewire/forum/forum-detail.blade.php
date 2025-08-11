<div class=" px-4 pt-6 xl:grid-cols-3 xl:gap-4 bg-white dark:bg-gray-900">
    <div class="mb-4 col-span-full xl:mb-2">
        <nav class="flex mb-5" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                @lang('menu.dashboard')
                </a>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <a href="{{ route('society-forum.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">@lang('modules.forum.societyForum')</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">@lang('modules.forum.forumDetails')</span>
                </div>
            </li>
            </ol>
        </nav>
    </div>

    <div class="max-w-4xl mx-auto mt-10 px-4">

        <div class="flex flex-wrap gap-2 mb-4 text-sm">
            <span class="bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded font-medium">
                # {{ $forum->category->name }}
            </span>
        </div>

        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
            {{ $forum->title }}
        </h1>

        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-6 space-x-3">
            <img src="{{ $forum->user->profile_photo_url }}" alt="{{ $forum->user->name }}"
                class="w-12 h-12 rounded-full object-cover">
            <div>
                <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $forum->user->name }}</div>
                <div>{{ \Carbon\Carbon::parse($forum->date)->format('F j, Y') }}</div>
            </div>
        </div>

        <article class="prose prose-base max-w-none mb-8 dark:prose-invert">
            {!! $forum->description !!}
        </article>

        @if($forum->files->isNotEmpty())
            <div class="mb-8">
                <p class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Forum Files') }}:</p>
                <div class="flex flex-wrap gap-4">
                    @foreach($forum->files as $file)
                        @php
                            $fileUrl = asset_url_local_s3(\App\Models\ForumFile::FILE_PATH . '/' . $file->file);
                            $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file->file);
                        @endphp
                        <div class="relative w-16 h-16 border rounded overflow-hidden shadow-sm group">
                            <a href="{{ $fileUrl }}" target="_blank" class="w-full h-full flex items-center justify-center">
                                @if($isImage)
                                    <img src="{{ $fileUrl }}" class="object-cover w-full h-full" alt="File preview">
                                @else
                                    <div class="flex items-center justify-center w-full h-full bg-gray-100 text-[10px] text-center px-1 text-gray-600">
                                        {{ \Illuminate\Support\Str::limit(basename($file->file), 20) }}
                                    </div>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mb-12">
            @livewire('forum.forum-replies', ['forum' => $forum])
        </div>
    </div>
</div>

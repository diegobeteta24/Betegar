@php($breadcrumbs = $breadcrumbs ?? [])
<div class="flex flex-col">
    @if(!empty($breadcrumbs))
        <nav class="mb-1 text-xs sm:text-sm text-gray-600" aria-label="Breadcrumb">
            <ol class="inline-flex flex-wrap items-center gap-x-1 gap-y-1">
                @foreach($breadcrumbs as $i => $item)
                    <li class="inline-flex items-center">
                        @if($i > 0)
                            <span class="mx-1 text-gray-400">/</span>
                        @endif
                        @isset($item['href'])
                            <a href="{{ $item['href'] }}" class="hover:underline">
                                {{ $item['name'] }}
                            </a>
                        @else
                            <span class="font-semibold text-gray-800">
                                {{ $item['name'] }}
                            </span>
                        @endisset
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif
    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $pageTitle ?? 'Untitled' }}</h1>
</div>
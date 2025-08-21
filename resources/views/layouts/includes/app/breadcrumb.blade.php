<div>

@if(count($breadcrumbs))
            <nav class="mb-2 text-sm text-gray-600" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1">
                    @foreach($breadcrumbs as $i => $item)
                        <li class="inline-flex items-center">
                            @if($i > 0)
                                <span class="mx-2 text-gray-400">/</span>
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
                        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $pageTitle }}</h1>

</div>
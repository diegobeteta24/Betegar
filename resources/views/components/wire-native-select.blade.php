@props([
    'name',
    'label' => null,
    'required' => false,
])
<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{$name}}" class="block text-xs font-medium text-gray-700 mb-1">{{$label}} @if($required)<span class="text-red-600">*</span>@endif</label>
    @endif
    <select id="{{$name}}" name="{{$name}}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except('class')->merge(['class'=>'w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm']) }}>
        {{ $slot }}
    </select>
    @error($name)
      <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

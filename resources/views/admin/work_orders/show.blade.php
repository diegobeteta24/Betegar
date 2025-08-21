{{-- resources/views/admin/work_orders/show.blade.php --}}
@extends('layouts.admin')
@php($breadcrumbs=[["name"=>"Órdenes","url"=>route('admin.work-orders.index')],["name"=>'O.T. #'.$workOrder->id]])
@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white p-6 shadow rounded">
        <h1 class="text-xl font-semibold mb-2">Orden de Trabajo #{{ $workOrder->id }}</h1>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-medium">Cliente:</span> {{ $workOrder->customer->name ?? '—' }}</div>
            <div><span class="font-medium">Estado:</span> <span class="px-2 py-0.5 rounded bg-gray-100">{{ $workOrder->status }}</span></div>
            <div class="col-span-2"><span class="font-medium">Dirección:</span> {{ $workOrder->address }}</div>
            <div class="col-span-2"><span class="font-medium">Objetivo:</span> {{ $WorkOrder->objective ?? $workOrder->objective }}</div>
            <div class="col-span-2"><span class="font-medium">Técnicos:</span> {{ $workOrder->technicians->pluck('name')->join(', ') }}</div>
        </dl>
    </div>
    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-lg font-semibold mb-4">Entradas</h2>
        @forelse($workOrder->entries->sortByDesc('work_date') as $entry)
            <div class="border-b py-3 last:border-0">
                <div class="flex justify-between text-sm">
                    <div class="font-medium">{{ $entry->work_date }} · {{ $entry->user->name ?? '—' }}</div>
                    <div class="text-xs text-gray-500">ID {{ $entry->id }}</div>
                </div>
                <p class="mt-1 text-sm whitespace-pre-line">{{ $entry->progress }}</p>
                @if($entry->requests)
                    <p class="mt-1 text-xs text-amber-700"><strong>Solicitudes:</strong> {{ $entry->requests }}</p>
                @endif
                @if($entry->images->count())
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($entry->images as $img)
                            <a href="{{ Storage::url($img->path) }}" target="_blank" class="block w-24 h-24 bg-gray-100 overflow-hidden rounded">
                                <img src="{{ Storage::url($img->path) }}" class="w-full h-full object-cover"/>
                            </a>
                        @endforeach
                    </div>
                @endif
                @if($entry->signature)
                    <div class="mt-2 text-xs text-gray-500">Firmado por {{ $entry->signature_by }} @ {{ $entry->signed_at }}</div>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-500">Sin entradas aún.</p>
        @endforelse
    </div>
</div>
@endsection

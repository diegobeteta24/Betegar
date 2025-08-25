@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <livewire:admin.crm.reminders />
    </div>
    <script>
        // Ensure push subscription exists on this page
        if(window.__initPush){ setTimeout(()=>window.__initPush(), 1000); }
    </script>
@endsection

@if(session('sweet-alert'))
    @php($sa = session('sweet-alert'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon:              @json($sa['icon']),
                title:             @json($sa['title']),
                text:              @json($sa['text']),
                @if(isset($sa['timer']))
                timer:             @json($sa['timer']),
                timerProgressBar:  true,
                @endif
                @if(isset($sa['showConfirmButton']))
                showConfirmButton: @json($sa['showConfirmButton']),
                @endif
            });
        });
    </script>
@endif

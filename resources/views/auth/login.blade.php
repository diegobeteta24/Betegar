<x-guest-layout>
    <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
        {{-- Panel de bienvenida (solo en escritorio) --}}
        <div
            class="hidden lg:flex items-center justify-center bg-cover bg-center"
            style="background-image: url('{{ asset('images/login-bg.jpg') }}'), linear-gradient(135deg,#0f172a,#1e293b)"
        >
            <div class="bg-black bg-opacity-50 p-8 rounded-md text-white max-w-sm text-center">
                <h2 class="text-3xl font-bold mb-4">Bienvenido a Betegar</h2>
                <p class="text-lg">Gestiona tu inventario y ventas donde sea, incluso offline.</p>
            </div>
        </div>

        {{-- Panel de login --}}
        <div class="flex items-center justify-center px-6 py-8 sm:px-10">
            <div class="w-full max-w-md space-y-6">
                {{-- Logo y título --}}
                <div class="text-center">
                    <x-authentication-card-logo class="mx-auto w-24 h-24 sm:w-32 sm:h-32" />
                    <h1 class="text-2xl sm:text-3xl font-semibold mt-4">Iniciar sesión</h1>
                </div>

                {{-- Errores y status --}}
                <x-validation-errors class="mb-4" />
                @if (session('status'))
                    <div class="text-sm text-green-600 mb-4 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Formulario --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-label for="email" value="Correo electrónico" />
                        <x-input
                            id="email"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required autofocus
                            autocomplete="username"
                            class="mt-1 w-full p-2 sm:p-3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                    </div>

                    <div>
                        <x-label for="password" value="Contraseña" />
                        <x-input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="mt-1 w-full p-2 sm:p-3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                    </div>

                    <div class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" class="h-4 w-4 text-indigo-600" />
                        <label for="remember_me" class="ml-2 text-sm text-gray-600">Mantener sesión activa</label>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-between items-center gap-4">
                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 transition"
                            >
                                ¿Olvidó su contraseña?
                            </a>
                        @endif

                        <x-button class="w-full sm:w-auto">
                            Iniciar sesión
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

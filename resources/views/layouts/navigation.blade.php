<nav x-data="{ open: false }" class="bg-green-900 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Logo --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                    <div class="w-8 h-8 rounded-full bg-green-400 flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 56 56" fill="none">
                            <circle cx="28" cy="28" r="26" fill="#dcfce7" stroke="#22c55e" stroke-width="3"/>
                            <path d="M6 22 Q28 13 50 22" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                            <path d="M6 34 Q28 43 50 34" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="font-extrabold text-sm tracking-widest uppercase text-white hidden sm:block">
                        Top Tennis Digital
                    </span>
                </a>

                {{-- Links desktop --}}
                <div class="hidden sm:flex items-center gap-1">
                    @php
                        $esStaffNav = in_array(Auth::user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]);
                        $links = $esStaffNav
                            ? [
                                ['route' => 'dashboard',     'label' => 'Inicio',    'pattern' => 'dashboard'],
                                ['route' => 'canchas.index', 'label' => 'Canchas',   'pattern' => 'canchas.*'],
                                ['route' => 'tarifas.index', 'label' => 'Tarifas',   'pattern' => 'tarifas.*'],
                                ['route' => 'horarios.index','label' => 'Horarios',  'pattern' => 'horarios.*'],
                                ['route' => 'reservas.index','label' => 'Reservas',  'pattern' => 'reservas.index'],
                            ]
                            : [
                                ['route' => 'dashboard',            'label' => 'Inicio',        'pattern' => 'dashboard'],
                                ['route' => 'reservas.disponibles', 'label' => 'Reservar',      'pattern' => 'reservas.disponibles'],
                                ['route' => 'reservas.index',       'label' => 'Mis Reservas',  'pattern' => 'reservas.index'],
                            ];
                    @endphp
                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors
                               {{ request()->routeIs($link['pattern'])
                                   ? 'bg-green-700 text-white'
                                   : 'text-green-200 hover:text-white hover:bg-green-800' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Usuario --}}
            <div class="hidden sm:flex items-center gap-3">
                @php
                    $rol = Auth::user()->rol->value ?? '';
                    $rolLabel = ['admin'=>'Admin','recepcionista'=>'Recepcionista','cliente'=>'Jugador'][$rol] ?? ucfirst($rol);
                @endphp

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold text-white hover:bg-green-800 transition-colors">
                        <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <p class="text-white text-xs font-bold leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-green-300 text-xs leading-none mt-0.5">{{ $rolLabel }}</p>
                        </div>
                        <svg class="w-4 h-4 text-green-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            Mi perfil
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Hamburger mobile --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                        class="p-2 rounded-md text-green-300 hover:text-white hover:bg-green-800">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-green-800">
        <div class="px-4 pt-2 pb-3 space-y-1">
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="block px-3 py-2 rounded-lg text-sm font-semibold
                       {{ request()->routeIs($link['pattern'])
                           ? 'bg-green-700 text-white'
                           : 'text-green-200 hover:bg-green-800' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
        <div class="px-4 py-3 border-t border-green-800">
            <div class="text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
            <div class="text-xs text-green-300">{{ Auth::user()->email }}</div>
            <div class="mt-2 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-green-200 hover:bg-green-800 rounded-lg">Mi perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 text-sm text-red-400 hover:bg-green-800 rounded-lg">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

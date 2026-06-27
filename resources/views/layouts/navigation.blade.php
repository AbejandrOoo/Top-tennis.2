<nav x-data="{ open: false }" class="sticky top-0 z-50"
     style="background: linear-gradient(135deg, #0d3d22 0%, #155e36 60%, #166534 100%);
            border-bottom: 1px solid rgba(255,255,255,.08);
            box-shadow: 0 2px 16px rgba(0,0,0,.18);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-15" style="height:3.75rem;">

            {{-- ── Logo ── --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 shrink-0 group">
                    {{-- Pelota de tennis SVG --}}
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                         style="background:rgba(255,255,255,.12); border:1.5px solid rgba(255,255,255,.2);
                                transition: background .2s, border-color .2s;"
                         onmouseover="this.style.background='rgba(255,255,255,.2)'"
                         onmouseout="this.style.background='rgba(255,255,255,.12)'">
                        <svg width="22" height="22" viewBox="0 0 56 56" fill="none">
                            <circle cx="28" cy="28" r="24" fill="#dcfce7" stroke="#4ade80" stroke-width="2.5"/>
                            <path d="M7 20 Q28 11 49 20" stroke="#15803d" stroke-width="2.8" fill="none" stroke-linecap="round"/>
                            <path d="M7 36 Q28 45 49 36" stroke="#15803d" stroke-width="2.8" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="hidden sm:block">
                        <p class="font-black text-white text-sm tracking-widest uppercase leading-none">Top Tennis</p>
                        <p class="text-green-300 text-xs font-medium leading-none mt-0.5 tracking-wide">Digital</p>
                    </div>
                </a>

                {{-- ── Links desktop ── --}}
                <div class="hidden sm:flex items-center gap-0.5 ml-2">
                    @php
                        $esStaffNav = in_array(Auth::user()->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]);
                        $links = $esStaffNav
                            ? [
                                ['route' => 'dashboard',     'label' => 'Inicio',    'pattern' => 'dashboard',    'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'],
                                ['route' => 'canchas.index', 'label' => 'Canchas',   'pattern' => 'canchas.*',    'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M3 9Q12 5 21 9M3 15Q12 19 21 15" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>'],
                                ['route' => 'tarifas.index', 'label' => 'Tarifas',   'pattern' => 'tarifas.*',    'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
                                ['route' => 'horarios.index','label' => 'Horarios',  'pattern' => 'horarios.*',   'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'],
                                ['route' => 'reservas.index','label' => 'Reservas',  'pattern' => 'reservas.index','icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'],
                            ]
                            : [
                                ['route' => 'dashboard',            'label' => 'Inicio',        'pattern' => 'dashboard',            'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'],
                                ['route' => 'reservas.disponibles', 'label' => 'Reservar',      'pattern' => 'reservas.disponibles', 'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M3 9Q12 5 21 9M3 15Q12 19 21 15" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>'],
                                ['route' => 'reservas.index',       'label' => 'Mis Reservas',  'pattern' => 'reservas.index',       'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'],
                            ];
                    @endphp

                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all duration-150
                               {{ request()->routeIs($link['pattern'])
                                   ? 'bg-white/15 text-white'
                                   : 'text-green-200 hover:text-white hover:bg-white/10' }}">
                            {!! $link['icon'] !!}
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ── Usuario desktop ── --}}
            <div class="hidden sm:flex items-center gap-3">
                @php
                    $rol = Auth::user()->rol->value ?? '';
                    $rolLabel = ['admin'=>'Administrador','recepcionista'=>'Recepcionista','cliente'=>'Jugador'][$rol] ?? ucfirst($rol);
                    $initial = strtoupper(substr(Auth::user()->name, 0, 1));
                @endphp

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl transition-all duration-150 hover:bg-white/10"
                            style="border: 1px solid rgba(255,255,255,.1);">
                        {{-- Avatar inicial --}}
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black"
                             style="background: linear-gradient(135deg,#4ade80,#22c55e); color:#14532d;">
                            {{ $initial }}
                        </div>
                        <div class="text-left">
                            <p class="text-white text-xs font-bold leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-green-300 text-xs leading-none mt-0.5">{{ $rolLabel }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-green-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl py-1 z-50"
                         style="box-shadow: 0 8px 30px rgba(0,0,0,.12); border: 1px solid #f0f0f0;">
                        <div class="px-4 py-2.5 border-b border-gray-50">
                            <p class="text-xs font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Mi perfil
                        </a>
                        <div class="border-t border-gray-50 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Hamburger mobile ── --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = !open" class="p-2 rounded-lg text-green-300 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Mobile menu ── --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden"
         style="border-top: 1px solid rgba(255,255,255,.08); background: rgba(0,0,0,.2);">
        <div class="px-4 pt-2 pb-3 space-y-0.5">
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold
                       {{ request()->routeIs($link['pattern'])
                           ? 'bg-white/15 text-white'
                           : 'text-green-200 hover:text-white hover:bg-white/10' }}">
                    {!! $link['icon'] !!} {{ $link['label'] }}
                </a>
            @endforeach
        </div>
        <div class="px-4 py-3" style="border-top: 1px solid rgba(255,255,255,.08);">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black"
                     style="background: linear-gradient(135deg,#4ade80,#22c55e); color:#14532d;">
                    {{ $initial }}
                </div>
                <div>
                    <p class="text-white text-sm font-bold leading-none">{{ Auth::user()->name }}</p>
                    <p class="text-green-300 text-xs mt-0.5">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="space-y-0.5">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-green-200 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Mi perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 w-full text-left px-3 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-white/10 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

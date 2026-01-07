<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm mb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('imagenes/logo_principal.png') }}" alt="Logo principal" class="h-16 w-auto">
                    </a>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-10 relative group">
                    <button class="inline-flex items-center px-3 py-2 text-sm font-semibold text-gray-700 hover:text-blue-600 focus:outline-none transition-colors duration-200">
                        {{ __('Administracion') }}
                        <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute left-0 top-full mt-1 w-52 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 group-hover:opacity-100 transform scale-95 group-hover:scale-100 transition-all duration-300 ease-out pointer-events-none group-hover:pointer-events-auto z-50">
                        <a href="{{ route('users.index') }}" class="block px-5 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-colors duration-150">
                            {{ __('Usuarios') }}
                        </a>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-10 relative group">
                    <button class="inline-flex items-center px-3 py-2 text-sm font-semibold {{ request()->routeIs('citas.DoctorAgenda') || request()->routeIs('citas.reporte') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' }} focus:outline-none transition-colors duration-200">
                        {{ __('Especialista') }}
                        <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute left-0 top-full mt-1 w-60 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 group-hover:opacity-100 transform scale-95 group-hover:scale-100 transition-all duration-300 ease-out pointer-events-none group-hover:pointer-events-auto z-50">
                        <a href="{{ route('citas.DoctorAgenda') }}" class="block px-5 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-t-xl transition-colors duration-150">
                            {{ __('Agenda Médica Oftalmológica') }}
                        </a>
                        <a href="{{ route('citas.reporte') }}" class="block px-5 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-b-xl transition-colors duration-150">
                            {{ __('Agenda del Día') }}
                        </a>
                    </div>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('citas.bitacora')" :active="request()->routeIs('citas.bitacora')">
                        {{ __('Bitácora') }}
                    </x-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('citas.index')" :active="request()->routeIs('citas.index')">
                        {{ __('Citas Médicas') }}
                    </x-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('historias.index')" :active="request()->routeIs('historia.*')">
                        {{ __('Historia Clínica') }}
                    </x-nav-link>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-10 relative group">
                    <button class="inline-flex items-center px-3 py-2 text-sm font-semibold {{ request()->routeIs('pacientes.*') || request()->routeIs('legacy.index') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' }} focus:outline-none transition-colors duration-200">
                        {{ __('Pacientes') }}
                        <svg class="ml-1 h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute left-0 top-full mt-1 w-60 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 group-hover:opacity-100 transform scale-95 group-hover:scale-100 transition-all duration-300 ease-out pointer-events-none group-hover:pointer-events-auto z-50">
                        <a href="{{ route('pacientes.index') }}" class="block px-5 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-t-xl transition-colors duration-150">
                            {{ __('Listado Moderno') }}
                        </a>
                        <a href="{{ route('legacy.index') }}" class="block px-5 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-b-xl transition-colors duration-150">
                            {{ __('Contenedor HCEO/HCUT (Legacy)') }}
                        </a>
                    </div>
                </div>
            </div>

            @if(Auth::check())
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-blue-600 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->nombres }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 0 01-1.414 0l-4-4a1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endif

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @if(Auth::check())
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                {{ __('users') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('legacy.index')" :active="request()->routeIs('legacy.index')">
                {{ __('Contenedor Legacy') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('citas.bitacora')" :active="request()->routeIs('citas.bitacora')">
                {{ __('Bitácora') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('citas.index')" :active="request()->routeIs('citas.index')">
                {{ __('Citas Médicas') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('historias.index')" :active="request()->routeIs('historia.*')">
                {{ __('Historia Clínica') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->nombres }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endif
    </div>
</nav>
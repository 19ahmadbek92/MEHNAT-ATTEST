<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto" />
                        <span class="hidden sm:inline text-sm font-semibold text-gray-700">E-Attestatsiya</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Boshqaruv paneli
                    </x-nav-link>

                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Admin
                        </x-nav-link>
                        <x-nav-link :href="route('admin.campaigns.index')" :active="request()->routeIs('admin.campaigns.*')">
                            Kampaniyalar
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'employer')
                        <x-nav-link :href="route('employer.organization.index')" :active="request()->routeIs('employer.organization.*')">
                            Korxona
                        </x-nav-link>
                        <x-nav-link :href="route('employer.workplaces.index')" :active="request()->routeIs('employer.workplaces.*')">
                            Ish o'rinlari
                        </x-nav-link>
                        <x-nav-link :href="route('employer.tenders.index')" :active="request()->routeIs('employer.tenders.*')">
                            Tender
                        </x-nav-link>
                        <x-nav-link :href="route('employee.applications.index')" :active="request()->routeIs('employee.applications.*')">
                            Arizalar
                        </x-nav-link>
                        <x-nav-link :href="route('employer.expertise.index')" :active="request()->routeIs('employer.expertise.*')">
                            Ekspertiza
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'laboratory')
                        <x-nav-link :href="route('laboratory.profile.index')" :active="request()->routeIs('laboratory.profile.*')">
                            Profil
                        </x-nav-link>
                        <x-nav-link :href="route('laboratory.protocols.index')" :active="request()->routeIs('laboratory.protocols.*')">
                            Protokollar
                        </x-nav-link>
                        <x-nav-link :href="route('laboratory.workplaces.index')" :active="request()->routeIs('laboratory.workplaces.*')">
                            O'lchov
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'commission')
                        <x-nav-link :href="route('commission.evaluations.index')" :active="request()->routeIs('commission.evaluations.*')">
                            Tekshirish
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'hr')
                        <x-nav-link :href="route('hr.applications.index')" :active="request()->routeIs('hr.applications.*')">
                            HR ko'rib chiqish
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'institute_expert')
                        <x-nav-link :href="route('institute.expertise.index')" :active="request()->routeIs('institute.expertise.*')">
                            Institut baholash
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'expert')
                        <x-nav-link :href="route('ministry.expertise.index')" :active="request()->routeIs('ministry.expertise.*')">
                            Davlat ekspertizasi
                        </x-nav-link>
                    @endif

                    @if(in_array(Auth::user()->role, ['admin','expert'], true))
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                            Hisobotlar
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Language Switcher -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="uppercase">{{ app()->getLocale() }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('lang.switch', 'uz')">O'zbek (UZ)</x-dropdown-link>
                        <x-dropdown-link :href="route('lang.switch', 'ru')">Русский (RU)</x-dropdown-link>
                        <x-dropdown-link :href="route('lang.switch', 'en')">English (EN)</x-dropdown-link>
                    </x-slot>
                </x-dropdown>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
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
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Chiqish
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Boshqaruv paneli
            </x-responsive-nav-link>

            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Admin
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.campaigns.index')" :active="request()->routeIs('admin.campaigns.*')">
                    Kampaniyalar
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'employer')
                <x-responsive-nav-link :href="route('employer.organization.index')" :active="request()->routeIs('employer.organization.*')">
                    Korxona
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.workplaces.index')" :active="request()->routeIs('employer.workplaces.*')">
                    Ish o'rinlari
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.tenders.index')" :active="request()->routeIs('employer.tenders.*')">
                    Tender
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employee.applications.index')" :active="request()->routeIs('employee.applications.*')">
                    Arizalar
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employer.expertise.index')" :active="request()->routeIs('employer.expertise.*')">
                    Ekspertiza
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'laboratory')
                <x-responsive-nav-link :href="route('laboratory.profile.index')" :active="request()->routeIs('laboratory.profile.*')">
                    Profil
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('laboratory.protocols.index')" :active="request()->routeIs('laboratory.protocols.*')">
                    Protokollar
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('laboratory.workplaces.index')" :active="request()->routeIs('laboratory.workplaces.*')">
                    O'lchov
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'commission')
                <x-responsive-nav-link :href="route('commission.evaluations.index')" :active="request()->routeIs('commission.evaluations.*')">
                    Tekshirish
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'hr')
                <x-responsive-nav-link :href="route('hr.applications.index')" :active="request()->routeIs('hr.applications.*')">
                    HR ko'rib chiqish
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'institute_expert')
                <x-responsive-nav-link :href="route('institute.expertise.index')" :active="request()->routeIs('institute.expertise.*')">
                    Institut baholash
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'expert')
                <x-responsive-nav-link :href="route('ministry.expertise.index')" :active="request()->routeIs('ministry.expertise.*')">
                    Davlat ekspertizasi
                </x-responsive-nav-link>
            @endif

            @if(in_array(Auth::user()->role, ['admin','expert'], true))
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                    Hisobotlar
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Chiqish
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<nav x-data="{ open: false }" class="bg-white shadow-sm" style="border-bottom: 1px solid var(--border-color); position: sticky; top: 0; z-index: 50;">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold" style="color: var(--text-primary);">LMS Platform</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                @auth
                <div class="hidden space-x-1 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-600 hover:text-gray-900' }}" style="{{ request()->routeIs('dashboard') ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));' : '' }}">
                        <i class="fas fa-home mr-2"></i>
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('videos.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('videos.*') ? 'text-white' : 'text-gray-600 hover:text-gray-900' }}" style="{{ request()->routeIs('videos.*') ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));' : '' }}">
                        <i class="fas fa-play-circle mr-2"></i>
                        {{ __('Videos') }}
                    </a>
                    <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-gray-600 hover:text-gray-900' }}" style="{{ request()->routeIs('categories.*') ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));' : '' }}">
                        <i class="fas fa-folder mr-2"></i>
                        {{ __('Categories') }}
                    </a>
                    <a href="{{ route('pdfs.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('pdfs.*') ? 'text-white' : 'text-gray-600 hover:text-gray-900' }}" style="{{ request()->routeIs('pdfs.*') ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));' : '' }}">
                        <i class="fas fa-file-pdf mr-2"></i>
                        {{ __('PDFs') }}
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.*') ? 'text-white' : 'text-gray-600 hover:text-gray-900' }}" style="{{ request()->routeIs('admin.*') ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));' : '' }}">
                        <i class="fas fa-cog mr-2"></i>
                        {{ __('Admin') }}
                    </a>
                    @endif
                </div>
                @endauth
            </div>

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-xl transition-all duration-200 hover:shadow-md" style="background: var(--background-color); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold mr-3">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="text-left">
                                <div class="font-medium">{{ auth()->user()->name }}</div>
                                <div class="text-xs opacity-75">{{ ucfirst(auth()->user()->role) }}</div>
                            </div>
                            <div class="ml-2">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b" style="border-color: var(--border-color);">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium" style="color: var(--text-primary);">{{ auth()->user()->name }}</div>
                                    <div class="text-sm" style="color: var(--text-secondary);">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm hover:bg-gray-50">
                                <i class="fas fa-user mr-3 text-gray-400"></i>
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            @if(auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('admin.dashboard')" class="flex items-center px-4 py-2 text-sm hover:bg-gray-50">
                                <i class="fas fa-cog mr-3 text-gray-400"></i>
                                {{ __('Admin Panel') }}
                            </x-dropdown-link>
                            @endif
                        </div>
                        <div class="border-t py-2" style="border-color: var(--border-color);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();"
                                        class="flex items-center px-4 py-2 text-sm hover:bg-gray-50 text-red-600">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
            @else
            <!-- Guest Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
                <a href="{{ route('login') }}" class="btn-secondary">
                    <i class="fas fa-sign-in-alt"></i>
                    {{ __('Log in') }}
                </a>
                <a href="{{ route('register') }}" class="btn-primary">
                    <i class="fas fa-user-plus"></i>
                    {{ __('Register') }}
                </a>
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
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
        @auth
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('videos.index')" :active="request()->routeIs('videos.*')">
                {{ __('Videos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                {{ __('Categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pdfs.index')" :active="request()->routeIs('pdfs.*')">
                {{ __('PDFs') }}
            </x-responsive-nav-link>
            @if(auth()->user()->isAdmin())
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                {{ __('Admin') }}
            </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                <div class="font-medium text-xs text-gray-400">Role: {{ ucfirst(auth()->user()->role) }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                @if(auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')">
                    {{ __('Admin Panel') }}
                </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <!-- Guest Responsive Navigation -->
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                {{ __('Log in') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                {{ __('Register') }}
            </x-responsive-nav-link>
        </div>
        @endauth
    </div>
</nav>

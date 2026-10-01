<aside class="admin__sidebar" :class="{ 'is-open': menu }">
    <a href="{{ route('dashboard') }}" class="admin__brand">
        <x-application-logo width="180" />
    </a>

    <nav class="admin__nav">
            <a href="{{ route('dashboard') }}" class="admin__nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                <svg class="admin__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span>{{ __('Dashboard') }}</span>
            </a>
            <a href="{{ route('dashboard.leads') }}" class="admin__nav-link {{ request()->routeIs('dashboard.leads') ? 'is-active' : '' }}">
                <svg class="admin__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <span>{{ __('Leads') }}</span>
            </a>
            <a href="{{ route('dashboard.calendar') }}" class="admin__nav-link {{ request()->routeIs('dashboard.calendar') ? 'is-active' : '' }}">
                <svg class="admin__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
                <span>{{ __('Calendar') }}</span>
            </a>
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('dashboard.content') }}" class="admin__nav-link {{ request()->routeIs('dashboard.content') ? 'is-active' : '' }}">
                <svg class="admin__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg>
                <span>{{ __('Content') }}</span>
            </a>
            <a href="{{ route('dashboard.pricing') }}" class="admin__nav-link {{ request()->routeIs('dashboard.pricing') ? 'is-active' : '' }}">
                <svg class="admin__nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 6.5C16 5.3 14.2 4.5 12 4.5c-2.8 0-5 1.3-5 3.5s2 3 5 3.5 5 1.3 5 3.5-2.2 3.5-5 3.5c-2.2 0-4-.8-5-2"/></svg>
                <span>{{ __('Pricing') }}</span>
            </a>
        @endif
    </nav>

    <div class="admin__user">
        <div class="admin__user-info">
            <span class="admin__user-name">{{ Auth::user()->name }}</span>
            <span class="admin__user-email">{{ Auth::user()->email }}</span>
        </div>
        <div class="admin__user-actions">
            <a href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">{{ __('Log Out') }}</button>
            </form>
        </div>
    </div>
</aside>

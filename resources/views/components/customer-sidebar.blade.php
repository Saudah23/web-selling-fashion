{{-- Customer Sidebar Component --}}
<li class="nav-item">
    <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('addresses.*') ? 'active' : '' }}">
    <a href="{{ route('addresses.index') }}" class="nav-link">
        <i class="fas fa-map-marker-alt"></i>
        <p>My Addresses</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <a href="{{ route('profile.show') }}" class="nav-link">
        <i class="fas fa-user"></i>
        <p>Profile Settings</p>
    </a>
</li>
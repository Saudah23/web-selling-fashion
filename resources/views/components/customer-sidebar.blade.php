{{-- Customer Sidebar Component --}}
<li class="nav-item">
    <a href="{{ route('customer.dashboard') }}"
        class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <p>Beranda</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
    <a href="{{ route('orders.index') }}" class="nav-link">
        <i class="fas fa-shopping-bag"></i>
        <p>Pesanan Saya</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('addresses.*') ? 'active' : '' }}">
    <a href="{{ route('addresses.index') }}" class="nav-link">
        <i class="fas fa-map-marker-alt"></i>
        <p>Alamat Saya</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <a href="{{ route('profile.show') }}" class="nav-link">
        <i class="fas fa-user"></i>
        <p>Pengaturan Profil</p>
    </a>
</li>
{{-- Admin Sidebar Component --}}
<li class="nav-item">
    <a href="{{ route('admin.dashboard') }}"
        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <p>Beranda</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
    <a href="{{ route('admin.categories.index') }}" class="nav-link">
        <i class="fas fa-tags"></i>
        <p>Kelola Kategori</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
    <a href="{{ route('admin.products.index') }}" class="nav-link">
        <i class="fas fa-box"></i>
        <p>Kelola Produk</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
    <a href="{{ route('admin.orders.index') }}" class="nav-link">
        <i class="fas fa-shopping-cart"></i>
        <p>Kelola Pesanan</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <a href="{{ route('admin.users.index') }}" class="nav-link">
        <i class="fas fa-users"></i>
        <p>Kelola Pengguna</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
    <a href="{{ route('admin.banners.index') }}" class="nav-link">
        <i class="fas fa-images"></i>
        <p>Banner Homepage</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
    <a href="{{ route('admin.settings.index') }}" class="nav-link">
        <i class="fas fa-cog"></i>
        <p>Pengaturan Sistem</p>
    </a>
</li>
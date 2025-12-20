{{-- Owner Sidebar Component --}}
<li class="nav-item">
    <a href="{{ route('owner.dashboard') }}"
        class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <p>Beranda</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('owner.settings.*') ? 'active' : '' }}">
    <a href="{{ route('owner.settings.index') }}" class="nav-link">
        <i class="fas fa-cog"></i>
        <p>Pengaturan Sistem</p>
    </a>
</li>

{{-- Analytics Feature --}}
<li class="nav-item {{ request()->routeIs('owner.analytics') ? 'active' : '' }}">
    <a href="{{ route('owner.analytics') }}" class="nav-link">
        <i class="fas fa-chart-line"></i>
        <p>Analitik</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('owner.financial-reports') ? 'active' : '' }}">
    <a href="{{ route('owner.financial-reports') }}" class="nav-link">
        <i class="fas fa-dollar-sign"></i>
        <p>Laporan Keuangan</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('owner.business-reports') ? 'active' : '' }}">
    <a href="{{ route('owner.business-reports') }}" class="nav-link">
        <i class="fas fa-file-alt"></i>
        <p>Laporan Bisnis</p>
    </a>
</li>
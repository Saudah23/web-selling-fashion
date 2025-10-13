{{-- Owner Sidebar Component --}}
<li class="nav-item">
    <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('owner.settings.*') ? 'active' : '' }}">
    <a href="{{ route('owner.settings.index') }}" class="nav-link">
        <i class="fas fa-cog"></i>
        <p>System Settings</p>
    </a>
</li>

{{-- Analytics Feature --}}
<li class="nav-item {{ request()->routeIs('owner.analytics') ? 'active' : '' }}">
    <a href="{{ route('owner.analytics') }}" class="nav-link">
        <i class="fas fa-chart-line"></i>
        <p>Analytics</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('owner.financial-reports') ? 'active' : '' }}">
    <a href="{{ route('owner.financial-reports') }}" class="nav-link">
        <i class="fas fa-dollar-sign"></i>
        <p>Financial Reports</p>
    </a>
</li>


<li class="nav-item {{ request()->routeIs('owner.business-reports') ? 'active' : '' }}">
    <a href="{{ route('owner.business-reports') }}" class="nav-link">
        <i class="fas fa-file-alt"></i>
        <p>Business Reports</p>
    </a>
</li>
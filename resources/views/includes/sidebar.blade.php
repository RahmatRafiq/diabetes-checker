<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebarMenuScroll custom-scrollbar">
        <ul class="sidebar-menu">
            <li class="{{ request()->routeIs('dashboard') ? 'active current-page' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="treeview {{ request()->is('mbkm/admin/role-permissions*') ? 'active current-page open' : '' }}">
                <a href="#" class="treeview-toggle">
                    <i class="bi bi-person-gear"></i>
                    <span class="menu-text">Manajemen Pengguna</span>
                </a>
                <ul class="treeview-menu"
                    style="{{ request()->is('admin/role-permissions*') ? 'display: block;' : 'display: none;' }}">
                    <li class="{{ request()->routeIs('permission.index') ? 'active-sub' : '' }}">
                        <a href="{{ route('permission.index') }}">Permissions</a>
                    </li>
                    <li class="{{ request()->routeIs('role.index') ? 'active-sub' : '' }}">
                        <a href="{{ route('role.index') }}">Role</a>
                    </li>
                    <li class="{{ request()->routeIs('user.index') ? 'active-sub' : '' }}">
                        <a href="{{ route('user.index') }}">Users</a>
                    </li>
                    <li class="{{ request()->routeIs('about-app.index') ? 'active-sub' : '' }}">
                        <a href="{{ route('about-app.index') }}">Tentang Aplikasi</a>
                    </li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('profile.edit') ? 'active current-page' : '' }}">
                <a href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i>
                    <span class="menu-text">Manajemen Profil</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('medical-record.index') ? 'active current-page' : '' }}">
                <a href="{{ route('medical-record.index') }}">
                    <i class="bi bi-heart-pulse"></i> <!-- Ikon sesuai keinginan -->
                    <span class="menu-text">Diagnosa Risiko Diabetes</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('medical-record.create') ? 'active current-page' : '' }}">
                <a href="{{ route('medical-record.create') }}">
                    <i class="bi bi-clipboard-plus"></i> <!-- Ganti dengan ikon yang sesuai -->
                    <span class="menu-text">Pemeriksaan DFU</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
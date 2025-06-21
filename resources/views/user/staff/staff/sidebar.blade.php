<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('user.staff.staff.dashboard') }}" class="sidebar__main-logo"><img src="{{ siteLogo() }}" alt="image"></a>
            <button type="button" class="navbar__expand"></button>
        </div>
        
        <div class="sidebar__user-info d-flex align-items-center px-3 py-3 mb-3">
            <div class="sidebar__user-thumb me-3">
                <img src="{{ getImage(getFilePath('userProfile').'/'.auth()->user()->image, getFileSize('userProfile')) }}" alt="User" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover; border: 2px solid rgba(255,255,255,0.2);">
            </div>
            <div class="sidebar__user-content">
                <h6 class="sidebar__user-name text-white mb-1">{{auth()->user()->fullname}}</h6>
                <span class="sidebar__user-designation text--secondary text-capitalize">@lang('Nhân viên kinh doanh')</span>
            </div>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.staff.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.staff.dashboard') }}" class="nav-link">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Bảng điều khiển')</span>
                    </a>
                </li>
                
                <li class="sidebar__menu-header text-white opacity-75 text-uppercase small px-3 my-2">@lang('Quản lý hợp đồng')</li>
                
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.staff.contracts') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.staff.contracts') }}" class="nav-link">
                        <i class="menu-icon las la-file-contract"></i>
                        <span class="menu-title">@lang('Hợp đồng của tôi')</span>
                    </a>
                </li>
                
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.staff.create_contract') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.staff.create_contract') }}" class="nav-link">
                        <i class="menu-icon las la-plus-circle"></i>
                        <span class="menu-title">@lang('Tạo hợp đồng mới')</span>
                    </a>
                </li>
                
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.staff.alerts') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.staff.alerts') }}" class="nav-link">
                        <i class="menu-icon las la-bell"></i>
                        <span class="menu-title">@lang('Cảnh báo hợp đồng')</span>
                    </a>
                </li>
                
                <li class="sidebar__menu-header text-white opacity-75 text-uppercase small px-3 my-2">@lang('Khách hàng')</li>
                
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.staff.customers') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.staff.customers') }}" class="nav-link">
                        <i class="menu-icon las la-user-tag"></i>
                        <span class="menu-title">@lang('Khách hàng của tôi')</span>
                    </a>
                </li>
                
                <li class="sidebar__menu-header text-white opacity-75 text-uppercase small px-3 my-2">@lang('Tài khoản')</li>
                
                <li class="sidebar-menu-item">
                    <a href="{{ route('user.profile.setting') }}" class="nav-link">
                        <i class="menu-icon las la-user-cog"></i>
                        <span class="menu-title">@lang('Cài đặt tài khoản')</span>
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="{{ route('user.logout') }}" class="nav-link">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">@lang('Đăng xuất')</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- sidebar end -->

<style>
    .sidebar__menu-header {
        font-size: 11px;
        letter-spacing: 0.5px;
    }
    
    .sidebar__user-info {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-menu-item > a {
        padding: 12px 15px;
        border-left: 3px solid transparent;
        transition: all 0.25s ease;
    }
    
    .sidebar-menu-item.active > a,
    .sidebar-submenu .sidebar-menu-item.active > a {
        border-left-color: var(--secondary-color);
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    .sidebar-menu-item > a:hover {
        border-left-color: rgba(16, 185, 129, 0.5);
        background-color: rgba(255, 255, 255, 0.03);
    }
    
    .menu-arrow {
        transition: transform 0.25s ease;
    }
    
    .sidebar-menu-item.active .menu-arrow i {
        transform: rotate(180deg);
    }
    
    .menu-badge {
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
    }
</style> 
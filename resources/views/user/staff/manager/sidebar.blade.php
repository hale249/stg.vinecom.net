<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('user.staff.manager.dashboard') }}" class="sidebar__main-logo"><img src="{{ siteLogo() }}" alt="image"></a>
            <button type="button" class="navbar__expand"></button>
        </div>
        
        <div class="sidebar__user-info d-flex align-items-center px-3 py-3 mb-3">
            <div class="sidebar__user-thumb me-3">
                <img src="{{ asset('assets/images/avatar.png') }}" alt="User" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover; border: 2px solid rgba(255,255,255,0.2);">
            </div>
            <div class="sidebar__user-content">
                <h6 class="sidebar__user-name text-white mb-1">{{auth()->user()->fullname}}</h6>
                <span class="sidebar__user-designation text--secondary text-capitalize">
                    @if(auth()->user()->position_level)
                        {{ auth()->user()->position_level }}
                    @else
                        @php
                            $roleNames = [
                                'sales_staff' => 'Nhân viên kinh doanh',
                                'sales_manager' => 'Quản lý kinh doanh',
                                'sales_director' => 'Giám đốc kinh doanh',
                                'regional_director' => 'Giám đốc vùng',
                            ];
                            $displayRole = $roleNames[auth()->user()->role] ?? 'Quản lý';
                        @endphp
                        @lang($displayRole)
                    @endif
                </span>
            </div>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.dashboard') }}" class="nav-link">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Bảng điều khiển')</span>
                    </a>
                </li>
                
                <li class="sidebar__menu-header text-white opacity-75 text-uppercase small px-3 my-2">@lang('Quản lý nhóm')</li>
                
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.team_members') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.team_members') }}" class="nav-link">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title">@lang('Thành viên nhóm')</span>
                    </a>
                </li>
                
                <li class="sidebar__menu-header text-white opacity-75 text-uppercase small px-3 my-2">@lang('Hợp đồng')</li>
                
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.contracts') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.contracts') }}" class="nav-link">
                        <i class="menu-icon las la-file-contract"></i>
                        <span class="menu-title">@lang('Hợp đồng nhóm')</span>
                        @if($stats['total_contracts'] ?? 0 > 0)
                            <span class="menu-badge pill bg--primary">{{ $stats['total_contracts'] ?? 0 }}</span>
                        @endif
                    </a>
                </li>

                <li class="sidebar__menu-header text-white opacity-75 text-uppercase small px-3 my-2">@lang('Quản trị nhân sự')</li>
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.hr.salary') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.hr.salary') }}" class="nav-link">
                        <i class="menu-icon las la-money-bill"></i>
                        <span class="menu-title">@lang('Lương & Thu nhập')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.hr.attendance') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.hr.attendance') }}" class="nav-link">
                        <i class="menu-icon las la-calendar-check"></i>
                        <span class="menu-title">@lang('Quản lý Chấm công')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.hr.kpi') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.hr.kpi') }}" class="nav-link">
                        <i class="menu-icon las la-bullseye"></i>
                        <span class="menu-title">@lang('KPI & Hiệu suất')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.hr.performance') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.hr.performance') }}" class="nav-link">
                        <i class="menu-icon las la-chart-line"></i>
                        <span class="menu-title">@lang('Hiệu suất làm việc')</span>
                    </a>
                </li>
                
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.alerts') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.alerts') }}" class="nav-link">
                        <i class="menu-icon las la-bell"></i>
                        <span class="menu-title">@lang('Cảnh báo hợp đồng')</span>
                    </a>
                </li>
                
                <li class="sidebar__menu-header text-white opacity-75 text-uppercase small px-3 my-2">@lang('Tài liệu')</li>
                
                <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.documents*') ? 'active' : '' }}">
                    <a href="{{ route('user.staff.manager.documents') }}" class="nav-link">
                        <i class="menu-icon las la-file-alt"></i>
                        <span class="menu-title">@lang('Tài liệu học tập')</span>
                    </a>
                </li>
                
                <li class="sidebar__menu-header text-white opacity-75 text-uppercase small px-3 my-2">@lang('Báo cáo')</li>
                
                <li class="sidebar-menu-item sidebar-dropdown {{ request()->routeIs('user.staff.manager.reports') || request()->routeIs('user.staff.manager.report.transactions') || request()->routeIs('user.staff.manager.report.interests') || request()->routeIs('user.staff.manager.report.commissions') ? 'active' : '' }}">
                    <a href="javascript:void(0)" class="{{ request()->routeIs('user.staff.manager.reports') || request()->routeIs('user.staff.manager.report.transactions') || request()->routeIs('user.staff.manager.report.interests') || request()->routeIs('user.staff.manager.report.commissions') ? 'active' : '' }}">
                        <i class="menu-icon las la-chart-bar"></i>
                        <span class="menu-title">@lang('Báo cáo')</span>
                        <span class="menu-arrow"><i class="las la-chevron-down"></i></span>
                    </a>
                    <div class="sidebar-submenu {{ request()->routeIs('user.staff.manager.reports') || request()->routeIs('user.staff.manager.report.transactions') || request()->routeIs('user.staff.manager.report.interests') || request()->routeIs('user.staff.manager.report.commissions') ? 'active' : '' }}">
                        <ul>
                            <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.reports') ? 'active' : '' }}">
                                <a href="{{ route('user.staff.manager.reports') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Báo cáo đầu tư')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.report.transactions') ? 'active' : '' }}">
                                <a href="{{ route('user.staff.manager.report.transactions') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Báo cáo giao dịch')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.report.interests') ? 'active' : '' }}">
                                <a href="{{ route('user.staff.manager.report.interests') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Báo cáo lãi suất')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ request()->routeIs('user.staff.manager.report.commissions') ? 'active' : '' }}">
                                <a href="{{ route('user.staff.manager.report.commissions') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Báo cáo hoa hồng')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
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
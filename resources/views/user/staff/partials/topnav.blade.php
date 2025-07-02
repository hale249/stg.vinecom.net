<nav class="navbar-wrapper bg-white">
    <div class="navbar__left">
        <button class="navbar-toggles"><i class="las la-bars"></i></button>
        <span class="navbar-text">{{ $pageTitle ?? request()->route()->getName() }}</span>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li class="dropdown">
                <button type="button" class="primary--layer" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <i class="las la-bell text--primary"></i>
                    @if(isset($pending_notifications) && $pending_notifications > 0)
                        <span class="pulse--primary"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu--md p-0 border-0 dropdown-menu-right shadow">
                    <div class="dropdown-menu__header">
                        <span class="caption">@lang('Thông báo')</span>
                        @if(isset($pending_notifications) && $pending_notifications > 0)
                            <p>@lang('Bạn có') {{ $pending_notifications }} @lang('thông báo chưa đọc')</p>
                        @else
                            <p>@lang('Không có thông báo mới')</p>
                        @endif
                    </div>
                    <div class="dropdown-menu__body">
                        @if(isset($notifications) && $notifications->count() > 0)
                            @foreach($notifications as $notification)
                                <div class="dropdown-menu__item">
                                    <div class="navbar-notifi">
                                        <div class="navbar-notifi__left bg--primary b-radius--rounded">
                                            <i class="las la-bell"></i>
                                        </div>
                                        <div class="navbar-notifi__right">
                                            <h6 class="notifi__title">{{ __($notification->subject ?? 'Notification') }}</h6>
                                            <span class="time"><i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="dropdown-menu__item">
                                <p class="text-muted mb-0">@lang('Không có thông báo')</p>
                            </div>
                        @endif
                    </div>
                    <div class="dropdown-menu__footer">
                        <a href="{{ route('user.staff.notifications.index') }}" class="view-all-message">@lang('Xem tất cả')</a>
                    </div>
                </div>
            </li>

            <li class="dropdown">
                <button type="button" class="" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb">
                            <img src="{{ getImage(getFilePath('userProfile').'/'.auth()->user()->image, getFileSize('userProfile')) }}" alt="@lang('image')">
                        </span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->user()->fullname }}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 dropdown-menu-right shadow">
                    <a href="{{ route('user.profile.setting') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Thông tin cá nhân')</span>
                    </a>

                    <a href="{{ route('user.change.password') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Đổi mật khẩu')</span>
                    </a>

                    <a href="{{ route('user.logout') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Đăng xuất')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<style>
    .navbar-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        height: 70px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .navbar__left {
        display: flex;
        align-items: center;
    }
    
    .navbar-toggles {
        background: transparent;
        border: none;
        font-size: 24px;
        margin-right: 15px;
        cursor: pointer;
        color: #333;
    }
    
    .navbar-text {
        font-size: 18px;
        font-weight: 500;
    }
    
    .navbar__action-list {
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    
    .navbar__action-list li {
        margin-left: 20px;
    }
    
    .navbar-user {
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    
    .navbar-user__thumb {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 10px;
    }
    
    .navbar-user__thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .navbar-user__name {
        font-size: 14px;
        font-weight: 500;
    }
    
    .dropdown-menu__item {
        text-decoration: none;
        color: #333;
        transition: all 0.2s;
    }
    
    .dropdown-menu__item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-menu__icon {
        font-size: 18px;
        margin-right: 10px;
        color: var(--primary-color);
    }
    
    .pulse--primary {
        display: block;
        position: absolute;
        top: 3px;
        right: 3px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ef4444;
    }
    
    @media (max-width: 767px) {
        .navbar-user__info {
            display: none;
        }
    }
</style> 
<div class="navbar-wrapper">
    <div class="navbar-top">
        <div class="navbar-left">
            <button class="navbar-toggles">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="navbar-right">
            <ul class="navbar-action">
                <li class="dropdown d-flex profile-dropdown">
                    <button type="button" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        <span class="navbar-user">
                            <span class="navbar-user__thumb">
                                @php $userImage = auth()->user()->image ? getImage(getFilePath('userProfile').'/'.auth()->user()->image, getFileSize('userProfile')) : asset('assets/images/avatar.png') @endphp
                                <img src="{{ $userImage }}" alt="image">
                            </span>
                            <span class="navbar-user__info">
                                <span class="navbar-user__name">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</span>
                            </span>
                            <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                        <a href="{{ route('user.profile.setting') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-user-circle"></i>
                            <span class="dropdown-menu__caption">@lang('Hồ sơ')</span>
                        </a>
                        <a href="{{ route('user.change.password') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-key"></i>
                            <span class="dropdown-menu__caption">@lang('Mật khẩu')</span>
                        </a>
                        <a href="{{ route('user.logout') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                            <span class="dropdown-menu__caption">@lang('Đăng xuất')</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div> 
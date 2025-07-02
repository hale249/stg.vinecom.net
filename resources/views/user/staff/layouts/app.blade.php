@extends('user.staff.layouts.master')
@section('content')
    <div class="page-wrapper default-version">
        @include('user.staff.manager.sidebar')
        @include('user.staff.partials.topnav')

        <div class="body-wrapper">
            <div class="bodywrapper__inner">
                @include('partials.notify')
                @include('user.staff.partials.breadcrumb')
                @yield('panel')
            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->
    </div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Active menu based on route
        $('.sidebar-menu-item.has-submenu').each(function() {
            let hasSub = false;
            $(this).find('.sidebar-submenu li').each(function() {
                if($(this).hasClass('active')) {
                    hasSub = true;
                }
            });
            if(hasSub) {
                $(this).addClass('active');
                $(this).find('.sidebar-submenu').addClass('active');
            }
        });

        $('.navbar-toggles').on('click', function() {
            $('.sidebar').toggleClass('active');
            $('.navbar-wrapper').toggleClass('active');
            $('.body-wrapper').toggleClass('active');
        });

    })(jQuery);
</script>
@endpush 
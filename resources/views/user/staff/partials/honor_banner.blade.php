@php
    $honorService = app(App\Services\HonorService::class);
    $activeHonors = App\Models\Honor::with('images')->active()->orderBy('id', 'desc')->get();
    $debug = [];
    $debug['honor_count'] = $activeHonors->count();
@endphp

@if($activeHonors->count() > 0)
<div class="honor-banner-section mb-4">
    <h4 class="honor-section-title mb-3">
        <i class="las la-crown text-warning me-2"></i>@lang('Vinh Danh Thành Viên')
        <span class="sub-title">@lang('Những người xuất sắc của chúng ta')</span>
    </h4>
    
    <!-- Slideshow Gallery -->
    <div class="honor-gallery-wrapper">
        @foreach($activeHonors as $honor)
            <div class="honor-slideshow">
                <div class="honor-slideshow-header">
                    <div class="honor-title-area">
                        <h5 class="honor-title"><i class="las la-trophy text-warning me-2"></i>{{ $honor->title }}</h5>
                        <p class="honor-description mb-0">{{ $honor->description }}</p>
                    </div>
                </div>
                
                <div class="honor-slideshow-body">
                    <!-- Main Image Slider -->
                    <div class="honor-main-slider">
                        @if($honor->images->count() > 0)
                            @foreach($honor->images as $image)
                                <div class="honor-slide">
                                    <div class="honor-image-container">
                                        <img src="{{ getImage(getFilePath('honor_images') . '/' . $image->image) }}" alt="{{ $image->caption ?? $honor->title }}" class="img-fluid">
                                        @if($image->caption)
                                            <div class="honor-image-caption">{{ $image->caption }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="honor-slide">
                                <div class="honor-image-container">
                                    <img src="{{ getImage(getFilePath('honors') . '/' . $honor->image) }}" alt="{{ $honor->title }}" class="img-fluid">
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Thumbnails for navigation -->
                    @if($honor->images->count() > 1)
                        <div class="honor-thumbnail-slider">
                            @foreach($honor->images as $image)
                                <div class="honor-thumbnail">
                                    <img src="{{ getImage(getFilePath('honor_images') . '/' . $image->image) }}" alt="{{ $image->caption ?? $honor->title }}" class="img-fluid">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
<div class="alert alert-info mb-4">
    <p class="mb-0"><i class="las la-info-circle me-2"></i>@lang('Không có thông tin vinh danh nào đang hoạt động.')</p>
</div>
@endif

<!-- Debug info for development -->
@if(config('app.debug', false))
<div class="alert alert-warning mb-4 d-none">
    <p class="mb-0">Debug: Số lượng vinh danh: {{ $debug['honor_count'] }}</p>
</div>
@endif

@push('style')
<style>
    .honor-section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        position: relative;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    .honor-section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #4CAF50, #8BC34A);
        border-radius: 3px;
    }
    
    .honor-section-title i {
        font-size: 1.75rem;
    }
    
    .honor-section-title .sub-title {
        margin-left: 10px;
        font-size: 0.9rem;
        color: #666;
        font-weight: normal;
    }
    
    /* Gallery Styles */
    .honor-gallery-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .honor-slideshow {
        flex: 1 1 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .honor-slideshow-header {
        padding: 15px 20px;
        background: linear-gradient(135deg, #f5f7ff 0%, #e8f0ff 100%);
        border-bottom: 1px solid #eaeaea;
    }
    
    .honor-title-area {
        display: flex;
        flex-direction: column;
    }
    
    .honor-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .honor-title i {
        font-size: 1.5rem;
        margin-right: 8px;
    }
    
    .honor-description {
        font-size: 0.9rem;
        color: #666;
    }
    
    .honor-slideshow-body {
        padding: 15px;
    }
    
    .honor-main-slider {
        position: relative;
        height: 300px;
        overflow: hidden;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    
    .honor-slide {
        width: 100%;
        height: 300px;
        position: relative;
    }
    
    .honor-image-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .honor-image-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    
    .honor-image-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 10px;
        font-size: 14px;
    }
    
    .honor-thumbnail-slider {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 10px 0;
    }
    
    .honor-thumbnail {
        flex: 0 0 80px;
        height: 60px;
        cursor: pointer;
        border-radius: 5px;
        overflow: hidden;
        border: 2px solid #eaeaea;
        transition: all 0.3s ease;
    }
    
    .honor-thumbnail.active {
        border-color: #4CAF50;
    }
    
    .honor-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Slick Slider Custom Styles */
    .honor-main-slider .slick-prev,
    .honor-main-slider .slick-next {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        z-index: 10;
        transition: all 0.3s ease;
    }
    
    .honor-main-slider .slick-prev:hover,
    .honor-main-slider .slick-next:hover {
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .honor-main-slider .slick-prev {
        left: 10px;
    }
    
    .honor-main-slider .slick-next {
        right: 10px;
    }
    
    .honor-main-slider .slick-prev:before,
    .honor-main-slider .slick-next:before {
        font-family: 'Line Awesome Free';
        font-weight: 900;
        font-size: 24px;
        color: #333;
    }
    
    .honor-main-slider .slick-prev:before {
        content: "\f104";
    }
    
    .honor-main-slider .slick-next:before {
        content: "\f105";
    }
    
    .honor-thumbnail-slider .slick-slide {
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    .honor-thumbnail-slider .slick-current {
        opacity: 1;
    }
    
    .honor-thumbnail-slider .slick-slide.slick-current .honor-thumbnail {
        border-color: #4CAF50;
        transform: scale(1.05);
    }
    
    @media (min-width: 768px) {
        .honor-slideshow {
            flex: 0 0 calc(50% - 10px);
        }
    }
    
    @media (min-width: 1200px) {
        .honor-slideshow {
            flex: 0 0 calc(33.333% - 14px);
        }
    }
    
    @media (max-width: 767px) {
        .honor-main-slider {
            height: 250px;
        }
        
        .honor-slide {
            height: 250px;
        }
        
        .honor-title-area {
            flex-direction: column;
        }
        
        .honor-title {
            margin-bottom: 5px;
        }
    }
    
    /* Animation styles for slides */
    .honor-slide {
        transform: scale(0.95);
        opacity: 0.8;
        transition: transform 0.8s ease, opacity 0.8s ease;
    }
    
    .honor-slide.animated,
    .slick-current .honor-slide {
        transform: scale(1);
        opacity: 1;
    }
    
    /* Fade-in animation for thumbnails */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .honor-thumbnail {
        animation: fadeIn 0.3s ease forwards;
        animation-delay: calc(var(--index) * 0.1s);
        opacity: 0;
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Check if jQuery and Slick are loaded
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded. Honor banner functionality will not work.');
            return;
        }
        
        if (typeof $.fn.slick === 'undefined') {
            console.error('Slick slider is not loaded. Honor banner functionality will not work properly.');
            // Continue with fallback implementation
        }
        
        $(document).ready(function() {
            // Initialize Slick slider for each honor slideshow
            $('.honor-main-slider').each(function(index) {
                const $mainSlider = $(this);
                const $thumbnailSlider = $(this).siblings('.honor-thumbnail-slider');
                
                try {
                    $mainSlider.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: true,
                        fade: true,
                        autoplay: true,
                        autoplaySpeed: 5000,
                        speed: 800,
                        cssEase: 'cubic-bezier(0.645, 0.045, 0.355, 1)',
                        prevArrow: '<button type="button" class="slick-prev"><i class="las la-angle-left"></i></button>',
                        nextArrow: '<button type="button" class="slick-next"><i class="las la-angle-right"></i></button>',
                        asNavFor: $thumbnailSlider.length ? $thumbnailSlider : null,
                        responsive: [
                            {
                                breakpoint: 768,
                                settings: {
                                    arrows: false,
                                    dots: true
                                }
                            }
                        ]
                    });
                    
                    if ($thumbnailSlider.length) {
                        $thumbnailSlider.slick({
                            slidesToShow: 5,
                            slidesToScroll: 1,
                            asNavFor: $mainSlider,
                            dots: false,
                            centerMode: false,
                            focusOnSelect: true,
                            arrows: false,
                            responsive: [
                                {
                                    breakpoint: 768,
                                    settings: {
                                        slidesToShow: 3
                                    }
                                }
                            ]
                        });
                    }
                    
                    // Add entrance animation to first slide
                    setTimeout(function() {
                        $mainSlider.find('.slick-current').addClass('animated');
                    }, 100);
                    
                    // Add animation when slide changes
                    $mainSlider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                        $(this).find('.slick-slide').removeClass('animated');
                    });
                    
                    $mainSlider.on('afterChange', function(event, slick, currentSlide) {
                        $(this).find('.slick-current').addClass('animated');
                    });
                } catch (error) {
                    console.error('Error initializing Slick slider:', error);
                    // Implement fallback when Slick fails
                    setupFallbackSlider($mainSlider, $thumbnailSlider);
                }
            });
            
            // Fallback function for when Slick initialization fails
            function setupFallbackSlider($mainSlider, $thumbnailSlider) {
                const $slides = $mainSlider.find('.honor-slide');
                $slides.hide().first().show().addClass('active');
                
                if ($thumbnailSlider.length) {
                    const $thumbs = $thumbnailSlider.find('.honor-thumbnail');
                    $thumbs.first().addClass('active');
                    
                    $thumbs.on('click', function() {
                        const index = $(this).index();
                        $slides.removeClass('active').hide();
                        $slides.eq(index).addClass('active').show();
                        $thumbs.removeClass('active');
                        $(this).addClass('active');
                    });
                    
                    // Simple auto-play functionality
                    setInterval(function() {
                        const $active = $slides.filter('.active');
                        let $next = $active.next('.honor-slide');
                        
                        if ($next.length === 0) {
                            $next = $slides.first();
                        }
                        
                        $slides.removeClass('active').hide();
                        $next.addClass('active').show();
                        
                        // Update thumbnails
                        const index = $next.index();
                        $thumbs.removeClass('active');
                        $thumbs.eq(index).addClass('active');
                    }, 5000);
                }
            }
        });
        
    })(jQuery);
</script>
@endpush
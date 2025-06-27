@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog py-120">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                <div class="col-xl-8 col-lg-7">
                    <div class="blog-item style-two bg-white">
                        <div class="blog-item__thumb">
                            <img src="{{ frontendImage('blog', @$blog->data_values->image, '830x420') }}" alt="blog_image" class="blog-detail-image">
                            @php
                                $categoryClass = '';
                                $categoryName = '';
                                
                                if($blog->category == 'company') {
                                    $categoryClass = 'company-news';
                                    $categoryName = 'Tin tức doanh nghiệp';
                                } elseif($blog->category == 'market') {
                                    $categoryClass = 'market-news';
                                    $categoryName = 'Tin tức thị trường';
                                }
                            @endphp
                            
                            @if($blog->category)
                            <span class="blog-category-badge {{ $categoryClass }}">{{ $categoryName }}</span>
                            @endif
                        </div>
                        <div class="blog-item__content">
                            <ul class="text-list inline">
                                <li class="text-list__item">
                                    <span class="text-list__item-icon"><i class="fas fa-calendar-alt"></i></span>
                                    {{ showDateTime($blog->created_at, 'd M Y') }}
                                </li>
                            </ul>
                            <h5 class="blog-item__title">{{ __($blog->data_values->title) }}</h5>
                            <div class="blog-details-content offer-details-desc">
                                @php echo $blog->data_values->description @endphp
                            </div>
                        </div>
                    </div>

                    <div class="blog-details__footer flex-wrap flex-column">
                        <h4 class="caption">@lang('Share This Post')</h4>
                        <ul class="social__links">
                            <li>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                    target="_blank"><i class="fab fa-facebook-f"></i></a>
                            </li>
                            <li>
                                <a href="https://twitter.com/intent/tweet?text={{ __($blog->data_values->title) }}&amp;url={{ urlencode(url()->current()) }}"
                                    target="_blank"><i class="fab fa-twitter"></i></a>
                            </li>
                            <li>
                                <a href="https://pinterest.com/pin/create/bookmarklet/?media={{ frontendImage('blog', @$blog->data_values->image, '1020x820') }}&url={{ urlencode(url()->current()) }}"
                                    target="_blank"><i class="fab fa-pinterest-p"></i></a>
                            </li>
                            <li>
                                <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}"
                                    target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            </li>
                        </ul>
                    </div>

                    <div class="fb-comments" data-href="{{ route('blog.details', $blog->slug) }}" data-numposts="5">
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="blog-sidebar">
                        <h6 class="blog-sidebar__title">@lang('Blog Categories')</h6>
                        <div class="blog-sidebar__content">
                            <div class="category-links mb-4">
                                <a href="{{ route('blogs') }}" class="category-link">Tất cả</a>
                                <a href="{{ route('blogs.category', 'company') }}" class="category-link">Tin tức doanh nghiệp</a>
                                <a href="{{ route('blogs.category', 'market') }}" class="category-link">Tin tức thị trường</a>
                            </div>
                        </div>
                    </div>

                    <div class="blog-sidebar mt-4">
                        <h6 class="blog-sidebar__title">@lang('Latest Blog Posts')</h6>
                        <div class="blog-sidebar__content">
                            <div class="row gy-4">
                                @foreach ($latestBlogs as $latestBlog)
                                    <div class="col-md-12">
                                        <div class="latest-blog">
                                            <div class="latest-blog__thumb">
                                                <a href="{{ route('blog.details', $latestBlog->slug) }}">
                                                    <img src="{{ frontendImage('blog', 'thumb_' . @$latestBlog->data_values->image, '415x210') }}"
                                                        alt="image">
                                                </a>
                                                
                                                @php
                                                    $latestCategoryClass = '';
                                                    $latestCategoryName = '';
                                                    
                                                    if($latestBlog->category == 'company') {
                                                        $latestCategoryClass = 'company-news';
                                                        $latestCategoryName = 'Tin tức doanh nghiệp';
                                                    } elseif($latestBlog->category == 'market') {
                                                        $latestCategoryClass = 'market-news';
                                                        $latestCategoryName = 'Tin tức thị trường';
                                                    }
                                                @endphp
                                                
                                                @if($latestBlog->category)
                                                <span class="sidebar-blog-category {{ $latestCategoryClass }}">{{ $latestCategoryName }}</span>
                                                @endif
                                            </div>
                                            <div class="latest-blog__content">
                                                <h4 class="latest-blog__title">
                                                    <a href="{{ route('blog.details', $latestBlog->slug) }}">
                                                        {{ __($latestBlog->data_values->title) }}
                                                    </a>
                                                </h4>
                                                <span class="latest-blog__date">
                                                    {{ $latestBlog->created_at->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
<style>
    .blog-item.style-two {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    
    .blog-item__thumb {
        position: relative;
        width: 100%;
        padding-top: 50.6%; /* 420/830 = 0.506 = 50.6% */
        overflow: hidden;
    }
    
    .blog-item__thumb img.blog-detail-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .blog-category-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 6px 15px;
        border-radius: 4px;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        z-index: 1;
        text-transform: uppercase;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .blog-category-badge.company-news {
        background-color: #3498db;
    }
    
    .blog-category-badge.market-news {
        background-color: #e74c3c;
    }
    
    .blog-item__content {
        padding: 30px;
    }
    
    .blog-item__title {
        margin: 20px 0;
        font-size: 24px;
        line-height: 1.4;
    }
    
    .blog-details-content {
        margin-top: 20px;
    }
    
    .blog-details-content img {
        max-width: 100%;
        height: auto;
        margin: 20px 0;
    }
    
    .blog-details-content p {
        margin-bottom: 20px;
        line-height: 1.8;
    }
    
    .blog-details__footer {
        margin-top: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .social__links {
        margin-top: 15px;
    }
    
    .social__links li {
        margin-right: 10px;
    }
    
    .social__links a {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border-radius: 50%;
        color: #666;
        transition: all 0.3s ease;
    }
    
    .social__links a:hover {
        background: var(--base);
        color: #fff;
    }
    
    .category-links {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .category-link {
        display: block;
        padding: 10px 15px;
        background: #f8f9fa;
        color: #555;
        border-left: 3px solid transparent;
        transition: all 0.3s;
        font-weight: 500;
        border-radius: 0 4px 4px 0;
    }
    
    .category-link:hover {
        background: #f0f0f0;
        color: var(--primary-color);
        border-left-color: var(--primary-color);
    }
    
    .latest-blog__thumb {
        position: relative;
    }
    
    .sidebar-blog-category {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 3px 8px;
        border-radius: 3px;
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        z-index: 1;
        text-transform: uppercase;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    
    .sidebar-blog-category.company-news {
        background-color: #3498db;
    }
    
    .sidebar-blog-category.market-news {
        background-color: #e74c3c;
    }
</style>
@endpush

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush

@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog py-120">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                <div class="col-xl-8 col-lg-7">
                    <div class="blog-item style-two bg-white">
                        <div class="blog-item__thumb">
                            <img src="{{ frontendImage('blog', @$blog->data_values->image, '830x420') }}" alt="blog_image" class="blog-detail-image">
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
</style>
@endpush

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush

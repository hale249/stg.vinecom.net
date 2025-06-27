@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="blogs py-120 bg--white">
        <div class="container">
            <!-- Blog Categories Navigation -->
            <div class="blog-categories mb-4">
                <ul class="nav nav-tabs blog-category-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ !$category ? 'active' : '' }}" href="{{ route('blogs') }}">Tất cả</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $category == 'company' ? 'active' : '' }}" href="{{ route('blogs.category', 'company') }}">Tin tức doanh nghiệp</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $category == 'market' ? 'active' : '' }}" href="{{ route('blogs.category', 'market') }}">Tin tức thị trường</a>
                    </li>
                </ul>
            </div>
            
            <div class="row gy-4">
                @include($activeTemplate . 'partials.blog')
            </div>

            @if ($blogs->hasPages())
                <div class="mt-4">
                    {{ paginateLinks($blogs) }}
                </div>
            @endif
        </div>
    </section>

    @if ($sections != null)
        @foreach (json_decode($sections) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endSection

@push('style')
<style>
    .blog-category-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 30px;
    }
    
    .blog-category-tabs .nav-item {
        margin-right: 10px;
    }
    
    .blog-category-tabs .nav-link {
        font-size: 16px;
        font-weight: 600;
        color: #555;
        padding: 10px 20px;
        border: none;
        border-radius: 0;
        position: relative;
        transition: all 0.3s;
    }
    
    .blog-category-tabs .nav-link.active,
    .blog-category-tabs .nav-link:hover {
        color: var(--primary-color);
        background-color: transparent;
    }
    
    .blog-category-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: var(--primary-color);
    }
</style>
@endpush

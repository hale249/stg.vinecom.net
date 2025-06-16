@foreach ($blogs as $blog)
    <div class="col-sm-6 col-lg-4">
        <article class="card card--blog">
            <a href="{{ route('blog.details', $blog->slug) }}" class="card-thumb">
                <img src="{{ frontendImage('blog', 'thumb_' . @$blog->data_values->image, '416x185') }}"
                    alt="{{ __($blog->data_values->title) }}"
                    class="blog-thumbnail">
            </a>
            <div class="card-body">
                <h6 class="card-title">
                    <a href="{{ route('blog.details', $blog->slug) }}">{{ __($blog->data_values->title) }}</a>
                </h6>
                <ul class="blog-meta">
                    <li class="card-meta__item">
                        <span class="blog-meta-item__icon"><i class="fas fa-calendar-days"></i></span>
                        <span class="blog-meta-item__text">{{ __(showDateTime($blog->created_at)) }}</span>
                    </li>
                </ul>
                <p class="card-desc">
                    @php echo substr(strip_tags($blog->data_values->description), 0, 100) @endphp
                </p>
                <a class="btn btn--sm btn--outline"
                    href="{{ route('blog.details', $blog->slug) }}">@lang('Read More')</a>
            </div>
        </article>
    </div>
@endforeach

@push('style')
<style>
    .card--blog .card-thumb {
        position: relative;
        width: 100%;
        padding-top: 44.47%; /* 185/416 = 0.4447 = 44.47% */
        overflow: hidden;
    }
    
    .card--blog .card-thumb img.blog-thumbnail {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .card--blog:hover .card-thumb img.blog-thumbnail {
        transform: scale(1.1);
    }
</style>
@endpush

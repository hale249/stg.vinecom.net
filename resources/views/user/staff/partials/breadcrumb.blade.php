<!-- breadcrumb -->
<div class="row mb-3">
    <div class="col-12">
        <div class="breadcrumb-wrapper d-flex flex-wrap align-items-center justify-content-between">
            <div class="breadcrumb-title">
                <h4 class="page-title mb-0">{{ $pageTitle ?? request()->route()->getName() }}</h4>
            </div>
            <ul class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('user.home') }}"><i class="las la-home"></i> @lang('Trang chủ')</a></li>
                @php
                    $segments = request()->segments();
                    $url = '';
                @endphp
                
                @foreach($segments as $key => $segment)
                    @php
                        $url .= '/'.$segment;
                        $isLast = $key == count($segments) - 1;
                    @endphp
                    
                    @if($isLast)
                        <li class="breadcrumb-item active">{{ __(ucwords(str_replace('-', ' ', $segment))) }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ url($url) }}">{{ __(ucwords(str_replace('-', ' ', $segment))) }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>
<!-- /breadcrumb -->

<style>
    .breadcrumb-wrapper {
        padding: 15px 0;
    }
    
    .page-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #334155;
    }
    
    .breadcrumb {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
    }
    
    .breadcrumb-item {
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        padding: 0 0.5rem;
        color: #cbd5e1;
    }
    
    .breadcrumb-item a {
        color: #6366f1;
        text-decoration: none;
    }
    
    .breadcrumb-item.active {
        color: #94a3b8;
    }
    
    @media (max-width: 767px) {
        .breadcrumb-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .breadcrumb {
            margin-top: 0.5rem;
        }
    }
</style> 
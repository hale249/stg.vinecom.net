@extends('templates.basic.layouts.app')

@section('content')
<div class="dashboard-section pt-120 pb-120">
    <div class="container">
        <div class="dashboard-style-3">
            <div class="dash-inner">
                <!-- Sidebar Menu -->
                @include('templates.basic.partials.sidebar_dashboard')
                
                <!-- Dashboard Content -->
                <div class="dashboard-inner__main">
                    <div class="dashboard-inner__content">
                        <div class="dashboard-inner__header">
                            <h5 class="dashboard-inner__header-title">@lang('Tài liệu tham khảo')</h5>
                        </div>
                        
                        <!-- Filter Section -->
                        <div class="dashboard-inner__filter mb-4">
                            <form action="{{ route('reference.documents') }}" method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <select name="category_id" class="form-select form--select" onchange="this.form.submit()">
                                        <option value="">@lang('Tất cả danh mục')</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request()->category_id == $category->id ? 'selected' : '' }}>
                                                <i class="{{ $category->icon }}"></i> {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control form--control" placeholder="@lang('Tìm kiếm tài liệu...')" value="{{ request()->search }}">
                                        <button class="input-group-text btn btn--base"><i class="las la-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Document Grid -->
                        <div class="row g-4">
                            @forelse($documents as $document)
                                <div class="col-md-6 col-xl-4">
                                    <div class="card custom--card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <span class="card-title me-auto">
                                                <i class="{{ $document->category->icon ?? 'las la-folder' }} text--base me-2"></i>
                                                {{ $document->category->name ?? 'Không phân loại' }}
                                            </span>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="document-title">{{ $document->title }}</h6>
                                            <p class="document-desc text-muted mb-3 small">
                                                {{ Str::limit($document->description, 100) ?? 'Không có mô tả' }}
                                            </p>
                                            <div class="document-meta small text-muted mt-auto">
                                                <p class="mb-1"><i class="far fa-file me-1"></i> {{ $document->file_name }}</p>
                                                <p class="mb-1"><i class="far fa-hdd me-1"></i> {{ $document->file_size_formatted }}</p>
                                                <p class="mb-0"><i class="far fa-calendar-alt me-1"></i> {{ showDateTime($document->created_at, 'd/m/Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between">
                                                @php
                                                    $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
                                                    $viewable = in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
                                                @endphp
                                                
                                                @if($viewable)
                                                    <a href="{{ route('reference.document.view', $document->id) }}" class="btn btn-sm btn--base">
                                                        <i class="far fa-eye me-1"></i> @lang('Xem')
                                                    </a>
                                                @else
                                                    <button disabled class="btn btn-sm btn--dark opacity-50">
                                                        <i class="far fa-eye-slash me-1"></i> @lang('Không xem được')
                                                    </button>
                                                @endif
                                                
                                                <a href="{{ route('reference.document.download', $document->id) }}" class="btn btn-sm btn--secondary">
                                                    <i class="fas fa-download me-1"></i> @lang('Tải xuống')
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center">
                                        <i class="far fa-folder-open fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">@lang('Không tìm thấy tài liệu tham khảo')</h5>
                                        <p class="text-muted">@lang('Không có tài liệu phù hợp với điều kiện tìm kiếm hoặc không có tài liệu nào cho vai trò của bạn.')</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        
                        @if($documents->hasPages())
                            <div class="d-flex justify-content-end mt-4">
                                {{ paginateLinks($documents) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .document-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .document-desc {
        flex-grow: 1;
        font-size: 14px;
    }
    .document-meta p {
        font-size: 12px;
    }
    .card.custom--card {
        transition: all 0.3s ease;
    }
    .card.custom--card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endpush 
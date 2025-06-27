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
                        <div class="dashboard-inner__header d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="dashboard-inner__header-title">{{ $document->title }}</h5>
                                <span class="badge bg--primary">
                                    <i class="{{ $document->category->icon ?? 'las la-folder' }}"></i>
                                    {{ $document->category->name }}
                                </span>
                            </div>
                            <div>
                                <a href="{{ route('reference.document.download', $document->id) }}" class="btn btn-sm btn--secondary">
                                    <i class="fas fa-download"></i> @lang('Tải xuống')
                                </a>
                                <a href="{{ route('reference.documents') }}" class="btn btn-sm btn--dark">
                                    <i class="fas fa-arrow-left"></i> @lang('Quay lại')
                                </a>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-body">
                                @if($document->description)
                                <div class="mb-4 p-3 bg-light rounded">
                                    <h6>@lang('Mô tả'):</h6>
                                    <p>{{ $document->description }}</p>
                                </div>
                                @endif
                                
                                @php
                                    $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
                                    $isPdf = strtolower($extension) === 'pdf';
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                    $publicPath = asset('storage/' . $document->file_path);
                                    $streamPath = route('reference.document.stream', $document->id);
                                @endphp
                                
                                <div class="document-viewer mt-3">
                                    @if($isPdf)
                                        <div class="pdf-container" style="height: 80vh;">
                                            <!-- Primary method - Object tag -->
                                            <object data="{{ $streamPath }}" type="application/pdf" width="100%" height="100%" class="w-100 h-100 border rounded">
                                                <!-- Fallback - iframe -->
                                                <iframe src="{{ $streamPath }}" width="100%" height="100%" style="border: 1px solid #ddd; border-radius: 4px;">
                                                    <!-- Final fallback - download link -->
                                                    <p>@lang('Trình duyệt của bạn không hỗ trợ xem PDF. Vui lòng')
                                                    <a href="{{ route('reference.document.download', $document->id) }}">@lang('tải xuống tài liệu')</a>
                                                    @lang('để xem').</p>
                                                </iframe>
                                            </object>
                                        </div>
                                    @elseif($isImage)
                                        <div class="text-center">
                                            <img src="{{ $streamPath }}" alt="{{ $document->title }}" class="img-fluid border rounded">
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="far fa-file-alt fa-4x text-muted mb-3"></i>
                                            <h5>@lang('Tài liệu này không thể xem trực tiếp')</h5>
                                            <p>@lang('Vui lòng tải tài liệu xuống để xem.')</p>
                                            <a href="{{ route('reference.document.download', $document->id) }}" class="btn btn--base">
                                                <i class="fas fa-download me-2"></i> @lang('Tải xuống ngay')
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between text-muted">
                                <div><i class="far fa-file me-1"></i> {{ $document->file_name }} ({{ $document->file_size_formatted }})</div>
                                <div><i class="far fa-calendar-alt me-1"></i> {{ showDateTime($document->created_at) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .document-viewer {
        width: 100%;
        overflow: hidden;
    }
    
    .document-viewer object,
    .document-viewer iframe {
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .document-viewer img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }
    
    .pdf-container {
        position: relative;
        width: 100%;
    }
</style>
@endpush 
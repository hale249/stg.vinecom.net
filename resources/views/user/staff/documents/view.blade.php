@extends($layout ?? 'user.staff.layouts.staff_app')

@section('panel')
<div class="row align-items-center mb-30 justify-content-between">
    <div class="col-lg-6 col-sm-6">
        <h6 class="page-title">{{ $document->title }}</h6>
        <span class="badge bg--primary">
            <i class="{{ $document->category->icon ?? 'las la-folder' }} me-1"></i>
            {{ $document->category->name }}
        </span>
    </div>
    <div class="col-lg-6 col-sm-6 text-sm-end mt-sm-0 mt-3">
        @php
            $routePrefix = $isManager ? 'user.staff.manager' : 'user.staff.staff';
        @endphp
        <a href="{{ route($routePrefix . '.documents.download', $document->id) }}" class="btn btn--info">
            <i class="fas fa-download"></i> @lang('Tải xuống')
        </a>
        <a href="{{ route($routePrefix . '.documents') }}" class="btn btn--primary">
            <i class="fas fa-backward"></i> @lang('Quay lại')
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="document-viewer">
                    @if($document->isViewable())
                        @if($document->isPDF())
                            <iframe src="{{ asset('storage/' . $document->file_path) }}" width="100%" height="600px" frameborder="0"></iframe>
                        @elseif($document->isImage())
                            <img src="{{ asset('storage/' . $document->file_path) }}" alt="{{ $document->title }}" class="img-fluid">
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="far fa-file-alt display-4 text-muted"></i>
                            <p class="mt-3">@lang('Tài liệu này không thể xem trực tiếp. Vui lòng tải xuống để xem.')</p>
                            <a href="{{ route($routePrefix . '.documents.download', $document->id) }}" class="btn btn--primary mt-3">
                                <i class="fas fa-download"></i> @lang('Tải xuống')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@if($documents->isNotEmpty())
<div class="project-documents-section mt-4">
    <h5 class="section-title mb-3">
        <i class="las la-file-pdf text--primary"></i>
        @lang('Tài liệu dự án')
    </h5>
    
    <div class="row">
        @if($legalDocuments->isNotEmpty())
        <div class="col-md-6 mb-3">
            <div class="document-category">
                <h6 class="document-category-title">
                    <i class="las la-gavel text--danger"></i>
                    @lang('Tài liệu pháp lý')
                </h6>
                <div class="document-list">
                    @foreach($legalDocuments as $document)
                    <div class="document-item">
                        <div class="document-info">
                            <i class="las la-file-pdf text--danger"></i>
                            <div class="document-details">
                                <h6 class="document-title">{{ $document->title }}</h6>
                                @if($document->description)
                                    <p class="document-description">{{ Str::limit($document->description, 60) }}</p>
                                @endif
                                <small class="text-muted">
                                    <i class="las la-download"></i> {{ $document->file_size_formatted }}
                                </small>
                            </div>
                        </div>
                        <div class="document-actions">
                            <a href="{{ $document->getPreviewUrl() }}" target="_blank" class="btn btn-sm btn-outline--primary" title="@lang('Xem trước')">
                                <i class="las la-eye"></i>
                            </a>
                            <a href="{{ $document->getDownloadUrl() }}" class="btn btn-sm btn--primary" title="@lang('Tải xuống')">
                                <i class="las la-download"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($financialDocuments->isNotEmpty())
        <div class="col-md-6 mb-3">
            <div class="document-category">
                <h6 class="document-category-title">
                    <i class="las la-chart-line text--warning"></i>
                    @lang('Tài liệu tài chính')
                </h6>
                <div class="document-list">
                    @foreach($financialDocuments as $document)
                    <div class="document-item">
                        <div class="document-info">
                            <i class="las la-file-pdf text--warning"></i>
                            <div class="document-details">
                                <h6 class="document-title">{{ $document->title }}</h6>
                                @if($document->description)
                                    <p class="document-description">{{ Str::limit($document->description, 60) }}</p>
                                @endif
                                <small class="text-muted">
                                    <i class="las la-download"></i> {{ $document->file_size_formatted }}
                                </small>
                            </div>
                        </div>
                        <div class="document-actions">
                            <a href="{{ $document->getPreviewUrl() }}" target="_blank" class="btn btn-sm btn-outline--warning" title="@lang('Xem trước')">
                                <i class="las la-eye"></i>
                            </a>
                            <a href="{{ $document->getDownloadUrl() }}" class="btn btn-sm btn--warning" title="@lang('Tải xuống')">
                                <i class="las la-download"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($technicalDocuments->isNotEmpty())
        <div class="col-md-6 mb-3">
            <div class="document-category">
                <h6 class="document-category-title">
                    <i class="las la-cogs text--info"></i>
                    @lang('Tài liệu kỹ thuật')
                </h6>
                <div class="document-list">
                    @foreach($technicalDocuments as $document)
                    <div class="document-item">
                        <div class="document-info">
                            <i class="las la-file-pdf text--info"></i>
                            <div class="document-details">
                                <h6 class="document-title">{{ $document->title }}</h6>
                                @if($document->description)
                                    <p class="document-description">{{ Str::limit($document->description, 60) }}</p>
                                @endif
                                <small class="text-muted">
                                    <i class="las la-download"></i> {{ $document->file_size_formatted }}
                                </small>
                            </div>
                        </div>
                        <div class="document-actions">
                            <a href="{{ $document->getPreviewUrl() }}" target="_blank" class="btn btn-sm btn-outline--info" title="@lang('Xem trước')">
                                <i class="las la-eye"></i>
                            </a>
                            <a href="{{ $document->getDownloadUrl() }}" class="btn btn-sm btn--info" title="@lang('Tải xuống')">
                                <i class="las la-download"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($documents->where('type', 'other')->isNotEmpty())
        <div class="col-md-6 mb-3">
            <div class="document-category">
                <h6 class="document-category-title">
                    <i class="las la-file-alt text--secondary"></i>
                    @lang('Tài liệu khác')
                </h6>
                <div class="document-list">
                    @foreach($documents->where('type', 'other') as $document)
                    <div class="document-item">
                        <div class="document-info">
                            <i class="las la-file-pdf text--secondary"></i>
                            <div class="document-details">
                                <h6 class="document-title">{{ $document->title }}</h6>
                                @if($document->description)
                                    <p class="document-description">{{ Str::limit($document->description, 60) }}</p>
                                @endif
                                <small class="text-muted">
                                    <i class="las la-download"></i> {{ $document->file_size_formatted }}
                                </small>
                            </div>
                        </div>
                        <div class="document-actions">
                            <a href="{{ $document->getPreviewUrl() }}" target="_blank" class="btn btn-sm btn-outline--secondary" title="@lang('Xem trước')">
                                <i class="las la-eye"></i>
                            </a>
                            <a href="{{ $document->getDownloadUrl() }}" class="btn btn-sm btn--secondary" title="@lang('Tải xuống')">
                                <i class="las la-download"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.project-documents-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.section-title {
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.document-category {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: 100%;
}

.document-category-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #dee2e6;
}

.document-list {
    max-height: 300px;
    overflow-y: auto;
}

.document-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    margin-bottom: 10px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.document-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.document-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.document-info i {
    font-size: 1.5rem;
    margin-right: 12px;
}

.document-details {
    flex: 1;
}

.document-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0;
    color: #495057;
}

.document-description {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 4px 0;
}

.document-actions {
    display: flex;
    gap: 5px;
}

.document-actions .btn {
    padding: 4px 8px;
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .document-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .document-actions {
        margin-top: 10px;
        width: 100%;
        justify-content: flex-end;
    }
}
</style>
@endif 
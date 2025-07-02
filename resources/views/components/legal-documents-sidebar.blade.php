@if($legalDocuments->isNotEmpty())
<div class="legal-documents-sidebar">
    <div class="sidebar-card">
        <div class="sidebar-card-header">
            <h6 class="sidebar-card-title">
                <i class="las la-gavel text--danger"></i>
                @lang('Tài liệu pháp lý')
            </h6>
            <small class="text-muted">@lang('Các tài liệu pháp lý quan trọng của dự án')</small>
        </div>
        
        <div class="sidebar-card-body">
            <div class="legal-documents-list">
                @foreach($legalDocuments as $document)
                <div class="legal-document-item">
                    <div class="document-icon">
                        <i class="las la-file-pdf text--danger"></i>
                    </div>
                    <div class="document-content">
                        <h6 class="document-title">{{ $document->title }}</h6>
                        @if($document->description)
                            <p class="document-description">{{ Str::limit($document->description, 80) }}</p>
                        @endif
                        <div class="document-meta">
                            <span class="document-size">
                                <i class="las la-download"></i> {{ $document->file_size_formatted }}
                            </span>
                            <span class="document-date">
                                <i class="las la-calendar"></i> {{ $document->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="document-actions">
                        <a href="{{ $document->getPreviewUrl() }}" target="_blank" class="btn btn-sm btn-outline--danger" title="@lang('Xem trước')">
                            <i class="las la-eye"></i>
                        </a>
                        <a href="{{ $document->getDownloadUrl() }}" class="btn btn-sm btn--danger" title="@lang('Tải xuống')">
                            <i class="las la-download"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="legal-notice mt-3">
                <div class="alert alert-warning">
                    <i class="las la-exclamation-triangle"></i>
                    <small>
                        <strong>@lang('Lưu ý pháp lý'):</strong> 
                        @lang('Các tài liệu này được cung cấp để minh bạch thông tin. Vui lòng đọc kỹ trước khi đầu tư.')
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.legal-documents-sidebar {
    margin-top: 20px;
}

.sidebar-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.sidebar-card-header {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 15px 20px;
    text-align: center;
}

.sidebar-card-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.sidebar-card-title i {
    margin-right: 8px;
}

.sidebar-card-body {
    padding: 20px;
}

.legal-documents-list {
    max-height: 400px;
    overflow-y: auto;
}

.legal-document-item {
    display: flex;
    align-items: flex-start;
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
    position: relative;
}

.legal-document-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.legal-document-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #dc3545;
    border-radius: 2px;
}

.document-icon {
    margin-right: 12px;
    margin-top: 2px;
}

.document-icon i {
    font-size: 1.8rem;
}

.document-content {
    flex: 1;
    min-width: 0;
}

.document-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 5px 0;
    color: #495057;
    line-height: 1.3;
}

.document-description {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.document-meta {
    display: flex;
    gap: 15px;
    font-size: 0.75rem;
    color: #6c757d;
}

.document-meta span {
    display: flex;
    align-items: center;
}

.document-meta i {
    margin-right: 4px;
}

.document-actions {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-left: 10px;
}

.document-actions .btn {
    padding: 6px 10px;
    font-size: 0.8rem;
    min-width: 35px;
}

.legal-notice .alert {
    border: none;
    background: #fff3cd;
    color: #856404;
    font-size: 0.8rem;
    padding: 12px;
    border-radius: 6px;
}

.legal-notice .alert i {
    margin-right: 5px;
}

@media (max-width: 768px) {
    .legal-document-item {
        flex-direction: column;
    }
    
    .document-actions {
        flex-direction: row;
        margin-left: 0;
        margin-top: 10px;
        justify-content: flex-end;
    }
    
    .document-meta {
        flex-direction: column;
        gap: 5px;
    }
}

/* Custom scrollbar for documents list */
.legal-documents-list::-webkit-scrollbar {
    width: 6px;
}

.legal-documents-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.legal-documents-list::-webkit-scrollbar-thumb {
    background: #dc3545;
    border-radius: 3px;
}

.legal-documents-list::-webkit-scrollbar-thumb:hover {
    background: #c82333;
}
</style>
@endif 
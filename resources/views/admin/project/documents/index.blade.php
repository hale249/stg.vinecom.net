@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="mb-0">@lang('Tài liệu dự án'): <span class="text--primary">{{ __($project->title) }}</span></h5>
                            <a href="{{ route('admin.project.documents.create', $project->id) }}" class="btn btn--primary btn-sm">
                                <i class="las la-plus"></i> @lang('Thêm tài liệu mới')
                            </a>
                        </div>
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Tên tài liệu')</th>
                                    <th>@lang('Loại')</th>
                                    <th>@lang('Kích thước')</th>
                                    <th>@lang('Trạng thái')</th>
                                    <th>@lang('Thứ tự')</th>
                                    <th>@lang('Ngày tạo')</th>
                                    <th>@lang('Thao tác')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="{{ $document->type_icon }} text--primary me-2"></i>
                                                <div>
                                                    <span class="fw-bold">{{ __($document->title) }}</span>
                                                    @if($document->description)
                                                        <br><small class="text-muted">{{ Str::limit($document->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge--{{ $document->type == 'legal' ? 'danger' : ($document->type == 'financial' ? 'success' : ($document->type == 'technical' ? 'info' : 'secondary')) }}">
                                                {{ $document->type_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $document->file_size_formatted }}</span>
                                        </td>
                                        <td>
                                            @if($document->is_public)
                                                <span class="badge badge--success">@lang('Công khai')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Riêng tư')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $document->sort_order }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ showDateTime($document->created_at) }}</span>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.project.documents.download', [$project->id, $document->id]) }}" 
                                                   class="btn btn-sm btn-outline--primary" title="@lang('Tải xuống')">
                                                    <i class="las la-download"></i>
                                                </a>
                                                <a href="{{ route('admin.project.documents.edit', [$project->id, $document->id]) }}" 
                                                   class="btn btn-sm btn-outline--info" title="@lang('Chỉnh sửa')">
                                                    <i class="las la-pen"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" 
                                                        data-action="{{ route('admin.project.documents.delete', [$project->id, $document->id]) }}"
                                                        data-question="@lang('Bạn có chắc chắn muốn xóa tài liệu này?')"
                                                        title="@lang('Xóa')">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="empty-notification-list text-center py-5">
                                                <img src="{{ getImage('assets/images/empty.png') }}" alt="empty" class="mb-3" style="width: 80px;">
                                                <p class="text-muted">@lang('Chưa có tài liệu nào được thêm cho dự án này.')</p>
                                                <a href="{{ route('admin.project.documents.create', $project->id) }}" class="btn btn--primary btn-sm">
                                                    <i class="las la-plus"></i> @lang('Thêm tài liệu đầu tiên')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($documents->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($documents) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.project.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-undo"></i> @lang('Quay lại danh sách dự án')
    </a>
@endpush 
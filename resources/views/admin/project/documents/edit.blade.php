@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="{{ route('admin.project.documents.update', [$project->id, $document->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Tên tài liệu') <span class="text--danger">*</span></label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $document->title) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Loại tài liệu') <span class="text--danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="">@lang('Chọn loại')</option>
                                        <option value="legal" {{ old('type', $document->type) == 'legal' ? 'selected' : '' }}>@lang('Tài liệu pháp lý')</option>
                                        <option value="financial" {{ old('type', $document->type) == 'financial' ? 'selected' : '' }}>@lang('Tài liệu tài chính')</option>
                                        <option value="technical" {{ old('type', $document->type) == 'technical' ? 'selected' : '' }}>@lang('Tài liệu kỹ thuật')</option>
                                        <option value="other" {{ old('type', $document->type) == 'other' ? 'selected' : '' }}>@lang('Tài liệu khác')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Mô tả')</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="@lang('Mô tả ngắn gọn về tài liệu...')">{{ old('description', $document->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('File PDF')</label>
                                    <input type="file" class="form-control" name="document_file" accept=".pdf">
                                    <small class="text-muted">@lang('Chỉ chấp nhận file PDF, tối đa 10MB. Để trống nếu không muốn thay đổi file.')</small>
                                    @if($document->file_path)
                                        <div class="mt-2">
                                            <small class="text-info">
                                                <i class="las la-file-pdf"></i> 
                                                @lang('File hiện tại'): {{ $document->file_name }} ({{ $document->file_size_formatted }})
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Thứ tự hiển thị')</label>
                                    <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $document->sort_order) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input for is_public with current document value -->
                        <input type="hidden" name="is_public" value="{{ $document->is_public ? '1' : '0' }}">

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Cập nhật tài liệu')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.project.documents.index', $project->id) }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-undo"></i> @lang('Quay lại danh sách tài liệu')
    </a>
@endpush 
@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">@lang('Tiêu đề tài liệu')</label>
                                    <input type="text" name="title" class="form-control" required value="{{ old('title', $document->title) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">@lang('Danh mục')</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="" selected disabled>@lang('Chọn danh mục')</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (old('category_id', $document->category_id) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('File hiện tại')</label>
                                    <div class="border p-3 rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="far fa-file me-2 fs-3"></i>
                                                <span>{{ $document->file_name }} ({{ $document->file_size_formatted }})</span>
                                            </div>
                                            <a href="{{ storage_path('app/public/' . $document->file_path) }}" download class="btn btn-sm btn--primary">
                                                <i class="las la-download"></i> @lang('Tải xuống')
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="document">@lang('Thay thế tài liệu') <small class="text-muted">(@lang('Để trống nếu không thay đổi'))</small></label>
                                    <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png">
                                    <small class="text-danger">@lang('PDF, Word, Excel, PowerPoint, Image, ZIP, RAR tối đa 10MB')</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">@lang('Mô tả')</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $document->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Quyền xem')</label>
                                    <div class="d-flex flex-wrap gap-3 mt-2">
                                        <div class="custom-control custom-checkbox form-check">
                                            <input type="checkbox" name="for_manager" id="for_manager" value="1" class="custom-control-input" {{ old('for_manager', $document->for_manager) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="for_manager">@lang('Quản lý')</label>
                                        </div>
                                        <div class="custom-control custom-checkbox form-check">
                                            <input type="checkbox" name="for_staff" id="for_staff" value="1" class="custom-control-input" {{ old('for_staff', $document->for_staff) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="for_staff">@lang('Nhân viên')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sort_order">@lang('Thứ tự sắp xếp')</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $document->sort_order) }}" min="0">
                                    <small class="text-muted">@lang('Số thấp hơn sẽ hiển thị trước')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Trạng thái')</label>
                                    <input type="checkbox" data-width="100%" data-height="40px" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Kích hoạt')" data-off="@lang('Vô hiệu hóa')" name="status" {{ $document->status ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Cập nhật tài liệu')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
        <i class="fa fa-fw fa-list"></i>@lang('Danh sách tài liệu')
    </a>
@endpush 
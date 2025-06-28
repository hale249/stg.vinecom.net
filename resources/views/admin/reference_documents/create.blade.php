@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="las la-plus-circle me-2"></i>@lang('Thêm tài liệu tham khảo')
                    </h5>
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-outline--primary">
                        <i class="las la-list"></i> @lang('Danh sách')
                    </a>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row gy-3">
                            <!-- Basic Information Section -->
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <h5 class="border-bottom pb-2 mb-3 w-100">@lang('Thông tin cơ bản')</h5>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">@lang('Tiêu đề') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="las la-font"></i></span>
                                        <input type="text" class="form-control" placeholder="@lang('Nhập tiêu đề tài liệu')" name="title" value="{{ old('title') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">@lang('Danh mục') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="las la-folder"></i></span>
                                        <select name="category_id" class="form-control" required>
                                            <option value="" disabled selected>@lang('Chọn danh mục')</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-bold">@lang('Mô tả')</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="las la-align-left"></i></span>
                                        <textarea name="description" rows="3" class="form-control" placeholder="@lang('Nhập mô tả ngắn về tài liệu')">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- File Upload Section -->
                            <div class="col-12 mt-4">
                                <div class="d-flex align-items-center">
                                    <h5 class="border-bottom pb-2 mb-3 w-100">@lang('Tệp tài liệu')</h5>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-bold">@lang('Tải lên tệp tin') <span class="text-danger">*</span></label>
                                    
                                    <div class="custom-file-upload">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="las la-upload"></i></span>
                                            <input type="file" name="document" id="document" class="form-control" required>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            <i class="las la-info-circle me-1"></i>
                                            @lang('Định dạng cho phép'): PDF, Word, Excel, PowerPoint, Images, ZIP, RAR - @lang('Tối đa'): 10MB
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Permissions Section -->
                            <div class="col-12 mt-4">
                                <div class="d-flex align-items-center">
                                    <h5 class="border-bottom pb-2 mb-3 w-100">@lang('Quyền truy cập & Cài đặt khác')</h5>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label fw-bold d-block">@lang('Manager có thể xem?')</label>
                                    <input type="checkbox" name="for_manager" value="1" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="@lang('Có')" data-off="@lang('Không')" checked>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label fw-bold d-block">@lang('Staff có thể xem?')</label>
                                    <input type="checkbox" name="for_staff" value="1" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="@lang('Có')" data-off="@lang('Không')" checked>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label fw-bold d-block">@lang('Trạng thái')</label>
                                    <input type="hidden" name="status" value="0">
                                    <input type="checkbox" name="status" value="1" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="@lang('Kích hoạt')" data-off="@lang('Vô hiệu hóa')" checked>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">@lang('Thứ tự hiển thị')</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="las la-sort-numeric-up"></i></span>
                                        <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="las la-info-circle me-1"></i>@lang('Số thấp hơn sẽ được hiển thị trước')
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn--primary w-100 h-45">
                                <i class="las la-plus-circle me-2"></i>@lang('Thêm tài liệu')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .form-label {
        margin-bottom: 0.5rem;
    }
    .custom-file-upload {
        position: relative;
    }
</style>
@endpush 
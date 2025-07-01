@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.honors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Ảnh đại diện <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" accept=".jpg, .jpeg, .png" required>
                                <small class="text-muted">
                                    Các định dạng hỗ trợ: jpg, jpeg, png. 
                                    Kích thước khuyên dùng: 800x600px.
                                    Đây là ảnh chính sẽ hiển thị trong danh sách vinh danh.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Mô tả</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Thêm nhiều ảnh vinh danh</h5>
                                </div>
                                <div class="card-body">
                                    <div class="images-container">
                                        <div class="row image-row mb-4">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Hình ảnh</label>
                                                    <input type="file" name="images[]" class="form-control" accept=".jpg, .jpeg, .png">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>Chú thích</label>
                                                    <input type="text" name="captions[]" class="form-control" placeholder="Nhập chú thích cho ảnh">
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger remove-image"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-sm btn-success add-more-images">
                                            <i class="fas fa-plus-circle"></i> Thêm ảnh
                                        </button>
                                    </div>
                                    <small class="form-text text-muted mt-2">
                                        Bạn có thể thêm nhiều ảnh cho vinh danh. Ảnh đầu tiên sẽ được chọn làm ảnh nổi bật.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" 
                                       data-toggle="toggle" data-on="Kích hoạt" data-off="Không kích hoạt" 
                                       name="is_active" value="1" checked>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary btn-block h-45">Lưu</button>
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
    <a href="{{ route('admin.honors.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="la la-undo"></i> Quay lại
    </a>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Thêm ảnh mới
        $('.add-more-images').on('click', function() {
            let html = `
                <div class="row image-row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Hình ảnh</label>
                            <input type="file" name="images[]" class="form-control" accept=".jpg, .jpeg, .png">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Chú thích</label>
                            <input type="text" name="captions[]" class="form-control" placeholder="Nhập chú thích cho ảnh">
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-image"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            $('.images-container').append(html);
        });
        
        // Xóa ảnh
        $(document).on('click', '.remove-image', function() {
            $(this).closest('.image-row').remove();
        });
        
    })(jQuery);
</script>
@endpush 
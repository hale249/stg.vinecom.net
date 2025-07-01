@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.honors.update', $honor->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ $honor->title }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Ảnh đại diện</label>
                                <input type="file" name="image" class="form-control" accept=".jpg, .jpeg, .png">
                                <small class="text-muted">
                                    Các định dạng hỗ trợ: jpg, jpeg, png. 
                                    Kích thước khuyên dùng: 800x600px.
                                    Đây là ảnh chính sẽ hiển thị trong danh sách vinh danh.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-lg-12 text-center">
                            <div class="form-group">
                                <img src="{{ getImage(getFilePath('honors') . '/' . $honor->image, '350x200') }}" 
                                     alt="{{ $honor->title }}" class="img-thumbnail">
                                <small class="d-block mt-2">Ảnh đại diện hiện tại</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Mô tả</label>
                                <textarea name="description" class="form-control" rows="4">{{ $honor->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quản lý nhiều ảnh -->
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="card border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Ảnh vinh danh ({{ count($honor->images) }} ảnh)</h5>
                                    <button type="button" class="btn btn-sm btn-success add-more-images">
                                        <i class="fas fa-plus-circle"></i> Thêm ảnh
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Danh sách ảnh hiện tại -->
                                    @if($honor->images->count() > 0)
                                    <div class="existing-images mb-4">
                                        <h6 class="text-muted mb-3">Ảnh đã có</h6>
                                        <div class="row">
                                            @foreach($honor->images as $image)
                                            <div class="col-md-4 mb-4">
                                                <div class="card">
                                                    <div class="card-header p-2">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="custom-control-input" id="delete-{{ $image->id }}">
                                                            <label class="custom-control-label text-danger" for="delete-{{ $image->id }}">Xóa ảnh này</label>
                                                        </div>
                                                    </div>
                                                    <div class="position-relative">
                                                        <img src="{{ getImage(getFilePath('honor_images') . '/' . $image->image, '350x200') }}" class="card-img-top" alt="Vinh danh">
                                                        <div class="position-absolute" style="top: 10px; right: 10px;">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="featured_image" value="{{ $image->id }}" class="custom-control-input" id="featured-{{ $image->id }}" {{ $image->is_featured ? 'checked' : '' }}>
                                                                <label class="custom-control-label bg-white px-2 py-1 rounded text-primary" for="featured-{{ $image->id }}">Nổi bật</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="form-group mb-0">
                                                            <input type="text" name="existing_captions[{{ $image->id }}]" class="form-control form-control-sm" placeholder="Chú thích" value="{{ $image->caption }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Thêm ảnh mới -->
                                    <div class="new-images">
                                        <h6 class="text-muted mb-3">Thêm ảnh mới</h6>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ $honor->start_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" value="{{ $honor->end_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" 
                                       data-toggle="toggle" data-on="Kích hoạt" data-off="Không kích hoạt" 
                                       name="is_active" value="1" {{ $honor->is_active ? 'checked' : '' }}>
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

@push('style')
<style>
    .custom-control-label {
        cursor: pointer;
    }
    
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
</style>
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
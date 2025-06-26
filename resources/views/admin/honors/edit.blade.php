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
                                <label>Ảnh vinh danh</label>
                                <input type="file" name="image" class="form-control" accept=".jpg, .jpeg, .png">
                                <small class="text-muted">
                                    Các định dạng hỗ trợ: jpg, jpeg, png. 
                                    Kích thước khuyên dùng: 800x600px
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-lg-12 text-center">
                            <div class="form-group">
                                <img src="{{ getImage(getFilePath('honors') . '/' . $honor->image, '350x200') }}" 
                                     alt="{{ $honor->title }}" class="img-thumbnail">
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
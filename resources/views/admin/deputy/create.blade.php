@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.deputy.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>@lang('Họ và tên')</label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label>@lang('Tên đăng nhập')</label>
                        <input type="text" class="form-control" name="username" required value="{{ old('username') }}">
                    </div>
                    <div class="form-group">
                        <label>@lang('Email')</label>
                        <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label>@lang('Mật khẩu')</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Xác nhận mật khẩu')</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Tạo tài khoản')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.deputy.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="la la-list"></i> @lang('Danh sách phó tổng giám đốc')
    </a>
@endpush 
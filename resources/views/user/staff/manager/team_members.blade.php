@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Thành viên nhóm')</h5>
                <form action="" method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm...">
                        <button class="btn btn-sm btn-primary" type="submit"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                @if($staffMembers->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>@lang('STT')</th>
                                    <th>@lang('Họ tên')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Ngày tạo')</th>
                                    <th>@lang('Hành động')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffMembers as $key => $staff)
                                    <tr>
                                        <td>{{ $staffMembers->firstItem() + $key }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-sm">
                                                    <img src="{{ asset('assets/images/avatar.png') }}" alt="avatar" class="rounded-circle">
                                                </span>
                                                <span>{{ $staff->fullname }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info">{{ $staff->email }}</span></td>
                                        <td>{{ showDateTime($staff->created_at) }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-title="@lang('Xem hồ sơ')">
                                                    <i class="las la-user"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-title="@lang('Xóa')">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 pb-3">
                        {{ $staffMembers->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted">@lang('Không có thành viên nào trong nhóm.')</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .avatar {
        width: 32px;
        height: 32px;
        overflow: hidden;
    }
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endpush

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush 
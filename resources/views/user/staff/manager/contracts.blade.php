@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Hợp đồng nhóm')</h5>
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm...">
                            <button class="btn btn-sm btn-primary" type="submit"><i class="las la-search"></i></button>
                        </div>
                    </form>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="las la-filter"></i> @lang('Lọc')
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">@lang('Tất cả')</a>
                            <a class="dropdown-item" href="#">@lang('Đang chạy')</a>
                            <a class="dropdown-item" href="#">@lang('Hoàn thành')</a>
                            <a class="dropdown-item" href="#">@lang('Chờ duyệt')</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                @if($contracts->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>@lang('STT')</th>
                                    <th>@lang('Mã hợp đồng')</th>
                                    <th>@lang('Dự án')</th>
                                    <th>@lang('Thành viên')</th>
                                    <th>@lang('Số tiền')</th>
                                    <th>@lang('Trạng thái')</th>
                                    <th>@lang('Ngày tạo')</th>
                                    <th>@lang('Hành động')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contracts as $key => $contract)
                                    <tr>
                                        <td>{{ $contracts->firstItem() + $key }}</td>
                                        <td><span class="badge bg-primary">{{ $contract->trx }}</span></td>
                                        <td>{{ Str::limit($contract->project->name ?? '-', 20) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-sm">
                                                    <img src="{{ asset('assets/images/avatar.png') }}" alt="avatar" class="rounded-circle">
                                                </span>
                                                <span>{{ $contract->user->fullname ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ showAmount($contract->amount) }} {{ $general->cur_text }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($contract->status) {
                                                    'RUNNING' => 'bg-success',
                                                    'PENDING' => 'bg-warning',
                                                    'COMPLETED' => 'bg-info',
                                                    'REJECTED' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ __($contract->status) }}</span>
                                        </td>
                                        <td>{{ showDateTime($contract->created_at) }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-title="@lang('Chi tiết')">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" data-bs-title="@lang('In hợp đồng')">
                                                    <i class="las la-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 pb-3">
                        {{ $contracts->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted">@lang('Không có hợp đồng nào trong nhóm.')</p>
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
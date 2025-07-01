@extends('user.staff.layouts.staff_app')

@section('panel')
@include('user.staff.partials.honor_banner')

<!-- Stats Cards -->
<div class="row gy-4 mb-30">
    <div class="col-xxl-3 col-sm-6">
        <div class="card bg-gradient-primary overflow-hidden box--shadow2 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="avatar-lg">
                        <span class="avatar-title bg-white bg-opacity-10 rounded-circle">
                            <i class="fa fa-file-contract fs-1 text-white"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <span class="text-white fs-4 fw-bold">{{ $stats['total_contracts'] }}</span>
                        <h5 class="text-white mb-0">@lang('Tổng hợp đồng')</h5>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('user.staff.staff.contracts') }}" class="btn btn-sm btn-outline-light">@lang('Xem tất cả')</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xxl-3 col-sm-6">
        <div class="card bg-gradient-success overflow-hidden box--shadow2 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="avatar-lg">
                        <span class="avatar-title bg-white bg-opacity-10 rounded-circle">
                            <i class="fa fa-check-circle fs-1 text-white"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <span class="text-white fs-4 fw-bold">{{ $stats['active_contracts'] }}</span>
                        <h5 class="text-white mb-0">@lang('Hợp đồng hoạt động')</h5>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('user.staff.staff.contracts') }}?status=active" class="btn btn-sm btn-outline-light">@lang('Xem tất cả')</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xxl-3 col-sm-6">
        <div class="card bg-gradient-warning overflow-hidden box--shadow2 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="avatar-lg">
                        <span class="avatar-title bg-white bg-opacity-10 rounded-circle">
                            <i class="fa fa-clock fs-1 text-white"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <span class="text-white fs-4 fw-bold">{{ $stats['pending_contracts'] }}</span>
                        <h5 class="text-white mb-0">@lang('Hợp đồng chờ duyệt')</h5>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('user.staff.staff.contracts') }}?status=pending" class="btn btn-sm btn-outline-light">@lang('Xem tất cả')</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xxl-3 col-sm-6">
        <div class="card bg-gradient-info overflow-hidden box--shadow2 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="avatar-lg">
                        <span class="avatar-title bg-white bg-opacity-10 rounded-circle">
                            <i class="fa fa-user-tag fs-1 text-white"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <span class="text-white fs-4 fw-bold">{{ $stats['customers'] }}</span>
                        <h5 class="text-white mb-0">@lang('Khách hàng của tôi')</h5>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('user.staff.staff.customers') }}" class="btn btn-sm btn-outline-light">@lang('Xem tất cả')</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile & Quick Links -->
<div class="row gy-4 mb-30">
    <div class="col-xl-5">
        <div class="card h-100 box--shadow2">
            <div class="card-header bg-primary d-flex align-items-center py-3">
                <div class="avatar avatar--lg me-3">
                    <img src="{{ getImage(getFilePath('userProfile').'/'. $user->image,getFileSize('userProfile'))}}" alt="@lang('Hình ảnh')" class="border">
                </div>
                <div>
                    <h4 class="text-white mb-0">{{__($user->fullname)}}</h4>
                    <p class="text-white-50 mb-0">{{ $user->email }}</p>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0 border-bottom">
                        <span class="fw-medium"><i class="las la-user-tag text-primary me-2"></i> @lang('Vai trò')</span>
                        <span class="badge badge--success">@lang('Nhân viên kinh doanh')</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 border-bottom">
                        <span class="fw-medium"><i class="las la-user-tie text-primary me-2"></i> @lang('Quản lý')</span>
                        <span class="fw-bold">{{ $user->manager ? $user->manager->fullname : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 border-bottom">
                        <span class="fw-medium"><i class="las la-file-contract text-primary me-2"></i> @lang('Tổng hợp đồng')</span>
                        <span class="badge badge--primary">{{ $stats['total_contracts'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="fw-medium"><i class="las la-phone text-primary me-2"></i> @lang('Điện thoại')</span>
                        <span class="fw-bold">{{ $user->mobile ?: 'Chưa cài đặt' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card h-100 box--shadow2">
            <div class="card-header bg-primary py-3">
                <h5 class="card-title text-white mb-0"><i class="las la-bolt me-2"></i>@lang('Truy cập nhanh')</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('user.staff.staff.contracts') }}" class="card text-center p-3 h-100 border-primary border-hover">
                            <div class="card-body p-0">
                                <div class="avatar avatar--lg mx-auto mb-3 border border-primary bg-light-primary rounded-circle avatar-gradient shadow avatar-animate">
                                    <i class="fa fa-file-contract text-primary fs-1"></i>
                                </div>
                                <h6 class="mb-0">@lang('Hợp đồng của tôi')</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('user.staff.staff.create_contract') }}" class="card text-center p-3 h-100 border-success border-hover">
                            <div class="card-body p-0">
                                <div class="avatar avatar--lg mx-auto mb-3 border border-success bg-light-success rounded-circle">
                                    <i class="fa fa-plus-circle text-success fs-2"></i>
                                </div>
                                <h6 class="mb-0">@lang('Tạo hợp đồng')</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('user.staff.staff.customers') }}" class="card text-center p-3 h-100 border-info border-hover">
                            <div class="card-body p-0">
                                <div class="avatar avatar--lg mx-auto mb-3 border border-info bg-light-info rounded-circle">
                                    <i class="fa fa-user-tag text-info fs-2"></i>
                                </div>
                                <h6 class="mb-0">@lang('Khách hàng')</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('user.staff.staff.alerts') }}" class="card text-center p-3 h-100 border-warning border-hover">
                            <div class="card-body p-0">
                                <div class="avatar avatar--lg mx-auto mb-3 border border-warning bg-light-warning rounded-circle">
                                    <i class="fa fa-bell text-warning fs-2"></i>
                                </div>
                                <h6 class="mb-0">@lang('Thông báo')</h6>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Payments Section -->
<div class="row gy-4 mb-30">
    <div class="col-xl-12">
        <div class="card box--shadow2">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title text-white mb-0">
                    <i class="las la-calendar-day me-2"></i>@lang('Thanh toán lãi sắp tới')
                </h5>
                <a href="{{ route('user.staff.staff.alerts') }}" class="btn btn-sm btn-outline-light">
                    <i class="las la-eye"></i> @lang('Xem tất cả')
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md">
                    <table class="table table--light style--two mb-0">
                        <thead>
                            <tr>
                                <th>@lang('Hợp đồng')</th>
                                <th>@lang('Dự án')</th>
                                <th>@lang('Ngày thanh toán')</th>
                                <th>@lang('Số tiền')</th>
                                <th>@lang('Số ngày còn lại')</th>
                                <th>@lang('Hành động')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interestAlerts as $alert)
                                @php
                                    $daysLeft = (int)\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($alert->next_time), false);
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('user.staff.staff.contract.details', $alert->id) }}" class="fw-medium text-primary">
                                            {{ $alert->invest_no }}
                                        </a>
                                    </td>
                                    <td>{{ $alert->project ? Str::limit($alert->project->title, 20) : 'N/A' }}</td>
                                    <td>{{ showDateTime($alert->next_time) }}</td>
                                    <td>{{ showAmount($alert->monthly_roi_amount) }}</td>
                                    <td>
                                        @if($daysLeft < 0)
                                            <span class="badge badge--danger">@lang('Quá hạn')</span>
                                        @elseif($daysLeft <= 7)
                                            <span class="badge badge--warning">{{ $daysLeft }} @lang('ngày')</span>
                                        @else
                                            <span class="badge badge--success">{{ $daysLeft }} @lang('ngày')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.staff.staff.contract.details', $alert->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="las la-eye"></i> @lang('Xem')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Contracts Section -->
<div class="row gy-4">
    <div class="col-xl-12">
        <div class="card box--shadow2">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title text-white mb-0">
                    <i class="las la-file-contract me-2"></i>@lang('Hợp đồng của tôi')
                </h5>
                <a href="{{ route('user.staff.staff.contracts') }}" class="btn btn-sm btn-outline-light">
                    <i class="las la-eye"></i> @lang('Xem tất cả')
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md">
                    <table class="table table--light style--two mb-0">
                        <thead>
                            <tr>
                                <th>@lang('Hợp đồng')</th>
                                <th>@lang('Dự án')</th>
                                <th>@lang('Khách hàng')</th>
                                <th>@lang('Số tiền')</th>
                                <th>@lang('Ngày bắt đầu')</th>
                                <th>@lang('Trạng thái')</th>
                                <th>@lang('Hành động')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentContracts as $contract)
                                <tr>
                                    <td>
                                        <a href="{{ route('user.staff.staff.contract.details', $contract->id) }}" class="fw-medium text-primary">
                                            {{ $contract->invest_no }}
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($contract->project->title ?? 'N/A', 20) }}</td>
                                    <td>{{ $contract->user->fullname ?? 'N/A' }}</td>
                                    <td>{{ showAmount($contract->total_price) }}</td>
                                    <td>{{ showDateTime($contract->created_at) }}</td>
                                    <td>{!! $contract->statusBadge !!}</td>
                                    <td>
                                        <a href="{{ route('user.staff.staff.contract.details', $contract->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="las la-eye"></i> @lang('Xem')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.staff.staff.partials.honor_modal')
@endsection

@push('style')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #6e00ff 0%, #5661f1 100%) !important;
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%) !important;
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%) !important;
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #00cdac 100%) !important;
    }
    .avatar-lg {
        height: 5rem;
        width: 5rem;
    }
    .bg-light-primary {
        background-color: rgba(110, 0, 255, 0.1) !important;
    }
    .bg-light-success {
        background-color: rgba(11, 163, 96, 0.1) !important;
    }
    .bg-light-warning {
        background-color: rgba(246, 211, 101, 0.1) !important;
    }
    .bg-light-info {
        background-color: rgba(23, 162, 184, 0.1) !important;
    }
    .border-hover {
        transition: all 0.3s ease;
    }
    .border-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .table--light.style--two tbody tr {
        border-left: none;
        border-right: none;
    }
    .box--shadow2 {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05) !important;
    }
    .avatar-gradient {
        background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%) !important;
        box-shadow: 0 4px 24px 0 rgba(110,0,255,0.15), 0 1.5px 4px 0 rgba(86,97,241,0.10);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-gradient i {
        font-size: 2.5rem !important;
        color: #6e00ff !important;
        text-shadow: 0 2px 8px rgba(110,0,255,0.10);
    }
    .avatar-animate:hover {
        transform: scale(1.08) rotate(-2deg);
        box-shadow: 0 8px 32px 0 rgba(110,0,255,0.25), 0 3px 8px 0 rgba(86,97,241,0.15);
    }
</style>
@endpush 
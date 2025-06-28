@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Báo cáo')</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">@lang('Báo cáo giao dịch')</h5>
                                    <div class="icon-box bg-primary-subtle">
                                        <i class="las la-exchange-alt fs-1 text-primary"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-4">@lang('Xem chi tiết các giao dịch của nhóm, bao gồm đầu tư, rút tiền và các giao dịch khác.')</p>
                                <a href="{{ route('user.staff.manager.report.transactions') }}" class="btn btn-primary mt-auto">@lang('Xem báo cáo')</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">@lang('Báo cáo lãi suất')</h5>
                                    <div class="icon-box bg-success-subtle">
                                        <i class="las la-percentage fs-1 text-success"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-4">@lang('Xem chi tiết về lãi suất từ các khoản đầu tư, bao gồm lãi đã trả và lãi sắp đến hạn.')</p>
                                <a href="{{ route('user.staff.manager.report.interests') }}" class="btn btn-success mt-auto">@lang('Xem báo cáo')</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">@lang('Báo cáo hoa hồng')</h5>
                                    <div class="icon-box bg-warning-subtle">
                                        <i class="las la-hand-holding-usd fs-1 text-warning"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-4">@lang('Xem chi tiết về hoa hồng từ các hợp đồng đầu tư, bao gồm hoa hồng đã nhận và sắp nhận.')</p>
                                <a href="{{ route('user.staff.manager.report.commissions') }}" class="btn btn-warning mt-auto">@lang('Xem báo cáo')</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5">
                    <h5 class="mb-3">@lang('Thống kê theo nhân viên')</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('Nhân viên')</th>
                                    <th>@lang('Tổng hợp đồng')</th>
                                    <th>@lang('Hợp đồng đang chạy')</th>
                                    <th>@lang('Tỉ lệ hoạt động')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffStats as $staff)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar avatar-sm">
                                                <img src="{{ getImage(getFilePath('userProfile').'/'. $staff->image, getFileSize('userProfile')) }}" alt="avatar" class="rounded-circle">
                                            </span>
                                            <span>{{ $staff->fullname }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $staff->invests_count }}</td>
                                    <td>{{ $staff->active_invests_count }}</td>
                                    <td>
                                        @php
                                            $activeRate = $staff->invests_count > 0 ? ($staff->active_invests_count / $staff->invests_count) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $activeRate }}%"></div>
                                        </div>
                                        <span class="small mt-1 d-block">{{ number_format($activeRate, 1) }}%</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">@lang('Không có dữ liệu')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
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
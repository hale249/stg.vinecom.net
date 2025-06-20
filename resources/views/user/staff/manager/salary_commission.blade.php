@extends('user.staff.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <h5 class="mb-0">@lang('Bảng lương - Hoa hồng - Doanh số')</h5>
                <form action="" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month', '2025-06') }}">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">@lang('Tất cả nhân viên')</option>
                        <option value="1">Nguyễn A</option>
                        <option value="2">Trần B</option>
                    </select>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">@lang('Tất cả dự án')</option>
                        <option value="1">Dự án Alpha</option>
                        <option value="2">Dự án Beta</option>
                    </select>
                    <button class="btn btn-sm btn-primary" type="submit"><i class="las la-filter"></i> @lang('Lọc')</button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>@lang('Nhân viên')</th>
                                <th>@lang('Tháng')</th>
                                <th>@lang('Lương cứng')</th>
                                <th>@lang('Doanh số')</th>
                                <th>@lang('Hoa hồng')</th>
                                <th>@lang('Tổng lương')</th>
                                <th>@lang('KPI (%)')</th>
                                <th>@lang('Trạng thái')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nguyễn A</td>
                                <td>06/2025</td>
                                <td>6.000.000</td>
                                <td>2.000.000.000</td>
                                <td>8.000.000</td>
                                <td>14.000.000</td>
                                <td><span class="badge bg-success">110%</span></td>
                                <td><span class="badge bg-success"><i class="las la-check-circle"></i> Vượt KPI</span></td>
                            </tr>
                            <tr>
                                <td>Trần B</td>
                                <td>06/2025</td>
                                <td>5.000.000</td>
                                <td>800.000.000</td>
                                <td>3.200.000</td>
                                <td>8.200.000</td>
                                <td><span class="badge bg-warning">80%</span></td>
                                <td><span class="badge bg-warning"><i class="las la-exclamation-triangle"></i> Gần đạt</span></td>
                            </tr>
                            <tr>
                                <td>Lê C</td>
                                <td>06/2025</td>
                                <td>5.000.000</td>
                                <td>400.000.000</td>
                                <td>1.600.000</td>
                                <td>6.600.000</td>
                                <td><span class="badge bg-danger">50%</span></td>
                                <td><span class="badge bg-danger"><i class="las la-times-circle"></i> Không đạt</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="empty-state text-center py-5 d-none">
                    <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                    <p class="text-muted">@lang('Không có dữ liệu bảng lương cho bộ lọc này.')</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .badge.bg-success { background: #22c55e!important; }
    .badge.bg-warning { background: #f59e42!important; color: #fff; }
    .badge.bg-danger { background: #ef4444!important; }
</style>
@endpush 
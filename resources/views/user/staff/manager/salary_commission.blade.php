@extends('user.staff.layouts.app')

@section('panel')
<div class="row mb-4">
    <!-- Summary Cards -->
    <div class="col-lg-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6 class="mb-1">@lang('Tổng lương cứng')</h6>
                        <h4 class="mb-0">{{ showAmount($summary['total_base_salary'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6 class="mb-1">@lang('Tổng hoa hồng')</h6>
                        <h4 class="mb-0">{{ showAmount($summary['total_commission'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6 class="mb-1">@lang('Tổng thưởng')</h6>
                        <h4 class="mb-0">{{ showAmount($summary['total_bonus'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h6 class="mb-1">@lang('Tổng lương')</h6>
                        <h4 class="mb-0">{{ showAmount($summary['total_salary'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <h5 class="mb-0">@lang('Bảng lương - Hoa hồng - Doanh số')</h5>
                <form action="" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ $month }}">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">@lang('Tất cả nhân viên')</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ $staffId == $staff->id ? 'selected' : '' }}>
                                {{ $staff->fullname }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-primary" type="submit"><i class="las la-filter"></i> @lang('Lọc')</button>
                </form>
            </div>
            <div class="card-body p-0">
                @if($salaries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>@lang('Nhân viên')</th>
                                    <th>@lang('Tháng')</th>
                                    <th>@lang('Lương cứng')</th>
                                    <th>@lang('Doanh số')</th>
                                    <th>@lang('Hoa hồng')</th>
                                    <th>@lang('Thưởng')</th>
                                    <th>@lang('Khấu trừ')</th>
                                    <th>@lang('Tổng lương')</th>
                                    <th>@lang('KPI (%)')</th>
                                    <th>@lang('Trạng thái')</th>
                                    <th>@lang('Thao tác')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salaries as $salary)
                                    <tr>
                                        <td>{{ $salary->staff->fullname ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $salary->month_year)->format('m/Y') }}</td>
                                        <td>{{ showAmount($salary->base_salary) }}</td>
                                        <td>{{ showAmount($salary->sales_amount) }}</td>
                                        <td>{{ showAmount($salary->commission_amount) }}</td>
                                        <td>{{ showAmount($salary->bonus_amount) }}</td>
                                        <td>{{ showAmount($salary->deduction_amount) }}</td>
                                        <td><strong>{{ showAmount($salary->total_salary) }}</strong></td>
                                        <td>
                                            @if($salary->kpi_percentage >= 120)
                                                <span class="badge bg-success">{{ number_format($salary->kpi_percentage, 1) }}%</span>
                                            @elseif($salary->kpi_percentage >= 100)
                                                <span class="badge bg-primary">{{ number_format($salary->kpi_percentage, 1) }}%</span>
                                            @elseif($salary->kpi_percentage >= 80)
                                                <span class="badge bg-warning">{{ number_format($salary->kpi_percentage, 1) }}%</span>
                                            @else
                                                <span class="badge bg-danger">{{ number_format($salary->kpi_percentage, 1) }}%</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($salary->kpi_status == 'exceeded')
                                                <span class="badge bg-success"><i class="las la-check-circle"></i> Vượt KPI</span>
                                            @elseif($salary->kpi_status == 'achieved')
                                                <span class="badge bg-primary"><i class="las la-check-circle"></i> Đạt KPI</span>
                                            @elseif($salary->kpi_status == 'near_achieved')
                                                <span class="badge bg-warning"><i class="las la-exclamation-triangle"></i> Gần đạt</span>
                                            @else
                                                <span class="badge bg-danger"><i class="las la-times-circle"></i> Không đạt</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#salaryModal{{ $salary->id }}">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="approveSalary({{ $salary->id }})">
                                                    <i class="las la-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 pb-3">
                        {{ $salaries->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted">@lang('Không có dữ liệu bảng lương cho bộ lọc này.')</p>
                        <button class="btn btn-primary" onclick="createSalary()">
                            <i class="las la-plus"></i> @lang('Tạo bảng lương')
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Salary Detail Modals -->
@foreach($salaries as $salary)
<div class="modal fade" id="salaryModal{{ $salary->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Chi tiết lương') - {{ $salary->staff->fullname ?? 'N/A' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>@lang('Thông tin cơ bản')</h6>
                        <table class="table table-sm">
                            <tr><td>@lang('Nhân viên'):</td><td>{{ $salary->staff->fullname ?? 'N/A' }}</td></tr>
                            <tr><td>@lang('Tháng'):</td><td>{{ \Carbon\Carbon::createFromFormat('Y-m', $salary->month_year)->format('m/Y') }}</td></tr>
                            <tr><td>@lang('Trạng thái'):</td><td>{{ $salary->status_text }}</td></tr>
                            <tr><td>@lang('Ngày tạo'):</td><td>{{ showDateTime($salary->created_at) }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>@lang('Chi tiết lương')</h6>
                        <table class="table table-sm">
                            <tr><td>@lang('Lương cứng'):</td><td>{{ showAmount($salary->base_salary) }}</td></tr>
                            <tr><td>@lang('Doanh số'):</td><td>{{ showAmount($salary->sales_amount) }}</td></tr>
                            <tr><td>@lang('Tỷ lệ hoa hồng'):</td><td>{{ $salary->commission_rate }}%</td></tr>
                            <tr><td>@lang('Hoa hồng'):</td><td>{{ showAmount($salary->commission_amount) }}</td></tr>
                            <tr><td>@lang('Thưởng'):</td><td>{{ showAmount($salary->bonus_amount) }}</td></tr>
                            <tr><td>@lang('Khấu trừ'):</td><td>{{ showAmount($salary->deduction_amount) }}</td></tr>
                            <tr class="table-primary"><td><strong>@lang('Tổng lương'):</strong></td><td><strong>{{ showAmount($salary->total_salary) }}</strong></td></tr>
                        </table>
                    </div>
                </div>
                @if($salary->notes)
                    <div class="mt-3">
                        <h6>@lang('Ghi chú')</h6>
                        <p class="text-muted">{{ $salary->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('script')
<script>
function approveSalary(salaryId) {
    if (confirm('@lang("Bạn có chắc chắn muốn duyệt bảng lương này?")')) {
        // Add AJAX call to approve salary
        console.log('Approving salary:', salaryId);
    }
}

function createSalary() {
    // Add logic to create new salary record
    console.log('Creating new salary record');
}
</script>
@endpush

@push('style')
<style>
    .badge.bg-success { background: #22c55e!important; }
    .badge.bg-primary { background: #3b82f6!important; }
    .badge.bg-warning { background: #f59e42!important; color: #fff; }
    .badge.bg-danger { background: #ef4444!important; }
</style>
@endpush 
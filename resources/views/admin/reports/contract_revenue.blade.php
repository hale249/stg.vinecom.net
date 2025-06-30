@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Mã hợp đồng')</th>
                                    <th>@lang('Dự án')</th>
                                    <th>@lang('Khách hàng')</th>
                                    <th>@lang('Số lượng')</th>
                                    <th>@lang('Đơn giá')</th>
                                    <th>@lang('Tổng giá trị')</th>
                                    <th>@lang('Lợi nhuận')</th>
                                    <th>@lang('Ngày tạo')</th>
                                    <th>@lang('Trạng thái')</th>
                                    <th>@lang('Hành động')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contracts as $contract)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $contract->invest_no }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ @$contract->project->title }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                <a href="{{ route('admin.users.detail', $contract->user_id) }}">
                                                    {{ @$contract->user->fullname }}
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $contract->quantity }}</span>
                                        </td>
                                        <td>
                                            <span>{{ showAmount($contract->unit_price) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($contract->total_price) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">{{ showAmount($contract->total_earning) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ showDateTime($contract->created_at) }}</span>
                                            <br>
                                            <span>{{ diffForHumans($contract->created_at) }}</span>
                                        </td>
                                        <td>
                                            @php echo $contract->statusBadge @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.invest.details', $contract->id) }}"
                                                class="btn btn-sm btn-outline--primary">
                                                <i class="la la-desktop"></i> @lang('Chi tiết')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'Không có dữ liệu') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($contracts->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($contracts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row mt-30">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title">@lang('Tổng kết doanh số')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                                <div class="widget-two__content">
                                    <h2 class="text-dark">{{ $totalContractCount }}</h2>
                                    <p>@lang('Tổng số hợp đồng')</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                                <div class="widget-two__content">
                                    <h2 class="text-dark">{{ showAmount($totalContractAmount) }}</h2>
                                    <p>@lang('Tổng giá trị hợp đồng')</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="widget-two box--shadow2 b-radius--5 bg--white">
                                <div class="widget-two__content">
                                    <h2 class="text-dark">{{ showAmount($totalEarnings) }}</h2>
                                    <p>@lang('Tổng lợi nhuận')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.report.contract.revenue', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn--success">
            <i class="la la-file-excel-o"></i> Xuất Excel
        </a>
        
        <form action="" method="GET" class="d-flex flex-wrap gap-2">
            <div class="input-group w-auto">
                <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Tìm kiếm...')"
                    value="{{ request()->search }}">
                <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
            </div>

            <div class="input-group w-auto">
                <select name="status" class="form-control">
                    <option value="">@lang('Tất cả trạng thái')</option>
                    <option value="{{ Status::INVEST_PENDING }}" @selected(request()->status == Status::INVEST_PENDING)>@lang('Chờ xử lý')</option>
                    <option value="{{ Status::INVEST_PENDING_ADMIN_REVIEW }}" @selected(request()->status == Status::INVEST_PENDING_ADMIN_REVIEW)>@lang('Chờ duyệt')</option>
                    <option value="{{ Status::INVEST_RUNNING }}" @selected(request()->status == Status::INVEST_RUNNING)>@lang('Đang hoạt động')</option>
                    <option value="{{ Status::INVEST_COMPLETED }}" @selected(request()->status == Status::INVEST_COMPLETED)>@lang('Hoàn thành')</option>
                    <option value="{{ Status::INVEST_CLOSED }}" @selected(request()->status == Status::INVEST_CLOSED)>@lang('Đã đóng')</option>
                    <option value="{{ Status::INVEST_CANCELED }}" @selected(request()->status == Status::INVEST_CANCELED)>@lang('Đã hủy')</option>
                </select>
                <button class="btn btn--primary input-group-text">@lang('Lọc')</button>
            </div>

            <div class="input-group w-auto">
                <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="vi"
                    class="datepicker-here form-control bg--white" data-position='bottom right'
                    placeholder="@lang('Chọn thời gian')" autocomplete="off" value="{{ request()->date }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-calendar"></i></button>
            </div>
        </form>
    </div>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.vi.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            
            // Initialize datepicker with proper options
            $('.datepicker-here').datepicker({
                range: true,
                multipleDatesSeparator: ' - ',
                language: 'vi',
                position: 'bottom right',
                autoClose: true,
                maxDate: new Date(),
                toggleSelected: false,
                
                // Add predefined date ranges
                onRenderCell: function(date, cellType) {
                    if (cellType == 'day') {
                        return {
                            html: date.getDate(),
                            classes: 'day-cell'
                        }
                    }
                },
                
                onSelect: function(formattedDate, date, inst) {
                    if (formattedDate.includes('-')) {
                        // Only submit when a complete range is selected
                        // Uncomment below if you want auto-submit
                        // $('form').submit();
                    }
                }
            });
            
            // Add custom buttons for predefined date ranges
            const dateRangeContainer = $('<div class="date-range-buttons mt-2 d-flex flex-wrap gap-1"></div>');
            
            // Today
            $('<button type="button" class="btn btn-sm btn--dark date-range-btn">Hôm nay</button>')
                .on('click', function() {
                    const today = new Date();
                    $('.datepicker-here').data('datepicker').selectDate([today, today]);
                    setTimeout(() => {
                        $(this).closest('form').submit();
                    }, 100);
                    return false;
                })
                .appendTo(dateRangeContainer);
            
            // Yesterday
            $('<button type="button" class="btn btn-sm btn--dark date-range-btn">Hôm qua</button>')
                .on('click', function() {
                    const yesterday = new Date();
                    yesterday.setDate(yesterday.getDate() - 1);
                    $('.datepicker-here').data('datepicker').selectDate([yesterday, yesterday]);
                    setTimeout(() => {
                        $(this).closest('form').submit();
                    }, 100);
                    return false;
                })
                .appendTo(dateRangeContainer);
            
            // Last 7 days
            $('<button type="button" class="btn btn-sm btn--dark date-range-btn">7 ngày qua</button>')
                .on('click', function() {
                    const today = new Date();
                    const last7Days = new Date();
                    last7Days.setDate(last7Days.getDate() - 6);
                    $('.datepicker-here').data('datepicker').selectDate([last7Days, today]);
                    setTimeout(() => {
                        $(this).closest('form').submit();
                    }, 100);
                    return false;
                })
                .appendTo(dateRangeContainer);
            
            // This month
            $('<button type="button" class="btn btn-sm btn--dark date-range-btn">Tháng này</button>')
                .on('click', function() {
                    const today = new Date();
                    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    $('.datepicker-here').data('datepicker').selectDate([firstDayOfMonth, today]);
                    setTimeout(() => {
                        $(this).closest('form').submit();
                    }, 100);
                    return false;
                })
                .appendTo(dateRangeContainer);
            
            // Last month
            $('<button type="button" class="btn btn-sm btn--dark date-range-btn">Tháng trước</button>')
                .on('click', function() {
                    const today = new Date();
                    const firstDayLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastDayLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                    $('.datepicker-here').data('datepicker').selectDate([firstDayLastMonth, lastDayLastMonth]);
                    setTimeout(() => {
                        $(this).closest('form').submit();
                    }, 100);
                    return false;
                })
                .appendTo(dateRangeContainer);
            
            // This year
            $('<button type="button" class="btn btn-sm btn--dark date-range-btn">Năm nay</button>')
                .on('click', function() {
                    const today = new Date();
                    const firstDayOfYear = new Date(today.getFullYear(), 0, 1);
                    $('.datepicker-here').data('datepicker').selectDate([firstDayOfYear, today]);
                    setTimeout(() => {
                        $(this).closest('form').submit();
                    }, 100);
                    return false;
                })
                .appendTo(dateRangeContainer);
                
            // Insert the date range buttons after the datepicker input group
            $('.datepicker-here').closest('.input-group').after(dateRangeContainer);
            
        })(jQuery)
    </script>
@endpush

@push('style')
<style>
    .date-range-buttons {
        margin-left: 40px;
    }
    .date-range-btn {
        font-size: 12px;
        padding: 3px 8px;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .date-range-btn:hover {
        background-color: var(--primary);
        color: white;
    }
</style>
@endpush 
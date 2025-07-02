@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i>
                    @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Tìm kiếm')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control" placeholder="Mã HĐ, dự án, khách hàng...">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Trạng thái')</label>
                                <select name="status" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="" @selected(request()->status === null || request()->status === '')>@lang('Tất cả')</option>
                                    <option value="{{ Status::INVEST_PENDING_ADMIN_REVIEW }}" @selected(request()->status == Status::INVEST_PENDING_ADMIN_REVIEW)>@lang('Chờ duyệt')</option>
                                    <option value="{{ Status::INVEST_RUNNING }}" @selected(request()->status == Status::INVEST_RUNNING)>@lang('Đang hoạt động')</option>
                                    <option value="{{ Status::INVEST_COMPLETED }}" @selected(request()->status == Status::INVEST_COMPLETED)>@lang('Hoàn thành')</option>
                                    <option value="{{ Status::INVEST_CLOSED }}" @selected(request()->status == Status::INVEST_CLOSED)>@lang('Đã đóng')</option>
                                    <option value="{{ Status::INVEST_CANCELED }}" @selected(request()->status == Status::INVEST_CANCELED)>@lang('Đã hủy')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Ngày')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Ngày bắt đầu - Ngày kết thúc')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Lọc')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
    <a href="{{ route('admin.report.contract.revenue', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn--success">
        <i class="la la-file-excel-o"></i> Xuất Excel
    </a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                applyButtonClasses: 'btn btn--primary',
                locale: {
                    cancelLabel: 'Xóa',
                    applyLabel: 'Áp dụng',
                    fromLabel: 'Từ',
                    toLabel: 'Đến',
                    customRangeLabel: 'Tùy chỉnh',
                    daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                    monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                    firstDay: 1
                },
                showDropdowns: true,
                ranges: {
                    'Hôm nay': [moment(), moment()],
                    'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 ngày qua': [moment().subtract(6, 'days'), moment()],
                    '15 ngày qua': [moment().subtract(14, 'days'), moment()],
                    '30 ngày qua': [moment().subtract(30, 'days'), moment()],
                    'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                    'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                    '6 tháng qua': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'Năm nay': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MM/DD/YYYY') + ' - ' + endDate.format('MM/DD/YYYY'));
            }

            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                .startDate, picker.endDate));

            $('.date-range').on('cancel.daterangepicker', function(event, picker) {
                $(this).val('');
            });

            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                let format = 'MM/DD/YYYY';
                
                // Try to detect the format - we need to handle different possible formats
                if (dateRange[0].match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                    // MM/DD/YYYY format
                    format = 'MM/DD/YYYY';
                } else if (dateRange[0].match(/^\d{2}-\d{2}-\d{4}$/)) {
                    // MM-DD-YYYY format
                    format = 'MM-DD-YYYY';
                } else if (dateRange[0].match(/^\d{4}-\d{2}-\d{2}$/)) {
                    // YYYY-MM-DD format
                    format = 'YYYY-MM-DD';
                } else if (dateRange[0].match(/^\d{2}\/\d{2}\/\d{2}$/)) {
                    // DD/MM/YY format
                    format = 'DD/MM/YY';
                }
                
                $('.date-range').data('daterangepicker').setStartDate(moment(dateRange[0], format));
                $('.date-range').data('daterangepicker').setEndDate(moment(dateRange[1], format));
            }

        })(jQuery)
    </script>
@endpush 
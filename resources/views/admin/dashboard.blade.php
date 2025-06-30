@extends('admin.layouts.app')

@section('panel')

    <div class="row gy-4 justify-content-center mb-4">
        <div class="col-12 col-md-6 col-xl-4">
            <x-widget
                style="6"
                link="{{route('admin.users.all')}}"
                icon="las la-users"
                title="Tổng số người dùng"
                value="{{$widget['total_users']}}"
                bg="primary"
            />
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <x-widget
                style="6"
                link="{{route('admin.users.active')}}"
                icon="las la-user-check"
                title="Người dùng đang hoạt động"
                value="{{$widget['verified_users']}}"
                bg="success"
            />
        </div>
    </div>

    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget
                style="6"
                link="{{ route('admin.report.invest.history') }}"
                title="Tổng GTHĐ đầu tư"
                icon="las la-chart-bar"
                value="{{ showAmount($invest['total_invests']) }}"
                bg="primary"
            />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget
                style="6"
                link="{{ route('admin.report.transaction') }}?remark=profit"
                title="Tổng lợi tức"
                icon="las la-chart-pie"
                value="{{ showAmount($invest['total_interests']) }}"
                bg="1"
            />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget
                style="6"
                link="{{ route('admin.report.invest.history') }}?status={{ Status::INVEST_RUNNING }}"
                title="Hợp đồng đang hoạt động"
                icon="las la-chart-area"
                value="{{ showAmount($invest['running_invests']) }}"
                bg="12"
            />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget
                style="6"
                link="{{ route('admin.report.invest.history') }}?status={{ Status::INVEST_COMPLETED }}"
                title="Tổng GTHĐ tất toán"
                icon="las la-chart-line"
                value="{{ showAmount($invest['completed_invests']) }}"
                bg="9"
            />
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->

    <!-- Alert Summary Section -->
    <div class="row gy-4 mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">Tổng quan cảnh báo hợp đồng</h5>
                        <a href="{{ route('admin.alert.dashboard') }}" class="btn btn-sm btn-outline--primary">
                            <i class="las la-eye"></i> Xem tất cả
                        </a>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert {{ $alertSummary['interest_alerts'] > 0 ? 'alert-warning' : 'alert-info' }} d-flex align-items-center">
                                <div class="alert-icon me-3">
                                    <i class="las {{ $alertSummary['interest_alerts'] > 0 ? 'la-exclamation-triangle' : 'la-info-circle' }} fs-1"></i>
                                </div>
                                <div>
                                    <h5 class="mt-1">Thanh toán lãi ({{ $alertSummary['alert_period'] }} ngày)</h5>
                                    <p class="mb-0">
                                        {{ $alertSummary['interest_alerts'] }} hợp đồng đến hạn thanh toán lãi
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-danger d-flex align-items-center">
                                <div class="alert-icon me-3">
                                    <i class="las la-exclamation-triangle fs-1"></i>
                                </div>
                                <div>
                                    <h5 class="mt-1">Đáo hạn hợp đồng ({{ $alertSummary['alert_period'] }} ngày)</h5>
                                    <p class="mb-0">
                                        {{ $alertSummary['maturity_alerts'] }} hợp đồng sắp đến ngày đáo hạn
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Alert Summary Section -->
    <!-- Add Recent Investments Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-list text-primary me-2"></i>Hợp đồng gần đây
                    </h5>
                    <a href="{{ route('admin.report.invest.history') }}" class="btn btn-sm btn-outline--primary">
                        <i class="las la-list-alt"></i> Xem tất cả
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>Mã HĐ</th>
                                    <th>Người dùng</th>
                                    <th>Dự án</th>
                                    <th>Số lượng</th>
                                    <th>Số tiền</th>
                                    <th>Lợi nhuận</th>
                                    <th>Hình thức trả</th>
                                    <th>Cần trả</th>
                                    <th>Đã trả</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInvests as $invest)
                                    <tr>
                                        <td>{{ $invest->invest_no }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $invest->user->fullname }}</span>
                                            <br>
                                            <span class="small"><a href="{{ route('admin.users.detail', $invest->user_id) }}"><span>@</span>{{ $invest->user->username }}</a></span>
                                        </td>
                                        <td>{{ __($invest->project->title) }}</td>
                                        <td>{{ __($invest->quantity) }} @lang('Units')</td>
                                        <td>{{ showAmount($invest->total_price) }}</td>
                                        <td>{{ showAmount($invest->total_earning) }}</td>
                                        <td> @php echo $invest->project->typeBadge @endphp </td>
                                        <td>{{ $invest->project->return_type != Status::LIFETIME ? showAmount($invest->recurring_pay) : '**' }}</td>
                                        <td>{{ showAmount($invest->paid) }}</td>
                                        <td>@php echo $invest->statusBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-outline--primary btn-sm" href="{{ route('admin.invest.details', $invest->id) }}">
                                                    <i class="las la-desktop"></i> Chi tiết
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <div class="card animate-fade-in delay-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-chart-bar text-primary me-2"></i>Biểu đồ số lượng hợp đồng theo tháng
                    </h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline--primary refresh-chart" data-chart="investCountChart">
                            <i class="las la-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-info text-center mb-3">
                        <p class="text-muted mb-0">Thống kê số lượng hợp đồng được ký kết theo từng tháng</p>
                    </div>
                    <div id="investCountChart" class="chart-container"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card animate-fade-in delay-200">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-money-bill-wave text-success me-2"></i>Tổng số tiền đầu tư theo tháng
                    </h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline--primary refresh-chart" data-chart="investAmountChart">
                            <i class="las la-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="chart-info text-center mb-3">
                        <p class="text-muted mb-0">Thống kê tổng giá trị đầu tư theo từng tháng</p>
                    </div>
                    <div id="investAmountChart" class="chart-container"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-none-30 mt-5">
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title">Đăng nhập theo trình duyệt (30 ngày gần nhất)</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Đăng nhập theo hệ điều hành (30 ngày gần nhất)</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Đăng nhập theo quốc gia (30 ngày gần nhất)</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
    </div>



    @include('admin.partials.cron_modal')
@endsection
@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm" data-bs-toggle="modal" data-bs-target="#cronModal">
        <i class="las la-server"></i>Thiết lập Cron
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
    <script src="{{ asset('assets/admin/js/dashboard-charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/dashboard-animations.css') }}">
@endpush

@push('script')
    <script>
        "use strict";

        const start = moment().subtract(14, 'days');
        const end = moment();

        const dateRangeOptions = {
            startDate: start,
            endDate: end,
            ranges: {
                'Hôm nay': [moment(), moment()],
                'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 ngày qua': [moment().subtract(6, 'days'), moment()],
                '15 ngày qua': [moment().subtract(14, 'days'), moment()],
                '30 ngày qua': [moment().subtract(30, 'days'), moment()],
                'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                '6 tháng qua': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'Năm nay': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment()
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
        }



        // Chart functions removed as they reference non-existent elements


        // Date picker initialization removed as they reference non-existent elements

        const userBrowserChartEl = document.getElementById('userBrowserChart');
        if (userBrowserChartEl) {
            piChart(
                userBrowserChartEl,
                @json(@$chart['user_browser_counter']->keys()),
                @json(@$chart['user_browser_counter']->flatten())
            );
        }

        const userOsChartEl = document.getElementById('userOsChart');
        if (userOsChartEl) {
            piChart(
                userOsChartEl,
                @json(@$chart['user_os_counter']->keys()),
                @json(@$chart['user_os_counter']->flatten())
            );
        }
        const userCountryChartEl = document.getElementById('userCountryChart');
        if (userCountryChartEl) {
            piChart(
                userCountryChartEl,
                @json(@$chart['user_country_counter']->keys()),
                @json(@$chart['user_country_counter']->flatten())
            );
        }


        // Store chart instances for later reference
        let investCountChartInstance = null;
        let investAmountChartInstance = null;
        
        // Function to load the contract count chart
        function loadInvestCountChart() {
            // Show loading state
            $('#investCountChart').html('<div class="text-center py-5"><i class="las la-spinner fa-spin fa-3x"></i><p class="mt-2">Đang tải dữ liệu...</p></div>');
            
            $.get("{{ route('admin.invest.report.statistics') }}", function(response) {
                const investCountChartEl = document.getElementById('investCountChart');
                // Clear the loading indicator
                $('#investCountChart').html('');
                
                if (investCountChartEl && response && response.months && response.invest_counts) {
                    // Format data to ensure integers for contract counts
                    const formattedCounts = response.invest_counts.map(count => Math.round(count));
                    
                    investCountChartInstance = barChart(
                        investCountChartEl,
                        '{{ __($general->cur_text ?? "") }}',
                        [{ name: 'Số lượng hợp đồng', data: formattedCounts }],
                        response.months
                    );
                }
            });
        }
        
        // Function to load the investment amount chart
        function loadInvestAmountChart() {
            // Show loading state
            $('#investAmountChart').html('<div class="text-center py-5"><i class="las la-spinner fa-spin fa-3x"></i><p class="mt-2">Đang tải dữ liệu...</p></div>');
            
            $.get("{{ route('admin.invest.report.statistics') }}", function(response) {
                const investAmountChartEl = document.getElementById('investAmountChart');
                // Clear the loading indicator
                $('#investAmountChart').html('');
                
                if (investAmountChartEl && response && response.months && response.invest_amounts) {
                    investAmountChartInstance = barAmountChart(
                        investAmountChartEl,
                        '{{ __($general->cur_text ?? "") }}',
                        [{ name: 'Tổng số tiền đầu tư', data: response.invest_amounts }],
                        response.months
                    );
                }
            });
        }
        
        // Load charts initially
        loadInvestCountChart();
        loadInvestAmountChart();
        
        // Handle refresh button clicks
        $('.refresh-chart').on('click', function() {
            const chartId = $(this).data('chart');
            
            // Add spinning animation to the refresh button
            $(this).find('i').addClass('fa-spin');
            
            // Remove spinning animation after 1 second
            setTimeout(() => {
                $(this).find('i').removeClass('fa-spin');
            }, 1000);
            
            if (chartId === 'investCountChart') {
                loadInvestCountChart();
            } else if (chartId === 'investAmountChart') {
                loadInvestAmountChart();
            }
        });
    </script>
@endpush
@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }
        
        #investCountChart .apexcharts-yaxis-label tspan,
        #investCountChart .apexcharts-tooltip-text-y-value {
            font-weight: 600;
        }
        
        #investCountChart .apexcharts-datalabel {
            font-weight: bold;
        }
        
        /* Add animation to the charts */
        #investCountChart, #investAmountChart {
            transition: all 0.3s ease;
        }
        
        #investCountChart:hover, #investAmountChart:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        /* Make sure the y-axis values are clearly visible */
        .apexcharts-yaxis text {
            font-weight: 600 !important;
        }
    </style>
@endpush

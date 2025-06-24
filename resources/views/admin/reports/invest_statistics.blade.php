@extends('admin.layouts.app')

@section('panel')
    <div class="row g-4">
        <div class="col-md-6">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card full-view">
                        <div class="card-header">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-6">
                                    <h5 class="card-title mb-0">Tổng số đầu tư</h5>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <button class="exit-btn">
                                            <i class="fullscreen-open las la-compress" onclick="openFullscreen();"></i>
                                            <i class="fullscreen-close las la-compress-arrows-alt"
                                                onclick="closeFullscreen();"></i>
                                        </button>
                                        <select class="widget_select" name="invest_time">
                                            <option value="week">Tuần này</option>
                                            <option value="month">Tháng này</option>
                                            <option value="year" selected>Năm nay</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center pb-0 px-0">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <p>Đầu tư <span class="time_type"></span> này</p>
                                </div>
                                <div class="col-md-4">
                                    <h3><span>{{ gs('cur_sym') }}</span><span class="total_invest"></span></h3>
                                </div>
                                <div class="col-md-4">
                                    <p class="up_down">

                                    </p>
                                </div>
                            </div>
                            <div class="my_invest_canvas" style="background:#f8f9fa;min-height:180px;position:relative;border-radius:8px;">
                                <canvas height="150" id="invest_chart" class="chartjs-chart mt-4"></canvas>
                                <div id="no-data-message" style="display:none;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#888;font-size:16px;">Không có dữ liệu để hiển thị</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="card h-100">
                        <div class="card-body">
                            @if ($widget['total_invest'] > 0)
                                <div class="card-container">
                                    <div class="investments-scheme">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="mb-0">Tổng đầu tư →</h3>
                                            <h3 class="mb-6">
                                                {{ showAmount($widget['total_invest']) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h5 class="text-center">Không tìm thấy đầu tư</h5>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title mb-0">Lợi nhuận cần trả</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($widget['profit_to_give'])
                                <div class="card-container">
                                    <div class="row align-items-center pb-3 pb-xxl-0">
                                        <div class="col-6">
                                            <p>Cần trả</p>
                                            <h3 class="deposit-amount">
                                                <sup>{{ gs('cur_sym') }}</sup>{{ showAmount($widget['profit_to_give'], currencyFormat: false) }}
                                            </h3>
                                        </div>
                                        <div class="col-6 text-end">
                                            <a class="btn btn--primary"
                                                href="{{ route('admin.report.invest.history') }}">@lang('History')</a>
                                        </div>
                                    </div>
                                    <div class="progress-info">
                                        <div class="progress-info-content">
                                            <p>Đã trả
                                                @if ($widget['profit_paid'] + $widget['profit_to_give'] != 0)
                                                    {{ showAmount(($widget['profit_paid'] / ($widget['profit_paid'] + $widget['profit_to_give'])) * 100, currencyFormat: false) }}%
                                                @else
                                                    0%
                                                @endif
                                            </p>
                                        </div>
                                        <div class="progress-info-content">
                                            <p>
                                                {{ showAmount($widget['profit_paid']) }} /
                                                {{ showAmount($widget['profit_paid'] + $widget['profit_to_give']) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="progress mb-2 my-progressbar">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $widget['profit_paid'] + $widget['profit_to_give'] != 0 ? getAmount(($widget['profit_paid'] / ($widget['profit_paid'] + $widget['profit_to_give'])) * 100) : 0 }}%;">
                                        </div>
                                    </div>
                                    <p class="font-12 mb-0">*Thống kê này không bao gồm đầu tư trọn đời.</p>
                                </div>
                            @else
                                <h5 class="text-center">Không có đầu tư cần trả!</h5>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">Thống kê lãi theo dự án</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <div class="chart-info">
                                    <a class="chart-info-toggle" href="#">
                                        <img class="chart-info-img" src="{{ asset('assets/images/collapse.svg') }}"
                                            alt="image">
                                    </a>
                                    <div class="chart-info-content">
                                        <ul class="chart-info-list">
                                            @foreach ($interestByProjects as $key => $invest)
                                                <li class="chart-info-list-item">
                                                    <i
                                                        class="fas fa-plane projectPointInterest me-2"></i>{{ __($key) }}
                                                    {{ $totalInterest > 0 ? showAmount(($invest / $totalInterest) * 100, currencyFormat: false) : 0 }}
                                                    %
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="chart-area">
                                    <canvas class="chartjs-chart" id="interest_by_project" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-sm-6">
                                            <h5 class="card-title mb-0">Đầu tư & Lãi</h5>
                                        </div>
                                        <div class="col-sm-6 text-sm-end">
                                            <select class="widget_select" id="project_statistics_time"
                                                name="invest_interest_time">
                                                <option value="all">Tất cả thời gian</option>
                                                <option value="week">Tuần này</option>
                                                <option value="month">Tháng này</option>
                                                <option value="year">Năm nay</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="interest-scheme">
                                <div class="interest-scheme__content">
                                    <p class="mb-0">Đầu tư đang chạy</p>
                                    <h5 class="mb-1 text-success runningInvests"></h5>
                                    <p class="mb-0">
                                        <a class="btn btn-sm btn-outline-success font-12 px-2"
                                            href="{{ route('admin.report.invest.history') }}?status={{ Status::INVEST_RUNNING }}">@lang('History')</a>
                                    </p>
                                </div>
                                <div class="interest-scheme__content text-sm-center">
                                    <p class="mb-0 font-12">Đầu tư đã hoàn thành</p>
                                    <h5 class="mb-1 text-warning counter completedInvests"></h5>
                                    <p class="mb-0">
                                        <a
                                            href="{{ route('admin.report.invest.history') }}?status={{ Status::INVEST_COMPLETED }}">
                                            <button class="btn btn-sm btn-outline-warning font-12 px-2"
                                                type="button">@lang('History')</button>
                                        </a>
                                    </p>
                                </div>
                                <div class="interest-scheme__content text-sm-end">
                                    <p class="mb-0 font-12">Lãi</p>
                                    <h5 class="mb-1 text-primary interests"></h5>
                                    <p class="mb-0">
                                        <a class="btn btn-sm btn-outline-primary font-12 px-2 speedUp"
                                            href="{{ route('admin.report.transaction') }}?remark=profit">@lang('History')</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-6 col-md-12 col-xl-5">
                                    <h5 class="card-title mb-0">Thống kê đầu tư theo dự án</h5>
                                </div>
                                <div class="col-sm-6 col-md-12 col-xl-7">
                                    <div class="pair-option justify-content-md-start justify-content-xl-end">
                                        <select class="widget_select" name="project_statistics_invests">
                                            <option value="all">Tất cả đầu tư</option>
                                            <option value="active">Đang chạy</option>
                                            <option value="closed">Đã đóng</option>
                                        </select>
                                        <select class="widget_select" name="project_statistics_time">
                                            <option value="all">Tất cả thời gian</option>
                                            <option value="week">Tuần này</option>
                                            <option value="month">Tháng này</option>
                                            <option value="year">Năm nay</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <div class="chart-info">
                                    <a class="chart-info-toggle" href="#">
                                        <img class="chart-info-img" src="{{ asset('assets/images/collapse.svg') }}"
                                            alt="image">
                                    </a>
                                    <div class="chart-info-content">
                                        <ul class="chart-info-list project-info-data"></ul>
                                    </div>
                                </div>
                                <div class="chart-area chart-area--fixed">
                                    <div class="plan_invest_canvas"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="row align-items-center g-2">
                                <div class="col-sm-6">
                                    <h5 class="card-title mb-0">Đầu tư & Lãi</h5>
                                </div>
                                @if (@$firstInvestYear->date)
                                    <div class="col-sm-6">
                                        <div class="pair-option">
                                            <select class="widget_select" name="invest_interest_year">
                                                @for ($i = $firstInvestYear->date; $i <= date('Y'); $i++)
                                                    <option value="{{ $i }}"
                                                        @if (date('Y') == $i) selected @endif>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <select class="widget_select" name="invest_interest_month">
                                                <option value="01" @if (date('m') == '01') selected @endif>Tháng 1</option>
                                                <option value="02" @if (date('m') == '02') selected @endif>Tháng 2</option>
                                                <option value="03" @if (date('m') == '03') selected @endif>Tháng 3</option>
                                                <option value="04" @if (date('m') == '04') selected @endif>Tháng 4</option>
                                                <option value="05" @if (date('m') == '05') selected @endif>Tháng 5</option>
                                                <option value="06" @if (date('m') == '06') selected @endif>Tháng 6</option>
                                                <option value="07" @if (date('m') == '07') selected @endif>Tháng 7</option>
                                                <option value="08" @if (date('m') == '08') selected @endif>Tháng 8</option>
                                                <option value="09" @if (date('m') == '09') selected @endif>Tháng 9</option>
                                                <option value="10" @if (date('m') == '10') selected @endif>Tháng 10</option>
                                                <option value="11" @if (date('m') == '11') selected @endif>Tháng 11</option>
                                                <option value="12" @if (date('m') == '12') selected @endif>Tháng 12</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas class="chartjs-chart" id="chartjs-boundary-area-chart" height="80"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title mb-0">Đầu tư gần đây</div>
                        </div>
                        <div class="card-body">
                            <div class="plan-list d-flex flex-wrap flex-xxl-column gap-3 gap-xxl-0">
                                @foreach ($recentInvests as $invest)
                                    <div class="plan-item-two">
                                        <div class="plan-info plan-inner-div">
                                            <div class="plan-name fw-bold">{{ $invest->project->title }} - Mỗi {{ __($invest->time_name) }} {{ $invest->project->return_type != Status::LIFETIME ? gs('cur_sym') : '' }}{{ showAmount($invest->project->share_amount, currencyFormat: false) }} cho @if ($invest->project->return_type == Status::REPEAT) {{ __($invest->project->repeat_time) }} {{ __(@$invest->project->time->name) }} @else Trọn đời @endif</div>
                                            <div class="plan-desc text-end text-xl-start">Đã đầu tư: <span
                                                    class="fw-bold">{{ showAmount($invest->total_price) }}</span></div>
                                        </div>
                                        <div class="plan-start plan-inner-div">
                                            <p class="plan-label">Thời gian đáo hạn</p>
                                            <p class="plan-value date">
                                                @if ($invest->project->return_type == Status::LIFETIME)
                                                    Trọn đời
                                                @else
                                                    {{ convertMatureTime($invest->project->maturity_time) }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="plan-end plan-inner-div">
                                            <p class="plan-label">Ngày đầu tư</p>
                                            <p class="plan-value date">
                                                {{ showDateTime($invest->created_at, 'd M, y h:i A') }}</p>
                                        </div>
                                        <div class="plan-amount plan-inner-div text-end">
                                            <p class="plan-label">Lợi nhuận ròng</p>
                                            <p class="plan-value amount">
                                                {{ $invest->project->roi_percentage }}%
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">
@endpush
@push('style')
    <style>
        .widget_select {
            padding: 3px 3px;
            font-size: 13px;
        }
    </style>
@endpush

@push('script')
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script>
        'use strict';
        (function($) {
            $('[name=invest_time]').on('change', function() {
                let time = $(this).val();
                var url = "{{ route('admin.invest.report.statistics') }}";
                $.get(url, {
                    time: time
                }, function(response) {
                    $('.time_type').text(time === 'week' ? 'tuần này' : time === 'month' ? 'tháng này' : time === 'year' ? 'năm nay' : time);
                    $('.total_invest').text(response.total_invest.toFixed(2));

                    let upDown = `<small>Không có đầu tư trong ${time === 'week' ? 'tuần trước' : time === 'month' ? 'tháng trước' : time === 'year' ? 'năm trước' : time}</small>`;
                    if (response.invest_diff != 0) {
                        if (response.up_down == 'up') {
                            var className = 'success'
                        } else {
                            var className = 'danger';
                        }
                        upDown =
                            `<span class="badge badge-${className}-inverse font-16">${response.invest_diff}%<i class="las la-arrow-${response.up_down}"></i></span>`;
                    }
                    $('.up_down').html(upDown);
                    $('.my_invest_canvas').html('<canvas height="150" id="invest_chart" class="chartjs-chart mt-4"></canvas><div id="no-data-message" style="display:none;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#888;font-size:16px;">Không có dữ liệu để hiển thị</div>');
                    var ctx = document.getElementById('invest_chart');
                    var chartData = response.invest_amounts || [];
                    var chartLabels = response.months || [];
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Tổng số đầu tư theo tháng',
                                data: chartData,
                                backgroundColor: [
                                    @for ($i = 0; $i < 12; $i++)
                                        '#6c5ce7',
                                    @endfor
                                ],
                                borderColor: ['rgba(231, 80, 90, 0.75)'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            aspectRatio: 1,
                            responsive: true,
                            maintainAspectRatio: true,
                            elements: { line: { tension: 0 } },
                            scales: { xAxes: [{ display: true }], yAxes: [{ ticks: { suggestedMin: 0 }, display: true }] },
                            legend: { display: true },
                            tooltips: { callbacks: { label: (tooltipItem, data) => data.datasets[0].data[tooltipItem.index] + ' {{ gs('cur_text') }}' } }
                        }
                    });
                    if (!chartData.length || chartData.every(v => v === 0)) {
                        $('#no-data-message').show();
                    }
                });
            }).change();

            $('[name=project_statistics_time]').on('change', function() {
                let time = $('[name=project_statistics_time]').val();
                let investType = $('[name=project_statistics_invests]').val();
                var url = "{{ route('admin.invest.report.statistics.project') }}";
                $.get(url, {
                    time: time,
                    invest_type: investType
                }, function(response) {
                    $('.plan_invest_canvas').html(
                        '<canvas height="250" id="project_invest_statistics"></canvas>');
                    let invests = response.invest_data;
                    let planInfo = '';
                    let investAmount = [];
                    let planName = [];
                    let planUrl = "{{ route('admin.report.invest.history') }}";
                    $.each(invests, function(key, invest) {
                        let investPercent = response.total_invest > 0 ? (invest.investAmount / response.total_invest) * 100 : 0;
                        investAmount.push(parseFloat(invest.investAmount).toFixed(2));
                        planName.push(invest.project.title);
                        planInfo +=
                            `<li class="chart-info-list-item"><i class="fas fa-plane projectPoint me-2"></i>${investPercent.toFixed(2)}% - ${invest.project.title} <a href="${planUrl}?search=${invest.project.title}"><i class="las la-external-link-alt" title="@lang('Go to project')"></i></a></li>`
                    });
                    $('.project-info-data').html(planInfo);

                    /* -- Chartjs - Pie Chart -- */
                    var pieChartID = document.getElementById("project_invest_statistics").getContext(
                        '2d');
                    var pieChart = new Chart(pieChartID, {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: investAmount,
                                borderColor: 'transparent',
                                backgroundColor: projectColors()
                            }]
                        },
                        options: {
                            responsive: true,
                            legend: {
                                display: false
                            },
                            tooltips: {
                                callbacks: {
                                    label: (tooltipItem, data) => data.datasets[0].data[
                                        tooltipItem.index] + ' {{ gs('cur_text') }}'
                                }
                            }
                        }
                    });

                    var planPoints = $('.projectPoint');
                    planPoints.each(function(key, projectPoint) {
                        var projectPoint = $(projectPoint)
                        projectPoint.css('color', projectColors()[key])
                    })

                });
            }).change();

            $('[name=project_statistics_invests]').on('change', function() {
                $('[name=project_statistics_time]').trigger('change');
            });

            $('[name=invest_interest_time]').on('change', function() {
                let time = $(this).val();
                var url = "{{ route('admin.invest.report.interest') }}";
                $.get(url, {
                    time: time
                }, function(response) {
                    $('.runningInvests').text(`${response.running_invests}`);
                    $('.completedInvests').text(`${response.completed_invests}`);
                    $('.interests').text(`${response.interests}`);
                });
            }).change();

            $('[name=invest_interest_year]').on('change', function() {
                let year = $('[name=invest_interest_year]').val();
                let month = $('[name=invest_interest_month]').val();
                let url = "{{ route('admin.invest.report.interest.chart') }}";
                $.get(url, {
                    year: year,
                    month: month
                }, function(response) {

                    var boundaryAreaID = document.getElementById("chartjs-boundary-area-chart")
                        .getContext('2d');
                    var boundaryArea = new Chart(boundaryAreaID, {
                        type: 'line',
                        data: {
                            labels: response.keys,
                            datasets: [{
                                    backgroundColor: ["rgba(110, 129, 220,0.2)"],
                                    borderColor: ["#6e81dc"],
                                    pointBorderColor: ["#6e81dc", "#6e81dc", "#6e81dc",
                                        "#6e81dc", "#6e81dc", "#6e81dc", "#6e81dc"
                                    ],
                                    pointBackgroundColor: ["#6e81dc", "#6e81dc", "#6e81dc",
                                        "#6e81dc", "#6e81dc", "#6e81dc", "#6e81dc"
                                    ],
                                    pointBorderWidth: 0,
                                    data: response.invests,
                                    label: 'Invests',
                                    fill: 'start'
                                },
                                {
                                    backgroundColor: ["rgba(252, 193, 0,0.2)"],
                                    borderColor: ["#fcc100"],
                                    pointBorderColor: ["#fcc100", "#fcc100", "#fcc100",
                                        "#fcc100", "#fcc100", "#fcc100", "#fcc100"
                                    ],
                                    pointBackgroundColor: ["#fcc100", "#fcc100", "#fcc100",
                                        "#fcc100", "#fcc100", "#fcc100", "#fcc100"
                                    ],
                                    pointBorderWidth: 0,
                                    data: response.interests,
                                    label: 'Interests',
                                    fill: 'start'
                                }
                            ]
                        },
                        options: {
                            title: {
                                text: 'fill: start',
                                display: false
                            },
                            maintainAspectRatio: true,
                            spanGaps: true,
                            elements: {
                                point: {
                                    radius: 0,
                                }
                            },
                            plugins: {
                                filler: {
                                    propagate: false
                                }
                            },
                            legend: {
                                display: true
                            },
                            scales: {
                                xAxes: [{
                                    display: true,
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 0
                                    },
                                    gridLines: {
                                        color: '#dcdde1',
                                        lineWidth: 1,
                                        borderDash: [1]
                                    }
                                }],
                                yAxes: [{
                                    display: true,
                                    gridLines: {
                                        color: '#dcdde1',
                                        lineWidth: 1,
                                        borderDash: [1],
                                        zeroLineColor: '#dcdde1',
                                    }
                                }]
                            }
                        }
                    });

                });
            }).change();

            $('[name=invest_interest_month]').on('change', function() {
                $('[name=invest_interest_year]').trigger('change');
            });


            var doughnutChartID = document.getElementById("interest_by_project").getContext('2d');
            var doughnutChart = new Chart(doughnutChartID, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: @json($interestByProjects->values()),
                        borderColor: 'transparent',
                        backgroundColor: projectColors(),
                    }],
                },
                options: {
                    responsive: true,
                    cutoutPercentage: 75,
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: false,
                        text: 'Chart.js Doughnut Chart'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    tooltips: {
                        callbacks: {
                            label: (tooltipItem, data) => data.datasets[0].data[tooltipItem.index] +
                                ' {{ gs('cur_text') }}'
                        }
                    }
                }
            });

            var planPointInterests = $('.projectPointInterest');
            planPointInterests.each(function(key, projectPointInterest) {
                var projectPointInterest = $(projectPointInterest)
                projectPointInterest.css('color', projectColors()[key])
            })

            function projectColors() {
                return [
                    '#ff7675',
                    '#6c5ce7',
                    '#ffa62b',
                    '#ffeaa7',
                    '#D980FA',
                    '#fccbcb',
                    '#45aaf2',
                    '#05dfd7',
                    '#FF00F6',
                    '#1e90ff',
                    '#2ed573',
                    '#eccc68',
                    '#ff5200',
                    '#cd84f1',
                    '#7efff5',
                    '#7158e2',
                    '#fff200',
                    '#ff9ff3',
                    '#08ffc8',
                    '#3742fa',
                    '#1089ff',
                    '#70FF61',
                    '#bf9fee',
                    '#574b90'
                ]
            }

            let chartToggle = $('.chart-info-toggle');
            let chartContent = $(".chart-info-content");
            if (chartToggle || chartContent) {
                chartToggle.each(function() {
                    $(this).on("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).siblings().toggleClass("is-open");
                    });
                });
                chartContent.each(function() {
                    $(this).on("click", function(e) {
                        e.stopPropagation();
                    });
                });
                $(document).on("click", function() {
                    chartContent.removeClass("is-open");
                });
            }

            $('.exit-btn').on('click', function() {
                $(this).toggleClass('active');
            });

        })(jQuery);
        var elems = document.querySelector(".full-view");

        function openFullscreen() {
            if (elems.requestFullscreen) {
                elems.requestFullscreen();
            } else if (elems.mozRequestFullScreen) {
                /* Firefox */
                elems.mozRequestFullScreen();
            } else if (elems.webkitRequestFullscreen) {
                /* Chrome, Safari & Opera */
                elems.webkitRequestFullscreen();
            } else if (elems.msRequestFullscreen) {
                /* IE/Edge */
                elems.msRequestFullscreen();
            }
        }

        function closeFullscreen() {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                /* Firefox */
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                /* Chrome, Safari and Opera */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                /* IE/Edge */
                document.msExitFullscreen();
            }
        }
    </script>
@endpush

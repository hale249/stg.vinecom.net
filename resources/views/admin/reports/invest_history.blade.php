@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-xxl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 has-link b-radius--5 bg--info">
                <div class="widget-two__content">
                    <h2 class="text-white">{{ $totalInvestCount }}</h2>
                    <p class="text-white">Tổng số lượt đầu tư</p>
                </div>
            </div><!-- widget-two end -->
        </div>
        <div class="col-xxl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--success has-link">
                <div class="widget-two__content">
                    <h2 class="text-white">{{ showAmount($totalInvestAmount) }}</h2>
                    <p class="text-white">Tổng số tiền đầu tư</p>
                </div>
            </div><!-- widget-two end -->
        </div>
        <div class="col-xxl-4 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--7 has-link">
                <div class="widget-two__content">
                    <h2 class="text-white">{{ showAmount($totalPaid) }}</h2>
                    <p class="text-white">Tổng số đã trả</p>
                </div>
            </div><!-- widget-two end -->
        </div>


        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button class="btn btn-outline--primary showFilterBtn btn-sm" type="button"><i class="las la-filter"></i> Lọc</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>Dự án/Mã HĐ</label>
                                <input class="form-control" name="search" type="text" value="{{ request()->search }}">
                            </div>
                            <div class="flex-grow-1">
                                <label>Trạng thái</label>
                                <select class="form-control select2" name="status" data-minimum-results-for-search="-1">
                                    <option value="">Tất cả</option>
                                    <option value="2" @selected(request()->status == '2')>Đang chạy</option>
                                    <option value="3" @selected(request()->status == '3')>Hoàn thành</option>
                                    <option value="4" @selected(request()->status == '4')>Đã đóng</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>Ngày</label>
                                <x-search-date-field :icon="false" />
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> Lọc</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
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
                                @forelse($invests as $invest)
                                    <tr>
                                        <td>{{ $invest->invest_no }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $invest->user->fullname }}</span>
                                            <br>
                                            <span class="small"> <a
                                                    href="{{ appendQuery('search', $invest->user->username) }}"><span>@</span>{{ $invest->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ __($invest->project->title) }}</td>
                                        <td>{{ __($invest->quantity) }} @lang('Units')</td>
                                        <td>{{ showAmount($invest->total_price) }}</td>
                                        <td>{{ showAmount($invest->total_earning) }}</td>
                                        <td> @php echo $invest->project->typeBadge @endphp </td>
                                        <td>{{ $invest->project->return_type != Status::LIFETIME ? showAmount($invest->recurring_pay) : '**' }}
                                        </td>
                                        <td>{{ showAmount($invest->total_earning) }}</td>
                                        <td>
                                            @php echo $invest->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-outline--primary btn-sm"
                                                    href="{{ route('admin.invest.details', $invest->id) }}"><i
                                                        class="las la-desktop"></i> Chi tiết</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($invests->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($invests) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.cancelBtn').on('click', function() {
                let modal = $('#cancelModal');
                $('[name=invest_id]').val($(this).data('invest_id'));
                modal.modal('show');
            });
        })(jQuery)
    </script>
@endpush

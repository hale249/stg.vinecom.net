@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title mb-0">@lang('Danh sách hợp đồng của tôi')</h5>
                <div class="d-flex flex-wrap gap-2">
                    <div>
                        <a href="{{ route('user.staff.staff.create_contract') }}" class="btn btn-sm btn--primary">
                            <i class="las la-plus"></i> @lang('Tạo hợp đồng mới')
                        </a>
                    </div>
                    <div class="input-group w-auto">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="@lang('Tìm kiếm...')" value="{{ request()->search }}">
                        <button class="input-group-text bg-primary text-white border-0">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Mã hợp đồng')</th>
                                <th>@lang('Dự án')</th>
                                <th>@lang('Khách hàng')</th>
                                <th>@lang('Số tiền')</th>
                                <th>@lang('Lãi suất')</th>
                                <th>@lang('Ngày bắt đầu')</th>
                                <th>@lang('Trạng thái')</th>
                                <th>@lang('Hành động')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contracts as $contract)
                            <tr>
                                <td>{{ $contract->invest_no }}</td>
                                <td>{{ Str::limit($contract->project->name ?? 'N/A', 20) }}</td>
                                <td>{{ $contract->user->fullname ?? 'N/A' }}</td>
                                <td>{{ showAmount($contract->amount) }} {{ __($general->cur_text) }}</td>
                                <td>{{ $contract->interest_rate }}%</td>
                                <td>{{ showDateTime($contract->created_at) }}</td>
                                <td>
                                    @php
                                        $status = $contract->status;
                                        $statusClass = '';
                                        $statusText = '';
                                        
                                        if($status == \App\Constants\Status::INVEST_PENDING) {
                                            $statusClass = 'warning';
                                            $statusText = 'Chờ duyệt';
                                        } elseif($status == \App\Constants\Status::INVEST_RUNNING) {
                                            $statusClass = 'success';
                                            $statusText = 'Đang chạy';
                                        } elseif($status == \App\Constants\Status::INVEST_COMPLETED) {
                                            $statusClass = 'primary';
                                            $statusText = 'Hoàn thành';
                                        } elseif($status == \App\Constants\Status::INVEST_REJECTED) {
                                            $statusClass = 'danger';
                                            $statusText = 'Từ chối';
                                        } elseif($status == \App\Constants\Status::INVEST_CANCELED) {
                                            $statusClass = 'dark';
                                            $statusText = 'Đã hủy';
                                        }
                                    @endphp
                                    <span class="badge badge--{{ $statusClass }}">{{ __($statusText) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('user.staff.staff.contract_details', $contract->id) }}" class="btn btn-sm btn-outline--primary">
                                        <i class="las la-eye"></i> @lang('Chi tiết')
                                    </a>
                                    
                                    @if($contract->status == \App\Constants\Status::INVEST_PENDING)
                                    <button type="button" class="btn btn-sm btn-outline--danger cancelBtn" data-bs-toggle="modal" data-bs-target="#cancelModal" data-id="{{ $contract->id }}">
                                        <i class="las la-times-circle"></i> @lang('Hủy')
                                    </button>
                                    @endif
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
            @if($contracts->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($contracts) }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- CANCEL MODAL --}}
<div id="cancelModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Xác nhận hủy hợp đồng')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>@lang('Bạn có chắc chắn muốn hủy hợp đồng này không?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Đóng')</button>
                    <button type="submit" class="btn btn--danger">@lang('Xác nhận')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($){
        "use strict";
        
        $('.cancelBtn').on('click', function() {
            var modal = $('#cancelModal');
            var id = $(this).data('id');
            var form = modal.find('form');
            form.attr('action', '{{ route("user.staff.staff.cancel_contract", ":id") }}'.replace(':id', id));
            modal.modal('show');
        });
        
    })(jQuery);
</script>
@endpush 
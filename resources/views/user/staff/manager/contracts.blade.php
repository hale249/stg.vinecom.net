@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Hợp đồng nhóm')</h5>
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm...">
                            <button class="btn btn-sm btn-primary" type="submit"><i class="las la-search"></i></button>
                        </div>
                    </form>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="las la-filter"></i> @lang('Lọc')
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">@lang('Tất cả')</a>
                            <a class="dropdown-item" href="#">@lang('Đang hoạt động')</a>
                            <a class="dropdown-item" href="#">@lang('Hoàn thành')</a>
                            <a class="dropdown-item" href="#">@lang('Chờ duyệt')</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                @if($contracts->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>@lang('STT')</th>
                                    <th>@lang('Mã hợp đồng')</th>
                                    <th>@lang('Dự án')</th>
                                    <th>@lang('Thành viên')</th>
                                    <th>@lang('Số tiền')</th>
                                    <th>@lang('Trạng thái')</th>
                                    <th>@lang('Ngày tạo')</th>
                                    <th>@lang('Hành động')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contracts as $key => $contract)
                                    <tr>
                                        <td>{{ $contracts->firstItem() + $key }}</td>
                                        <td><span class="badge bg-primary">{{ $contract->invest_no }}</span></td>
                                        <td>{{ Str::limit($contract->project->title ?? '-', 20) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-sm">
                                                    <img src="{{ getImage(getFilePath('userProfile').'/'.$contract->user->image, getFileSize('userProfile'), true) }}" alt="avatar" class="rounded-circle">
                                                </span>
                                                <span>{{ $contract->user->fullname ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ showAmount($contract->total_price) }} {{ $general->cur_text }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($contract->status) {
                                                    \App\Constants\Status::INVEST_RUNNING => 'bg-success',
                                                    \App\Constants\Status::INVEST_PENDING => 'bg-warning',
                                                    \App\Constants\Status::INVEST_PENDING_ADMIN_REVIEW => 'bg-info',
                                                    \App\Constants\Status::INVEST_COMPLETED => 'bg-info',
                                                    \App\Constants\Status::INVEST_CLOSED => 'bg-secondary',
                                                    \App\Constants\Status::INVEST_CANCELED => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                
                                                $statusText = match($contract->status) {
                                                    \App\Constants\Status::INVEST_RUNNING => 'Đang hoạt động',
                                                    \App\Constants\Status::INVEST_PENDING => 'Chờ xử lý',
                                                    \App\Constants\Status::INVEST_PENDING_ADMIN_REVIEW => 'Chờ duyệt',
                                                    \App\Constants\Status::INVEST_COMPLETED => 'Hoàn thành',
                                                    \App\Constants\Status::INVEST_CLOSED => 'Đã đóng',
                                                    \App\Constants\Status::INVEST_CANCELED => 'Đã hủy',
                                                    default => 'Không xác định'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>{{ showDateTime($contract->created_at) }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('user.staff.manager.contract', $contract->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-title="@lang('Chi tiết hợp đồng')">
                                                    <i class="las la-file-contract"></i> @lang('Hợp đồng')
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-info view-investment-details" data-bs-toggle="tooltip" data-bs-title="@lang('Chi tiết đầu tư')" data-id="{{ $contract->id }}">
                                                    <i class="las la-info-circle"></i> @lang('Chi tiết')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 pb-3">
                        {{ $contracts->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted">@lang('Không có hợp đồng nào trong nhóm.')</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Investment Details Modal -->
<div class="modal fade" id="investmentDetailsModal" tabindex="-1" aria-labelledby="investmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="investmentDetailsModalLabel">@lang('Chi tiết đầu tư')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row investment-details">
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Mã hợp đồng'):</span>
                            <span class="detail-value" id="invest_no"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Dự án'):</span>
                            <span class="detail-value" id="project_title"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Khách hàng'):</span>
                            <span class="detail-value" id="customer_name"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Số tiền'):</span>
                            <span class="detail-value" id="amount"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Lãi suất'):</span>
                            <span class="detail-value" id="interest_rate"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Thời hạn'):</span>
                            <span class="detail-value" id="duration"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Ngày bắt đầu'):</span>
                            <span class="detail-value" id="start_date"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Ngày kết thúc'):</span>
                            <span class="detail-value" id="end_date"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Thanh toán tiếp theo'):</span>
                            <span class="detail-value" id="next_payment_date"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Trạng thái'):</span>
                            <span class="detail-value" id="status_badge"></span>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="detail-item">
                            <span class="detail-label">@lang('Ghi chú'):</span>
                            <span class="detail-value" id="notes"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Đóng')</button>
                <a href="#" class="btn btn-primary" id="viewContractBtn">@lang('Xem hợp đồng')</a>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
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

    .detail-item {
        margin-bottom: 8px;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
    }

    .detail-value {
        margin-left: 5px;
    }

    .investment-details {
        font-size: 14px;
    }
</style>
@endpush

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle investment details view
        $('.view-investment-details').on('click', function(e) {
            e.preventDefault();
            
            var id = $(this).data('id');
            var modal = $('#investmentDetailsModal');
            var contractUrl = '{{ route("user.staff.manager.contract", ":id") }}'.replace(':id', id);
            
            // Show loading
            modal.find('.modal-body').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">@lang("Đang tải...")</p></div>');
            modal.modal('show');
            
            // Fetch investment details via AJAX
            $.ajax({
                url: '{{ route("user.staff.manager.contract.details.ajax") }}',
                type: 'GET',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        var data = response.data;
                        
                        // Update modal with data
                        $('#invest_no').text(data.invest_no);
                        $('#project_title').text(data.project_title);
                        $('#customer_name').text(data.customer_name);
                        $('#amount').text(data.amount);
                        $('#interest_rate').text(data.interest_rate);
                        $('#duration').text(data.duration);
                        $('#start_date').text(data.start_date);
                        $('#end_date').text(data.end_date);
                        $('#next_payment_date').text(data.next_payment_date);
                        $('#status_badge').html(data.status_badge);
                        $('#notes').text(data.notes || 'N/A');
                        
                        // Set contract link
                        $('#viewContractBtn').attr('href', contractUrl);
                        
                        // Show the modal content
                        modal.find('.modal-body').html($('.investment-details').parent().html());
                    } else {
                        modal.find('.modal-body').html('<div class="alert alert-danger">@lang("Không thể tải thông tin đầu tư. Vui lòng thử lại.")</div>');
                    }
                },
                error: function() {
                    modal.find('.modal-body').html('<div class="alert alert-danger">@lang("Có lỗi xảy ra. Vui lòng thử lại sau.")</div>');
                }
            });
        });
    });
</script>
@endpush 
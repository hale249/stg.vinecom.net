@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    @lang('Chi tiết hợp đồng') - {{ $invest->invest_no }}
                    @php
                        $status = $invest->status;
                        $statusClass = '';
                        $statusText = '';
                        
                        if($status == \App\Constants\Status::INVEST_PENDING) {
                            $statusClass = 'warning';
                            $statusText = 'Chờ duyệt';
                        } elseif($status == \App\Constants\Status::INVEST_RUNNING) {
                            $statusClass = 'success';
                            $statusText = 'Đang hoạt động';
                        } elseif($status == \App\Constants\Status::INVEST_COMPLETED) {
                            $statusClass = 'primary';
                            $statusText = 'Hoàn thành';
                        } elseif($status == \App\Constants\Status::INVEST_CANCELED) {
                            $statusClass = 'danger';
                            $statusText = 'Từ chối';
                        }
                    @endphp
                    <span class="badge badge--{{ $statusClass }} ms-2">{{ __($statusText) }}</span>
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('user.staff.staff.contracts') }}" class="btn btn-sm btn--dark">
                        <i class="las la-arrow-left"></i> @lang('Quay lại')
                    </a>
                    
                    <a href="{{ route('user.staff.staff.contract.documents', $invest->id) }}" class="btn btn-sm btn--info">
                        <i class="las la-file-upload"></i> @lang('Tài liệu hợp đồng')
                    </a>
                    
                    @if($invest->status == \App\Constants\Status::INVEST_PENDING)
                    <button type="button" class="btn btn-sm btn--danger cancelBtn" data-bs-toggle="modal" data-bs-target="#cancelModal" data-id="{{ $invest->id }}">
                        <i class="las la-times-circle"></i> @lang('Hủy hợp đồng')
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Thông tin hợp đồng')</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Mã hợp đồng')</span>
                                        <span class="fw-bold">{{ $invest->invest_no }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Dự án')</span>
                                        <span class="fw-bold">{{ $invest->project->title ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Số tiền đầu tư')</span>
                                        <span class="fw-bold">{{ showAmount($invest->total_price) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Lãi suất')</span>
                                        <span class="fw-bold">{{ $invest->display_roi_percentage }}%</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Tiền lãi mỗi kỳ')</span>
                                        <span class="fw-bold">{{ showAmount(($invest->roi_amount ?? ($invest->total_price * ($invest->roi_percentage/100))) / 12) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Thời hạn')</span>
                                        <span class="fw-bold">{{ $invest->project_duration > 0 ? $invest->project_duration : $invest->period }} @lang('tháng')</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Ngày bắt đầu')</span>
                                        <span class="fw-bold">{{ showDateTime($invest->created_at) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Thanh toán lãi tiếp theo')</span>
                                        <span class="fw-bold">{{ $invest->next_time ? showDateTime($invest->next_time) : 'Chưa xác định' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Thanh toán lãi gần nhất')</span>
                                        <span class="fw-bold">{{ $invest->last_time ? showDateTime($invest->last_time) : 'Chưa có' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Ngày đáo hạn')</span>
                                        <span class="fw-bold">{{ $invest->project_closed ? showDateTime($invest->project_closed) : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Trạng thái')</span>
                                        {!! $invest->statusBadge !!}
                                    </li>
                                    @if($invest->referral_code)
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Người giới thiệu')</span>
                                        <span class="fw-bold">{{ $invest->referrer->fullname ?? 'N/A' }}</span>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Thông tin khách hàng')</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="thumb me-3">
                                        <img src="{{ getImage(getFilePath('userProfile').'/'.$invest->user->image, getFileSize('userProfile')) }}" alt="@lang('image')" class="rounded-circle" width="60">
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $invest->user->fullname }}</h6>
                                        <span class="text-muted">{{ $invest->user->email }}</span>
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Số điện thoại')</span>
                                        <span class="fw-bold">{{ $invest->user->mobile }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Địa chỉ')</span>
                                        <span class="fw-bold">{{ $invest->user->address->address ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Thành phố')</span>
                                        <span class="fw-bold">{{ $invest->user->address->city ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Quốc gia')</span>
                                        <span class="fw-bold">{{ $invest->user->address->country ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>@lang('Ngày tham gia')</span>
                                        <span class="fw-bold">{{ showDateTime($invest->user->created_at) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    @if($invest->note)
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Ghi chú')</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $invest->note }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($invest->attachments)
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Tài liệu đính kèm')</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach(json_decode($invest->attachments) as $key => $attachment)
                                        <div class="col-md-2">
                                            <div class="attachment-item">
                                                <a href="{{ route('user.download.attachment', $attachment) }}" class="attachment-link">
                                                    @php
                                                        $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                                        $icon = 'las la-file';
                                                        
                                                        if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                            $icon = 'las la-image';
                                                        } elseif(in_array($ext, ['pdf'])) {
                                                            $icon = 'las la-file-pdf';
                                                        } elseif(in_array($ext, ['doc', 'docx'])) {
                                                            $icon = 'las la-file-word';
                                                        } elseif(in_array($ext, ['xls', 'xlsx'])) {
                                                            $icon = 'las la-file-excel';
                                                        }
                                                    @endphp
                                                    <i class="{{ $icon }} fa-3x"></i>
                                                    <span class="mt-2">{{ Str::limit($attachment, 10) }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($invest->status == \App\Constants\Status::INVEST_RUNNING)
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Thông tin lãi suất')</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    @php
                                        // Calculate Total Earnings and Profit
                                        $quantity = $invest->quantity > 0 ? $invest->quantity : 1;
                                        $roiAmount = $invest->roi_amount;
                                        $roiPercentage = $invest->display_roi_percentage > 0 ? $invest->display_roi_percentage : 0;
                                        $capitalBack = $invest->capital_back ?? 1;
                                        $returnType = $invest->project->return_type ?? 2;
                                        $projectDuration = $invest->project_duration > 0 ? $invest->project_duration : $invest->period;
                                        $repeatTimes = $invest->repeat_times ?? $projectDuration;
                                        $timeName = 'Tháng';
                                        
                                        $totalEarnings = 0;
                                        // Calculate annual ROI
                                        $annualROI = $invest->total_price * ($roiPercentage/100);
                                        // Calculate monthly payment by dividing annual amount by 12
                                        $monthlyPayAmount = $annualROI / 12;
                                        $totalEarnings = $annualROI * $repeatTimes * $quantity;
                                        
                                        // Profit Earning (excluding capital back)
                                        $profitEarning = $totalEarnings;
                                        
                                        // Total Earning (including capital back if applicable)
                                        $totalEarning = $profitEarning;
                                        if ($capitalBack) {
                                            $totalEarning += $invest->total_price;
                                        }
                                    @endphp
                                    
                                    <div class="col-md-6">
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Lãi suất hàng tháng')</span>
                                                <span class="fw-bold text-primary">{{ $roiPercentage }}%</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Tiền lãi mỗi tháng')</span>
                                                <span class="fw-bold text-primary">{{ showAmount(($invest->total_price * ($roiPercentage/100)) / 12) }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Thời hạn đầu tư')</span>
                                                <span class="fw-bold">{{ $projectDuration }} {{ __($timeName) }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Số kỳ thanh toán')</span>
                                                <span class="fw-bold">{{ $repeatTimes }} @lang('kỳ')</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Tổng tiền đầu tư')</span>
                                                <span class="fw-bold">{{ showAmount($invest->total_price) }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Tổng tiền lãi')</span>
                                                <span class="fw-bold text-success">{{ showAmount($profitEarning) }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Hoàn trả gốc')</span>
                                                <span class="badge {{ $capitalBack ? 'badge--success' : 'badge--warning' }}">{{ $capitalBack ? __('Có') : __('Không') }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Tổng nhận về')</span>
                                                <span class="fw-bold text-success">{{ showAmount($totalEarning) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Lịch sử thanh toán lãi')</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>@lang('Ngày thanh toán')</th>
                                                <th>@lang('Số tiền')</th>
                                                <th>@lang('Trạng thái')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invest->interests ?? collect() as $interest)
                                            <tr>
                                                <td>{{ showDateTime($interest->created_at) }}</td>
                                                <td>{{ showAmount($interest->amount) }}</td>
                                                <td>
                                                    @if($interest->status == 1)
                                                        <span class="badge badge--success">@lang('Đã thanh toán')</span>
                                                    @else
                                                        <span class="badge badge--warning">@lang('Chưa thanh toán')</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="100%" class="text-center">@lang('Chưa có thanh toán lãi')</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
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
            <form action="{{ route('user.staff.staff.cancel_contract', $invest->id) }}" method="POST">
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

@push('style')
<style>
    .attachment-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 15px;
        transition: all 0.3s;
    }
    
    .attachment-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .attachment-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #333;
        text-decoration: none;
    }
    
    .attachment-link:hover {
        color: var(--primary-color);
    }
</style>
@endpush 
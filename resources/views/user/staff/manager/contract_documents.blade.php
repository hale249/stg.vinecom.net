@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    @lang('Tài liệu hợp đồng') - {{ $invest->invest_no }}
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('user.staff.manager.contract', $invest->id) }}" class="btn btn-sm btn--dark">
                        <i class="las la-arrow-left"></i> @lang('Quay lại')
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row gy-4">
                    @if(count($documents) > 0)
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Tài liệu đính kèm')</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach($documents as $document)
                                        <div class="col-md-2">
                                            <div class="attachment-item">
                                                <a href="{{ route('user.download.attachment', $document['name']) }}" class="attachment-link">
                                                    <i class="{{ $document['icon'] }} fa-3x"></i>
                                                    <span class="mt-2">{{ Str::limit($document['name'], 10) }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            @lang('Không có tài liệu nào được đính kèm cho hợp đồng này.')
                        </div>
                    </div>
                    @endif

                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Thông tin hợp đồng')</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
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
                                                <span class="fw-bold">{{ $invest->profit_percentage }}%</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Khách hàng')</span>
                                                <span class="fw-bold">{{ $invest->user->fullname }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Ngày tạo')</span>
                                                <span class="fw-bold">{{ showDateTime($invest->created_at) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Trạng thái')</span>
                                                {!! $invest->statusBadge !!}
                                            </li>
                                            @if($invest->staff_id)
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                <span>@lang('Nhân viên tạo')</span>
                                                <span class="fw-bold">{{ $invest->staff->fullname ?? 'N/A' }}</span>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

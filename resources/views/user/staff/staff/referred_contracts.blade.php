@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title mb-0">@lang('Danh sách hợp đồng được giới thiệu')</h5>
                <div class="d-flex flex-wrap gap-2">
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
                                <td>{{ Str::limit($contract->project->title ?? 'N/A', 20) }}</td>
                                <td>{{ $contract->user->fullname ?? 'N/A' }}</td>
                                <td>{{ showAmount($contract->total_price) }}</td>
                                <td>{{ $contract->display_roi_percentage }}%</td>
                                <td>{{ showDateTime($contract->created_at) }}</td>
                                <td>{!! $contract->statusBadge !!}</td>
                                <td>
                                    <a href="{{ route('user.staff.staff.contract.details', $contract->id) }}" class="btn btn-sm btn-outline--primary">
                                        <i class="las la-eye"></i> @lang('Chi tiết')
                                    </a>
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
@endsection 
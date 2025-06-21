@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title mb-0">@lang('Danh sách khách hàng của tôi')</h5>
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
                                <th>@lang('Khách hàng')</th>
                                <th>@lang('Email')</th>
                                <th>@lang('Số điện thoại')</th>
                                <th>@lang('Số hợp đồng')</th>
                                <th>@lang('Tổng đầu tư')</th>
                                <th>@lang('Ngày tham gia')</th>
                                <th>@lang('Hành động')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                            <tr>
                                <td>
                                    <div class="user">
                                        <div class="thumb">
                                            <img src="{{ getImage(getFilePath('userProfile').'/'.$customer->image, getFileSize('userProfile')) }}" alt="@lang('image')">
                                        </div>
                                        <span class="name">{{ $customer->fullname }}</span>
                                    </div>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->mobile }}</td>
                                <td>
                                    <span class="badge badge--primary">{{ $customer->invests_count }}</span>
                                </td>
                                <td>{{ showAmount($customer->invests_sum_amount) }} {{ __($general->cur_text) }}</td>
                                <td>{{ showDateTime($customer->created_at) }}</td>
                                <td>
                                    <a href="{{ route('user.staff.staff.create_contract') }}?customer_id={{ $customer->id }}" class="btn btn-sm btn-outline--primary">
                                        <i class="las la-plus-circle"></i> @lang('Tạo hợp đồng')
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
            @if($customers->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($customers) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .user {
        display: flex;
        align-items: center;
    }
    
    .user .thumb {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 10px;
        border: 2px solid #e5e7eb;
    }
    
    .user .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .user .name {
        font-size: 14px;
        font-weight: 500;
    }
</style>
@endpush 
@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('ID')</th>
                                <th>@lang('Họ và tên')</th>
                                <th>@lang('Tên đăng nhập')</th>
                                <th>@lang('Email')</th>
                                <th>@lang('Ngày tạo')</th>
                                <th>@lang('Hành động')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deputies as $deputy)
                                <tr>
                                    <td>{{ $deputy->id }}</td>
                                    <td>{{ $deputy->name }}</td>
                                    <td>{{ $deputy->username }}</td>
                                    <td>{{ $deputy->email }}</td>
                                    <td>{{ showDateTime($deputy->created_at) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Bạn có chắc chắn muốn xóa phó tổng giám đốc này?')" data-action="{{ route('admin.deputy.delete', $deputy->id) }}">
                                            <i class="la la-trash"></i> @lang('Xóa')
                                        </button>
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
            @if($deputies->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($deputies) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.deputy.create') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-plus"></i> @lang('Thêm mới')
    </a>
@endpush 
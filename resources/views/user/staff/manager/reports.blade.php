@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Báo cáo đầu tư')</h5>
                <form action="" method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm...">
                        <button class="btn btn-sm btn-primary" type="submit"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                @if(isset($reports) && $reports->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>@lang('STT')</th>
                                    <th>@lang('Mã hợp đồng')</th>
                                    <th>@lang('Dự án')</th>
                                    <th>@lang('Thành viên')</th>
                                    <th>@lang('Số tiền đầu tư')</th>
                                    <th>@lang('Ngày đầu tư')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $key => $item)
                                    <tr>
                                        <td>{{ $reports->firstItem() + $key }}</td>
                                        <td><span class="badge bg-primary">{{ $item->trx ?? '-' }}</span></td>
                                        <td>{{ Str::limit($item->project->name ?? '-', 20) }}</td>
                                        <td>{{ $item->user->fullname ?? '-' }}</td>
                                        <td>{{ showAmount($item->amount) }} {{ $general->cur_text }}</td>
                                        <td>{{ showDateTime($item->created_at) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 pb-3">
                        {{ $reports->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted">@lang('Không có dữ liệu báo cáo đầu tư.')</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 
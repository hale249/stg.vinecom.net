@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Duyệt hợp đồng')</h5>
            </div>
            <div class="card-body p-0">
                @if($pendingContracts->count())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>@lang('STT')</th>
                                    <th>@lang('Mã hợp đồng')</th>
                                    <th>@lang('Dự án')</th>
                                    <th>@lang('Thành viên')</th>
                                    <th>@lang('Số tiền')</th>
                                    <th>@lang('Ngày tạo')</th>
                                    <th>@lang('Hành động')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingContracts as $key => $contract)
                                    <tr>
                                        <td>{{ $pendingContracts->firstItem() + $key }}</td>
                                        <td><span class="badge bg-primary">{{ $contract->trx }}</span></td>
                                        <td>{{ Str::limit($contract->project->name ?? '-', 20) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-sm">
                                                    <img src="{{ asset('assets/images/avatar.png') }}" alt="avatar" class="rounded-circle">
                                                </span>
                                                <span>{{ $contract->user->fullname ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ showAmount($contract->amount) }} {{ $general->cur_text }}</td>
                                        <td>{{ showDateTime($contract->created_at) }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <form action="{{ route('user.staff.manager.approve_contract', $contract->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-title="@lang('Duyệt')"><i class="las la-check"></i></button>
                                                </form>
                                                <form action="{{ route('user.staff.manager.reject_contract', $contract->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-title="@lang('Từ chối')"><i class="las la-times"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 pb-3">
                        {{ $pendingContracts->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted">@lang('Không có hợp đồng chờ duyệt.')</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

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
</style>
@endpush

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush 
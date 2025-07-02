@extends($layout ?? 'user.staff.layouts.staff_app')

@section('panel')
<div class="row align-items-center mb-30 justify-content-between">
    <div class="col-lg-6 col-sm-6">
        <h6 class="page-title">{{ __($pageTitle) }}</h6>
    </div>
    <div class="col-lg-6 col-sm-6 text-sm-end mt-sm-0 mt-3">
        <div class="d-flex justify-content-end">
            <div class="input-group w-auto">
                <input type="text" name="search_table" class="form-control bg--white" placeholder="@lang('Tìm kiếm...')" id="searchInput">
                <button class="input-group-text bg--primary border-0 text-white">
                    <i class="las la-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--sm">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Tiêu đề')</th>
                                <th>@lang('Danh mục')</th>
                                <th>@lang('Mô tả')</th>
                                <th>@lang('Thao tác')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $document)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $document->title }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg--primary">
                                            <i class="{{ $document->category->icon ?? 'las la-folder' }} me-1"></i>
                                            {{ $document->category->name }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($document->description, 100) }}</td>
                                    <td>
                                        @php
                                            $routePrefix = $isManager ? 'user.staff.manager' : 'user.staff.staff';
                                        @endphp
                                        <a href="{{ route($routePrefix . '.documents.view', $document->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="las la-eye"></i> @lang('Xem')
                                        </a>
                                        <a href="{{ route($routePrefix . '.documents.download', $document->id) }}" class="btn btn-sm btn-outline--info">
                                            <i class="las la-download"></i> @lang('Tải xuống')
                                        </a>
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
            @if($documents->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($documents) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Handle search functionality
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $(".card").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    })(jQuery);
</script>
@endpush 
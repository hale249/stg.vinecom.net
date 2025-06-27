@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Tiêu đề')</th>
                                    <th>@lang('Danh mục')</th>
                                    <th>@lang('File')</th>
                                    <th>@lang('Quyền xem')</th>
                                    <th>@lang('Ngày tạo')</th>
                                    <th>@lang('Trạng thái')</th>
                                    <th>@lang('Thao tác')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ Str::limit($document->title, 30) }}</td>
                                        <td>
                                            <span class="badge badge--primary">
                                                <i class="{{ $document->category->icon ?? 'las la-folder' }}"></i>
                                                {{ $document->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $document->file_name }}">
                                                {{ Str::limit($document->file_name, 15) }}
                                                <br>
                                                <small>{{ $document->file_size_formatted }}</small>
                                            </span>
                                        </td>
                                        <td>
                                            @if($document->for_manager && $document->for_staff)
                                                <span class="badge badge--success">@lang('Tất cả')</span>
                                            @elseif($document->for_manager)
                                                <span class="badge badge--primary">@lang('Manager')</span>
                                            @elseif($document->for_staff)
                                                <span class="badge badge--info">@lang('Staff')</span>
                                            @endif
                                        </td>
                                        <td>{{ showDateTime($document->created_at) }}</td>
                                        <td>
                                            @if ($document->status)
                                                <span class="badge badge--success">@lang('Kích hoạt')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Vô hiệu hóa')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.documents.edit', $document->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-edit"></i> @lang('Edit')
                                                </a>

                                                <a href="{{ route('admin.documents.download', $document->id) }}" class="btn btn-sm btn-outline--info">
                                                    <i class="las la-download"></i> @lang('Download')
                                                </a>

                                                @if ($document->status)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.documents.status', $document->id) }}"
                                                        data-question="@lang('Bạn có chắc chắn muốn vô hiệu hóa tài liệu này?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.documents.status', $document->id) }}"
                                                        data-question="@lang('Bạn có chắc chắn muốn kích hoạt tài liệu này?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @endif
                                                
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.documents.destroy', $document->id) }}"
                                                    data-question="@lang('Bạn có chắc chắn muốn xóa tài liệu này?')">
                                                    <i class="la la-trash"></i> @lang('Delete')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($documents->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($documents) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <div class="d-inline">
            <div class="input-group justify-content-end">
                <select class="form-control" name="category_id" id="category_filter">
                    <option value="">@lang('Tất cả danh mục')</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request()->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn--primary input-group-text" type="button" id="filter_btn"><i class="las la-filter"></i></button>
            </div>
        </div>

        <form action="{{ route('admin.documents.index') }}" method="GET" class="form-inline float-sm-end">
            <div class="input-group justify-content-end">
                <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Tìm theo tiêu đề...')" value="{{ request()->search }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="las la-search"></i></button>
            </div>
        </form>
        
        <a href="{{ route('admin.documents.create') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
            <i class="fa fa-fw fa-plus"></i>@lang('Thêm mới')
        </a>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            
            $('#filter_btn').on('click', function() {
                var categoryId = $('#category_filter').val();
                var currentURL = new URL(window.location.href);
                
                if (categoryId) {
                    currentURL.searchParams.set('category_id', categoryId);
                } else {
                    currentURL.searchParams.delete('category_id');
                }
                
                window.location.href = currentURL.toString();
            });

        })(jQuery);
    </script>
@endpush 
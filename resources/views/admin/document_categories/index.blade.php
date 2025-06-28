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
                                    <th>@lang('Icon')</th>
                                    <th>@lang('Tên danh mục')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Trạng thái')</th>
                                    <th>@lang('Thao tác')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} fs-3"></i>
                                            @else
                                                <i class="las la-folder fs-3"></i>
                                            @endif
                                        </td>
                                        <td>{{ __($category->name) }}</td>
                                        <td>{{ Str::limit($category->description, 50) }}</td>
                                        <td>
                                            @if ($category->status == 1)
                                                <span class="badge badge--success">@lang('Kích hoạt')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Vô hiệu hóa')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary editBtn"
                                                    data-id="{{ $category->id }}"
                                                    data-name="{{ $category->name }}"
                                                    data-description="{{ $category->description }}"
                                                    data-icon="{{ $category->icon }}"
                                                    data-status="{{ $category->status }}">
                                                    <i class="las la-edit"></i> @lang('Edit')
                                                </button>

                                                @if ($category->status == 1)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.document.categories.status', $category->id) }}"
                                                        data-question="@lang('Bạn có chắc chắn muốn vô hiệu hóa danh mục này?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.document.categories.status', $category->id) }}"
                                                        data-question="@lang('Bạn có chắc chắn muốn kích hoạt danh mục này?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @endif
                                                
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.document.categories.destroy', $category->id) }}"
                                                    data-question="@lang('Bạn có chắc chắn muốn xóa danh mục này?')">
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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add New Modal -->
    <div id="createModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Thêm danh mục tài liệu mới')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.document.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Tên danh mục')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Icon')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="icon" placeholder="las la-folder">
                                <div class="input-group-append">
                                    <button class="btn btn--primary iconPicker" data-icon="las la-home" role="iconpicker"></button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                @lang('Sử dụng class icon từ LineAwesome. Ví dụ: las la-folder')
                            </small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Mô tả')</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('Trạng thái')</label>
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" data-width="100%" data-height="40px" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Kích hoạt')" data-off="@lang('Vô hiệu hóa')" name="status" value="1" checked>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Thêm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Cập nhật danh mục tài liệu')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Tên danh mục')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Icon')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="icon">
                                <div class="input-group-append">
                                    <button class="btn btn--primary iconPicker" data-icon="las la-home" role="iconpicker"></button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                @lang('Sử dụng class icon từ LineAwesome. Ví dụ: las la-folder')
                            </small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Mô tả')</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('Trạng thái')</label>
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" data-width="100%" data-height="40px" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Kích hoạt')" data-off="@lang('Vô hiệu hóa')" name="status" value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Cập nhật')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
<button class="btn btn-sm btn--primary box--shadow1 text--small addBtn">
    <i class="fa fa-fw fa-plus"></i>@lang('Thêm mới')
</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.addBtn').on('click', function() {
                var modal = $('#createModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                var modal = $('#editModal');
                var url = '{{ route("admin.document.categories.update", ":id") }}';
                var id = $(this).data('id');

                modal.find('form').attr('action', url.replace(':id', id));
                modal.find('input[name=name]').val($(this).data('name'));
                modal.find('textarea[name=description]').val($(this).data('description'));
                modal.find('input[name=icon]').val($(this).data('icon'));
                
                if ($(this).data('status') == 1) {
                    modal.find('input[name=status]').bootstrapToggle('on');
                } else {
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                modal.modal('show');
            });

            // Icon picker functionality
            $('.iconPicker').iconpicker({
                align: 'center',
                arrowClass: 'btn-danger',
                arrowPrevIconClass: 'fas fa-angle-left',
                arrowNextIconClass: 'fas fa-angle-right',
                cols: 10,
                footer: true,
                header: true,
                icon: 'las la-folder',
                iconset: 'lineawesome',
                labelHeader: '{0} of {1} pages',
                labelFooter: '{0} - {1} of {2} icons',
                placement: 'bottom',
                rows: 5,
                search: true,
                searchText: 'Search',
                selectedClass: 'btn-success',
                unselectedClass: ''
            }).on('change', function(e) {
                $(this).parent().siblings('input[name=icon]').val(e.icon);
            });

        })(jQuery);
    </script>
@endpush 
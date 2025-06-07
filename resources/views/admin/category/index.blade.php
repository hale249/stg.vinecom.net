@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Created At')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ __($category->name) }}</td>
                                    <td>@php echo $category->statusBadge @endphp</td>
                                    <td>{{ showDateTime($category->created_at) }}</td>
                                    <td>
                                        <div class="button-group">
                                            <button class="btn btn-outline--primary cuModalBtn btn-sm editBtn"
                                                    data-modal_title="@lang('Edit Category')"
                                                    data-resource="{{ $category }}"
                                            >
                                                <i class="las la-pen"></i>@lang('Edit')
                                            </button>

                                            @if ($category->status == Status::ENABLE)
                                                <button
                                                    class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                    data-question="@lang('Are you sure to disable this category?')"
                                                    data-action="{{ route('admin.category.status',$category->id) }}">
                                                    <i class="la la-eye-slash"></i> @lang('Disable')
                                                </button>
                                            @else
                                                <button
                                                    class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                                    data-question="@lang('Are you sure to enable this category?')"
                                                    data-action="{{ route('admin.category.status',$category->id) }}">
                                                    <i class="la la-eye"></i> @lang('Enable')
                                                </button>
                                            @endif
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


    <!-- Confirmation Modal Start -->
    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Add Category')</span></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.category.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang(' Name')</label>
                            <input name="name" type="text" class="form-control bg--white pe-2"
                                   placeholder="@lang(' Name')" autocomplete="off" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal/>
@endsection
@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
    <button class="btn btn-sm btn-outline--primary float-sm-end cuModalBtn addBtn"
            data-modal_title="@lang('Create New category')" type="button">
        <i class="las la-plus"></i>@lang('Add New')</button>
@endpush
@push('script-lib')
    <script src="{{ asset('assets/global/js/cu-modal.js') }}"></script>
@endpush

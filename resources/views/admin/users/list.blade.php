@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Email-Mobile')</th>
                                <th>@lang('Role')</th>
                                <th>@lang('Mã người dùng')</th>
                                <th>@lang('Joined At')</th>
                                <th>@lang('Balance')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="user">
                                            <div class="thumb me-2">
                                                <img
                                                    src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}"
                                                    alt="{{ __($user->fullname) }}" class="plugin_bg">
                                            </div>
                                            <div>
                                                <span class="fw-bold">{{ $user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                        <a
                                                            href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                                    </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        {{ $user->email }}<br>{{ $user->mobileNumber }}
                                    </td>
                                    <td>
                                        @if($user->is_staff)
                                            @if($user->role == 'sales_manager')
                                                <span class="badge badge--success"
                                                      style="font-size: 1.2em; padding: 0.5em 1em;">@lang('Quản lý')</span>
                                            @elseif($user->role == 'sales_staff')
                                                <span class="badge badge--info"
                                                      style="font-size: 1.2em; padding: 0.5em 1em;">@lang('Giám đốc')</span>
                                            @else
                                                <span class="badge badge--success"
                                                      style="font-size: 1.2em; padding: 0.5em 1em;">@lang('Staff')</span>
                                            @endif
                                        @else
                                            <span class="badge badge--primary"
                                                  style="font-size: 1.2em; padding: 0.5em 1em;">@lang('User')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_staff)
                                            <span class="fw-bold">{{ $user->referral_code }}</span>
                                        @else
                                            <span class="fw-bold">{{ $user->username }}</span>
                                        @endif
                                    </td>


                                    <td>
                                        {{ showDateTime($user->created_at) }} <br>
                                        {{ diffForHumans($user->created_at) }}
                                    </td>


                                    <td>
                                            <span class="fw-bold">

                                                {{ showAmount($user->balance) }}
                                            </span>
                                    </td>

                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.users.detail', $user->id) }}"
                                               class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
                                            @if (request()->routeIs('admin.users.kyc.pending'))
                                                <a href="{{ route('admin.users.kyc.details', $user->id) }}"
                                                   target="_blank" class="btn btn-sm btn-outline--dark">
                                                    <i class="las la-user-check"></i>@lang('KYC Data')
                                                </a>
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
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($users->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($users) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="createStaffModal" tabindex="-1" aria-labelledby="createStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="createStaffModalLabel">@lang('Tạo Quản lý/Giám đốc')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.staff.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="firstname" class="required">@lang('First Name')</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="lastname" class="required">@lang('Last Name')</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="required">@lang('Email')</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="required">@lang('Password')</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="role" class="required">@lang('Vai trò')</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="sales_manager">@lang('Quản lý')</option>
                                <option value="sales_staff">@lang('Giám đốc')</option>
                            </select>
                        </div>
                        <div class="form-group mb-3" id="manager-select-container" style="display: none;">
                            <label for="manager_id">@lang('Chọn quản lý')</label>
                            <select class="form-control" id="manager_id" name="manager_id">
                                <option value="">@lang('Chọn một quản lý')</option>
                                @foreach(\App\Models\User::salesManagers()->get() as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->fullname }} ({{ $manager->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email"/>
    <button class="btn btn-sm btn-outline--primary float-sm-end" data-bs-toggle="modal"
            data-bs-target="#createStaffModal" type="button">
        <i class="las la-plus"></i>@lang('Tạo Quản lý/Giám đốc')
    </button>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            // Show/hide manager select based on role
            $('#role').on('change', function() {
                if ($(this).val() === 'sales_staff') {
                    $('#manager-select-container').show();
                } else {
                    $('#manager-select-container').hide();
                }
            });

            // Handle form submission
            $('#createStaffModal form').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var submitBtn = form.find('button[type=submit]');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        if (response.success) {
                            $('#createStaffModal').modal('hide');
                            form.trigger('reset');
                            location.reload();
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
                                var input = form.find('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').remove();
                                input.after('<div class="invalid-feedback">' + errors[key][0] + '</div>');
                            });
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false);
                    }
                });
            });

            // Clear validation errors when modal is hidden
            $('#createStaffModal').on('hidden.bs.modal', function () {
                var form = $(this).find('form');
                form.trigger('reset');
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                $('#manager-select-container').hide();
            });

        })(jQuery);
    </script>
@endpush

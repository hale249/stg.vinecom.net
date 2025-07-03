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
                                            @if($user->role == 'sales_staff')
                                                <span class="badge badge--info"
                                                      style="font-size: 1.2em; padding: 0.5em 1em;">@lang('Quản lý kinh doanh')</span>
                                            @elseif($user->role == 'sales_manager')
                                                <span class="badge badge--primary"
                                                      style="font-size: 1.2em; padding: 0.5em 1em;">@lang('Giám đốc kinh doanh')</span>
                                            @elseif($user->role == 'sales_director')
                                                <span class="badge badge--warning"
                                                      style="font-size: 1.2em; padding: 0.5em 1em;">@lang('Giám đốc trung tâm')</span>
                                            @elseif($user->role == 'regional_director')
                                                <span class="badge badge--success"
                                                      style="font-size: 1.2em; padding: 0.5em 1em;">@lang('Giám đốc vùng')</span>
                                            @else
                                                <span class="badge badge--success"
                                                      style="font-size: 1.2em; padding: 0.5em 1em;">@lang('Staff')</span>
                                            @endif
                                            @if($user->position_level)
                                                <div class="mt-2">
                                                    <small class="text-muted">{{ $user->position_level }}</small>
                                                </div>
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
                                <option value="sales_staff">@lang('Quản lý kinh doanh')</option>
                                <option value="sales_manager">@lang('Giám đốc kinh doanh')</option>
                                <option value="sales_director">@lang('Giám đốc trung tâm')</option>
                                <option value="regional_director">@lang('Giám đốc vùng')</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="position_level" class="required">@lang('Chức danh')</label>
                            <select class="form-control" id="position_level" name="position_level" required>
                                <!-- Options will be dynamically populated based on role selection -->
                            </select>
                        </div>
                        <div class="form-group mb-3" id="manager-select-container">
                            <label for="manager_id" class="required">@lang('Cấp quản lý trực tiếp')</label>
                            <select class="form-control" id="manager_id" name="manager_id" required>
                                <option value="">@lang('Chọn quản lý trực tiếp')</option>
                                <!-- Options will be dynamically populated based on position level -->
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

            // Empty positions object to be filled from API
            let positionsByRole = {
                'sales_staff': [],
                'sales_manager': [],
                'sales_director': [],
                'regional_director': []
            };

            // Fetch positions from KPI data
            $.ajax({
                url: '{{ route("admin.users.list.positions") }}',
                type: 'GET',
                success: function(response) {
                    if (response.success && response.positionsByRole) {
                        positionsByRole = response.positionsByRole;
                        // If the form is already open, update the positions
                        if ($('#createStaffModal').hasClass('show')) {
                            loadPositions($('#role').val());
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching positions:', xhr);
                }
            });

            // Role hierarchy mapping (which roles can be managers of which roles)
            const managerRoleMapping = {
                'sales_staff': ['sales_manager'],
                'sales_manager': ['sales_director'],
                'sales_director': ['regional_director'],
                'regional_director': []
            };

            // Load positions based on selected role
            function loadPositions(roleValue) {
                const $positionSelect = $('#position_level');
                $positionSelect.empty();
                
                const positions = positionsByRole[roleValue] || [];
                
                $positionSelect.append('<option value="">-- Chọn chức danh --</option>');
                
                if (positions.length === 0) {
                    $positionSelect.append('<option value="" disabled>Không có chức danh nào được định nghĩa trong KPI</option>');
                } else {
                    positions.forEach(position => {
                        $positionSelect.append(`<option value="${position.value}">${position.label}</option>`);
                    });
                }
            }

            // Load potential managers based on selected role
            function loadManagers(roleValue) {
                const $managerContainer = $('#manager-select-container');
                const $managerSelect = $('#manager_id');
                $managerSelect.empty();
                
                // Get roles that can be managers for the selected role
                const managerRoles = managerRoleMapping[roleValue] || [];
                
                if (managerRoles.length === 0) {
                    // No managers for this role (regional_director), hide the select
                    $managerContainer.hide();
                    $managerSelect.prop('required', false);
                    return;
                }
                
                // Show the container and add a default option
                $managerContainer.show();
                $managerSelect.prop('required', true);
                $managerSelect.append('<option value="">-- Chọn quản lý trực tiếp --</option>');
                
                // Load managers via AJAX based on the potential manager roles
                $.ajax({
                    url: '{{ route("admin.users.list.managers") }}',
                    type: 'GET',
                    data: {
                        roles: managerRoles
                    },
                    success: function(response) {
                        if (response.success && response.managers) {
                            response.managers.forEach(manager => {
                                $managerSelect.append(`<option value="${manager.id}">${manager.fullname} (${manager.position_level}) - ${manager.email}</option>`);
                            });
                        }
                    },
                    error: function() {
                        // Show error if managers couldn't be loaded
                        $managerSelect.append('<option value="">Error loading managers</option>');
                    }
                });
            }

            // Initialize position and manager options on role change
            $('#role').on('change', function() {
                const roleValue = $(this).val();
                loadPositions(roleValue);
                loadManagers(roleValue);
            });

            // Trigger on page load to initialize form
            $('#role').trigger('change');

            // Handle form submission
            $('#createStaffModal form').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var submitBtn = form.find('button[type=submit]');
                submitBtn.prop('disabled', true);
                
                // Validate form fields
                let isValid = true;
                
                // Make sure position_level is selected
                const positionValue = $('#position_level').val();
                if (!positionValue) {
                    form.find('.position_level-error').remove();
                    $('#position_level').after('<div class="invalid-feedback position_level-error">Vui lòng chọn chức danh</div>');
                    $('#position_level').addClass('is-invalid');
                    isValid = false;
                }
                
                // Make sure manager_id is selected if the field is visible and required
                const managerField = $('#manager_id');
                if ($('#manager-select-container').is(':visible') && managerField.prop('required') && !managerField.val()) {
                    form.find('.manager_id-error').remove();
                    $('#manager_id').after('<div class="invalid-feedback manager_id-error">Vui lòng chọn quản lý trực tiếp</div>');
                    $('#manager_id').addClass('is-invalid');
                    isValid = false;
                }
                
                // Don't submit if validation fails
                if (!isValid) {
                    submitBtn.prop('disabled', false);
                    return false;
                }

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
                $('#role').trigger('change');
            });

        })(jQuery);
    </script>
@endpush

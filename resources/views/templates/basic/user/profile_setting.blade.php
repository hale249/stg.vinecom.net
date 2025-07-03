@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner__block">
        <form class="register" method="post" enctype="multipart/form-data">
            @csrf
            <div class="dashboard-card">
                <div class="dashboard-card__header">
                    <div class="user-info">
                        <div class="user-info__thumb">
                            <img id="image-preview"
                                src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile'), avatar: true) }}"
                                alt="@lang('User Profile Image')">

                            <div class="user-thumb-edit">
                                <input class="d-none" type="file" name="image" accept=".png, .jpg, .jpeg"
                                    id="user-thumb-edit-input">

                                <label class="user-thumb-edit__btn" role="button" for="user-thumb-edit-input">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>

                        </div>

                        <div class="user-info__content">
                            <h6 class="user-info__name">{{ __($user->fullName) }}</h6>
                            <span class="user-info__email">{{ $user->email }}</span>
                        </div>
                    </div>

                </div>
                <div class="dashboard-card__body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('First Name')</label>
                            <input class="form-control form--control" type="text" name="firstname"
                                value="{{ $user->firstname }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Last Name')</label>
                            <input class="form-control form--control" type="text" name="lastname"
                                value="{{ $user->lastname }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('E-mail Address')</label>
                            <input class="form-control form--control" value="{{ $user->email }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('Mobile Number')</label>
                            <input class="form-control form--control" value="{{ $user->mobile }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Address')</label>
                            <input class="form-control form--control" type="text" name="address"
                                value="{{ @$user->address }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Zip Code')</label>
                            <input class="form-control form--control" type="text" name="zip"
                                value="{{ @$user->zip }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('City')</label>
                            <input class="form-control form--control" type="text" name="city"
                                value="{{ @$user->city }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form--label">@lang('Country')</label>
                            <input class="form-control form--control" value="{{ @$user->country_name }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Ngày sinh')</label>
                            <input class="form-control form--control datepicker-here" type="text" name="date_of_birth" value="{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') : '' }}" data-date-format="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('CC/CCCD số')</label>
                            <input class="form-control form--control" type="text" name="id_number" value="{{ $user->id_number }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Cấp ngày')</label>
                            <input class="form-control form--control datepicker-here" type="text" name="id_issue_date" value="{{ $user->id_issue_date ? \Carbon\Carbon::parse($user->id_issue_date)->format('d/m/Y') : '' }}" data-date-format="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Nơi cấp')</label>
                            <input class="form-control form--control" type="text" name="id_issue_place" value="{{ $user->id_issue_place }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('CCCD/CMT Mặt trước')</label>
                            <input class="form-control form--control id-card-input" type="file" id="id_card_front_input" accept=".jpg,.jpeg,.png">
                            <input type="hidden" name="id_card_front_base64" id="id_card_front_base64">
                            @if($user->id_card_front)
                                <div class="mt-2">
                                    <img src="{{ getImage(getFilePath('idCards') . '/' . $user->id_card_front, '400x250') }}" alt="ID Card Front" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('CCCD/CMT Mặt sau')</label>
                            <input class="form-control form--control id-card-input" type="file" id="id_card_back_input" accept=".jpg,.jpeg,.png">
                            <input type="hidden" name="id_card_back_base64" id="id_card_back_base64">
                            @if($user->id_card_back)
                                <div class="mt-2">
                                    <img src="{{ getImage(getFilePath('idCards') . '/' . $user->id_card_back, '400x250') }}" alt="ID Card Back" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Số tài khoản')</label>
                            <input class="form-control form--control" type="text" name="bank_account_number" value="{{ $user->bank_account_number }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Ngân hàng')</label>
                            <input class="form-control form--control" type="text" name="bank_name" value="{{ $user->bank_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Chi nhánh')</label>
                            <input class="form-control form--control" type="text" name="bank_branch" value="{{ $user->bank_branch }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Tên chủ tài khoản')</label>
                            <input class="form-control form--control" type="text" name="bank_account_holder" value="{{ $user->bank_account_holder }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form--label">@lang('Mã số thuế TNCN')</label>
                            <input class="form-control form--control" type="text" name="tax_number" value="{{ $user->tax_number }}">
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <button class="btn btn--lg btn--base btn--action w-100"
                                type="submit">@lang('Submit')</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Image Cropper Modal -->
    <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropperModalLabel">Cắt ảnh CCCD/CMT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <img id="cropperImage" src="" alt="Ảnh cần cắt">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="btn btn--base" id="cropButton">Cắt ảnh</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
@endpush

@push('style')
<style>
    .img-container {
        max-height: 500px;
        width: 100%;
        margin: 0 auto;
    }
    #cropperImage {
        display: block;
        max-width: 100%;
    }
    .cropper-container {
        margin: 0 auto;
    }
</style>
@endpush

@push('script')
    <script>
        document.getElementById('user-thumb-edit-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        (function($) {
            "use strict"
            
            $('.datepicker-here').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });

            $('.datepicker-here').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY'));
            });

            // Image Cropper functionality
            let cropper;
            let currentInput;

            $('.id-card-input').on('change', function(e) {
                if (e.target.files.length) {
                    currentInput = $(this).attr('id');
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#cropperImage').attr('src', e.target.result);
                        $('#cropperModal').modal('show');
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });

            $('#cropperModal').on('shown.bs.modal', function () {
                cropper = new Cropper(document.getElementById('cropperImage'), {
                    aspectRatio: 16 / 10,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            });

            $('#cropperModal').on('hidden.bs.modal', function () {
                cropper.destroy();
                cropper = null;
            });

            $('#cropButton').on('click', function () {
                if (cropper) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 400,
                        height: 250
                    });
                    
                    const base64 = canvas.toDataURL('image/jpeg');
                    
                    if (currentInput === 'id_card_front_input') {
                        $('#id_card_front_base64').val(base64);
                    } else if (currentInput === 'id_card_back_input') {
                        $('#id_card_back_base64').val(base64);
                    }
                    
                    $('#cropperModal').modal('hide');
                    
                    // Show preview of cropped image
                    const previewDiv = $('<div class="mt-2"><img src="' + base64 + '" alt="ID Card" class="img-thumbnail" style="max-height: 100px;"></div>');
                    
                    if (currentInput === 'id_card_front_input') {
                        $('#id_card_front_input').next().next('.mt-2').remove();
                        $('#id_card_front_input').next().after(previewDiv);
                    } else if (currentInput === 'id_card_back_input') {
                        $('#id_card_back_input').next().next('.mt-2').remove();
                        $('#id_card_back_input').next().after(previewDiv);
                    }
                }
            });

        })(jQuery)
    </script>
@endpush

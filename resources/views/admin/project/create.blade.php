@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.project.store', $project->id ?? null) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('admin.project.form')
                        <button type="submit" class="btn btn--primary w-100 h-45">
                            @lang('Submit')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.project.index') }}" />
@endpush
@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/image-uploader.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/image-uploader.min.js') }}"></script>
@endpush

@push('script')
    <script>
        // Global money formatting functions
        window.formatMoney = function(amount) {
            if (!amount) return '';
            let num = amount.toString().replace(/[^\d]/g, '');
            num = parseFloat(num) || 0;
            return num.toLocaleString('vi-VN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        };

        window.unformatMoney = function(formattedAmount) {
            if (!formattedAmount) return 0;
            return parseFloat(formattedAmount.toString().replace(/\./g, '')) || 0;
        };
    </script>
    
    <script>
        (function($) {
            "use strict";
            
            $(document).ready(function() {
                // Format money inputs on page load
                $('.money-input').each(function() {
                    let value = $(this).val();
                    if (value) {
                        $(this).val(formatMoney(value));
                    }
                });

                // Simple money input handling - format on blur, allow typing
                $('.money-input').on('input', function(e) {
                    let input = this;
                    let value = input.value;
                    
                    // Only allow numeric characters and dots
                    let cleanValue = value.replace(/[^\d.]/g, '');
                    
                    // Prevent multiple consecutive dots
                    cleanValue = cleanValue.replace(/\.+/g, '.');
                    
                    // Update value if different
                    if (value !== cleanValue) {
                        input.value = cleanValue;
                    }
                });

                // Format on blur (when user leaves the input)
                $('.money-input').on('blur', function() {
                    let input = this;
                    let value = input.value;
                    let formatted = formatMoney(value);
                    input.value = formatted;
                });

                // Handle paste events
                $('.money-input').on('paste', function(e) {
                    let input = this;
                    setTimeout(() => {
                        let value = input.value;
                        let numericValue = value.replace(/[^\d]/g, '');
                        let formatted = formatMoney(numericValue);
                        input.value = formatted;
                    }, 10);
                });

                // Before form submission, convert formatted values back to numbers
                $('form').on('submit', function() {
                    $('.money-input').each(function() {
                        let numericValue = unformatMoney($(this).val());
                        $(this).val(numericValue);
                    });
                });

                // Date ranger Start here
                let maxYear = new Date().getFullYear() + 10;

                $('input[name="start_date"],input[name="end_date"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    minYear: 2020,
                    maxYear: maxYear,
                    applyButtonClasses: 'btn btn--primary',
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
                // Image Uploader Gallery Start Here
                let preloaded = [];
                @if (!empty($galleries))
                    preloaded = @json($galleries);
                @endif

                $('.input-images').imageUploader({
                    preloaded: preloaded,
                    imagesInputName: 'gallery',
                    preloadedInputName: 'old',
                    maxFiles: 4
                });
                $(document).on('input', 'input[name="gallery[]"]', function() {
                    var fileUpload = $("input[type='file']");
                    if (parseInt(fileUpload.get(0).files.length) > 4) {
                        notify('error', 'You can only upload 4 images');
                    }
                });


                $('.return_timespan').hide();

                function toggleFields() {
                    var returnType = $('select[name="return_type"]').val();

                    if (returnType === '-1') {
                        $('.return_timespan').hide().closest('.form-group').find('input').attr('required', false);
                        $('.return-type-wrapper').removeClass('col-md-4').addClass('col-md-6');
                        $('.time-settings-wrapper').removeClass('col-md-4').addClass('col-md-6');
                        $('.capital_back-wrapper').addClass('d-none');
                        $('.category-wrapper').removeClass('col-md-4').addClass('col-md-6');
                        $('.map-wrapper').removeClass('col-md-4').addClass('col-md-6');
                    } else if (returnType === '2') {
                        $('.return_timespan').show().closest('.form-group').find('input').attr('required', true);
                        $('.return-type-wrapper').removeClass('col-md-6').addClass('col-md-4');
                        $('.time-settings-wrapper').removeClass('col-md-6').addClass('col-md-4');
                        $('.capital_back-wrapper').removeClass('d-none');
                        $('.category-wrapper').removeClass('col-md-6').addClass('col-md-4');
                        $('.map-wrapper').removeClass('col-md-6').addClass('col-md-4');
                    }
                }

                // Call the function on page load
                toggleFields();

                // Call the function every time the return type changes
                $('select[name="return_type"]').change(function() {
                    toggleFields();
                });


                function clearIfGoalEmpty(goal) {
                    return false;
                }

                function calculateShareAmount() {
                    let goal = unformatMoney($('.target_amount').val().trim());
                    let shareCount = $('.share_count').val().trim();
                    let invalidInputPattern = /^-|\b0[0-9]/;

                    if (clearIfGoalEmpty(goal)) {
                        return;
                    }

                    goal = parseFloat(goal);
                    shareCount = parseFloat(shareCount);

                    if (!isNaN(goal) && !isNaN(shareCount) && shareCount > 0) {
                        let shareAmount = Math.round(goal / shareCount);
                        if (shareAmount <= 0.00) {
                            $('.share_amount').val('');
                        } else {
                            $('.share_amount').val(formatMoney(shareAmount));
                        }
                    } else {
                        $('.share_amount').val('');
                    }
                }

                $('.share_count, .target_amount').on('input', function(e) {
                    calculateShareAmount();
                });

                function calculateShareCount() {
                    let goal = unformatMoney($('.target_amount').val().trim());
                    let shareAmount = unformatMoney($('.share_amount').val().trim());
                    let invalidInputPattern = /^-|\b0[0-9]/;

                    if (clearIfGoalEmpty(goal)) {
                        return;
                    }

                    if (invalidInputPattern.test(goal) || invalidInputPattern.test(shareAmount)) {
                        return;
                    }

                    goal = parseFloat(goal);
                    shareAmount = parseFloat(shareAmount);

                    if (!isNaN(goal) && !isNaN(shareAmount) && shareAmount > 0) {
                        let shareCount = Math.round(goal / shareAmount);
                        if (shareCount <= 0.00) {
                            notify('error', 'Share count must be greater than 0');
                            $('.share_count').val('');
                        } else {
                            $('.share_count').val(shareCount.toFixed(2));
                        }
                    } else {
                        $('.share_count').val('');
                    }
                }

                $('.target_amount, .share_amount').on('input', function(e) {
                    calculateShareCount();
                });

                function calculateRoiAmount() {
                    let goal = unformatMoney($('.share_amount').val().trim());
                    let roi = $('.roi_percentage').val().trim();
                    let invalidInputPattern = /^-|\b0[0-9]/;


                    if (clearIfGoalEmpty(goal)) {
                        notify('error', 'Please enter project goal value first');
                    }
                    if (invalidInputPattern.test(roi)) {
                        notify('error', 'Please enter valid values');
                        return;
                    }

                    goal = parseFloat(goal);
                    roi = parseFloat(roi);
                    if (!isNaN(goal) && !isNaN(roi) && roi > 0) {
                        let roiAmount = (goal * roi) / 100;
                        if (roiAmount <= 0.00) {
                            notify('error', 'ROI amount must be greater than 0');
                            $('.roi_amount').val('');
                        } else {
                            $('.roi_amount').val(formatMoney(roiAmount));
                        }
                    } else {
                        $('.roi_amount').val('');
                    }

                }

                $('.roi_percentage, .share_amount').on('input', function(e) {
                    calculateRoiAmount();
                });

                function calculateRoiPercentage() {
                    let goal = unformatMoney($('.share_amount').val().trim());
                    let roiAmount = unformatMoney($('.roi_amount').val().trim());
                    let invalidInputPattern = /^-|\b0[0-9]/;

                    if (clearIfGoalEmpty(goal)) {
                        notify('error', 'Please enter project goal value first');
                        return;
                    }
                    if (invalidInputPattern.test(roiAmount)) {
                        notify('error', 'Please enter valid values');
                        return;
                    }

                    goal = parseFloat(goal);
                    roiAmount = parseFloat(roiAmount);
                    if (!isNaN(goal) && !isNaN(roiAmount) && roiAmount > 0) {
                        let roiPercentage = (roiAmount * 100) / goal;
                        if (roiPercentage <= 0.00) {
                            notify('error', 'ROI percentage must be greater than 0');
                            $('.roi_percentage').val('');
                        } else {
                            $('.roi_percentage').val(roiPercentage.toFixed(2));
                        }
                    } else {
                        $('.roi_percentage').val('');
                    }
                }

                $('.roi_amount, .share_amount').on('input', function(e) {
                    calculateRoiPercentage();
                });

                // Slug Code
                $('.buildSlug').on('click', function() {
                    let closestForm = $(this).closest('form');
                    let title = closestForm.find('[name=title]').val();
                    closestForm.find('[name=slug]').val(title);
                    closestForm.find('[name=slug]').trigger('input');
                });

                $('[name=slug]').on('input', function() {
                    let projectId = '{{ @$project->id ?? 0 }}';
                    let closestForm = $(this).closest('form');
                    closestForm.find('[type=submit]').addClass('disabled')
                    let slug = $(this).val();
                    slug = slug.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                    $(this).val(slug);
                    if (slug) {
                        $('.slug-verification').removeClass('d-none');
                        $('.slug-verification').html(`
                        <small class="text--info"><i class="las la-spinner la-spin"></i> @lang('Checking')</small>
                    `);
                        $.get("{{ route('admin.project.check.slug') }}", {
                            slug: slug,
                            id: projectId,
                        }, function(response) {
                            if (!response.exists) {
                                $('.slug-verification').html(`
                                <small class="text--success"><i class="las la-check"></i> @lang('Available')</small>
                            `);
                                closestForm.find('[type=submit]').removeClass('disabled')
                            }
                            if (response.exists) {
                                $('.slug-verification').html(`
                                <small class="text--danger"><i class="las la-times"></i> @lang('Slug already exists')</small>
                            `);
                            }
                        });
                    } else {
                        $('.slug-verification').addClass('d-none');
                    }
                });
            });
        })(jQuery);

        // Project calculation script
        document.addEventListener("DOMContentLoaded", function() {
            // Debug: Ensure share_count input is visible
            const shareCountInput = document.getElementById('share_count');
            if (shareCountInput) {
                console.log('Share count input found:', shareCountInput);
                shareCountInput.style.display = 'block';
                shareCountInput.style.visibility = 'visible';
                shareCountInput.style.opacity = '1';
            } else {
                console.error('Share count input not found!');
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Get form elements
            const targetAmountInput = document.getElementById('target_amount');
            const shareAmountInput = document.getElementById('share_amount');
            const calculateBtn = document.getElementById('calculate-btn');
            const summaryText = document.getElementById('summary-text');
            const validationAlert = document.getElementById('validation-alert');
            const validationText = document.getElementById('validation-text');
            const calculationModes = document.querySelectorAll('.calculation-mode');

            // Format number with commas
            function formatNumber(num) {
                return new Intl.NumberFormat('vi-VN').format(num);
            }

            // Parse number from formatted string
            function parseNumber(str) {
                if (!str) return 0;
                // Remove all dots and convert to number
                return parseFloat(str.toString().replace(/\./g, '')) || 0;
            }

            // Calculate values based on selected mode
            function calculateValues() {
                const targetAmount = unformatMoney(targetAmountInput.value);
                const shareCount = parseNumber(shareCountInput.value);
                const shareAmount = unformatMoney(shareAmountInput.value);
                const selectedMode = document.querySelector('input[name="calculation_mode"]:checked').value;

                // Debug logging
                console.log('Debug - Target Amount:', targetAmountInput.value, '->', targetAmount);
                console.log('Debug - Share Count:', shareCountInput.value, '->', shareCount);
                console.log('Debug - Share Amount:', shareAmountInput.value, '->', shareAmount);
                console.log('Debug - Selected Mode:', selectedMode);

                let calculatedTargetAmount = targetAmount;
                let calculatedShareCount = shareCount;
                let calculatedShareAmount = shareAmount;
                let calculationMessage = '';
                let validationMessage = '';

                // Hide validation alert initially
                validationAlert.style.display = 'none';

                switch (selectedMode) {
                    case 'target_share':
                        // Calculate share_amount from target_amount and share_count
                        if (targetAmount > 0 && shareCount > 0) {
                            calculatedShareAmount = targetAmount / shareCount;
                            console.log('Debug - Calculated Share Amount:', calculatedShareAmount);
                            
                            // Format the result for display
                            let formattedShareAmount = formatMoney(calculatedShareAmount);
                            shareAmountInput.value = formattedShareAmount;
                            
                            calculationMessage = `Mục tiêu: ${formatMoney(targetAmount)} VNĐ ÷ ${formatNumber(shareCount)} suất = ${formattedShareAmount} VNĐ/suất`;
                        } else {
                            validationMessage = 'Vui lòng nhập cả Mục tiêu dự án và Số lượng chia sẻ để tính toán';
                        }
                        break;

                    case 'target_amount':
                        // Calculate share_count from target_amount and share_amount
                        if (targetAmount > 0 && shareAmount > 0) {
                            calculatedShareCount = Math.round(targetAmount / shareAmount);
                            shareCountInput.value = calculatedShareCount;
                            calculationMessage = `Mục tiêu: ${formatMoney(targetAmount)} VNĐ ÷ ${formatMoney(shareAmount)} VNĐ/suất = ${formatNumber(calculatedShareCount)} suất`;
                            
                            // Check if calculation is exact
                            const exactAmount = calculatedShareCount * shareAmount;
                            if (Math.abs(exactAmount - targetAmount) > 0.01) {
                                validationMessage = `Lưu ý: Số lượng suất đã được làm tròn từ ${(targetAmount / shareAmount).toFixed(2)} thành ${calculatedShareCount}. Mục tiêu thực tế sẽ là ${formatMoney(exactAmount)} VNĐ`;
                            }
                        } else {
                            validationMessage = 'Vui lòng nhập cả Mục tiêu dự án và Giá mỗi suất để tính toán';
                        }
                        break;

                    case 'share_amount':
                        // Calculate target_amount from share_count and share_amount
                        if (shareCount > 0 && shareAmount > 0) {
                            calculatedTargetAmount = shareCount * shareAmount;
                            targetAmountInput.value = formatMoney(calculatedTargetAmount);
                            calculationMessage = `${formatNumber(shareCount)} suất × ${formatMoney(shareAmount)} VNĐ/suất = ${formatMoney(calculatedTargetAmount)} VNĐ`;
                        } else {
                            validationMessage = 'Vui lòng nhập cả Số lượng chia sẻ và Giá mỗi suất để tính toán';
                        }
                        break;
                }

                // Update summary
                if (calculationMessage) {
                    summaryText.innerHTML = calculationMessage;
                } else {
                    summaryText.innerHTML = 'Vui lòng nhập các giá trị để xem tóm tắt tính toán';
                }

                // Show validation message if any
                if (validationMessage) {
                    validationText.innerHTML = validationMessage;
                    validationAlert.style.display = 'block';
                }

                // Update final calculation summary
                updateFinalSummary();
            }

            // Update final calculation summary
            function updateFinalSummary() {
                const targetAmount = unformatMoney(targetAmountInput.value);
                const shareCount = parseNumber(shareCountInput.value);
                const shareAmount = unformatMoney(shareAmountInput.value);

                if (targetAmount > 0 && shareCount > 0 && shareAmount > 0) {
                    const calculatedTarget = shareCount * shareAmount;
                    const difference = Math.abs(calculatedTarget - targetAmount);

                    if (difference > 0.01) {
                        summaryText.innerHTML += `<br><span class="text-warning"><i class="las la-exclamation-triangle"></i> Cảnh báo: Mục tiêu nhập (${formatMoney(targetAmount)}) khác với tính toán (${formatMoney(calculatedTarget)}). Chênh lệch: ${formatMoney(difference)} VNĐ</span>`;
                    } else {
                        summaryText.innerHTML += `<br><span class="text-success"><i class="las la-check"></i> Tính toán chính xác!</span>`;
                    }
                }
            }

            // Auto-calculate when inputs change
            function setupAutoCalculation() {
                const inputs = [targetAmountInput, shareCountInput, shareAmountInput];
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        // Temporarily disable auto-calculation to prevent interference
                        // clearTimeout(input.calculationTimeout);
                        // input.calculationTimeout = setTimeout(calculateValues, 500);
                    });
                });
            }

            // Calculate button click
            if (calculateBtn) {
                calculateBtn.addEventListener('click', calculateValues);
            }

            // Calculation mode change
            calculationModes.forEach(mode => {
                mode.addEventListener('change', function() {
                    calculateValues();
                });
            });

            // Initialize
            setupAutoCalculation();
            calculateValues();
        });
    </script>
@endpush

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <div class="image-upload">
                <div class="thumb">
                    <div class="avatar-preview">
                        <x-image-uploader image="{{ @$project->image }}" class="w-100" type="project" :required=false />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label>@lang('Title')</label>
            <a href="javascript:void(0)" class="float-end buildSlug"><i class="las la-link"></i> @lang('Make Slug')</a>

            <input type="text" class="form-control" name="title" value="{{ old('title', @$project->title) }}"
                placeholder="@lang('Title')" required>
        </div>
        <div class="form-group">
            <div class="d-flex justify-content-between">
                <label> @lang('Slug')</label>
                <div class="slug-verification d-none"></div>
            </div>
            <input type="text" class="form-control" name="slug" value="{{ old('slug', @$project->slug) }}"
                required>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        @lang('Mục tiêu dự án (Target Amount)')
                        <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Tổng số tiền cần huy động cho dự án. Hệ thống sẽ tự động tính toán dựa trên số lượng chia sẻ và giá mỗi suất."></i>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control target_amount money-input" name="target_amount" id="target_amount"
                            value="{{ old('target_amount', isset($project) ? number_format(getAmount($project->share_amount * $project->share_count), 0, ',', '.') : '') }}"
                            placeholder="@lang('Nhập mục tiêu dự án')" required>
                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                    </div>
                    <small class="form-text text-muted">Nhập tổng số tiền cần huy động</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        @lang('Featured')
                        <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Highlighted or special investment opportunities."></i>
                    </label>
                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="featured"
                        value="1" @if (old('featured', @$project->featured)) checked @endif>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calculation Control Panel -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="las la-calculator"></i> 
                    @lang('Cách tính toán tự động')
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input calculation-mode" type="radio" name="calculation_mode" id="mode_target_share" value="target_share" checked>
                            <label class="form-check-label" for="mode_target_share">
                                <strong>Mục tiêu + Số lượng chia sẻ</strong><br>
                                <small class="text-muted">Tự động tính giá mỗi suất</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input calculation-mode" type="radio" name="calculation_mode" id="mode_target_amount" value="target_amount">
                            <label class="form-check-label" for="mode_target_amount">
                                <strong>Mục tiêu + Giá mỗi suất</strong><br>
                                <small class="text-muted">Tự động tính số lượng chia sẻ</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input calculation-mode" type="radio" name="calculation_mode" id="mode_share_amount" value="share_amount">
                            <label class="form-check-label" for="mode_share_amount">
                                <strong>Số lượng chia sẻ + Giá mỗi suất</strong><br>
                                <small class="text-muted">Tự động tính mục tiêu dự án</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="share_count">
                <strong>Số lượng chia sẻ (Share Count)</strong>
            </label>
            <input type="number" class="form-control" name="share_count" id="share_count"
                value="{{ isset($project) ? getAmount($project->share_count) : old('share_count') }}"
                placeholder="Nhập số lượng chia sẻ" min="1" required>
            <small class="form-text text-muted">Tổng số suất đầu tư có thể chia nhỏ</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>
                @lang('Giá mỗi suất (Share Amount)')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Giá của mỗi suất đầu tư. Hệ thống sẽ tự động tính toán dựa trên mục tiêu dự án và số lượng chia sẻ."></i>
            </label>
            <div class="input-group">
                <input type="text" class="form-control share_amount money-input" name="share_amount" id="share_amount"
                    value="{{ old('share_amount', number_format(getAmount(@$project->share_amount), 0, ',', '.')) }}" 
                    placeholder="@lang('Nhập giá mỗi suất')" required>
                <span class="input-group-text">{{ gs('cur_text') }}</span>
            </div>
            <small class="form-text text-muted">Giá của mỗi suất đầu tư</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>
                @lang('Tính toán tự động')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Nhấn để tính toán lại các giá trị dựa trên mode đã chọn"></i>
            </label>
            <button type="button" class="btn btn-primary w-100" id="calculate-btn">
                <i class="las la-calculator"></i> @lang('Tính toán')
            </button>
        </div>
    </div>
</div>

<!-- Calculation Summary -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info" id="calculation-summary">
            <div class="d-flex align-items-center">
                <i class="las la-calculator me-2"></i>
                <div>
                    <strong>@lang('Tóm tắt tính toán:')</strong>
                    <span id="summary-text">@lang('Vui lòng nhập các giá trị để xem tóm tắt tính toán')</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation Alert -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-warning" id="validation-alert" style="display: none;">
            <div class="d-flex align-items-center">
                <i class="las la-exclamation-triangle me-2"></i>
                <div>
                    <strong>@lang('Lưu ý:')</strong>
                    <span id="validation-text"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>
                @lang('ROI (in %) ')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="The expected percentage return on investment."></i>
            </label>
            <div class="input-group">
                <input type="number" class="form-control roi_percentage" name="roi_percentage"
                    value="{{ old('roi_percentage', getAmount(@$project->roi_percentage)) }}"
                    placeholder="@lang('ROI percentage')" step="any" required>
                <span class="input-group-text">@lang('%')</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>
                @lang('ROI (in Amount)')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="The projected monetary return from the investment."></i>
            </label>
            <div class="input-group">
                <input type="text" class="form-control roi_amount money-input" name="roi_amount"
                    value="{{ old('roi_amount', number_format(getAmount(@$project->roi_amount), 0, ',', '.')) }}" 
                    placeholder="@lang('ROI Amount')" required>
                <span class="input-group-text">{{ gs('cur_text') }}</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>
                @lang('Start Date')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="The date when the investment project begins."></i>
            </label>
            <input type="text" class="form-control start_date" name="start_date"
                value="{{ old('start_date', @$project->start_date ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>
                @lang('End Date')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="The date when the investment project concludes."></i>
            </label>
            <input type="text" class="form-control end_date" name="end_date"
                value="{{ old('end_date', isset($project) ? $project->end_date : '') }}" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>
                @lang('Maturity Time')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Users will begin to receive their investment returns after the maturity period. The maturity time is calculated from the project's end date. It is the total duration from the end date plus the specified maturity time."></i>
            </label>
            <div class="input-group">
                <input type="number" class="form-control maturity_time" name="maturity_time"
                    value="{{ old('maturity_time', @$project->maturity_time) }}" step="0" required>
                <span class="input-group-text">@lang('Months')</span>
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-6 return-type-wrapper">
        <label>
            @lang('Return Type')
            <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                title="The form in which the returns are provided."></i>
        </label>
        <select class="form-control select2" name="return_type" data-search="false" required>
            <option value="" selected disabled>@lang('Select Return Type')</option>
            <option value="-1" @selected(old('return_type', @$project->return_type) == -1 ? 'selected' : '')>@lang('Lifetime')</option>
            <option value="2" @selected(old('return_type', @$project->return_type) == 2 ? 'selected' : '')>@lang('Repeat')</option>
        </select>
    </div>
    <div class="col-md-6 time-settings-wrapper">
        <div class="form-group">
            <label>
                @lang('Time')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="The specific timeframe for receiving returns."></i>
            </label>
            <select class="form-control select2" name="time_id" data-search="false" required>
                <option value="" selected disabled>@lang('Select Time')</option>
                @foreach ($times as $time)
                    <option value="{{ $time->id }}" @selected(old('time_id', $project->time_id ?? null) == $time->id)>
                        {{ $time->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4 return_timespan">
        <div class="form-group">
            <label>
                @lang('Return Repeat Times')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="The number of times returns will be repeated."></i>
            </label>
            <div class="input-group">
                <input type="number" class="form-control return_timespan" id="repeat_times" name="repeat_times"
                    value="{{ old('repeat_times', @$project->repeat_times) }}" step="0" required>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4 category-wrapper">
        <div class="form-group">
            <label>@lang('Category')</label>
            <select class="form-control select2" name="category_id" data-search="true" required>
                <option value="" selected disabled>@lang('Select Category')</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $project->category_id ?? null) == $category->id)>
                        {{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4 map-wrapper">
        <div class="form-group">
            <label>
                @lang('Google Map Embed URL')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="URL for embedding the project's location on Google Maps."></i>
            </label>
            <input type="text" class="form-control" name="map_url"
                value="{{ old('map_url', @$project->map_url) }}" required>
        </div>
    </div>
    <div class="col-md-4 capital_back-wrapper">
        <div class="form-group">
            <label>
                @lang('Capital Back')
                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Indicates if the invested capital is returned after maturity."></i>
            </label>
            <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')"
                name="capital_back" value="1" @if (old('capital_back', @$project->capital_back)) checked @endif>
        </div>
    </div>
</div>
<div class="form-group">
    <label>@lang('Description')</label>
    <textarea rows="5" class="form-control nicEdit" name="description">{{ old('description', @$project->description) }}</textarea>
</div>
<div class="form-group">
    <div class="image-uploader-wrapper">
        <div class="gallery-uploader">
            <label class="form-label required">@lang('Gallery Image :') </label>
            <div class="input-field">
                <div class="input-images"></div>
                <small class="form-text text-muted">
                    <label><i class="las la-info-circle"></i> @lang('You can upload up to 4 images. For the best design result, it\'s recommended to upload all 4 images').</label>
                    @lang('Supported Files:')
                    <b>@lang('.png, .jpg, .jpeg')</b>
                    @lang('Image will be resized into') <b>{{ getFileSize('project') }}</b>@lang('px')
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Get form elements
        const targetAmountInput = document.getElementById('target_amount');
        const shareCountInput = document.getElementById('share_count');
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
            return parseFloat(str.replace(/[^\d.-]/g, '')) || 0;
        }

        // Calculate values based on selected mode
        function calculateValues() {
            const targetAmount = parseNumber(targetAmountInput.value);
            const shareCount = parseNumber(shareCountInput.value);
            const shareAmount = parseNumber(shareAmountInput.value);
            const selectedMode = document.querySelector('input[name="calculation_mode"]:checked').value;

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
                        shareAmountInput.value = calculatedShareAmount.toFixed(2);
                        calculationMessage = `Mục tiêu: ${formatNumber(targetAmount)} ${gs('cur_text')} ÷ ${formatNumber(shareCount)} suất = ${formatNumber(calculatedShareAmount)} ${gs('cur_text')}/suất`;
                    } else {
                        validationMessage = 'Vui lòng nhập cả Mục tiêu dự án và Số lượng chia sẻ để tính toán';
                    }
                    break;

                case 'target_amount':
                    // Calculate share_count from target_amount and share_amount
                    if (targetAmount > 0 && shareAmount > 0) {
                        calculatedShareCount = Math.round(targetAmount / shareAmount);
                        shareCountInput.value = calculatedShareCount;
                        calculationMessage = `Mục tiêu: ${formatNumber(targetAmount)} ${gs('cur_text')} ÷ ${formatNumber(shareAmount)} ${gs('cur_text')}/suất = ${formatNumber(calculatedShareCount)} suất`;
                        
                        // Check if calculation is exact
                        const exactAmount = calculatedShareCount * shareAmount;
                        if (Math.abs(exactAmount - targetAmount) > 0.01) {
                            validationMessage = `Lưu ý: Số lượng suất đã được làm tròn từ ${(targetAmount / shareAmount).toFixed(2)} thành ${calculatedShareCount}. Mục tiêu thực tế sẽ là ${formatNumber(exactAmount)} ${gs('cur_text')}`;
                        }
                    } else {
                        validationMessage = 'Vui lòng nhập cả Mục tiêu dự án và Giá mỗi suất để tính toán';
                    }
                    break;

                case 'share_amount':
                    // Calculate target_amount from share_count and share_amount
                    if (shareCount > 0 && shareAmount > 0) {
                        calculatedTargetAmount = shareCount * shareAmount;
                        targetAmountInput.value = calculatedTargetAmount.toFixed(2);
                        calculationMessage = `${formatNumber(shareCount)} suất × ${formatNumber(shareAmount)} ${gs('cur_text')}/suất = ${formatNumber(calculatedTargetAmount)} ${gs('cur_text')}`;
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
            const targetAmount = parseNumber(targetAmountInput.value);
            const shareCount = parseNumber(shareCountInput.value);
            const shareAmount = parseNumber(shareAmountInput.value);

            if (targetAmount > 0 && shareCount > 0 && shareAmount > 0) {
                const calculatedTarget = shareCount * shareAmount;
                const difference = Math.abs(calculatedTarget - targetAmount);

                if (difference > 0.01) {
                    summaryText.innerHTML += `<br><span class="text-warning"><i class="las la-exclamation-triangle"></i> Cảnh báo: Mục tiêu nhập (${formatNumber(targetAmount)}) khác với tính toán (${formatNumber(calculatedTarget)}). Chênh lệch: ${formatNumber(difference)} ${gs('cur_text')}</span>`;
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
                    // Debounce calculation
                    clearTimeout(input.calculationTimeout);
                    input.calculationTimeout = setTimeout(calculateValues, 500);
                });
            });
        }

        // Calculate button click
        calculateBtn.addEventListener('click', calculateValues);

        // Calculation mode change
        calculationModes.forEach(mode => {
            mode.addEventListener('change', function() {
                calculateValues();
            });
        });

        // Initialize
        setupAutoCalculation();
        calculateValues();

        // Slug generation
        $('.buildSlug').on('click', function() {
            let title = $('[name=title]').val();
            if (title) {
                let slug = title.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('[name=slug]').val(slug);
                $('[name=slug]').trigger('input');
            }
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
</script>

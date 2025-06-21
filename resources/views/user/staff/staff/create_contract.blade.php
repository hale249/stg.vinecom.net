@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Tạo hợp đồng mới')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.staff.staff.store_contract') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Chọn khách hàng') <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control form--control" required>
                                    <option value="">@lang('-- Chọn khách hàng --')</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request()->get('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->fullname }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Chọn dự án') <span class="text-danger">*</span></label>
                                <select name="project_id" class="form-control form--control" required>
                                    <option value="">@lang('-- Chọn dự án --')</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" data-min="{{ getAmount($project->minimum) }}" data-max="{{ getAmount($project->maximum) }}" data-fixed="{{ $project->fixed_amount }}">
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Số tiền đầu tư') <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="any" name="amount" class="form-control form--control" required>
                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                </div>
                                <small class="form-text text-muted">@lang('Số tiền tối thiểu: ') <span class="min-amount">0</span> {{ __($general->cur_text) }}</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Lãi suất') <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="any" name="interest_rate" class="form-control form--control" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Thời hạn (tháng)') <span class="text-danger">*</span></label>
                                <input type="number" name="period" class="form-control form--control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Ngày bắt đầu') <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control form--control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Ghi chú')</label>
                                <textarea name="note" class="form-control form--control" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Tài liệu đính kèm')</label>
                                <input type="file" name="attachments[]" class="form-control form--control" multiple>
                                <small class="form-text text-muted">@lang('Cho phép: .jpg, .jpeg, .png, .pdf, .doc, .docx. Kích thước tối đa: 5MB')</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn--primary w-100">@lang('Tạo hợp đồng')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($){
        "use strict";
        
        $('select[name=project_id]').on('change', function() {
            var option = $(this).find('option:selected');
            var minAmount = option.data('min') || 0;
            var maxAmount = option.data('max') || 0;
            var isFixed = option.data('fixed') || 0;
            
            $('.min-amount').text(minAmount);
            
            if(isFixed == 1) {
                $('input[name=amount]').val(minAmount).prop('readonly', true);
            } else {
                $('input[name=amount]').prop('readonly', false);
            }
        });
        
    })(jQuery);
</script>
@endpush 
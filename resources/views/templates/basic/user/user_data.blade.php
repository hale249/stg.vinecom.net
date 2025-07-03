@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="user-section py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card custom--card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('user.data.submit') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Username')</label>
                                            <input type="text" class="form-control form--control checkUser"
                                                   name="username" value="{{ old('username') }}">
                                            <small class="text--danger usernameExist"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Referral Code') <span class="text--danger">*</span></label>
                                            <input type="text" class="form-control form--control checkReferralCode"
                                                   name="referral_code" value="{{ old('referral_code') }}" required>
                                            <small class="text--danger referralCodeError"></small>
                                            <small class="text--success referralCodeSuccess"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Country')</label>
                                            <select name="country" class="form-control form--control select2" required>
                                                @foreach ($countries as $key => $country)
                                                    <option data-mobile_code="{{ $country->dial_code }}"
                                                            value="{{ $country->country }}" data-code="{{ $key }}"
                                                            {{ $country->country == 'Vietnam' ? 'selected' : '' }}>
                                                        {{ __($country->country) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Mobile')</label>
                                            <div class="input-group ">
                                                <span class="input-group-text mobile-code">

                                                </span>
                                                <input type="hidden" name="mobile_code">
                                                <input type="hidden" name="country_code">
                                                <input type="number" name="mobile" value="{{ old('mobile') }}"
                                                       class="form-control form--control checkUser" required>
                                            </div>
                                            <small class="text--danger mobileExist"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100">
                                        @lang('Submit')
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush


@push('script')
    <script>
        "use strict";
        (function ($) {

            // Vietnam will be selected by default through HTML attribute

            $('.select2').select2();

            $('select[name=country]').on('change', function () {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value, name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));


            $('.checkUser').on('focusout', function (e) {
                var value = $(this).val();
                var name = $(this).attr('name')
                checkUser(value, name);
            });

            $('.checkReferralCode').on('focusout', function (e) {
                var value = $(this).val();
                if (value) {
                    checkReferralCode(value);
                }
            });

            function checkUser(value, name) {
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code: $('.mobile-code').text().substr(1),
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function (response) {
                    if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.field} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            }

            function checkReferralCode(value) {
                var url = '{{ route('user.checkReferralCode') }}';
                var token = '{{ csrf_token() }}';

                $('.referralCodeError').text('');
                $('.referralCodeSuccess').text('');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        referral_code: value,
                        _token: token
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('.referralCodeSuccess').text(`Valid referral code from ${response.referrer_name}`);
                        } else {
                            $('.referralCodeError').text(response.message || 'Invalid referral code');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error:', xhr.responseText);
                        $('.referralCodeError').text('Error checking referral code: ' + error);
                    }
                });
            }
        })(jQuery);
    </script>
@endpush

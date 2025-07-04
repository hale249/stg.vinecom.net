@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row gy-4">
                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="6" link="{{ route('admin.report.transaction', $user->id) }}" title="Balance"
                        icon="las la-money-bill-wave-alt" value="{{ showAmount($user->balance) }}" bg="indigo"
                        type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="6" link="{{ route('admin.deposit.list', $user->id) }}" title="Deposits"
                        icon="las la-wallet" value="{{ showAmount($totalDeposit) }}" bg="8" type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="6" link="{{ route('admin.withdraw.data.all', $user->id) }}" title="Withdrawals"
                        icon="la la-bank" value="{{ showAmount($totalWithdrawals) }}" bg="6" type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="6" link="{{ route('admin.report.transaction', $user->id) }}" title="Transactions"
                        icon="las la-exchange-alt" value="{{ $totalTransaction }}" bg="17" type="2" />
                </div>
            </div>
            <div class="row gy-4 mt-2">
                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ showAmount(@$invest['total_invests']) }}" title="Total Investments" style="6"
                        link="{{ route('admin.report.invest.history') }}" icon="las la-chart-bar" bg="primary" />
                </div><!-- dashboard-w1 end -->
                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ showAmount(@$invest['total_interests']) }}" title="Total Interests" style="6"
                        link="{{ route('admin.report.transaction') }}?remark=profit" icon="las la-chart-pie"
                        bg="1" />
                </div><!-- dashboard-w1 end -->
                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ showAmount(@$invest['running_invests']) }}" title="Running Investments"
                        style="6"
                        link="{{ route('admin.report.invest.history') }}?search={{ $user->username }}&status={{ Status::INVEST_RUNNING }}"
                        icon="las la-chart-area" bg="12" />
                </div><!-- dashboard-w1 end -->
                <div class="col-xxl-3 col-sm-6">
                    <x-widget value="{{ showAmount(@$invest['completed_invests']) }}" title="Completed Investments"
                        style="6"
                        link="{{ route('admin.report.invest.history') }}?search={{ $user->username }}&status={{ Status::INVEST_COMPLETED }}"
                        icon="las la-chart-line" bg="9" />
                </div><!-- dashboard-w1 end -->
            </div><!-- row end-->

            <div class="d-flex flex-wrap gap-3 mt-4">
                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal"
                        class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add">
                        <i class="las la-plus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal"
                        class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub">
                        <i class="las la-minus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.report.login.history') }}?search={{ $user->username }}"
                        class="btn btn--primary btn--shadow w-100 btn-lg">
                        <i class="las la-list-alt"></i>@lang('Logins')
                    </a>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.users.notification.log', $user->id) }}"
                        class="btn btn--secondary btn--shadow w-100 btn-lg">
                        <i class="las la-bell"></i>@lang('Notifications')
                    </a>
                </div>

                @if ($user->kyc_data)
                    <div class="flex-fill">
                        <a href="{{ route('admin.users.kyc.details', $user->id) }}" target="_blank"
                            class="btn btn--dark btn--shadow w-100 btn-lg">
                            <i class="las la-user-check"></i>@lang('KYC Data')
                        </a>
                    </div>
                @endif

                <div class="flex-fill">
                    @if ($user->status == Status::USER_ACTIVE)
                        <button type="button" class="btn btn--warning btn--shadow w-100 btn-lg userStatus"
                            data-bs-toggle="modal" data-bs-target="#userStatusModal">
                            <i class="las la-ban"></i>@lang('Ban User')
                        </button>
                    @else
                        <button type="button" class="btn btn--success btn--shadow w-100 btn-lg userStatus"
                            data-bs-toggle="modal" data-bs-target="#userStatusModal">
                            <i class="las la-undo"></i>@lang('Unban User')
                        </button>
                    @endif
                </div>
            </div>


            @if($user->referred_by)
            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin giới thiệu</h5>
                </div>
                <div class="card-body">
                    @if($user->referrer)
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="las la-user text-primary me-2"></i>Được giới thiệu bởi</span>
                                        <a href="{{ route('admin.users.detail', $user->referrer->id) }}" class="fw-bold text-primary">
                                            {{ $user->referrer->username ?? $user->referrer->referral_code }}
                                        </a>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="las la-user-tag text-primary me-2"></i>Tên người giới thiệu</span>
                                        <span class="fw-bold">{{ $user->referrer->fullname }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="las la-envelope text-primary me-2"></i>Email người giới thiệu</span>
                                        <span class="fw-bold">{{ $user->referrer->email }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="las la-code text-primary me-2"></i>Mã giới thiệu đã sử dụng</span>
                                        <span class="fw-bold">{{ $user->referred_by }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="las la-user-slash fa-3x text-muted"></i>
                            <p class="mt-2">Không tìm thấy người giới thiệu</p>
                            <p class="small text-muted">Mã giới thiệu: {{ $user->referred_by }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            @if($user->id_card_front || $user->id_card_back)
            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">CCCD/CMT Images</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($user->id_card_front)
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0 text-primary">
                                        <i class="las la-id-card me-2"></i>CCCD/CMT Mặt trước
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="position-relative">
                                        <img src="{{ getImage(getFilePath('idCards') . '/' . $user->id_card_front, '300x180') }}"
                                             alt="ID Card Front"
                                             class="img-thumbnail w-100 id-card-thumbnail"
                                             style="cursor: pointer; max-height: 150px; object-fit: cover;"
                                             data-bs-toggle="modal"
                                             data-bs-target="#idCardModal"
                                             data-image-src="{{ getImage(getFilePath('idCards') . '/' . $user->id_card_front) }}"
                                             data-image-title="CCCD/CMT Mặt trước">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-primary">
                                                <i class="las la-expand-arrows-alt"></i> Click để phóng to
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($user->id_card_back)
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0 text-primary">
                                        <i class="las la-id-card me-2"></i>CCCD/CMT Mặt sau
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="position-relative">
                                        <img src="{{ getImage(getFilePath('idCards') . '/' . $user->id_card_back, '300x180') }}"
                                             alt="ID Card Back"
                                             class="img-thumbnail w-100 id-card-thumbnail"
                                             style="cursor: pointer; max-height: 150px; object-fit: cover;"
                                             data-bs-toggle="modal"
                                             data-bs-target="#idCardModal"
                                             data-image-src="{{ getImage(getFilePath('idCards') . '/' . $user->id_card_back) }}"
                                             data-image-title="CCCD/CMT Mặt sau">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-primary">
                                                <i class="las la-expand-arrows-alt"></i> Click để phóng to
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Information of') {{ $user->fullname }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', [$user->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" type="text" name="firstname" required
                                        value="{{ $user->firstname }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="lastname" required
                                        value="{{ $user->lastname }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" type="email" name="email"
                                        value="{{ $user->email }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number')</label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">+{{ $user->dial_code }}</span>
                                        <input type="number" name="mobile" value="{{ $user->mobile }}" id="mobile"
                                            class="form-control checkUser" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address"
                                        value="{{ @$user->address }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city"
                                        value="{{ @$user->city }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state"
                                        value="{{ @$user->state }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip"
                                        value="{{ @$user->zip }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Country') <span class="text--danger">*</span></label>
                                    <select name="country" class="form-control select2">
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}"
                                                value="{{ $key }}" @selected($user->country_code == $key)>
                                                {{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Email Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')"
                                        data-off="@lang('Unverified')" name="ev"
                                        @if ($user->ev) checked @endif>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Mobile Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')"
                                        data-off="@lang('Unverified')" name="sv"
                                        @if ($user->sv) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-3 col-12">
                                <div class="form-group">
                                    <label>@lang('2FA Verification') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')"
                                        data-off="@lang('Disable')" name="ts"
                                        @if ($user->ts) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-3 col-12">
                                <div class="form-group">
                                    <label>@lang('KYC') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')"
                                        data-off="@lang('Unverified')" name="kv"
                                        @if ($user->kv == Status::KYC_VERIFIED) checked @endif>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    {{-- Add Sub Balance MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.add.sub.balance', $user->id) }}"
                    class="balanceAddSub disableSubmission" method="POST">
                    @csrf
                    <input type="hidden" name="act">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control"
                                    placeholder="@lang('Please provide positive amount')" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($user->status == Status::USER_ACTIVE)
                            <i class="las la-user-slash text-danger"></i> @lang('Ban User')
                        @else
                            <i class="las la-user-check text-success"></i> @lang('Unban User')
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if ($user->status == Status::USER_ACTIVE)
                            <div class="alert alert-warning p-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="las la-exclamation-triangle me-2 fs-4"></i>
                                    <small>@lang('If you ban this user, they will not be able to access their dashboard.')</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ban-reason" class="form-label">@lang('Reason for Ban')</label>
                                <textarea class="form-control" id="ban-reason" name="reason" rows="4" required></textarea>
                            </div>
                        @else
                            <div class="alert alert-warning p-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="las la-exclamation-triangle me-2 fs-4"></i>
                                    <small>@lang('Are you sure you want to unban this user? They will regain access to their dashboard.')</small>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h6 class="text-danger mb-3">@lang('Previous Ban Reason')</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="text-muted mb-0">{{ $user->ban_reason ?: __('No reason provided') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($user->status == Status::USER_ACTIVE)
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="las la-ban"></i> @lang('Confirm Ban')
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">@lang('Cancel')</button>
                            <button type="submit" class="btn btn-success">
                                <i class="las la-user-check"></i> @lang('Confirm Unban')
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ID Card Image Modal -->
    <div class="modal fade" id="idCardModal" tabindex="-1" aria-labelledby="idCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="idCardModalLabel">
                        <i class="las la-id-card me-2"></i>
                        <span id="modalImageTitle">CCCD/CMT</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" alt="ID Card" class="img-fluid w-100" style="max-height: 70vh; object-fit: contain;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Đóng
                    </button>
                    <a id="downloadImageBtn" href="" download class="btn btn-primary">
                        <i class="las la-download me-1"></i>Tải xuống
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .id-card-thumbnail {
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .id-card-thumbnail:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .position-relative .badge {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .position-relative:hover .badge {
        opacity: 1;
    }

    #modalImage {
        border-radius: 8px;
    }
</style>
@endpush

@push('script')
<script>
    // Handle ID Card image modal
    document.addEventListener('DOMContentLoaded', function() {
        const idCardModal = document.getElementById('idCardModal');
        const modalImage = document.getElementById('modalImage');
        const modalImageTitle = document.getElementById('modalImageTitle');
        const downloadImageBtn = document.getElementById('downloadImageBtn');

        // Handle modal show event
        idCardModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const imageSrc = button.getAttribute('data-image-src');
            const imageTitle = button.getAttribute('data-image-title');

            modalImage.src = imageSrc;
            modalImageTitle.textContent = imageTitle;
            downloadImageBtn.href = imageSrc;
        });
    });
</script>
@endpush

@push('breadcrumb-plugins')
    <a href="{{ route('admin.users.login', $user->id) }}" target="_blank" class="btn btn-sm btn-outline--primary"><i
            class="las la-sign-in-alt"></i>@lang('Login as User')</a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"


            $('.bal-btn').on('click', function() {

                $('.balanceAddSub')[0].reset();

                var act = $(this).data('act');
                $('#addSubModal').find('input[name=act]').val(act);
                if (act == 'add') {
                    $('.type').text('Add');
                } else {
                    $('.type').text('Subtract');
                }
            });

            let mobileElement = $('.mobile-code');
            $('select[name=country]').on('change', function() {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });

        })(jQuery);
    </script>
@endpush

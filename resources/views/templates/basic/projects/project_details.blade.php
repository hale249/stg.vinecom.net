@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="offer-details py-120  bg--white">
        <div class="container">
            <div class="offer-details-content">
                <h1 class="offer-details-title pb-2">{{ __($project->title) }}</h1>
            </div>
            <div class="offer-details-top">
                <div class="offer-details-thumb">
                    <a href="{{ getImage(getFilePath('project') . '/' . $project->image) }}" data-rel="lightcase:my-slideshow">
                        <img src="{{ getImage(getFilePath('project') . '/' . $project->image) }}" alt="Project Image">
                    </a>

                    @if (!empty($project->gallery) && count($project->gallery) > 0)
                        @foreach ($project->gallery as $index => $gallery)
                            @if ($index < 5)
                                <a href="{{ getImage(getFilePath('project') . '/' . $gallery) }}" data-rel="lightcase:my-slideshow">
                                    <img src="{{ getImage(getFilePath('project') . '/' . $gallery) }}" alt="Project Gallery Image"></a>
                            @endif
                        @endforeach
                    @endif
                </div>

                @if (!empty($project->gallery) && count($project->gallery) > 0)
                    <div class="offer-details-slider d-lg-none">
                        <div class="offer-details-thumb-slider">
                            @foreach ($project->gallery as $index => $gallery)
                                @if ($index < 5)
                                    <div class="offer-details-thumb-slider__item">
                                        <img class="offer-details-thumb-slider__img" src="{{ getImage(getFilePath('project') . '/' . $gallery, getFileSize('project')) }}" alt="@lang('Project Image')" data-index="{{ $index }}">
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="offer-details-preview-slider">
                            @foreach ($project->gallery as $index => $gallery)
                                @if ($index < 5)
                                    <div class="offer-details-preview-slider__item">
                                        <img class="offer-details-preview-slider__img" src="{{ getImage(getFilePath('project') . '/' . $gallery, getFileSize('project')) }}" alt="@lang('Project Image')">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div class="offer-details-bottom">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="offer-details-content">
                            <ul class="offer-details-meta">
                                <li class="offer-details-meta__item">
                                    <span class="label">@lang('Per Share')</span>
                                    <span class="value">{{ __(showAmount($project->share_amount)) }}</span>
                                </li>
                                <li class="offer-details-meta__item">
                                    <span class="label">@lang('ROI')</span>
                                    <span class="value">{{ __(getAmount($project->roi_percentage)) }}@lang('%')</span>
                                </li>
                                <li class="offer-details-meta__item">
                                    <span class="label">@lang('Duration')</span>
                                    <span class="value">{{ $project->maturity_time }} @lang('Months')</span>
                                </li>
                                <li class="offer-details-meta__item">
                                    <span class="label">@lang('Max')</span>
                                    <span class="value">{{ __(getAmount($project->share_count)) }}
                                        @lang('Units')</span>
                                </li>
                                <li class="offer-details-meta__item">
                                    <span class="label">@lang('Remaining')</span>
                                    <span class="value">{{ __(getAmount($project->available_share)) }}
                                        @lang('Units')</span>
                                </li>
                            </ul>
                            @if ($project->end_date > now() && $project->status != Status::PROJECT_END && $project->available_share > 0)
                                <button class="btn btn--lg btn--base w-100 mt-4 d-lg-none" type="button" data-toggle="offcanvas-sidebar" data-target="#offer-details-offcanvas-sidebar">
                                    @lang('Check Details')
                                </button>
                            @endif

                            <div class="details-tabs">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-tab-pane" type="button" role="tab" aria-controls="details-tab-pane" aria-selected="true">@lang('Details')</button>
                                    </li>

                                    @if ($project->faqs->isNotEmpty())
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq-tab-pane" type="button" role="tab" aria-controls="faq-tab-pane" aria-selected="false">@lang('Faqs')</button>
                                        </li>
                                    @endif

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="maps-tab" data-bs-toggle="tab" data-bs-target="#maps-tab-pane" type="button" role="tab" aria-controls="maps-tab-pane" aria-selected="false">@lang('Maps')</button>
                                    </li>

                                    <li class="nav-item comment___number" role="presentation">
                                        <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments-tab-pane" type="button" role="tab" aria-controls="comments-tab-pane" aria-selected="false">@lang('Comments')(<span class="commentCount">{{ $commentCount }}</span>)</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                        @php echo $project->description @endphp
                                        <div class="mt-5 d-flex align-items-center justify-content-center flex-column project-share-box">
                                            <h6>@lang('Share Project')</h6>
                                            <ul class="social__links">
                                                <li>
                                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                                </li>
                                                <li>
                                                    <a href="https://twitter.com/intent/tweet?text={{ __($project->title) }}&amp;url={{ urlencode(url()->current()) }}" target="_blank"><i class="fab fa-twitter"></i></a>
                                                </li>
                                                <li>
                                                    <a href="https://pinterest.com/pin/create/bookmarklet/?media={{ getImage(getFilePath('project') . '/' . $project->image, getFileSize('project')) }}&url={{ urlencode(url()->current()) }}" target="_blank"><i class="fab fa-pinterest-p"></i></a>
                                                </li>
                                                <li>
                                                    <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    @if ($project->faqs->isNotEmpty())
                                        <div class="tab-pane fade" id="faq-tab-pane" role="tabpanel" aria-labelledby="faq-tab" tabindex="0">
                                            <div class="offer-details-block">
                                                <h5 class="offer-details-block__title">@lang('Frequently Asked Questions')</h5>
                                                <div id="faq-accordion" class="accordion custom--accordion">
                                                    @foreach ($project->faqs as $index => $faq)
                                                        <div class="accordion-item {{ $index == 0 ? 'active' : '' }}">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-accordion-question-{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="faq-accordion-question-{{ $index }}">
                                                                    {{ __($faq->question) }}
                                                                </button>
                                                            </h2>
                                                            <div id="faq-accordion-question-{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faq-accordion">
                                                                <div class="accordion-body">
                                                                    <p class="accordion-text">
                                                                        {{ __($faq->answer) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="tab-pane fade" id="maps-tab-pane" role="tabpanel" aria-labelledby="maps-tab" tabindex="0">
                                        <div class="offer-details-block">
                                            <h5 class="offer-details-block__title">@lang('Where This Project')</h5>

                                            <div class="offer-details-block__map">
                                                {!! @$project->map_url !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="comments-tab-pane" role="tabpanel" aria-labelledby="comments-tab" tabindex="0">
                                        <div class="top">
                                            <h5 class="comment-number"><span class="commentCount">{{ @$commentCount ?? 0 }}</span>
                                                @lang('Comments')</h5>
                                        </div>
                                        @if (auth()->check())
                                            <div class="comment-form-wrapper">
                                                @php
                                                    $userImage = auth()->check() ? auth()->user()->image : '';
                                                @endphp
                                                <span class="comment-author">
                                                    <img class="fir-image" src="{{ getImage(getFilePath('userProfile') . '/' . $userImage, getFileSize('userProfile'), avatar: true) }}" alt="profile">
                                                </span>

                                                <form action="{{ route('user.comment.store', $project->id) }}" class="comment-form ajaxForm" method="post">
                                                    @csrf
                                                    <input type="hidden" name="" value="" autocomplete="off">
                                                    <div class="form-group position-relative">
                                                        <textarea class="form--control commentBox" name="comment" placeholder="@lang('Write a comment')" id="comment" required></textarea>
                                                        <button class="comment-btn" type="submit">
                                                            <svg class="lucide lucide-send-horizontal" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z"></path>
                                                                <path d="M6 12h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif

                                        <div id="comment__main">
                                            @foreach ($comments as $comment)
                                                <div class="comment-box-item comment-item  parentComment ">
                                                    <div class="comment-box-item__thumb">
                                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$comment->user->image, getFileSize('userProfile'), avatar: true) }}" alt="User Image">
                                                    </div>
                                                    <div class="comment-box-item__content">
                                                        <div class="comment-box-item__top">
                                                            <p class="comment-box-item__name">{{ __(@$comment->user->fullname) }}
                                                            </p>
                                                            <p class="comment-box-item__text">
                                                                {{ __($comment->comment) }}
                                                            </p>
                                                        </div>
                                                        <div class="replay_box">
                                                            <div class="reaction-btn">
                                                                <span class="time">{{ diffForHumans(@$comment->created_at) }}</span>
                                                                <div class="reaction-btn__reply">
                                                                    <button class="reply replay_button">
                                                                        <span class="icon">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-quote">
                                                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                                                <path d="M8 12a2 2 0 0 0 2-2V8H8"></path>
                                                                                <path d="M14 12a2 2 0 0 0 2-2V8h-2"></path>
                                                                            </svg>
                                                                        </span>
                                                                        @lang('Reply')
                                                                        <span class="incrementCount">{{ $comment->replies->count() }}</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @if (auth()->check())
                                                                <div class="reply-wrapper d-none">
                                                                    <form action="{{ route('user.comment.store', [$project->id, $comment->id]) }}" class="reply-form mb-3 ajaxForm" method="post">
                                                                        @csrf
                                                                        <input name="reply_to" type="hidden" value="203">
                                                                        <textarea class="form--control reply-form__textarea commentBox" name="comment" placeholder="@lang('Write a Replay')" id="comment" required></textarea>
                                                                        <div class="reply-form__input-btn">
                                                                            <button class="reply-form__btn submit-reply" type="submit">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send-horizontal">
                                                                                    <path d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z">
                                                                                    </path>
                                                                                    <path d="M6 12h16"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @endif

                                                            <div class="comment-item d-none">
                                                                @foreach ($comment->replies as $replay)
                                                                    <div class="comment-box-item">
                                                                        @php
                                                                            if ($replay->admin_id) {
                                                                                $profileImage = getImage(getFilePath('adminProfile') . '/' . @$replay->admin->image, getFileSize('adminProfile'), avatar: true);
                                                                            } else {
                                                                                $profileImage = getImage(getFilePath('userProfile') . '/' . @$comment->user->image, getFileSize('userProfile'), avatar: true);
                                                                            }
                                                                        @endphp
                                                                        <div class="comment-box-item__thumb">
                                                                            <img src="{{ $profileImage }}" alt="User Image">
                                                                        </div>
                                                                        <div class="comment-box-item__content">
                                                                            <div class="comment-box-item__top">
                                                                                <p class="comment-box-item__name">{{ __(@$replay->user->fullname) }}
                                                                                </p>
                                                                                <p class="comment-box-item__text">
                                                                                    <span> {{ __(@$replay->comment) }}</span>
                                                                                </p>
                                                                            </div>
                                                                            <span class="time">{{ diffForHumans(@$replay->created_at) }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if ($commentCount > 5)
                                            <div class="text-center">
                                                <button id="load-more" data-page="2" data-project-slug="{{ $project->slug }}">@lang('Load more')</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($project->end_date > now() && $project->status != Status::PROJECT_END && $project->available_share > 0)
                        <div class="col-lg-4" id="sidebar-container">
                            @include($activeTemplate . 'projects.sidebar')
                        </div>
                    @endif
                </div>
            </div>

            @if (!blank($relates))
                <section class="our-offers pt-120">
                    <h4 class="section-heading__title">@lang('Related Offers')</h4>
                    <div class="tab-content">
                        <div id="high-offers" class="tab-pane fade show active">
                            <div class="project-slider related-slider">
                                @foreach ($relates as $relatedProject)
                                    <article class="card card--offer ">
                                        <div class="card-header">
                                            <a class="card-thumb" href="{{ route('project.details', $relatedProject->slug) }}">
                                                <img src="{{ getImage(getFilePath('project') . '/' . $relatedProject->image) }}" alt="{{ __($relatedProject->title) }}">
                                            </a>

                                            <div class="card-offer">
                                                <span class="card-offer__label">@lang('ROI')</span>
                                                <span class="card-offer__percentage">{{ getAmount($relatedProject->roi_percentage) }}%</span>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <a href="{{ route('project.details', $relatedProject->slug) }}">{{ __($relatedProject->title) }}</a>
                                            </h6>

                                            <div class="card-content">
                                                <div class="card-content__wrapper">
                                                    <span class="card-content__label">@lang('Per Share')</span>
                                                    <div class="card-content__price">
                                                        {{ __(showAmount($relatedProject->share_amount)) }}</div>
                                                </div>
                                                <a href="{{ route('project.details', $relatedProject->slug) }}" class="btn btn--xsm btn--outline">@lang('Invest Now')</a>
                                            </div>
                                            <div class="card-bottom">
                                                <span class="card-bottom__unit">
                                                    <i class="las la-boxes"></i>
                                                    {{ __($relatedProject->available_share) }} @lang('units')
                                                </span>
                                                <span class="card-bottom__duration">{{ __(diffForHumans($relatedProject->end_date)) }}</span>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </section>
    @include($activeTemplate . 'projects.buy-modal')
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/lightcase.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/lightcase.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $("a[data-rel^=lightcase]").lightcase();

                @if (!empty($project->gallery) && count($project->gallery) > 0)
                    $(".offer-details-thumb-slider").slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 1500,
                        dots: false,
                        arrows: false,
                        pauseOnHover: true,
                        asNavFor: ".offer-details-preview-slider",
                    });

                    $(".offer-details-preview-slider").slick({
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 1500,
                        dots: false,
                        arrows: false,
                        pauseOnHover: true,
                        asNavFor: ".offer-details-thumb-slider",
                        responsive: [{
                            breakpoint: 576,
                            settings: {
                                slidesToShow: 3,
                            },
                        }, ],
                    });
                @endif

                var STATUS_LIFETIME = {{ Status::LIFETIME }};
                var STATUS_YES = {{ Status::YES }};

                var project = {
                    shareAmount: {{ $project->share_amount }},
                    roiAmount: {{ $project->roi_amount }},
                    roiPercentage: {{ $project->roi_percentage }},
                    capitalBack: {{ $project->capital_back }},
                    returnType: {{ $project->return_type }},
                    projectDuration: {{ $project->project_duration }},
                    repeatTimes: {{ @$project->repeat_times ?? 0 }},
                    timeName: '{{ $project->time->name }}',
                    timeHours: {{ $project->time->hours }},
                    availableShare: {{ $project->available_share }},
                    currencySymbol: '{{ gs('cur_sym') }}',
                    currencyText: '{{ gs('cur_text') }}'
                };

                $(document).on('click', '.qty-btn', function() {
                    changeQuantity(this);
                });

                // Event listener for manual input in the quantity field
                $(document).on('change', '.product-qty__value', function() {
                    var quantity = parseInt($(this).val());
                    if (isNaN(quantity) || quantity < 1) {
                        quantity = 1;
                        $(this).val(quantity);
                    }
                    if (quantity > project.availableShare) {
                        quantity = project.availableShare;
                        $(this).val(quantity);
                        notify('error', 'Quantity cannot exceed available shares.');
                    }
                    updateValues(quantity);
                });

                // Function to handle increment/decrement actions
                function changeQuantity(element) {
                    var $input = $('.product-qty__value');
                    var currentValue = parseInt($input.val());
                    var inputValue = currentValue;

                    var minValue = parseInt($input.attr('min'));
                    var maxValue = parseInt($input.attr('max'));

                    if ($(element).hasClass('product-qty__increment')) {
                        if (currentValue < maxValue) {
                            inputValue = currentValue + 1;
                        }
                    } else if ($(element).hasClass('product-qty__decrement')) {
                        if (currentValue > minValue) {
                            inputValue = currentValue - 1;
                        }
                    }

                    $input.val(inputValue);
                    updateValues(inputValue);
                }

                // Function to update values on the page based on the quantity
                function updateValues(quantity) {
                    var totalPayable = project.shareAmount * quantity;
                    var totalEarnings = 0;

                    if (project.returnType == STATUS_LIFETIME) {
                        var totalMonths = project.projectDuration;
                        var payHours = project.timeHours;
                        var payAmount = project.roiAmount;

                        var totalHours = totalMonths * 720;

                        var totalPayments = Math.floor(totalHours / payHours);

                        totalEarnings = totalPayments * payAmount * quantity;
                    } else {
                        var payAmount = project.roiAmount;
                        totalEarnings = payAmount * project.repeatTimes * quantity;
                    }

                    $('#modal_quantity').val(quantity);

                    // Update total payable
                    $('#total-payable').text(project.currencySymbol + totalPayable.toFixed(2));


                    // Update total earning last
                    var totalEarningLast = totalEarnings;
                    if (project.capitalBack == STATUS_YES) {
                        totalEarningLast += project.shareAmount * quantity;
                    }

                    $('#total-earning-last').text(project.currencySymbol + totalEarningLast.toFixed(2));

                    // Update total earning
                    if (project.returnType == STATUS_LIFETIME) {
                        $('#total-earning').text(project.currencySymbol + (project.roiAmount * quantity).toFixed(2) + ' / ' + project.timeName);
                    } else {
                        $('#total-earning').text(project.currencySymbol + totalEarningLast.toFixed(2));
                    }

                    // Update quantity total price
                    $('.quantity-total-price').text(project.currencySymbol + totalPayable.toFixed(2));
                    $('#total__invest').text(project.currencySymbol + totalPayable.toFixed(2));

                    // Update Earning ROI Amount

                    $('.time-name').text(project.currencySymbol + (project.roiAmount * quantity).toFixed(2) + ' / ' + project.timeName);

                    // Update Earning ROI (%)
                    $('.roi-percentage').text(project.roiPercentage.toFixed(2) + '%');

                    // Update Capital Back
                    $('.capital-back').text(project.capitalBack == STATUS_YES ? 'Yes' : 'No');
                }


                document.querySelectorAll('.payment-options').forEach(function(option) {
                    option.addEventListener('click', function() {
                        document.querySelectorAll('.payment-options').forEach(function(
                            opt) {
                            opt.classList.remove('active');
                        });
                        option.classList.add('active');
                        $('#payment_type').val(option.getAttribute('data-payment-type'));
                    });
                });


                $('.project-slider').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    speed: 1000,
                    arrows: true,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    prevArrow: '<button type="button" class="slick-prev"><i class="las la-arrow-left"></i></button>',
                    nextArrow: '<button type="button" class="slick-next"><i class="las la-arrow-right"></i></button>',
                    responsive: [{
                            breakpoint: 1200,
                            settings: {
                                slidesToShow: 3,
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 2,
                            }
                        },
                        {
                            breakpoint: 576,
                            settings: {
                                slidesToShow: 2,
                                arrows: false,
                                dots: true
                            }
                        },
                        {
                            breakpoint: 425,
                            settings: {
                                slidesToShow: 1,
                                arrows: false,
                                dots: true
                            }
                        }
                    ],
                });

                $(document).on('click', '.replay_button', function() {
                    $(this).closest('.replay_box').find('.reply-wrapper,.comment-item').toggleClass('d-none');
                });


                let lastPage = `{{ $comments->lastPage() }}`;
                $('#load-more').on('click', function() {
                    let page = $(this).data('page');
                    let url = `{{ route('project.details', $project->slug) }}` + `?page=${page}`;

                    $.ajax({
                        url: url,
                        type: 'GET',
                        beforeSend: function() {
                            $('#load-more').html(`
                            <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                            </div>`);
                        },
                        success: function(response) {
                            if (lastPage <= page) {
                                $('#load-more').text('No more comments');
                                $('#load-more').prop('disabled', true);
                            } else {
                                $('#load-more').text('Load more');
                            }
                            if (response) {
                                $('#comment__main').append(response);
                                $('#load-more').data('page', page + 1);
                            }
                        },
                        error: function(response) {
                            alert('Something went wrong. Please try again.');
                            $('#load-more').text('Load more');
                        }
                    });
                });

                $(document).on('submit', '.ajaxForm', function(e) {
                    e.preventDefault();
                    let form = $(this);
                    let formData = $(this).serialize();
                    let actionUrl = $(this).attr('action');
                    let auth = `{{ auth()->check() }}`;

                    if (!auth) {
                        notify('error', 'You need to log in first');
                    }

                    $.ajax({
                        url: actionUrl,
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.type == 'success') {
                                form[0].reset();
                                if (response.comment) {
                                    $('#comment__main').prepend(response.data);

                                    $('.comment-number').find('.commentCount').text(function(i, oldText) {
                                        return parseInt(oldText) + 1;
                                    });

                                    $('.comment___number').find('.commentCount').text(function(i, oldText) {
                                        return parseInt(oldText) + 1;
                                    });

                                } else {
                                    form.closest('.replay_box').find('.comment-item').append(response.data);

                                    form.closest('.replay_box').find('.incrementCount').text(function(i, oldText) {
                                        return parseInt(oldText) + 1;
                                    });
                                }
                                notify('success', response.message);
                            } else {
                                notify('error', response.message);
                            }
                        }
                    });
                });

                // --- Sync sidebar quantity to modal ---
                $(document).on('click', '.bookNow', function() {
                    // Get quantity from sidebar
                    var sidebarQty = $(this).closest('.offcanvas-sidebar').find('.product-qty__value').val();
                    sidebarQty = parseInt(sidebarQty) || 1;
                    // Set biến toàn cục để modal lấy đúng số lượng khi mở
                    window._modalQuantity = sidebarQty;
                    // Gọi cập nhật modal
                    if (typeof updateModalValues === 'function') {
                        updateModalValues(sidebarQty);
                    } else if (window.updateModalValues) {
                        window.updateModalValues(sidebarQty);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush

@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-sm-12">
            <div class="sidebar-overlay"></div>
        </div>
        <div class="col-md-4 h-100">
            <div class="card  overflow-hidden box--shadow1 user-details">
                <div class="card-body">
                    <span class="close-icon">
                        <i class="las la-times"></i>
                    </span>
                    <div class="p-3 bg--white">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . @$comment->user->image, getFileSize('userProfile')) }}" alt="Profile Image" class="b-radius--10" style="max-width: 100px">
                            </div>
                            <div>
                                <h4 class="mb-1">
                                    <a href="{{ route('admin.users.detail', $comment->user->id) }}" class="text--primary"> {{ __(@$comment->user->fullname) }} ({{ __(@$comment->user->username) }})</a>
                                </h4>
                                <p class="mb-0"> {{ @$comment->user->email }}</p>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text--small">@lang('Project Name')</span>
                                <a href="{{ route('admin.project.edit', $comment->project->id) }}" class="text--small"><strong>{{ __(@$comment->project->title) }}</strong></a>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text--small">@lang('Start Date')</span>
                                <span class="text--small"><strong>{{ showDateTime($comment->project->start_date) }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text--small">@lang('End Date')</span>
                                <span class="text--small"><strong> {{ showDateTime($comment->project->end_date) }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text--small">@lang('Share Count')</span>
                                <span class="text--small"><strong>{{ getAmount(@$comment->project->share_count) }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text--small"> @lang('Available Share') </span>
                                <span class="text--small"><strong>{{ @$comment->project->available_share }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text--small"> @lang('ROI %') </span>
                                <span class="text--small"><strong> {{ showAmount($comment->project->roi_percentage) }} %</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text--small"> @lang('ROI Amount') </span>
                                <span class="text--small"><strong> {{ showAmount($comment->project->roi_amount) }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text--small"> @lang('Type') </span>
                                <span class="text--small"><strong> @php echo $comment->project->typeBadge @endphp</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
                                <span class="text--small">@lang('Status')</span>
                                <span class="text--small"><strong>@php echo @$comment->project->statusBadge @endphp</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 h-100">
            <div class="d-md-none d-block mb-3">
                <span class="comment-details-wrapper__icon"> <i class="las la-bars"></i> </span>
            </div>
            <div class="comment-details-wrapper">
                <div class="main-message-box">
                    <div class="message-box">
                        <div class="message-box__thumb">
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . @$comment->user->image, getFileSize('userProfile')) }}" alt="User">
                        </div>
                        <div>
                            <p class="message-box__text {{ $comment->admin_id ? '' : 'user' }} {{ $comment->status ? '' : 'delete' }} "> {{ __($comment->comment) }} </p>
                            <span class="message-box__time">{{ diffForHumans($comment->created_at) }}</span>
                        </div>
                        @php
                            $message = $comment->status ? 'Are you sure to delete this comment?' : 'Are you sure to enable this comment?';
                        @endphp
                        <span class="message-box__icon confirmationBtn" data-question="{{ $message }}" data-action="{{ route('admin.comment.status', $comment->id) }}"> {!! $comment->status ? '<i class="las la-trash-alt"></i>' : '<i class="las la-check"></i>' !!} </span>
                    </div>
                    @foreach ($comment->allReplies as $replay)
                        <div class="message-box">
                            <div class="message-box__thumb">
                                @php
                                    if ($replay->admin_id) {
                                        $ProfileImage = getImage(getFilePath('adminProfile') . '/' . @$replay->admin->image, getFileSize('adminProfile'));
                                    } else {
                                        $ProfileImage = getImage(getFilePath('userProfile') . '/' . @$comment->user->image, getFileSize('userProfile'));
                                    }
                                @endphp
                                <img src="{{ $ProfileImage }}" alt="User">
                            </div>
                            <div>
                                <p class="message-box__text {{ $replay->admin_id ? '' : 'user' }} {{ $replay->status ? '' : 'delete' }} "> {{ __($replay->comment) }} </p>
                                <span class="message-box__time">{{ diffForHumans($replay->created_at) }}</span>
                            </div>
                            @php
                                $message = $replay->status ? 'Are you sure to delete this comment?' : 'Are you sure to enable this comment?';
                            @endphp
                            <span class="message-box__icon confirmationBtn" data-question="{{ $message }}" data-action="{{ route('admin.comment.status', $replay->id) }}"> {!! $replay->status ? '<i class="las la-trash-alt"></i>' : '<i class="las la-check"></i>' !!} </span>
                        </div>
                    @endforeach
                </div>
                <div class="chat-box">
                    <form action="{{ route('admin.comment.store', [$comment->project_id, $comment->id]) }}" method="post">
                        @csrf
                        <textarea class="form--control" name="comment" placeholder="@lang('Enter your reply')" required></textarea>
                        <button type="submit" class="btn btn-primary">@lang('Reply')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.comment.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {
                $(".comment-details-wrapper__icon").on('click', function() {
                    $(".user-details").addClass("show");
                    $(".sidebar-overlay").addClass("show");
                })
                $(".close-icon, .sidebar-overlay").on('click', function() {
                    $(".user-details").removeClass("show");
                    $(".sidebar-overlay").removeClass("show");
                });
            });


            function scrollToBottom() {
                var chatBox = $(".main-message-box");
                chatBox.scrollTop(chatBox[0].scrollHeight);
            }

            scrollToBottom()

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .chat-box {
            position: sticky;
            width: 100%;
            background: #fff;
            z-index: 9;
            display: flex;
            gap: 20px;
            border-radius: 10px;
            margin-bottom: 2px;
            margin-top: auto;
            flex-direction: column;
        }

        .chat-box .btn {
            display: flex;
            width: max-content;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.8);
            z-index: 98;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }

        .message-box__thumb {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .message-box__thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px dotted #cccccc;
            border-radius: 50%;
        }

        .close-icon {
            display: none
        }

        .user-details .card {
            border-radius: 10px
        }

        .user-details .card-body {
            padding: 0;
        }

        @media (max-width:767px) {
            .user-details {
                position: fixed;
                height: 100vh;
                z-index: 9999;
                top: 0;
                left: 0;
                transform: translateX(-105%);
                border-radius: 0;
                transition: .2s linear;
            }

            .user-details.show {
                transform: translateX(0);
            }

            .user-details .card-body {
                position: relative;
                padding-top: 50px;

            }

            .user-details .card {
                border-radius: 0 !important;
            }

            .close-icon {
                display: block;
                position: absolute;
                top: 10px;
                right: 10px;
                color: #00o;
                font-size: 20px;
                cursor: pointer;
            }
        }

        .comment-details-wrapper__icon {
            background: #55d10a;
            padding: 5px 12px;
            border-radius: 6px;
            color: #000;
        }

        .main-message-box {
            height: 100%;
            overflow-y: auto;
        }

        .message-box__text.user {
            background: #2ee70e40;
        }

        .message-box {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
            max-width: 85%;
            width: max-content;
        }

        .message-box__time {
            font-size: 12px;
            font-weight: 400;
            color: rgba(0, 0, 0, 0.7);
            margin-top: 6px;
            display: inline-block;
        }

        .message-box:hover .message-box__icon {
            right: -35px;
            opacity: 1;
        }

        .message-box__text {
            background: #dee4ec;
            padding: 15px 20px;
            border-radius: 20px 20px 20px 0;
            font-size: 15px;
            position: relative;
            z-index: 1;
            color: #686868;
            max-width: 100%;
            display: flex;
        }

        .message-box__text.delete {
            background: rgba(255, 0, 0, 0.637) !important;
            color: white !important;
        }

        .message-box__icon {
            position: absolute;
            right: 4px;
            top: 16px;
            transform: translateY(0%);
            transition: .2s linear;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(15, 218, 15, 0.4);
            color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            opacity: 0;
            cursor: pointer;
        }

        .comment-details-wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            height: calc(100vh - 190px);
            display: flex;
            flex-direction: column;
        }
    </style>
@endpush

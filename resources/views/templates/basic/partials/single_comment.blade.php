<div class="comment-bow-wrapper">
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
                            @lang('Reply')({{ $comment->replies->count() }})
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
                </div>
            </div>
        </div>
    </div>
</div>

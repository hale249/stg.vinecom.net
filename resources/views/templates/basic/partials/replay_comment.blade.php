<div class="comment-box-item">
    <div class="comment-box-item__thumb">
        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$replay->user->image, getFileSize('userProfile'), avatar: true); }}" alt="User Image">
    </div>
    <div class="comment-box-item__content">
        <div class="comment-box-item__top">
            <p class="comment-box-item__name">{{ __(@$replay->user->fullname) }}</p>
            <p class="comment-box-item__text">
                <span> {{ __(@$replay->comment) }}</span>
            </p>
        </div>
        <span class="time">{{ diffForHumans(@$replay->created_at) }}</span>
    </div>
</div>

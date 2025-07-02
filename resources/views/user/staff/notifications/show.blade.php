@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="notification-detail">
                            <div class="notification-header mb-4">
                                <h4 class="notification-title">{{ __($notification->subject ?? 'System Notification') }}</h4>
                                <div class="notification-meta">
                                    <span class="badge badge--{{ $notification->notification_type == 'email' ? 'info' : 'primary' }} mb-2">
                                        {{ __(ucfirst($notification->notification_type ?? 'system')) }}
                                    </span>
                                    <p class="text-muted mb-0">
                                        <i class="far fa-clock"></i> {{ showDateTime($notification->created_at) }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="notification-content">
                                <div class="alert alert--{{ $notification->user_read ? 'success' : 'warning' }}">
                                    <p class="mb-0">{{ __($notification->message ?? 'No message content available.') }}</p>
                                </div>
                            </div>
                            
                            @if($notification->image)
                                <div class="notification-image mt-3">
                                    <img src="{{ getImage(getFilePath('notification').'/'.$notification->image) }}" 
                                         alt="Notification Image" 
                                         class="img-fluid rounded">
                                </div>
                            @endif
                            
                            <div class="notification-footer mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('user.staff.notifications.index') }}" class="btn btn--primary">
                                            <i class="fas fa-arrow-left"></i> @lang('Back to Notifications')
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        @if(!$notification->user_read)
                                            <form action="{{ route('user.staff.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn--success">
                                                    <i class="fas fa-check"></i> @lang('Mark as Read')
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge badge--success">@lang('Already Read')</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
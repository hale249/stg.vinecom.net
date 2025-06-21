@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Subject')</th>
                                <th>@lang('Message')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold">{{ __($notification->subject ?? 'System Notification') }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ Str::limit($notification->message, 50) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge--{{ $notification->notification_type == 'email' ? 'info' : 'primary' }}">
                                            {{ __(ucfirst($notification->notification_type ?? 'system')) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ showDateTime($notification->created_at) }}
                                    </td>
                                    <td>
                                        @if($notification->user_read)
                                            <span class="badge badge--success">@lang('Read')</span>
                                        @else
                                            <span class="badge badge--warning">@lang('Unread')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.staff.notifications.show', $notification->id) }}" class="icon-btn">
                                            <i class="fa fa-eye text--shadow"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($notifications->hasPages())
    <div class="card-footer py-4">
        {{ paginateLinks($notifications) }}
    </div>
@endif
@endsection 
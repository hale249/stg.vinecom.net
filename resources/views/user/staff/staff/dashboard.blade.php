@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-file-contract"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $stats['total_contracts'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Contracts')</span>
                </div>
                <a href="{{ route('user.staff.staff.contracts') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--success b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $stats['active_contracts'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Active Contracts')</span>
                </div>
                <a href="{{ route('user.staff.staff.contracts') }}?status=active" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--warning b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-clock"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $stats['pending_contracts'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Pending Contracts')</span>
                </div>
                <a href="{{ route('user.staff.staff.contracts') }}?status=pending" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--info b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-user-tag"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $stats['customers'] }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('My Customers')</span>
                </div>
                <a href="{{ route('user.staff.staff.customers') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-50 mb-none-30">
    <div class="col-xl-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body p-0">
                <div class="d-flex p-3 bg--primary align-items-center">
                    <div class="avatar avatar--lg">
                        <img src="{{ getImage(getFilePath('userProfile').'/'. $user->image,getFileSize('userProfile'))}}" alt="@lang('Image')">
                    </div>
                    <div class="pl-3">
                        <h4 class="text--white">{{__($user->fullname)}}</h4>
                    </div>
                </div>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Email')
                        <span class="font-weight-bold">{{$user->email}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Role')
                        <span class="badge badge--success">@lang('Sales Staff')</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Manager')
                        <span class="font-weight-bold">{{ $user->manager ? $user->manager->fullname : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Total Contracts')
                        <span class="badge badge--primary">{{ $stats['total_contracts'] }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xl-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2">@lang('Quick Links')</h5>
                <div class="row mt-3">
                    <div class="col-6 mb-3">
                        <a href="{{ route('user.staff.staff.contracts') }}" class="btn btn--success btn-block h-45">
                            <i class="fa fa-file-contract mr-2"></i>@lang('My Contracts')
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('user.staff.staff.create_contract') }}" class="btn btn--primary btn-block h-45">
                            <i class="fa fa-plus-circle mr-2"></i>@lang('Create Contract')
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('user.staff.staff.customers') }}" class="btn btn--info btn-block h-45">
                            <i class="fa fa-user-tag mr-2"></i>@lang('My Customers')
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('user.staff.staff.alerts') }}" class="btn btn--warning btn-block h-45">
                            <i class="fa fa-bell mr-2"></i>@lang('My Alerts')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-30">
    <div class="col-lg-12">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2">@lang('Upcoming Interest Payments')</h5>
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Contract')</th>
                                <th>@lang('Project')</th>
                                <th>@lang('Payment Date')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Days Left')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interestAlerts as $alert)
                                @php
                                    $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($alert->next_time), false);
                                @endphp
                                <tr>
                                    <td>{{ $alert->invest_no }}</td>
                                    <td>{{ $alert->project->name }}</td>
                                    <td>{{ showDateTime($alert->next_time) }}</td>
                                    <td>{{ showAmount($alert->recurring_pay) }} {{ __($general->cur_text) }}</td>
                                    <td>
                                        @if($daysLeft < 0)
                                            <span class="badge badge--danger">@lang('Overdue')</span>
                                        @elseif($daysLeft <= 7)
                                            <span class="badge badge--warning">{{ $daysLeft }} @lang('days')</span>
                                        @else
                                            <span class="badge badge--success">{{ $daysLeft }} @lang('days')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.staff.staff.contract.details', $alert->id) }}" class="icon-btn">
                                            <i class="fa fa-eye text--shadow"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __($emptyMessage ?? 'No upcoming payments') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-30">
    <div class="col-lg-12">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2">@lang('Recent Contracts')</h5>
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Contract')</th>
                                <th>@lang('Project')</th>
                                <th>@lang('Customer')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentContracts as $contract)
                                <tr>
                                    <td>{{ $contract->invest_no }}</td>
                                    <td>{{ $contract->project->title ?? 'N/A' }}</td>
                                    <td>{{ $contract->user->fullname ?? 'N/A' }}</td>
                                    <td>{{ showAmount($contract->total_price) }} {{ __($general->cur_text) }}</td>
                                    <td>{!! $contract->statusBadge !!}</td>
                                    <td>
                                        <a href="{{ route('user.staff.staff.contract.details', $contract->id) }}" class="icon-btn">
                                            <i class="fa fa-eye text--shadow"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __($emptyMessage ?? 'No recent contracts') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.staff.staff.partials.honor_modal')
@endsection 
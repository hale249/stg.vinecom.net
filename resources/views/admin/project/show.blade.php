@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <tbody>
                                <tr>
                                    <td><strong>@lang('Title')</strong></td>
                                    <td>{{ $project->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Goal')</strong></td>
                                    <td>{{ showAmount($project->goal) }} {{ $general->cur_text }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Share Amount')</strong></td>
                                    <td>{{ showAmount($project->share_amount) }} {{ $general->cur_text }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Share Count')</strong></td>
                                    <td>{{ $project->share_count }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Available Share')</strong></td>
                                    <td>{{ $project->available_share }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('ROI Amount')</strong></td>
                                    <td>{{ showAmount($project->roi_amount) }} {{ $general->cur_text }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('ROI Percentage')</strong></td>
                                    <td>{{ $project->roi_percentage }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Start Date')</strong></td>
                                    <td>{{ showDateTime($project->start_date) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('End Date')</strong></td>
                                    <td>{{ showDateTime($project->end_date) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Maturity Time')</strong></td>
                                    <td>{{ $project->maturity_time }} days</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Category')</strong></td>
                                    <td>{{ $project->category->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Time')</strong></td>
                                    <td>{{ $project->time->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Return Type')</strong></td>
                                    <td>
                                        @if($project->return_type == 1)
                                            <span class="badge badge--success">@lang('Lifetime')</span>
                                        @else
                                            <span class="badge badge--warning">@lang('Repeat')</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($project->return_type == 2)
                                <tr>
                                    <td><strong>@lang('Repeat Times')</strong></td>
                                    <td>{{ $project->repeat_times }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Capital Back')</strong></td>
                                    <td>
                                        @if($project->capital_back == 1)
                                            <span class="badge badge--success">@lang('Yes')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('No')</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>@lang('Status')</strong></td>
                                    <td>
                                        @if($project->status == 1)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @elseif($project->status == 2)
                                            <span class="badge badge--warning">@lang('Ended')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Featured')</strong></td>
                                    <td>
                                        @if($project->featured == 1)
                                            <span class="badge badge--success">@lang('Yes')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('No')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Created At')</strong></td>
                                    <td>{{ showDateTime($project->created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.project.edit', $project->id) }}" class="btn btn--primary">@lang('Edit Project')</a>
                    <a href="{{ route('admin.project.index') }}" class="btn btn--secondary">@lang('Back to List')</a>
                </div>
            </div>
        </div>
    </div>
@endsection 
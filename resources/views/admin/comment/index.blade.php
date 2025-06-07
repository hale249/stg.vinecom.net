@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Project Name')</th>
                                    <th>@lang('Total Replay')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Seen Status')</th>
                                    <th>@lang('Last Replay')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $comment)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb"><img src="{{ getImage(getFilePath('project') . '/' . $comment->project->image, getFileSize('project')) }}" alt="{{ __($comment->project->title) }}" class="plugin_bg"></div>
                                                <div>
                                                    <span class="name fw-bold">{{ __(Str::limit($comment->project->title, 20)) }}</span>
                                                    <br>
                                                    <span class="name text-muted">{{ showAmount($comment->project->goal) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $comment->all_replies_count }}</span>
                                        </td>

                                        <td>
                                            @php echo $comment->statusBadge @endphp
                                        </td>
                                        <td>
                                            @php echo $comment->seenBadge @endphp
                                        </td>
                                        <td>
                                            {{ diffForHumans($comment->updated_at) }}
                                        </td>
                                        <td> <a href="{{ route('admin.comment.details', $comment->id) }}" class="btn btn-outline--primary"><i class="las la-desktop"></i> @lang('Details')</button></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($comments->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($comments) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by project name" />
@endpush

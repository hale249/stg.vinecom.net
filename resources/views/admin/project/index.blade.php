@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Title - Goal')</th>
                                    <th>@lang('Start Date - End Date')</th>
                                    <th>@lang('Số lượng chia sẻ - Chia sẻ có sẵn')</th>
                                    <th>@lang('ROI % - ROI Amount')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Comment')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb"><img
                                                        src="{{ getImage(getFilePath('project') . '/' . $project->image, getFileSize('project')) }}"
                                                        alt="{{ __($project->title) }}" class="plugin_bg"></div>
                                                <div>
                                                    <span
                                                        class="name fw-bold">{{ __(Str::limit($project->title, 20)) }}</span>
                                                    <br>
                                                    <span class="name text-muted">{{ showAmount($project->goal) }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            {{ showDateTime($project->start_date) }}
                                            <br>
                                            {{ showDateTime($project->end_date) }}
                                        </td>
                                        <td class="share-count-column">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-primary">
                                                    {{ getAmount($project->share_count ?? 0) }}
                                                </span>
                                                <span class="text-muted small">
                                                    @lang('Tổng số chia sẻ')
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column mt-1">
                                                <span class="fw-bold {{ $project->available_share > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ getAmount($project->available_share ?? 0) }}
                                                </span>
                                                <span class="text-muted small">
                                                    @lang('Chia sẻ có sẵn')
                                                </span>
                                            </div>
                                            @if($project->share_count > 0)
                                                <div class="progress mt-1" style="height: 4px;">
                                                    @php
                                                        $percentage = $project->share_count > 0 ? (($project->share_count - $project->available_share) / $project->share_count) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ number_format($percentage, 1) }}% @lang('đã bán')</small>
                                            @endif
                                        </td>

                                        <td>
                                            {{ showAmount($project->roi_percentage) }} %
                                            <br>
                                            {{ showAmount($project->roi_amount) }}
                                        </td>
                                        <td>
                                            @php echo $project->typeBadge @endphp
                                        </td>
                                        <td>
                                            <a class="badge badge--primary" href="{{ route('admin.comment.index') }}?search={{ $project->title }}">{{ $project->comment_count }}</a>
                                        </td>
                                        <td>
                                            @php echo $project->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button-group">
                                                <button class="btn btn-sm btn-outline--primary text--primary"
                                                    data-bs-toggle="dropdown">
                                                    <i class="las la-ellipsis-v"></i> @lang('Action')
                                                </button>
                                                <div class="dropdown-menu p-0">
                                                    <a class="dropdown-item text--primary"
                                                        href="{{ route('admin.project.edit', $project->id) }}">
                                                        <i class="las la-pen"></i> @lang('Edit')
                                                    </a>
                                                    <a class="dropdown-item text--info"
                                                        href="{{ route('admin.project.faq.add', $project->id) }}"><i
                                                            class="la la-question-circle"></i> @lang('FAQ')
                                                    </a>

                                                    <a class="dropdown-item text--info"
                                                        href="{{ route('admin.project.documents.index', $project->id) }}">
                                                        <i class="las la-file-pdf"></i> @lang('Tài liệu')
                                                    </a>

                                                    <a class="dropdown-item text--info"
                                                        href="{{ route('admin.project.investHistory', $project->id) }}">
                                                        <i class="las la-user-secret"></i> @lang('Investors')
                                                    </a>

                                                    <a class="dropdown-item text--info"
                                                        href="{{ route('admin.project.seo', $project->id) }}">
                                                        <i class="la la-cog"></i> @lang('SEO Setting')
                                                    </a>

                                                    @if ($project->status != Status::PROJECT_END)
                                                        <a class="dropdown-item text--danger cancelOrderModal"
                                                            data-url="{{ route('admin.project.end', $project->id) }}">
                                                            <i class="lar la-times-circle"></i>
                                                            @lang('End Project')
                                                        </a>
                                                    @endif
                                                </div>

                                                @if ($project->status != Status::PROJECT_END)
                                                    @if ($project->status == Status::ENABLE)
                                                        <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn"
                                                            data-question="@lang('Are you sure to disable this project?')"
                                                            data-action="{{ route('admin.project.status', $project->id) }}">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn"
                                                            data-question="@lang('Are you sure to enable this project?')"
                                                            data-action="{{ route('admin.project.status', $project->id) }}">
                                                            <i class="la la-eye"></i> @lang('Enable')
                                                        </button>
                                                    @endif
                                                @endif

                                            </div>
                                        </td>
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
                @if ($projects->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($projects) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="orderStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="modal-detail"></p>
                        <input type="hidden" name="status">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Title" />
    <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.project.create') }}" type="button"><i
            class="las la-plus"></i> @lang('Add New')</a>
@endpush

@push('style')
    <style>
        .cancelOrderModal {
            cursor: pointer;
        }
        
        .share-count-column {
            min-width: 120px;
        }
        
        .share-count-column .progress {
            background-color: #e9ecef;
        }
        
        .share-count-column .progress-bar {
            transition: width 0.3s ease;
        }
        
        .share-count-column .text-success {
            color: #28a745 !important;
        }
        
        .share-count-column .text-danger {
            color: #dc3545 !important;
        }
        
        .share-count-column .text-primary {
            color: #007bff !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.cancelOrderModal').on('click', function() {
                var modal = $('#orderStatusModal');
                var url = $(this).data('url');
                var status = 2;
                modal.find('form').attr('action', url);
                modal.find('[name=status]').val(status);
                modal.find('.modal-detail').text(`@lang('Are you sure to end this project?')`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

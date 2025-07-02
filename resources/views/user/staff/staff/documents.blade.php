@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">@lang('Contract Documents')</h5>
                <div>
                    <a href="{{ route('user.staff.staff.contract.details', $invest->id) }}" class="btn btn-sm btn-outline--primary">
                        <i class="la la-arrow-left"></i> @lang('Back to Contract')
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>@lang('Project'):</strong> {{ __($invest->project->title) }}
                        </div>
                        <div>
                            <strong>@lang('Investment No'):</strong> {{ $invest->invest_no }}
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="mb-3">
                            <strong>@lang('User'):</strong> {{ __($invest->user->fullname) }} ({{ $invest->user->username }})
                        </div>
                        <div class="mb-3">
                            <strong>@lang('Amount'):</strong> {{ showAmount($invest->total_price) }}
                        </div>
                        <div>
                            <strong>@lang('Date'):</strong> {{ showDateTime($invest->created_at) }}
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="las la-upload"></i> @lang('Upload Document')
                    </button>
                </div>

                <div class="row">
                    <!-- Signed Contracts -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="las la-file-contract"></i> @lang('Signed Contracts')</h5>
                            </div>
                            <div class="card-body">
                                @if($signedContracts->count() > 0)
                                    <div class="table-responsive--md">
                                        <table class="table custom--table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Title')</th>
                                                    <th>@lang('Date')</th>
                                                    <th>@lang('Action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($signedContracts as $document)
                                                    <tr>
                                                        <td>
                                                            <div>{{ __($document->title) }}</div>
                                                            <small class="text-muted">{{ $document->file_size_formatted }}</small>
                                                        </td>
                                                        <td>{{ showDateTime($document->created_at) }}</td>
                                                        <td>
                                                            <div class="button-group">
                                                                <a href="{{ route('user.staff.staff.contract.documents.download', [$invest->id, $document->id]) }}" class="btn btn-sm btn-outline--success">
                                                                    <i class="las la-download"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline--danger delete-btn" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#deleteModal"
                                                                    data-id="{{ $document->id }}"
                                                                    data-title="{{ $document->title }}">
                                                                    <i class="las la-trash-alt"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <i class="las la-folder-open display-4 text-muted"></i>
                                        <p class="mt-2">@lang('No signed contracts uploaded yet')</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Bills -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="las la-money-bill"></i> @lang('Transfer Bills')</h5>
                            </div>
                            <div class="card-body">
                                @if($transferBills->count() > 0)
                                    <div class="table-responsive--md">
                                        <table class="table custom--table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Title')</th>
                                                    <th>@lang('Date')</th>
                                                    <th>@lang('Action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($transferBills as $document)
                                                    <tr>
                                                        <td>
                                                            <div>{{ __($document->title) }}</div>
                                                            <small class="text-muted">{{ $document->file_size_formatted }}</small>
                                                        </td>
                                                        <td>{{ showDateTime($document->created_at) }}</td>
                                                        <td>
                                                            <div class="button-group">
                                                                <a href="{{ route('user.staff.staff.contract.documents.download', [$invest->id, $document->id]) }}" class="btn btn-sm btn-outline--success">
                                                                    <i class="las la-download"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline--danger delete-btn" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#deleteModal"
                                                                    data-id="{{ $document->id }}"
                                                                    data-title="{{ $document->title }}">
                                                                    <i class="las la-trash-alt"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <i class="las la-folder-open display-4 text-muted"></i>
                                        <p class="mt-2">@lang('No transfer bills uploaded yet')</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Other Documents -->
                    @if($otherDocuments->count() > 0)
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="las la-file-alt"></i> @lang('Other Documents')</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive--md">
                                    <table class="table custom--table">
                                        <thead>
                                            <tr>
                                                <th>@lang('Title')</th>
                                                <th>@lang('Description')</th>
                                                <th>@lang('Date')</th>
                                                <th>@lang('Action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($otherDocuments as $document)
                                                <tr>
                                                    <td>
                                                        <div>{{ __($document->title) }}</div>
                                                        <small class="text-muted">{{ $document->file_size_formatted }}</small>
                                                    </td>
                                                    <td>{{ Str::limit($document->description, 50) }}</td>
                                                    <td>{{ showDateTime($document->created_at) }}</td>
                                                    <td>
                                                        <div class="button-group">
                                                            <a href="{{ route('user.staff.staff.contract.documents.download', [$invest->id, $document->id]) }}" class="btn btn-sm btn-outline--success">
                                                                <i class="las la-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline--danger delete-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deleteModal"
                                                                data-id="{{ $document->id }}"
                                                                data-title="{{ $document->title }}">
                                                                <i class="las la-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('user.staff.staff.contract.documents.upload', $invest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">@lang('Upload Document')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="title" class="form-label">@lang('Title') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="type" class="form-label">@lang('Document Type') <span class="text-danger">*</span></label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="signed_contract">@lang('Signed Contract')</option>
                            <option value="transfer_bill">@lang('Transfer Bill')</option>
                            <option value="other">@lang('Other Document')</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">@lang('Description')</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="document_file" class="form-label">@lang('Document File') <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="document_file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">@lang('Allowed file types: PDF, JPG, JPEG, PNG. Max size: 10MB')</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Upload')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">@lang('Delete Document')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure you want to delete this document?')</p>
                    <p class="document-title fw-bold"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--danger">@lang('Delete')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!$signedContracts->count() && !$transferBills->count() && !$otherDocuments->count())
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="las la-info-circle"></i> @lang('No documents have been uploaded for this contract yet. Use the Upload Document button to add documents.')
        </div>
    </div>
</div>
@endif
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Handle delete button click
        $('.delete-btn').on('click', function() {
            var documentId = $(this).data('id');
            var documentTitle = $(this).data('title');
            
            $('#deleteForm').attr('action', "{{ route('user.staff.staff.contract.documents.delete', [$invest->id, 'DOC_ID']) }}".replace('DOC_ID', documentId));
            $('.document-title').text(documentTitle);
        });
    })(jQuery);
</script>
@endpush 
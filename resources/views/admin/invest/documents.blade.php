@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">@lang('Tài liệu hợp đồng')</h5>
                <div>
                    <a href="{{ route('admin.invest.details', $invest->id) }}" class="btn btn-sm btn-outline--primary">
                        <i class="la la-arrow-left"></i> @lang('Quay lại chi tiết đầu tư')
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>@lang('Dự án'):</strong> {{ __($invest->project->title) }}
                        </div>
                        <div>
                            <strong>@lang('Mã đầu tư'):</strong> {{ $invest->invest_no }}
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="mb-3">
                            <strong>@lang('Người dùng'):</strong> {{ __($invest->user->fullname) }} ({{ $invest->user->username }})
                        </div>
                        <div class="mb-3">
                            <strong>@lang('Số tiền'):</strong> {{ showAmount($invest->total_price) }} {{ __($general->cur_text) }}
                        </div>
                        <div>
                            <strong>@lang('Ngày'):</strong> {{ showDateTime($invest->created_at) }}
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="las la-upload"></i> @lang('Thêm tài liệu')
                    </button>
                </div>

                <div class="row">
                    <!-- Signed Contracts -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="las la-file-contract"></i> @lang('Hợp đồng đã ký')</h5>
                            </div>
                            <div class="card-body">
                                @if($signedContracts->count() > 0)
                                    <div class="table-responsive--md">
                                        <table class="table custom--table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Tiêu đề')</th>
                                                    <th>@lang('Ngày')</th>
                                                    <th>@lang('Thao tác')</th>
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
                                                                <a href="{{ route('admin.invest.documents.download', [$invest->id, $document->id]) }}" class="btn btn-sm btn-outline--success">
                                                                    <i class="las la-download"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" 
                                                                    data-action="{{ route('admin.invest.documents.delete', [$invest->id, $document->id]) }}"
                                                                    data-question="@lang('Bạn có chắc chắn muốn xóa tài liệu này không?')"
                                                                    data-btn_class="btn btn--danger">
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
                                        <p class="mt-2">@lang('Chưa có hợp đồng đã ký nào được tải lên')</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Bills -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="las la-money-bill"></i> @lang('Bill chuyển khoản')</h5>
                            </div>
                            <div class="card-body">
                                @if($transferBills->count() > 0)
                                    <div class="table-responsive--md">
                                        <table class="table custom--table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Tiêu đề')</th>
                                                    <th>@lang('Ngày')</th>
                                                    <th>@lang('Thao tác')</th>
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
                                                                <a href="{{ route('admin.invest.documents.download', [$invest->id, $document->id]) }}" class="btn btn-sm btn-outline--success">
                                                                    <i class="las la-download"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" 
                                                                    data-action="{{ route('admin.invest.documents.delete', [$invest->id, $document->id]) }}"
                                                                    data-question="@lang('Bạn có chắc chắn muốn xóa tài liệu này không?')"
                                                                    data-btn_class="btn btn--danger">
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
                                        <p class="mt-2">@lang('Chưa có bill chuyển khoản nào được tải lên')</p>
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
                                <h5 class="mb-0"><i class="las la-file-alt"></i> @lang('Tài liệu khác')</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive--md">
                                    <table class="table custom--table">
                                        <thead>
                                            <tr>
                                                <th>@lang('Tiêu đề')</th>
                                                <th>@lang('Mô tả')</th>
                                                <th>@lang('Ngày')</th>
                                                <th>@lang('Thao tác')</th>
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
                                                            <a href="{{ route('admin.invest.documents.download', [$invest->id, $document->id]) }}" class="btn btn-sm btn-outline--success">
                                                                <i class="las la-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" 
                                                                data-action="{{ route('admin.invest.documents.delete', [$invest->id, $document->id]) }}"
                                                                data-question="@lang('Bạn có chắc chắn muốn xóa tài liệu này không?')"
                                                                data-btn_class="btn btn--danger">
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
            <form action="{{ route('admin.invest.documents.upload', $invest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">@lang('Thêm tài liệu')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="title" class="form-label">@lang('Tiêu đề') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="type" class="form-label">@lang('Loại tài liệu') <span class="text-danger">*</span></label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="signed_contract">@lang('Hợp đồng đã ký')</option>
                            <option value="transfer_bill">@lang('Bill chuyển khoản')</option>
                            <option value="other">@lang('Tài liệu khác')</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">@lang('Mô tả')</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="document_file" class="form-label">@lang('Tệp tài liệu') <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="document_file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">@lang('Định dạng cho phép: PDF, JPG, JPEG, PNG. Dung lượng tối đa: 10MB')</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Hủy')</button>
                    <button type="submit" class="btn btn--primary">@lang('Tải lên')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!$signedContracts->count() && !$transferBills->count() && !$otherDocuments->count())
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="las la-info-circle"></i> @lang('Chưa có tài liệu nào được tải lên cho hợp đồng này. Sử dụng nút Thêm tài liệu để tải lên.')
        </div>
    </div>
</div>
@endif

<x-confirmation-modal />
@endsection 
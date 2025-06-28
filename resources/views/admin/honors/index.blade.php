@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Ảnh</th>
                                <th>Khoảng thời gian</th>
                                <th>Kích hoạt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($honors as $honor)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $honor->title }}</span>
                                </td>
                                <td>
                                    <img src="{{ getImage(getFilePath('honors') . '/' . $honor->image, '100x100') }}" 
                                         alt="{{ $honor->title }}" class="rounded" width="60">
                                </td>
                                <td>
                                    <span>{{ showDateTime($honor->start_date, 'd/m/Y') }} - {{ showDateTime($honor->end_date, 'd/m/Y') }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        $text = '';
                                        if($honor->is_active) {
                                            $statusClass = 'badge--success';
                                            $text = 'Active';
                                        } else {
                                            $statusClass = 'badge--danger';
                                            $text = 'Inactive';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $text }}</span>
                                </td>
                                <td>
                                    <div class="button--group">
                                        <a href="{{ route('admin.honors.edit', $honor->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="la la-pencil"></i> Sửa
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-action="{{ route('admin.honors.destroy', $honor->id) }}"
                                                data-question="Bạn có chắc chắn muốn xóa vinh danh này không?">
                                            <i class="la la-trash"></i> Xóa
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline--{{ $honor->is_active ? 'warning' : 'success' }} confirmationBtn"
                                                data-action="{{ route('admin.honors.status', $honor->id) }}"
                                                data-question="Bạn có chắc chắn muốn thay đổi trạng thái của vinh danh này không?">
                                            @if($honor->is_active)
                                                <i class="la la-eye-slash"></i> Vô hiệu hóa
                                            @else
                                                <i class="la la-eye"></i> Kích hoạt
                                            @endif
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">Không tìm thấy vinh danh nào</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($honors->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($honors) }}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Xác nhận</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="question"></p>
            </div>
            <div class="modal-footer">
                <form action="" method="POST" class="confirmationForm">
                    @csrf
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">Không</button>
                    <button type="submit" class="btn btn--primary">Có</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.honors.create') }}" class="btn btn-sm btn-outline--primary">
        <i class="la la-plus"></i> Thêm mới
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            
            $('.confirmationBtn').on('click', function() {
                var modal = $('#confirmationModal');
                var form = $('.confirmationForm');
                var action = $(this).data('action');
                var question = $(this).data('question');
                
                form.attr('action', action);
                modal.find('.question').text(question);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush 
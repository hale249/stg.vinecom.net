@extends('user.staff.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 mb-4">
                <div class="card-body">
                    <form action="{{ route('user.staff.manager.hr.attendance') }}" method="GET" class="form-inline mb-4">
                        <div class="input-group mb-3 mr-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">@lang('Tháng')</span>
                            </div>
                            <input type="month" name="month" class="form-control" value="{{ $month ?? now()->format('Y-m') }}">
                        </div>

                        <div class="input-group mb-3 mr-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">@lang('Nhân viên')</span>
                            </div>
                            <select name="user_id" class="form-control">
                                <option value="">@lang('Tất cả')</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ request()->user_id == $staff->id ? 'selected' : '' }}>{{ $staff->fullname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group mb-3">
                            <button type="submit" class="btn btn--primary">@lang('Lọc')</button>
                        </div>
                    </form>

                    <div class="d-flex flex-wrap justify-content-between mb-3">
                        <h5 class="mb-3">@lang('Thống kê chấm công tháng') {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('m/Y') }}</h5>
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('user.staff.manager.hr.attendance.export', ['month' => $month, 'user_id' => request()->user_id]) }}" class="btn btn--info btn-sm mr-2">
                                <i class="la la-download"></i> @lang('Xuất CSV')
                            </a>
                            <button type="button" class="btn btn--success btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="la la-upload"></i> @lang('Nhập CSV')
                            </button>
                            <button type="button" class="btn btn--primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                                <i class="la la-plus"></i> @lang('Thêm chấm công')
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Nhân viên')</th>
                                    <th>@lang('Mã NV')</th>
                                    <th>@lang('Ngày')</th>
                                    <th>@lang('Số công')</th>
                                    <th>@lang('Ghi chú')</th>
                                    <th>@lang('Hành động')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->staff->fullname ?? 'N/A' }}</td>
                                        <td>{{ $attendance->employee_code }}</td>
                                        <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                        <td>{{ $attendance->working_day }}</td>
                                        <td>{{ $attendance->note ?? 'N/A' }}</td>
                                        <td>
                                            <button type="button" class="btn btn--primary btn-sm editBtn" 
                                                data-id="{{ $attendance->id }}"
                                                data-staff_id="{{ $attendance->staff_id }}"
                                                data-staff_name="{{ $attendance->staff->fullname ?? 'N/A' }}"
                                                data-date="{{ $attendance->date->format('Y-m-d') }}"
                                                data-working_day="{{ $attendance->working_day }}"
                                                data-note="{{ $attendance->note }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editAttendanceModal">
                                                <i class="la la-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn--danger btn-sm deleteBtn" 
                                                data-id="{{ $attendance->id }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="6">{{ __($emptyMessage ?? 'Không có dữ liệu') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>

            <div class="card b-radius--10">
                <div class="card-body">
                    <h5 class="mb-3">@lang('Tổng hợp theo nhân viên')</h5>
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Nhân viên')</th>
                                    <th>@lang('Mã NV')</th>
                                    <th>@lang('Tổng số công')</th>
                                    <th>@lang('Số ngày có mặt')</th>
                                    <th>@lang('Trung bình/ngày')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summaryByEmployee as $summary)
                                    <tr>
                                        <td>{{ $summary->staff->fullname ?? 'N/A' }}</td>
                                        <td>{{ $summary->employee_code }}</td>
                                        <td>{{ number_format($summary->total_working_days, 1) }}</td>
                                        <td>{{ $summary->total_days }}</td>
                                        <td>{{ $summary->total_days > 0 ? number_format($summary->total_working_days / $summary->total_days, 2) : 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="5">{{ __($emptyMessage ?? 'Không có dữ liệu') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Attendance Modal -->
    <div class="modal fade" id="addAttendanceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Thêm chấm công')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.staff.manager.hr.attendance.store') }}" method="POST" id="attendanceForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Nhân viên')</label>
                                    <select name="staff_id" id="staff_select" class="form-control" required>
                                        <option value="">@lang('Chọn nhân viên')</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Tháng')</label>
                                    <input type="month" id="month_select" class="form-control" value="{{ $month ?? now()->format('Y-m') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div id="calendar_container" class="mb-4">
                            <div class="alert alert-info">
                                @lang('Vui lòng chọn nhân viên để hiển thị lịch chấm công')
                            </div>
                        </div>
                        
                        <div id="attendance_form" class="border rounded p-3 mb-3" style="display: none;">
                            <h6 class="mb-3">@lang('Thông tin chấm công')</h6>
                            <input type="hidden" name="date" id="selected_date">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Ngày được chọn')</label>
                                        <input type="text" id="display_date" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Số công')</label>
                                        <select name="working_day" class="form-control" required>
                                            <option value="1">1 - @lang('Đủ công')</option>
                                            <option value="0.5">0.5 - @lang('Nửa công')</option>
                                            <option value="0">0 - @lang('Nghỉ')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="form-group">
                                        <label>@lang('Ghi chú')</label>
                                        <textarea name="note" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn--primary">@lang('Lưu')</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Đóng')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Sửa chấm công')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.staff.manager.hr.attendance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="attendance_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Nhân viên')</label>
                            <select name="staff_id" id="edit_staff_id" class="form-control" required>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Ngày')</label>
                            <input type="date" name="date" id="edit_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Số công')</label>
                            <select name="working_day" id="edit_working_day" class="form-control" required>
                                <option value="1">1 - @lang('Đủ công')</option>
                                <option value="0.5">0.5 - @lang('Nửa công')</option>
                                <option value="0">0 - @lang('Nghỉ')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Ghi chú')</label>
                            <textarea name="note" id="edit_note" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Hủy')</button>
                        <button type="submit" class="btn btn--primary">@lang('Cập nhật')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Xác nhận xóa')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="deleteForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Bạn có chắc chắn muốn xóa bản ghi chấm công này không?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Hủy')</button>
                        <button type="submit" class="btn btn--danger">@lang('Xóa')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Nhập dữ liệu từ CSV')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.staff.manager.hr.attendance.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('File CSV')</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                            <small class="text-muted">@lang('Định dạng: employee_code, employee_name, date (YYYY-MM-DD), working_day, note')</small>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="overwrite" class="custom-control-input" id="overwrite" checked>
                                <label class="custom-control-label" for="overwrite">@lang('Ghi đè dữ liệu nếu trùng (ngày + mã nhân viên)')</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('user.staff.manager.hr.attendance.export') }}" class="text-primary">
                                <i class="la la-download"></i> @lang('Tải mẫu CSV')
                            </a>
                        </div>
                        
                        @if(session()->has('import_errors'))
                            <div class="alert alert-danger">
                                <h5>@lang('Lỗi nhập dữ liệu:')</h5>
                                <ul>
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Hủy')</button>
                        <button type="submit" class="btn btn--primary">@lang('Nhập dữ liệu')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.staff.manager.hr.salary') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
        <i class="las la-angle-double-left"></i> @lang('Quay lại')
    </a>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Initialize modals properly
        $('#addAttendanceModal').on('show.bs.modal', function () {
            $(this).find('form')[0].reset();
            $('#calendar_container').html('<div class="alert alert-info">@lang("Vui lòng chọn nhân viên để hiển thị lịch chấm công")</div>');
            $('#attendance_form').hide();
        });
        
        // Calendar functionality
        $('#staff_select, #month_select').on('change', function() {
            const staffId = $('#staff_select').val();
            const month = $('#month_select').val();
            
            if (staffId && month) {
                generateCalendar(staffId, month);
            }
        });
        
        function generateCalendar(staffId, month) {
            const [year, monthNum] = month.split('-');
            const daysInMonth = new Date(year, monthNum, 0).getDate();
            const firstDay = new Date(year, monthNum - 1, 1).getDay(); // 0 = Sunday
            
            // Fetch existing attendance data for this staff and month
            $.ajax({
                url: '{{ route("user.staff.manager.hr.attendance") }}',
                type: 'GET',
                data: {
                    user_id: staffId,
                    month: month,
                    format: 'json'
                },
                success: function(response) {
                    renderCalendar(daysInMonth, firstDay, year, monthNum, response.attendances || []);
                },
                error: function() {
                    renderCalendar(daysInMonth, firstDay, year, monthNum, []);
                }
            });
        }
        
        function renderCalendar(daysInMonth, firstDay, year, month, attendanceData) {
            // Create attendance lookup for quick access
            const attendanceLookup = {};
            attendanceData.forEach(att => {
                const dateKey = new Date(att.date).getDate();
                attendanceLookup[dateKey] = att;
            });
            
            let html = `
                <div class="calendar">
                    <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                        <h6 class="m-0">@lang('Lịch chấm công tháng') ${month}/${year}</h6>
                    </div>
                    <div class="calendar-grid">
                        <div class="row">
                            <div class="col-sm">@lang('CN')</div>
                            <div class="col-sm">@lang('T2')</div>
                            <div class="col-sm">@lang('T3')</div>
                            <div class="col-sm">@lang('T4')</div>
                            <div class="col-sm">@lang('T5')</div>
                            <div class="col-sm">@lang('T6')</div>
                            <div class="col-sm">@lang('T7')</div>
                        </div>
            `;
            
            // Add empty cells for days before the first day of the month
            let dayCount = 0;
            html += '<div class="row">';
            
            for (let i = 0; i < firstDay; i++) {
                html += '<div class="col-sm empty-day"></div>';
                dayCount++;
            }
            
            // Add cells for each day of the month
            for (let day = 1; day <= daysInMonth; day++) {
                // Start a new row if necessary
                if (dayCount % 7 === 0 && dayCount > 0) {
                    html += '</div><div class="row">';
                }
                
                const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const att = attendanceLookup[day];
                
                let cellClass = 'day-cell';
                let statusBadge = '';
                
                if (att) {
                    if (att.working_day === 1) {
                        cellClass += ' bg-success-light';
                        statusBadge = '<span class="badge bg-success">1</span>';
                    } else if (att.working_day === 0.5) {
                        cellClass += ' bg-warning-light';
                        statusBadge = '<span class="badge bg-warning">0.5</span>';
                    } else {
                        cellClass += ' bg-danger-light';
                        statusBadge = '<span class="badge bg-danger">0</span>';
                    }
                } else {
                    cellClass += ' bg-light';
                }
                
                html += `
                    <div class="col-sm">
                        <div class="${cellClass}" data-date="${dateStr}" data-day="${day}">
                            <div class="day-number">${day}</div>
                            ${statusBadge}
                        </div>
                    </div>
                `;
                
                dayCount++;
            }
            
            // Add empty cells for the remaining days to complete the last row
            while (dayCount % 7 !== 0) {
                html += '<div class="col-sm empty-day"></div>';
                dayCount++;
            }
            
            html += '</div></div>';
            
            // Add some CSS for the calendar
            html += `
                <style>
                    .calendar-grid .row {
                        margin-bottom: 10px;
                    }
                    .calendar-grid .col-sm {
                        padding: 5px;
                        text-align: center;
                    }
                    .day-cell {
                        padding: 10px 5px;
                        border-radius: 5px;
                        cursor: pointer;
                        transition: all 0.2s;
                        min-height: 50px;
                    }
                    .day-cell:hover {
                        background-color: #f0f0f0;
                        transform: scale(1.05);
                    }
                    .day-cell.selected {
                        box-shadow: 0 0 0 2px #6366f1;
                    }
                    .empty-day {
                        background-color: transparent;
                    }
                    .bg-success-light {
                        background-color: rgba(16, 185, 129, 0.1);
                    }
                    .bg-warning-light {
                        background-color: rgba(245, 158, 11, 0.1);
                    }
                    .bg-danger-light {
                        background-color: rgba(239, 68, 68, 0.1);
                    }
                </style>
            `;
            
            $('#calendar_container').html(html);
            
            // Add click event to day cells
            $('.day-cell').on('click', function() {
                $('.day-cell').removeClass('selected');
                $(this).addClass('selected');
                
                const date = $(this).data('date');
                const day = $(this).data('day');
                
                $('#selected_date').val(date);
                $('#display_date').val(date);
                $('#attendance_form').show();
                
                // If there's existing attendance data for this day, pre-fill the form
                if (attendanceLookup[day]) {
                    const att = attendanceLookup[day];
                    $('select[name="working_day"]').val(att.working_day);
                    $('textarea[name="note"]').val(att.note);
                } else {
                    // Reset form for new entries
                    $('select[name="working_day"]').val(1);
                    $('textarea[name="note"]').val('');
                }
            });
        }
        
        $('.editBtn').on('click', function() {
            var modal = $('#editAttendanceModal');
            var data = $(this).data();
            
            modal.find('#attendance_id').val(data.id);
            modal.find('#edit_staff_id').val(data.staff_id);
            modal.find('#edit_date').val(data.date);
            modal.find('#edit_working_day').val(data.working_day);
            modal.find('#edit_note').val(data.note);
        });
        
        $('.deleteBtn').on('click', function() {
            var id = $(this).data('id');
            var form = $('#deleteForm');
            form.attr('action', '{{ route('user.staff.manager.hr.attendance.delete', 0) }}'.replace('0', id));
        });
        
        // Debug form submission
        $('form').on('submit', function(e) {
            console.log('Form submitted:', $(this).attr('action'));
            // Uncomment to debug form issues
            // e.preventDefault();
            // console.log('Form data:', $(this).serialize());
        });
        
    })(jQuery);
</script>
@endpush 
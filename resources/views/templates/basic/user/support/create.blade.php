@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card custom--card">
                    <div class="card-body">
                        <form action="{{ route('ticket.store') }}" class="disableSubmission" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Tiêu đề</label>
                                    <input type="text" name="subject" value="{{ old('subject') }}"
                                        class="form-control form--control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Độ ưu tiên</label>
                                    <select name="priority" class="form-select form--control select2-basic"
                                        data-minimum-results-for-search="-1" required>
                                        <option value="3">Cao</option>
                                        <option value="2">Trung bình</option>
                                        <option value="1">Thấp</option>
                                    </select>
                                </div>
                                <div class="col-12 form-group">
                                    <label class="form-label">Nội dung</label>
                                    <textarea name="message" id="inputMessage" rows="6" class="form-control form--control" required>{{ old('message') }}</textarea>
                                </div>


                                <div class="col-md-9">
                                    <button type="button" class="btn btn-dark btn-sm addAttachment my-2"><i
                                            class="fas fa-plus"></i> Thêm tệp đính kèm </button>
                                    <p class="mb-2"><span class="text--base">Tối đa 5 tệp có thể tải lên | Dung lượng tải lên tối đa là {{ convertToReadableSize(ini_get('upload_max_filesize')) }} | Định dạng tệp cho phép: .jpg, .jpeg, .png, .pdf, .doc, .docx</span>
                                    </p>
                                    <div class="row fileUploadsContainer">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn--base w-100 my-2" type="submit"><i
                                            class="las la-paper-plane"></i> Gửi yêu cầu
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $(".select2-basic").select2();


            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush

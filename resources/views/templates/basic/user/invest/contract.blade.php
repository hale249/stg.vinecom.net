@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Investment Contract')</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('user.invest.contract.watermark', $invest->id) }}" class="btn btn-sm btn-outline--warning">
                                <i class="las la-eye"></i> @lang('View with Status')
                            </a>
                            <a href="{{ route('user.invest.contract.download', $invest->id) }}" class="btn btn-sm btn-outline--success">
                                <i class="las la-download"></i> @lang('Download PDF')
                            </a>
                            <a href="{{ route('user.invest.documents', $invest->id) }}" class="btn btn-sm btn-outline--info">
                                <i class="las la-file-upload"></i> @lang('Contract Documents')
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="las la-info-circle"></i>
                            @lang('Please review your investment contract before proceeding.')
                        </div>

                        <div class="contract-content mb-4">
                            <div class="container">
                                <div class="contract-header">
                                    <div>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                                    <div>Độc lập - Tự do - Hạnh phúc</div>
                                    <div>-------o0o-------</div>
                                    <div class="date">Hà Nội, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}</div>
                                </div>
                                <div class="main-title">HỢP ĐỒNG HỢP TÁC KINH DOANH</div>
                                <div class="doc-number">Số: {{ $invest->invest_no }}/SMB/2025-BHG-…</div>

                                <div class="section-label">CĂN CỨ:</div>
                                <ul>
                                    <li class="italic">Căn cứ Bộ luật dân sự năm 2015;</li>
                                    <li class="italic">Căn cứ Luật thương mại năm 2005 và các văn bản hướng dẫn thi hành;</li>
                                    <li class="italic">Căn cứ Luật Đầu tư năm 2020 và các văn bản hướng dẫn thi hành;</li>
                                    <li class="italic">Căn cứ các văn bản Pháp luật Việt Nam liên quan;</li>
                                    <li class="italic">Căn cứ vào năng lực của BHG - Viettel Post;</li>
                                    <li class="italic">Căn cứ nhu cầu và khả năng của các Bên;</li>
                                </ul>

                                <p><strong><span class="italic">Hôm nay, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}, tại trụ sở Công ty cổ phần Tập đoàn đầu tư Bắc Hải, chúng tôi gồm có:</span></strong></p>

                                <p><strong>BÊN A: CÔNG TY CỔ PHẦN TẬP ĐOÀN ĐẦU TƯ BẮC HẢI (BHG)</strong></p>
                                <ul>
                                    <li>Trụ sở chính: Tầng 04, Tòa nhà Thương mại và Dịch vụ B-CC, Dự án khu nhà ở Ngân Hà Vạn Phúc, phố Tố Hữu, phường Vạn Phúc, quận Hà Đông, thành phố Hà Nội;</li>
                                    <li>Đại diện (Ông): TRẦN VĂN DUY – Chức vụ: Tổng Giám đốc;</li>
                                    <li>Mã doanh nghiệp: 0109034215;</li>
                                    <li>Điện thoại: 092 153 939 – Email: hotro@tapdoanbachai.vn;</li>
                                    <li>Website: tapdoanbachai.vn;</li>
                                    <li>Số tài khoản: 0511100235999 – Ngân hàng: Thương mại cổ phần Quân đội (MB) - CN Vạn Phúc;</li>
                                </ul>

                                <div class="section-label">BÊN B: Ông/Bà: {{ $invest->user->fullname }}</div>
                                <ul>
                                    <li>Địa chỉ: {{ $invest->user->address ?? 'N/A' }};</li>
                                    <li>Ngày sinh: {{ $invest->user->date_of_birth ? \Carbon\Carbon::parse($invest->user->date_of_birth)->format('d/m/Y') : 'N/A' }};</li>
                                    <li>CC/CCCD số: {{ $invest->user->id_number ?? 'N/A' }} – Cấp ngày: {{ $invest->user->id_issue_date ? \Carbon\Carbon::parse($invest->user->id_issue_date)->format('d/m/Y') : 'N/A' }} – Nơi cấp: {{ $invest->user->id_issue_place ?? 'N/A' }};</li>
                                    <li>Điện thoại: {{ $invest->user->mobile ?? 'N/A' }} – Email: {{ $invest->user->email ?? 'N/A' }};</li>
                                    <li>Số tài khoản: {{ $invest->user->bank_account_number ?? 'N/A' }} – Ngân hàng: {{ $invest->user->bank_name ?? 'N/A' }} – Chi nhánh: {{ $invest->user->bank_branch ?? 'N/A' }};</li>
                                    <li>Tên chủ tài khoản: {{ $invest->user->bank_account_holder ?? 'N/A' }};</li>
                                    <li>Mã số khách hàng: {{ $invest->user->username ?? 'N/A' }};</li>
                                    <li>Mã số thuế TNCN: {{ auth()->user()->tax_number ?? 'N/A' }};</li>
                                    <li>Họ tên chuyên viên tư vấn: {{ $invest->project->consultant_name ?? 'N/A' }} – Mã số CVTV: {{ $invest->project->consultant_code ?? 'N/A' }};</li>
                                </ul>

                                <div class="section-label">XÉT RẰNG:</div>
                                <ul>
                                    <li>Bên A là pháp nhân, được thành lập và hoạt động hợp pháp tại Việt Nam, có chức năng hoạt động đầu tư kinh doanh trong các lĩnh vực: Bất động sản, cho thuê máy móc, thiết bị, xây dựng … được Sở Kế hoạch và Đầu tư thành phố Hà Nội cấp giấy chứng nhận đăng ký kinh doanh;</li>
                                    <li>Bên A là pháp nhân đang thực hiện đầu tư hạ tầng hệ thống tủ Smartbox cho Tổng Công ty cổ phần Bưu chính Viettel thuê thông qua Hợp đồng hợp tác kinh doanh - kinh doanh hạ tầng Smartbox giữa Tổng Công ty cổ phần Bưu chính Viettel và Công ty cổ phần Tập đoàn đầu tư Bắc Hải;</li>
                                    <li>Bên B là cá nhân, có điều kiện về tài chính, có đầy đủ năng lực hành vi dân sự, có nhu cầu hợp tác với Bên A để cùng kinh doanh trong các lĩnh vực hoạt động của Bên A được nêu cụ thể trong hợp đồng này trên nguyên tắc hai bên cùng có lợi;</li>
                                </ul>

                                <p><strong><span class="italic">Bởi vậy, sau khi thống nhất, bàn bạc trên tinh thần hoàn toàn tự nguyện, hai bên đồng ý ký hợp đồng với các điều kiện và điều khoản sau đây:</span></strong></p>

                                <div class="section-title">ĐIỀU 1: NỘI DUNG HỢP TÁC KINH DOANH</div>
                                <p><strong>1.1 Mục đích hợp tác kinh doanh:</strong> Tại hợp đồng này các bên thống nhất thỏa thuận như sau: Bên A đồng ý nhận và Bên B tự nguyện hợp tác đầu tư theo hình thức góp vốn bằng tiền mặt/tài sản vào Công ty cổ phần Tập đoàn đầu tư Bắc Hải, để Bên A thực hiện dự án Smartbox, dựa vào kết quả kinh doanh phát sinh từ dự án Smartbox do Bên A, Bên B đã cùng nhau góp vốn, để phân chia lợi tức.</p>
                                <p><strong>1.2 Chi tiết dự án:</strong> Dự án đầu tư kinh doanh hạ tầng hệ thống tủ lưu giữ và giao nhận tự động Smartbox thông minh theo hình thức cho thuê, cụ thể:</p>
                                <ul>
                                    <li>Bên A thực hiện đầu tư các hệ thống tủ Smartbox tại các địa điểm mà Bên A và Viettel Post thống nhất. Tủ Smart Box phải đảm bảo theo tiêu chuẩn kỹ thuật, nhận diện hình ảnh đã được Bên A và Viettel Post thống nhất và đảm bảo hoạt động liên tục, ổn định để cung ứng dịch vụ cho Khách hàng;</li>
                                    <li>Bên A chịu trách nhiệm lắp đặt, chi trả chi phí thuê mặt bằng đặt máy và toàn bộ các chi phí vận hành liên quan (không bao gồm vận hành dịch vụ và vận hành phần mềm);</li>
                                    <li>Viettel Post chịu trách nhiệm quảng bá, tổ chức vận hành kinh doanh, các dịch vụ trên tủ Smart Box. Tiếp nhận, giải quyết khiếu nại của khách hàng sử dụng;</li>
                                    <li>Viettel Post chịu trách nhiệm chi trả phí dịch vụ khai thác tủ Smart Box cho Bên A;</li>
                                    <li>Nguồn thu của dự án: Nguồn thu từ phí dịch vụ khai thác tủ Smartbox mà Viettel Post chi trả cho Bên A.</li>
                                </ul>

                                <div class="section-title">ĐIỀU 2: PHẠM VI VÀ PHƯƠNG THỨC HỢP TÁC</div>
                                <p>Hai bên cùng hợp tác kinh doanh mà không thành lập tổ chức pháp nhân mới, theo đó Bên B ủy quyền cho Bên A được toàn quyền điều phối, sử dụng số tiền, để Bên A thực hiện đầu tư dự án Smartbox tại Điều 1 Hợp đồng này, đem lại kết quả cho cả Bên A và Bên B.</p>

                                <div class="section-title">ĐIỀU 3: THỜI HẠN THỰC HIỆN HỢP ĐỒNG</div>
                                <p>Thời hạn hợp tác là {{ $invest->project_duration ?? '…' }} tháng ({{ $invest->duration_in_words ?? '…' }}), không hủy ngang tính từ thời điểm Bên B thực hiện góp đủ tiền hợp tác kinh doanh.</p>

                                <div class="section-title">ĐIỀU 4: QUY ĐỊNH VỀ NGÀY LÀM VIỆC, NGÀY NGHỈ (NGHỈ LỄ, TẾT)</div>
                                <p>Ngày làm việc được quy định của hợp đồng này là từ thứ hai đến thứ sáu hàng tuần. Thời gian nghỉ Lễ, Tết hàng năm của Bên A theo quy định của Pháp luật Việt Nam.</p>

                                <div class="section-title">ĐIỀU 5: ĐẦU TƯ HỢP TÁC, PHÂN CHIA KẾT QUẢ KINH DOANH</div>
                                <p><strong>5.1 Đầu tư hợp tác kinh doanh</strong></p>
                                <p><strong>5.1.1 Hình thức đầu tư kinh doanh:</strong> Bên B thực hiện góp vốn đầu tư vào dự án Smartbox của Bên A bằng tiền mặt/tài sản (Việt Nam đồng).</p>
                                <p><strong>5.1.2 Mức đầu tư:</strong></p>
                                <ul>
                                    <li>Bên B đồng ý hợp tác kinh doanh bằng tài sản góp vốn là Đồng Việt Nam để Bên A thực hiện đầu tư theo Điều 1 của Hợp đồng này. Bên A đồng ý hợp tác với Bên B và cam kết sử dụng nguồn vốn hợp tác của Bên B để thực hiện dự án cụ thể như sau:</li>
                                    <li>Bên A nhận đầu tư theo từng suất đầu tư. Giá trị suất đầu tư được tính theo giá trị định giá của 01 (một) hệ tủ Smartbox tiêu chuẩn theo quy định của Viettel Post là Smartbox tiêu chuẩn tức Smartbox cấu hình 1 + 3 (1 module chính chứa CPU + 3 module phụ) tại 01 vị trí là: 100.000.000 Việt Nam đồng/suất đầu tư (Bằng chữ: Một trăm triệu đồng chẵn).</li>
                                    <li>Số tiền đầu tư theo Hợp đồng này là: {{ number_format($invest->amount, 0) }} VNĐ. (Bằng chữ: {{ $invest->amount_in_words ?? '…' }} chẵn). Tương đương: {{ $invest->smartbox_units ?? '…' }} suất đầu tư Smartbox.</li>
                                    <li>Nguyên tắc góp vốn: Nguyên tắc góp vốn phải đảm bảo hai bên cùng có lợi và không bên nào được can thiệp vào công việc nội bộ của nhau.</li>
                                </ul>
                                <p><strong>5.2 Phân chia kết quả kinh doanh</strong></p>
                                <p>Bên A thực hiện chia lợi tức cho Bên B, số tiền này là khoản lợi tức từ việc đầu tư giữa hai bên. Tiền lợi tức được tính theo thỏa thuận, được quy định tại Điều 5.3 của Hợp đồng này. Việc chia lợi tức, cụ thể như sau:</p>
                                <p><strong>5.3 Lợi tức kinh doanh:</strong> Bên B được chi trả lợi tức và tiền gốc từ dự án Smartbox như sau:</p>
                                <ul>
                                    <li>Bên B được hưởng {{ $invest->profit_percentage ?? '…' }}%/năm/số tiền Bên B đã ký kết ở Hợp đồng. Số tiền lợi tức đầu tư Bên A chuyển khoản cho Bên B vào tài khoản ngân hàng của Bên B do Bên B cung cấp, khi có sự thay đổi, Bên B phải chủ động thông báo cho Bên A bằng văn bản.</li>
                                    <li>Hiệu quả đầu tư chi tiết như sau:</li>
                                    <li>Đơn vị tính: VNĐ</li>
                                    <li>Ghi chú: Nếu ngày thanh toán vào thứ 7 hoặc CN hoặc ngày nghỉ lễ theo quy định của công ty thì sẽ chi trả vào ngày làm việc tiếp theo.</li>
                                    <li>Công thức tính: Tổng cộng dòng tiền đầu tư = Số tiền đầu tư ban đầu + Lợi tức</li>
                                    <li>Trong đó: Lợi tức là số tiền nhận theo thỏa thuận tại Điều 5.3 của hợp đồng số {{ $invest->invest_no }}/HĐHTKD/SMB/2025-BHG-… trừ đi thuế TNCN theo Điều 5.3 khoản 1 nêu trên.</li>
                                </ul>
                                <p><strong>5.3.1 Các khoản thuế, các phí, lệ phí theo quy định của pháp luật:</strong></p>
                                <ul>
                                    <li>Khi nhận được lợi tức do Bên A phân chia, bàn giao, Bên B phải có nghĩa vụ nộp thuế TNCN và các nghĩa vụ thuế khác theo quy định của pháp luật hiện hành.</li>
                                    <li>Thuế TNCN theo quy định tại điểm c khoản 2 Điều 6 Thông tư số 92/2015/TT-BTC ngày 15/6/2015 của Bộ Tài chính - thuế suất khấu trừ tại nguồn bằng 5% thu nhập chịu thuế. Bên B sẽ nhận được chứng từ khấu trừ thuế TNCN do Bên A xuất với số tiền lợi tức trừ đi số thuế phải nộp theo quy định.</li>
                                </ul>
                                <p><strong>5.4 Chuyển nhượng, tặng cho, rút vốn, thanh quyết toán Hợp đồng</strong></p>
                                <p><strong>5.4.1 Chuyển nhượng, tặng, cho vốn đã góp theo Hợp đồng:</strong></p>
                                <ul>
                                    <li>Bên B được quyền chuyển nhượng, tặng, cho tài sản đã góp theo hợp đồng này cho bất kỳ tổ chức, cá nhân nào khác (Bên thứ 3) với điều kiện Bên thứ 3 phải có nghĩa vụ và trách nhiệm kế thừa toàn bộ quyền và nghĩa vụ của Bên B tại hợp đồng này. Khi Bên B có nhu cầu thực hiện chuyển nhượng, tặng của Bên B cho Bên C thì Bên B có trách nhiệm thông báo cho Bên A và cung cấp cho Bên A bản hợp đồng gốc đã ký giữa hai bên, CC/CCCD/Hộ chiếu, giấy phép kinh doanh, quyết định thành lập của Bên thứ 3. Sự thay đổi chỉ có hiệu lực khi có sự đồng ý và xác nhận của Bên A bằng văn bản. Trường hợp này, Bên B phải chịu chi phí liên quan tới việc cho tặng là 2% (hai phần trăm) giá trị hợp đồng. Sau khi chuyển nhượng, chuyển đổi hợp đồng mọi quyền lợi và trách nhiệm giữa Bên A và Bên B sẽ đương nhiên chấm dứt theo Hợp đồng này.</li>
                                </ul>
                                <p><strong>5.4.2 Rút vốn góp hợp tác kinh doanh trước thời hạn:</strong></p>
                                <ul>
                                    <li>Trường hợp, Bên B rút vốn trước thời hạn ghi trong Hợp đồng này, Bên B phải thỏa thuận và nếu Bên A đồng ý, Bên B sẽ được rút lại số tiền đã đầu tư với Bên A vào bất kỳ thời điểm nào và Bên B sẽ phải chịu phạt hợp đồng với số tiền là 8% (tám phần trăm) giá trị Hợp đồng, đồng thời phải hoàn trả lại Bên A toàn bộ các quyền và lợi ích đã nhận bao gồm khoản tiền phân chia lợi tức theo kết quả kinh doanh, các khoản thưởng, các lợi ích khác (nếu có). Bên A sẽ giải quyết, xử lý yêu cầu rút vốn của Bên B trong vòng 30 (ba mươi) ngày làm việc kể từ ngày hai bên thống nhất thỏa thuận rút vốn. Kể từ thời điểm Bên A giải quyết xong yêu cầu rút vốn đầu tư của Bên B, các quyền và nghĩa vụ giữa hai bên theo hợp đồng này được chấm dứt.</li>
                                </ul>
                                <p><strong>5.4.3 Thanh quyết toán Hợp đồng:</strong></p>
                                <ul>
                                    <li>Hai bên tiến hành thanh quyết toán Hợp đồng hợp tác này vào ngày kết thúc Hợp đồng. Bên A phải thanh toán cho Bên B khoản vốn góp/tài sản hợp tác kinh doanh và lợi tức của kỳ chưa thanh toán theo phụ lục Hợp đồng.</li>
                                    <li>Khi thanh quyết toán Hợp đồng, quyền và nghĩa vụ theo hợp đồng của Bên A và Bên B sẽ mặc nhiên chấm dứt.</li>
                                </ul>
                                <p><strong>5.5 Tài sản đảm bảo:</strong></p>
                                <ul>
                                    <li>Căn cứ Hợp đồng nguyên tắc số 01/2024-HĐCNQSDĐ giữa Công ty cổ phần Sơn Hải và Công ty cổ phần Tập đoàn đầu tư Bắc Hải ngày 22 tháng 01 năm 2024 về việc chuyển nhượng quyền sử dụng đất;</li>
                                    <li>Bên A đồng ý sử dụng một phần tài sản tại Dự án khu dân cư SH-Land, đường Lý Nam Đế, phường Trà Bá, thành phố Pleiku, tỉnh Gia Lai thuộc quyền định đoạt của Bên A làm tài sản đảm bảo cho Hợp đồng hợp tác này.</li>
                                    <li>Chi tiết thông tin tài sản như sau:</li>
                                    <li>Vị trí số thửa: {{ $invest->asset_plot_number ?? '…' }};</li>
                                    <li>Tờ bản đồ số: {{ $invest->asset_map_number ?? '…' }};</li>
                                    <li>Diện tích lô đất: {{ $invest->asset_area ?? '…' }} m²;</li>
                                    <li>Mục đích sử dụng: Đất ở tại đô thị;</li>
                                    <li>Thời hạn sử dụng: Lâu dài;</li>
                                    <li>Địa chỉ thửa đất: Khu dân cư SH - Land đường Lý Nam Đế, phường Trà Bá, thành phố Pleiku, tỉnh Gia Lai;</li>
                                    <li>Giá trị tài sản: {{ number_format($invest->asset_value, 0) ?? '…' }} đồng (Bằng chữ: {{ $invest->asset_value_in_words ?? '…' }} đồng chẵn).</li>
                                </ul>
                                <p><strong>5.6 Quyền cấn trừ:</strong></p>
                                <p>Không ảnh hưởng tới bất kỳ các quyền và các biện pháp khắc phục khác của Bên A được quy định trong Hợp đồng này hoặc theo pháp luật Việt Nam, Bên A có quyền sử dụng một phần hoặc toàn bộ Tiền góp vốn, mà không cần phải thông báo cho Bên B, để: Cấn trừ hoặc trả cho khoản bồi thường cho bất kỳ tổn thất, thiệt hại, chi phí và phí tổn nào mà Bên A hoặc bất kỳ Bên thứ ba nào khác phải gánh chịu hoặc có thể phải gánh chịu do bất kỳ vi phạm nào của Bên B đối với việc góp vốn/tài sản vào công ty.</p>

                                <div class="section-title">ĐIỀU 6: QUYỀN VÀ NGHĨA VỤ CỦA BÊN A</div>
                                <ul>
                                    <li>Chịu trách nhiệm triển khai dự án Smartbox;</li>
                                    <li>Quản lý, điều hành, các hoạt động kinh doanh chung theo quy định của Pháp luật;</li>
                                    <li>Thanh quyết toán các khoản lợi ích mà Bên B được hưởng đúng như cam kết trong hợp đồng này, việc thanh toán các khoản lợi tức mà Bên B được hưởng, nhưng vẫn đảm bảo được hoạt động, đầu tư kinh doanh của Bên A, cũng như việc Bên A đảm bảo được việc thực hiện việc chi trả các nghĩa vụ thuế cho nhà nước và các nghĩa vụ khác đối với cá nhân, cơ quan tổ chức;</li>
                                    <li>Có quyền đơn phương chấm dứt Hợp đồng và không hoàn lại số tiền, tài sản Bên B đã hợp tác chuyển giao cho Bên A, khi Bên B có những hành vi cung cấp các thông tin, bí mật kinh doanh cho Bên thứ ba (3) nhằm cản trở, phá hoại các hoạt động kinh doanh hợp pháp của Bên A, cũng như có những phát ngôn tiêu cực gây mất đoàn kết nội bộ làm ảnh hưởng đến uy tín của Bên A, cũng như đối tác, khách hàng của Bên A;</li>
                                    <li>Trong trường hợp bất khả kháng xảy ra, Bên A sẽ ưu tiên quyền rút vốn đặc biệt cho Bên B, quy trình, thời hạn, số lượng rút vốn sẽ do hai bên tự thỏa thuận bằng văn bản;</li>
                                    <li>Bảo mật tuyệt đối các thông tin, văn bản, tài liệu và các thỏa thuận trong thời gian Hợp đồng có hiệu lực, kể cả khi Hợp đồng chấm dứt.</li>
                                </ul>

                                <div class="section-title">ĐIỀU 7: QUYỀN VÀ NGHĨA VỤ CỦA BÊN B</div>
                                <ul>
                                    <li>Được nhận khoản tiền phân chia từ kết quả kinh doanh dự án Smartbox;</li>
                                    <li>Thực hiện việc đầu tư hợp tác đầy đủ, đúng hạn như đã cam kết;</li>
                                    <li>Cung cấp đầy đủ, chính xác các giấy tờ tùy thân cho Bên A, đồng thời chịu trách nhiệm về tính trung thực, chính xác của những tài liệu này;</li>
                                    <li>Bên B cam kết việc ký kết hợp đồng hợp tác kinh doanh này là hoàn toàn tự nguyện trên nguyên tắc bình đẳng, không bị ép buộc, lừa dối;</li>
                                    <li>Cam kết nguồn vốn dùng để hợp tác đầu tư thuộc quyền sở hữu hợp pháp của Bên B, không bị chi phối bởi bất kỳ một tổ chức, cá nhân (Bên thứ 3) bất kỳ, nếu có gian dối hoặc tranh chấp nguồn vốn hợp tác Bên B có trách nhiệm tự xử lý không làm ảnh hưởng đến uy tín, lợi ích của Bên A;</li>
                                    <li>Được quyền tặng, cho hợp đồng như đã nêu tại hợp đồng này hoặc để lại thừa kế theo quy định của Pháp luật về thừa kế;</li>
                                    <li>Bồi thường thiệt hại cho Bên A nếu vi phạm các điều khoản đã cam kết trong hợp đồng;</li>
                                    <li>Chịu trách nhiệm nộp các khoản thuế có liên quan từ hoạt động hợp tác kinh doanh như: Thuế thu nhập cá nhân đối với các khoản lợi tức hàng tháng được hưởng; và các khoản Thuế, phí có liên quan từ việc đầu tư;</li>
                                    <li>Sẵn sàng và tự nguyện chia sẻ những rủi ro do các yếu tố khách quan có thể xảy ra tác động đến kết quả kinh doanh của Bên A như thiên tai, lũ lụt, chiến tranh, khủng hoảng kinh tế và các yếu tố bên ngoài khác;</li>
                                    <li>Thiện chí, trung thực và không lôi kéo người khác trong quá trình giải quyết mâu thuẫn giữa các bên;</li>
                                    <li>Đã hiểu rõ chức năng của Bên A, cũng như hiểu rõ ngành nghề, lĩnh vực, sản phẩm kinh doanh của Bên A trước khi quyết định việc góp tài sản và chuyển giao tài sản cho Bên A, để Bên A thực hiện các hoạt động đầu tư, kinh doanh theo hợp đồng này;</li>
                                    <li>Không đơn phương chấm dứt hợp đồng khi không thuộc một trong các trường hợp được quyền đơn phương chấm dứt hợp đồng, theo quy định của pháp luật;</li>
                                    <li>Thực hiện đúng những nội dung cam kết khác trong hợp đồng này. Các nghĩa vụ khác như quy định Hợp đồng này và các quy định khác của pháp luật Việt Nam.</li>
                                </ul>

                                <div class="section-title">ĐIỀU 8: BẤT KHẢ KHÁNG</div>
                                <p><strong>8.1</strong> Một sự kiện được coi là sự kiện bất khả kháng phải đáp ứng được ba điều kiện sau: 1) Xảy ra một cách khách quan; 2) Không thể lường trước được; 3) Không thể khắc phục được mặc dù đã áp dụng mọi biện pháp cần thiết và khả năng cho phép. Trên cơ sở đó, các Bên đồng ý rằng các sự kiện sau đây được coi là sự kiện bất khả kháng:</p>
                                <ul>
                                    <li>Các sự kiện xuất phát từ yếu tố tự nhiên như: sóng thần, động đất, bão tố, lũ lụt và các thảm họa tự nhiên khác;</li>
                                    <li>Các sự kiện xuất phát từ yếu tố xã hội như: chiến tranh, bạo loạn, đảo chính, dịch bệnh, hỏa hoạn, biểu tình, đình công, tình trạng khẩn cấp;</li>
                                    <li>Các sự kiện xuất phát từ yếu tố Nhà nước như: sự thay đổi chính sách của Nhà nước, thay đổi trong quy định pháp luật, các biện pháp áp dụng của cơ quan Nhà nước có thẩm quyền trong quản lý, điều hành hoặc việc thi hành văn bản quy phạm pháp luật cá biệt.</li>
                                </ul>
                                <p><strong>8.2</strong> Bên có nghĩa vụ không thực hiện đúng nghĩa vụ do sự kiện bất khả kháng mà gây thiệt hại cho Bên kia thì không phải chịu trách nhiệm bồi thường thiệt hại nhưng phải thông báo ngay cho Bên kia khi phát sinh sự kiện bất khả kháng và cung cấp các chứng cứ chứng minh hợp lệ, đồng thời phải áp dụng mọi biện pháp cần thiết và khả năng cho phép để hạn chế tác động của sự kiện bất khả kháng. Bên có nghĩa vụ phải tiếp tục thực hiện nghĩa vụ khi sự kiện bất khả kháng chấm dứt.</p>

                                <div class="section-title">ĐIỀU 9: BẢO ĐẢM THỰC HIỆN HỢP ĐỒNG VÀ BỒI THƯỜNG THIỆT HẠI</div>
                                <p><strong>9.1 Bảo đảm thực hiện Hợp đồng:</strong></p>
                                <p>Phạt hợp đồng: Sau khi ký kết Hợp đồng hợp tác kinh doanh và trong suốt quá trình thực hiện hợp đồng, nếu Bên B đơn phương chấm dứt hợp đồng hoặc vi phạm hợp đồng dẫn đến hợp đồng bị chấm dứt thì Bên B sẽ phải chịu phạt hợp đồng với số tiền là 8% (tám phần trăm) giá trị Hợp đồng, đồng thời phải hoàn trả lại Bên A toàn bộ các quyền và lợi ích đã nhận bao gồm khoản tiền phân chia kết quả kinh doanh, các khoản thưởng, các lợi ích khác.</p>
                                <p><strong>9.2 Bồi thường thiệt hại:</strong></p>
                                <ul>
                                    <li>Trường hợp Bên B đơn phương chấm dứt Hợp đồng hoặc vi phạm Hợp đồng dẫn đến hợp đồng bị chấm dứt thì Bên đó ngoài việc bị phạt Hợp đồng theo khoản 9.1 nêu trên còn phải bồi thường thiệt hại cho Bên A như sau: Trường hợp Bên B đơn phương chấm dứt Hợp đồng hoặc vi phạm hợp đồng dẫn đến Hợp đồng bị chấm dứt thì Bên B phải bồi thường cho Bên A toàn bộ thiệt hại thực tế mà Bên A chứng minh được.</li>
                                    <li>Trường hợp Bên B tuyên bố và xác nhận rằng, trước khi ký kết Hợp đồng này với Bên A, Bên B đã đọc và hiểu toàn bộ Hợp đồng này và các phụ lục hợp đồng này, đã xem xét và kiểm tra các cam đoan và cam kết của Bên A. Bên B tuyên bố và xác nhận rằng, Hợp đồng này, các cam đoan và cam kết của công ty là rõ ràng đối với Bên B. Do đó theo hợp đồng này, Bên B đồng ý rằng, Bên B sẽ không thực hiện bất kỳ khiếu nại, khiếu kiện nào chống lại Bên A để giải phóng các trách nhiệm và nghĩa vụ của Bên B theo hợp đồng này bằng việc đưa ra lý do rằng, Bên B không nhận thức đầy đủ về bất kỳ hoặc toàn bộ các quy định trong hợp đồng này.</li>
                                </ul>

                                <div class="section-title">ĐIỀU 10: LUẬT ĐIỀU CHỈNH VÀ GIẢI QUYẾT TRANH CHẤP</div>
                                <p><strong>10.1</strong> Hợp đồng này được điều chỉnh bởi luật pháp Việt Nam;</p>
                                <p><strong>10.2</strong> Các Bên cam kết thực hiện nghiêm chỉnh các nghĩa vụ của mình trong hợp đồng. Trong quá trình thực hiện hợp đồng, nếu có bất kỳ khó khăn, trở ngại hoặc vấn đề nào phát sinh, các Bên sẽ ngay lập tức cùng nhau bàn bạc, tìm biện pháp giải quyết. Bất kỳ một tranh chấp, bất đồng hay khiếu nại nào mà phát sinh từ Hợp đồng này hay liên quan đến Hợp đồng này, hay sự giải thích vi phạm, kết thúc hay hiệu lực của nó, sẽ được các Bên giải quyết thông qua đàm phán, trao đổi thân thiện, trên tinh thần hợp tác, tôn trọng quyền lợi lẫn nhau. Các cuộc đàm phán, trao đổi như vậy sẽ được bắt đầu ngay sau khi một Bên trao cho Bên kia yêu cầu bằng văn bản về việc cần giải quyết, hoặc yêu cầu thiết lập cuộc gặp để bàn bạc việc giải quyết.</p>
                                <p><strong>10.3</strong> Nếu trong vòng 60 ngày sau ngày gửi thông báo, Bên nhận được thông báo không trả lời, hoặc không gặp gỡ trao đổi, hoặc các Bên liên quan có gặp gỡ, trao đổi nhưng không thể giải quyết tranh chấp hoặc thỏa thuận hướng giải quyết phù hợp thì tranh chấp đó sẽ được giải quyết tại Tòa án có thẩm quyền.</p>
                                <p><strong>10.4</strong> Trong thời gian giải quyết tranh chấp, các Bên sẽ tiếp tục thực hiện các nội dung khác của hợp đồng mà không liên quan hoặc bị ảnh hưởng bởi sự tranh chấp.</p>
                                <p><strong>10.5</strong> Mỗi Bên sẽ cộng tác với Bên kia trong việc tiết lộ và cung cấp tất cả các thông tin và tài liệu mà Bên kia yêu cầu có liên quan đến việc giải quyết tranh chấp, tuân theo các nghĩa vụ giữ bí mật ràng buộc của Bên đó.</p>
                                <p><strong>10.6 Hòa giải:</strong> Trong trường hợp xảy ra tranh chấp liên quan đến các điều khoản của hợp đồng hợp tác kinh doanh, các Bên trước hết phải cố gắng giải quyết tranh chấp trên tinh thần hữu nghị.</p>
                                <p><strong>10.7 Phương thức giải quyết tranh chấp:</strong> Nếu tranh chấp không thể giải quyết bằng thương lượng, một Bên có quyền thực hiện các thủ tục tố tụng đưa tranh chấp ra giải quyết tại một Tòa án có thẩm quyền, phán quyết của Tòa án là phán quyết cuối cùng và có giá trị thực hiện đối với các Bên. Bên B phải thanh toán, bồi hoàn, hoàn lại cho Bên A mọi chi phí, lệ phí và phí tổn phát sinh cho Bên A do thực hiện thủ tục tố tụng và/hoặc xử lý hợp đồng hợp tác kinh doanh trước bất kỳ cơ quan tài phán nào, ngoại trừ trường hợp Tòa án quyết định ngược lại.</p>

                                <div class="section-title">ĐIỀU 11: BẢO MẬT</div>
                                <p><strong>11.1</strong> Ngoại trừ, được quy định khác đi trong Hợp đồng này, không Bên nào cung cấp bất kỳ thông cáo báo chí nào hoặc tuyên bố trước công luận, hoặc giao tiếp với bất kỳ phương tiện thông tin đại chúng, hoặc Bên thứ ba nào liên quan đến hợp đồng này hoặc các giao dịch dự kiến tại đây mà không có sự phê chuẩn trước của Bên còn lại, và các Bên sẽ phối hợp với nhau liên quan đến thời điểm và nội dung của bất kỳ tuyên bố công khai đó.</p>
                                <p><strong>11.2</strong> Hợp đồng này và bất kỳ thông tin nào nhận được trong quá trình thực hiện Hợp đồng này liên quan đến bất kỳ việc kinh doanh, hoạt động, khách hàng, nhà cung cấp hoặc đối tác kinh doanh nào của bất kỳ Bên nào (trừ những thông tin đã hoặc được coi là đã được công khai mà việc công khai đó không phải do việc vi phạm Điều này) sẽ được giữ bí mật một cách nghiêm ngặt và các Bên sẽ không báo cáo, tiết lộ cho Bên thứ ba hoặc công bố thông tin đó, toàn bộ hoặc một phần.</p>

                                <div class="section-title">ĐIỀU 13: HIỆU LỰC CỦA VIỆC SỬA ĐỔI VÀ CHẤM DỨT HỢP ĐỒNG</div>
                                <p><strong>13.1</strong> Trong trường hợp một trong hai Bên có mong muốn sửa đổi, bổ sung phải thông báo trước cho Bên kia bằng văn bản;</p>
                                <p><strong>13.2</strong> Khi chấm dứt hợp đồng trong mọi trường hợp, Hai Bên tiến hành đối chiếu công nợ và xác lập bằng văn bản;</p>
                                <p><strong>13.3</strong> Quyền và nghĩa vụ của các Bên được thực hiện theo hợp đồng này và các văn bản khác có liên quan;</p>
                                <p><strong>13.4</strong> Hai Bên cam kết thực hiện theo các điều kiện và điều khoản trên và nhất trí ký kết thỏa thuận hợp tác này.</p>

                                <div class="section-title">ĐIỀU 14: ĐIỀU KHOẢN CHUNG</div>
                                <p><strong>14.1</strong> Hợp đồng này được hiểu và chịu sự điều chỉnh của Pháp luật nước Cộng hòa xã hội chủ nghĩa Việt Nam;</p>
                                <p><strong>14.2</strong> Hai Bên cam kết thực hiện tất cả những điều khoản đã cam kết trong hợp đồng. Bên nào vi phạm hợp đồng gây thiệt hại cho Bên kia (trừ trong trường hợp bất khả kháng) thì phải bồi thường toàn bộ thiệt hại xảy ra và chịu phạt vi phạm hợp đồng theo quy định của pháp luật;</p>
                                <p><strong>14.3</strong> Hai Bên cam kết thực hiện mọi quy định của Pháp luật về phòng chống rửa tiền và phòng chống tài trợ khủng bố;</p>
                                <p><strong>14.4</strong> Mọi sửa đổi, bổ sung Hợp đồng này đều phải được làm bằng văn bản và có chữ ký của các Bên. Các Phụ lục là phần không tách rời của Hợp đồng;</p>
                                <p><strong>14.5</strong> Mọi tranh chấp phát sinh trong quá trình thực hiện hợp đồng được giải quyết trước hết qua thương lượng, hòa giải, nếu hòa giải không thành, việc tranh chấp sẽ được giải quyết tại Tòa án có thẩm quyền.</p>

                                <div class="section-title">ĐIỀU 15: HIỆU LỰC HỢP ĐỒNG</div>
                                <p><strong>15.1</strong> Hợp đồng này có hiệu lực thi hành kể từ ngày được ký. Mọi sửa đổi, bổ sung Hợp đồng này chỉ có hiệu lực khi được Hai Bên thỏa thuận bằng văn bản.</p>
                                <p><strong>15.2</strong> Hợp đồng này và các Phụ lục kèm theo tạo thành một Hợp đồng thống nhất, không thể tách rời và có giá trị pháp lý với các Bên kể từ ngày ký.</p>
                                <p><strong>15.3</strong> Hợp đồng này có giá trị ràng buộc đối với mọi cá nhân hay tổ chức tiếp nhận hoặc kế thừa toàn bộ hoặc từng phần các quyền và nghĩa vụ của mỗi Bên. Nếu vào bất cứ thời điểm nào, bất cứ điều khoản nào của Hợp đồng này trở nên vô hiệu hoặc bất hợp pháp hoặc không thể thi hành thì điều khoản đó sẽ không ảnh hưởng đến tính hợp pháp, hiệu lực và khả năng thi hành các điều khoản khác trong Hợp đồng này. Việc một Bên không thực hiện toàn bộ hoặc một phần bất cứ quyền nào hoặc việc bỏ qua bất cứ vi phạm nào sẽ không ngăn cản Bên đó thực hiện các quyền này sau đó và sẽ không được xem là việc từ bỏ bất cứ các vi phạm về sau đối với việc đó hoặc vi phạm bất cứ điều khoản khác của Hợp đồng. Hợp đồng này có giá trị bắt buộc thi hành đối với mỗi Bên và không thể hủy ngang trừ trường hợp chấm dứt theo quy định của Hợp đồng và theo phán quyết của cơ quan nhà nước có thẩm quyền.</p>
                                <p><strong>15.4</strong> Trong trường hợp có bất kỳ sự thay đổi cơ cấu tổ chức hay cơ cấu sở hữu vốn nào của một Bên trong Hợp đồng (bao gồm nhưng không giới hạn ở việc hợp nhất, sáp nhập, giải thể, chia tách, chấm dứt hoạt động, cổ phần hóa, cho thuê doanh nghiệp …), Bên đó có nghĩa vụ phải báo trước cho Bên kia ít nhất là 90 ngày trước khi việc thay đổi có hiệu lực. Trong các trường hợp đó, Bên có thay đổi có nghĩa vụ phải ưu tiên giải quyết các nghĩa vụ đối với Bên kia theo Hợp đồng này và phải đảm bảo để tổ chức hoặc cá nhân kế thừa các quyền và nghĩa vụ của Bên đó phải tôn trọng và tiếp tục thực hiện đầy đủ các quy định của Hợp đồng này.</p>
                                <p><strong>15.5</strong> Trong trường hợp vì bất kỳ lý do gì mà một điều khoản bất kỳ của Hợp Đồng này bị vô hiệu mà không ảnh hưởng đến hiệu lực của toàn bộ Hợp đồng thì điều khoản đó sẽ được coi là bị hủy bỏ và việc này không làm ảnh hưởng đến những điều khoản khác của Hợp đồng và hiệu lực pháp lý của Hợp đồng. Tùy thuộc tình hình cụ thể, Bên A và Bên B sẽ cùng bàn bạc, thảo luận để sửa đổi, bổ sung các điều khoản bị vô hiệu trên cơ sở các nguyên tắc và nội dung cơ bản của Hợp đồng này.</p>
                                <p><strong>15.6</strong> Hợp đồng hết hiệu lực, khi hết thời hạn Hợp đồng theo quy định tại Điều 3 của hợp đồng này hoặc các trường hợp khác theo quy định của Pháp luật.</p>
                                <p>Sau khi Bên B nhận được lợi tức kỳ cuối và nhận được số tiền gốc của Hợp đồng thì hợp đồng trên sẽ tự động được thanh lý.</p>
                                <p>Hợp đồng này có 15 Điều, được lập thành 02 (hai) bản bằng tiếng Việt, mỗi Bên giữ một (01) bản có giá trị pháp lý như nhau và có hiệu lực kể từ ngày ký./.</p>

                                <div class="text-center mt-4">
                                    <form action="{{ route('user.invest.confirm', $invest->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn--base">@lang('Confirm Investment')</button>
                                    </form>
                                    <a href="{{ route('user.home') }}" class="btn btn--danger">@lang('Cancel')</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        .contract-content {
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            line-height: 1.6;
            color: #000000;
        }
        .contract-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: #000000;
        }
        .contract-header div {
            margin-bottom: 5px;
            line-height: 1.8;
        }
        .contract-header .date {
            text-align: right;
            font-weight: normal;
            margin-bottom: 10px;
            margin-top: 15px;
        }
        .main-title {
            margin: 15px 0;
            font-size: 16pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            text-align: center;
            color: #000000;
        }
        .doc-number {
            text-align: center;
            font-weight: normal;
            margin-bottom: 10px;
            color: #000000;
        }
        .section-label {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 6px;
            color: #000000;
        }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
            text-align: left;
            color: #000000;
        }
        .contract-content p, .contract-content ul, .contract-content li {
            font-size: 13pt;
            text-align: justify;
            text-justify: inter-word;
            color: #000000;
        }
        .contract-content ul {
            list-style-type: disc;
            padding-left: 30px;
            margin: 5px 0 15px 0;
        }
        .contract-content li {
            margin-bottom: 5px;
            text-align: justify;
        }
        .italic { 
            font-style: italic;
            color: #000000;
        }
        .bold { 
            font-weight: bold;
            color: #000000;
        }
    </style>
@endsection
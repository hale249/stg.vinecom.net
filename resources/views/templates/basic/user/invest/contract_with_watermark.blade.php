@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="contract-page-bg py-4 d-flex justify-content-center align-items-center" style="min-height: 100vh; background: #f6f7fb;">
        <div class="contract-card card shadow-lg" style="max-width: 900px; width: 100%; border-radius: 18px;">
            <div class="card-header bg-white border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between" style="border-radius: 18px 18px 0 0;">
                <h4 class="mb-2 mb-md-0 font-weight-bold" style="font-size: 1.5rem;">@lang('Investment Contract')</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('user.invest.contract', $invest->id) }}" class="btn btn-outline-primary btn-sm px-3 fw-bold">
                        <i class="las la-eye"></i> @lang('View Original')
                    </a>
                    <a href="{{ route('user.invest.contract.download', $invest->id) }}" class="btn btn-outline-success btn-sm px-3 fw-bold">
                        <i class="las la-download"></i> @lang('Download PDF')
                    </a>
                </div>
            </div>
            <div class="card-body p-4" style="background: #fff; border-radius: 0 0 18px 18px;">
                <div class="alert alert-info mb-4" style="font-size: 1rem;">
                    <i class="las la-info-circle"></i>
                    @if($invest->status == \App\Constants\Status::INVEST_RUNNING)
                        @lang('This contract has been approved and is legally valid.')
                    @else
                        @lang('This contract is pending approval and is not yet legally valid.')
                    @endif
                </div>
                <div class="contract-content position-relative" style="background: #fff; border-radius: 12px; padding: 32px 24px; box-shadow: 0 2px 16px rgba(0,0,0,0.07); min-height: 600px;">
                    {!! $contractContent !!}
                </div>
            </div>
        </div>
    </div>
    <style>
        .contract-content {
            font-family: "Times New Roman", Times, serif;
            font-size: 15px;
            line-height: 1.7;
            color: #222;
            word-break: break-word;
            margin-top: 40px !important;
        }
        .contract-content .container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            padding: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            margin-top: 24px !important;
        }
        .contract-content .header,
        .contract-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: #000;
        }
        .contract-content .header div, .contract-header div {
            margin-bottom: 5px;
            line-height: 1.8;
        }
        .contract-content .header .date, .contract-header .date {
            text-align: right;
            font-weight: normal;
            margin-bottom: 10px;
            margin-top: 15px;
        }
        .contract-content .main-title, .main-title {
            margin: 15px 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            text-align: center;
            color: #000;
        }
        .contract-content .doc-number, .doc-number {
            text-align: center;
            font-weight: normal;
            margin-bottom: 10px;
            color: #000;
        }
        .contract-content .section-label, .section-label {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 6px;
            color: #000;
        }
        .contract-content p, .contract-content ul, .contract-content li {
            font-size: 15px;
            text-align: justify;
            text-justify: inter-word;
            color: #222;
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
        .italic { font-style: italic; color: #222; }
        .bold { font-weight: bold; color: #222; }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
            text-align: left;
            color: #000;
        }
        /* Watermark styles */
        .contract-content div[style*="position: fixed"] {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) rotate(-45deg) !important;
            z-index: 1000 !important;
            pointer-events: none !important;
            opacity: 0.18 !important;
            font-size: 3.2rem !important;
            color: #e74c3c !important;
            font-family: Arial, sans-serif !important;
            font-weight: bold !important;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.08);
        }
        @media (max-width: 991px) {
            .contract-card { max-width: 100% !important; }
            .contract-content { padding: 16px 4px !important; font-size: 13px; }
        }
    </style>
@endsection 
@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="project-details-modern">
        <!-- Hero Section -->
        <div class="project-hero">
            <div class="container">
                <div class="hero-content">
                    <div class="breadcrumb-modern">
                        <a href="{{ route('home') }}" class="breadcrumb-link">
                            <i class="las la-home"></i>
                            Trang chủ
                        </a>
                        <i class="las la-angle-right"></i>
                        <a href="{{ route('projects') }}" class="breadcrumb-link">Dự án</a>
                        <i class="las la-angle-right"></i>
                        <span class="breadcrumb-current">{{ __($project->title) }}</span>
                    </div>
                    
                    <div class="project-header">
                        <div class="project-title-section">
                            <h1 class="project-title">{{ __($project->title) }}</h1>
                            <div class="project-status-badge">
                                @if($project->status == 1)
                                    <span class="status-active">
                                        <i class="las la-check-circle"></i>
                                        Đang hoạt động
                                    </span>
                                @else
                                    <span class="status-inactive">
                                        <i class="las la-pause-circle"></i>
                                        Tạm dừng
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="project-meta-header">
                            <div class="meta-item">
                                <i class="las la-calendar"></i>
                                <span>Bắt đầu: {{ $project->start_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="las la-clock"></i>
                                <span>Kết thúc: {{ $project->end_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="las la-chart-line"></i>
                                <span>ROI: {{ getAmount($project->roi_percentage) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="project-content-wrapper">
                <div class="row">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <!-- Project Gallery -->
                        <div class="project-gallery-modern">
                            <div class="main-image-container">
                                <img src="{{ getImage(getFilePath('project') . '/' . $project->image) }}" 
                                     alt="{{ __($project->title) }}" 
                                     class="main-project-image"
                                     id="mainProjectImage">
                                
                                @if($project->status == 1 && $project->available_share > 0)
                                    <div class="investment-badge">
                                        <i class="las la-fire"></i>
                                        <span>Đang đầu tư</span>
                                    </div>
                                @endif
                            </div>
                            
                            @if (!empty($project->gallery) && count($project->gallery) > 0)
                                <div class="gallery-thumbnails">
                                    <div class="thumbnail-item active" data-image="{{ getImage(getFilePath('project') . '/' . $project->image) }}">
                                        <img src="{{ getImage(getFilePath('project') . '/' . $project->image) }}" alt="Main Image">
                                    </div>
                                    @foreach ($project->gallery as $index => $gallery)
                                        @if ($index < 4)
                                            <div class="thumbnail-item" data-image="{{ getImage(getFilePath('project') . '/' . $gallery) }}">
                                                <img src="{{ getImage(getFilePath('project') . '/' . $gallery) }}" alt="Gallery Image {{ $index + 1 }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Project Stats Cards -->
                        <div class="project-stats-modern">
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="las la-chart-line"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-value">{{ getAmount($project->roi_percentage) }}%</span>
                                        <span class="stat-label">Tỷ suất lợi nhuận</span>
                                    </div>
                                </div>
                                
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="las la-calendar-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-value">{{ $project->maturity_time }}</span>
                                        <span class="stat-label">Tháng</span>
                                    </div>
                                </div>
                                
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="las la-wallet"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-value">{{ showAmount($project->share_amount) }}</span>
                                        <span class="stat-label">Số tiền tối thiểu đầu tư</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Investment Progress -->
                        <div class="investment-progress-modern">
                            <div class="progress-header">
                                <h4>Tiến độ đầu tư</h4>
                                <span class="progress-percentage">{{ $project->investment_progress }}%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: {{ $project->investment_progress }}%"></div>
                            </div>
                            <div class="progress-stats">
                                <div class="progress-stat">
                                    <span class="stat-number">{{ showAmount($project->target_amount) }}</span>
                                    <span class="stat-text">Mục tiêu dự án</span>
                                </div>
                                <div class="progress-stat">
                                    <span class="stat-number">{{ showAmount($project->remaining_amount) }}</span>
                                    <span class="stat-text">Số tiền còn lại</span>
                                </div>
                                <div class="progress-stat">
                                    <span class="stat-number">{{ showAmount($project->invested_amount) }}</span>
                                    <span class="stat-text">Đã đầu tư</span>
                                </div>
                            </div>
                        </div>

                        <!-- Content Tabs -->
                        <div class="content-tabs-modern">
                            <ul class="nav nav-tabs-modern" id="projectTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                        <i class="las la-info-circle"></i>
                                        Tổng quan
                                    </button>
                                </li>
                                
                                @if ($project->faqs->isNotEmpty())
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab">
                                            <i class="las la-question-circle"></i>
                                            Câu hỏi thường gặp
                                        </button>
                                    </li>
                                @endif
                                
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="location-tab" data-bs-toggle="tab" data-bs-target="#location" type="button" role="tab">
                                        <i class="las la-map-marker-alt"></i>
                                        Vị trí
                                    </button>
                                </li>
                                
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button" role="tab">
                                        <i class="las la-comments"></i>
                                        Bình luận <span class="comment-count">({{ $commentCount }})</span>
                                    </button>
                                </li>
                                
                                @if($documents->isNotEmpty())
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                                            <i class="las la-file-pdf"></i>
                                            Tài liệu
                                        </button>
                                    </li>
                                @endif
                            </ul>
                            
                            <div class="tab-content-modern" id="projectTabContent">
                                <!-- Overview Tab -->
                                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                    <div class="overview-content">
                                        <div class="project-description">
                                            @php echo $project->description @endphp
                                        </div>
                                        
                                        <div class="share-section">
                                            <h5>Chia sẻ dự án</h5>
                                            <div class="social-share-buttons">
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                                                   target="_blank" class="share-btn facebook">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>
                                                <a href="https://twitter.com/intent/tweet?text={{ __($project->title) }}&amp;url={{ urlencode(url()->current()) }}" 
                                                   target="_blank" class="share-btn twitter">
                                                    <i class="fab fa-twitter"></i>
                                                </a>
                                                <a href="https://pinterest.com/pin/create/bookmarklet/?media={{ getImage(getFilePath('project') . '/' . $project->image, getFileSize('project')) }}&url={{ urlencode(url()->current()) }}" 
                                                   target="_blank" class="share-btn pinterest">
                                                    <i class="fab fa-pinterest-p"></i>
                                                </a>
                                                <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}" 
                                                   target="_blank" class="share-btn linkedin">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </a>
                                                <button class="share-btn copy-link" data-link="{{ url()->current() }}">
                                                    <i class="las la-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ Tab -->
                                @if ($project->faqs->isNotEmpty())
                                    <div class="tab-pane fade" id="faq" role="tabpanel">
                                        <div class="faq-section">
                                            <div class="faq-accordion" id="faqAccordion">
                                                @foreach ($project->faqs as $index => $faq)
                                                    <div class="faq-item">
                                                        <div class="faq-header" id="faq{{ $index }}">
                                                            <button class="faq-button {{ $index == 0 ? '' : 'collapsed' }}" 
                                                                    type="button" 
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#faqCollapse{{ $index }}">
                                                                <span>{{ __($faq->question) }}</span>
                                                                <i class="las la-plus"></i>
                                                            </button>
                                                        </div>
                                                        <div id="faqCollapse{{ $index }}" 
                                                             class="faq-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                                             data-bs-parent="#faqAccordion">
                                                            <div class="faq-body">
                                                                {{ __($faq->answer) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Location Tab -->
                                <div class="tab-pane fade" id="location" role="tabpanel">
                                    <div class="location-section">
                                        <h5>Vị trí dự án</h5>
                                        <div class="map-container">
                                            {!! @$project->map_url !!}
                                        </div>
                                    </div>
                                </div>

                                <!-- Comments Tab -->
                                <div class="tab-pane fade" id="comments" role="tabpanel">
                                    <div class="comments-section">
                                        @if (auth()->check())
                                            <div class="comment-form-modern">
                                                <div class="user-avatar">
                                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile'), avatar: true) }}" 
                                                         alt="User Avatar">
                                                </div>
                                                <form action="{{ route('user.comment.store', $project->id) }}" 
                                                      class="comment-form ajaxForm" method="post">
                                                    @csrf
                                                    <div class="form-group">
                                                        <textarea class="form-control comment-input" 
                                                                  name="comment" 
                                                                  placeholder="Viết bình luận của bạn..." 
                                                                  required></textarea>
                                                        <button class="comment-submit" type="submit">
                                                            <i class="las la-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        
                                        <div class="comments-list" id="commentsList">
                                            @foreach ($comments as $comment)
                                                <div class="comment-item">
                                                    <div class="comment-avatar">
                                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$comment->user->image, getFileSize('userProfile'), avatar: true) }}" 
                                                             alt="User Avatar">
                                                    </div>
                                                    <div class="comment-content">
                                                        <div class="comment-header">
                                                            <span class="comment-author">{{ __(@$comment->user->fullname) }}</span>
                                                            <span class="comment-date">{{ diffForHumans(@$comment->created_at) }}</span>
                                                        </div>
                                                        <div class="comment-text">
                                                            {{ __($comment->comment) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Tab -->
                                @if($documents->isNotEmpty())
                                    <div class="tab-pane fade" id="documents" role="tabpanel">
                                        @include('components.project-documents')
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <div class="project-sidebar">
                            @if ($project->end_date > now() && $project->status != Status::PROJECT_END && $project->available_share > 0)
                                <div class="investment-card">
                                    <div class="investment-header">
                                        <h4>Đầu tư ngay</h4>
                                        <p>Tham gia dự án này để nhận lợi nhuận hấp dẫn</p>
                                    </div>
                                    
                                    <div class="investment-action">
                                        <div class="form-group mb-3">
                                            <label>Số tiền đầu tư</label>
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="investment_amount_input"
                                                       placeholder="Nhập số tiền"
                                                       min="{{ $project->share_amount }}"
                                                       step="1000000">
                                                <span class="input-group-text">VNĐ</span>
                                            </div>
                                            <small class="form-text">Tối thiểu: {{ showAmount($project->share_amount) }}</small>
                                        </div>
                                        
                                        <div class="investment-summary mb-3">
                                            <div class="summary-item">
                                                <span>Kỳ hạn:</span>
                                                <select id="term_months" class="form-select">
                                                    @for ($i = 1; $i <= 36; $i++)
                                                        <option value="{{ $i }}">{{ $i }} tháng</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="summary-item mt-3">
                                                <span>Lợi nhuận dự kiến:</span>
                                                <span id="roiDisplay">0 VNĐ</span>
                                            </div>
                                        </div>
                                        
                                        <button type="button" class="btn btn--primary-modern w-100" data-bs-toggle="modal" data-bs-target="#bitModal">
                                            <i class="las la-arrow-right"></i>
                                            Đầu tư ngay
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="project-closed-card">
                                    <div class="closed-icon">
                                        <i class="las la-lock"></i>
                                    </div>
                                    <h4>Dự án đã kết thúc</h4>
                                    <p>Dự án này không còn nhận đầu tư mới</p>
                                </div>
                            @endif
                            
                            <!-- Related Projects -->
                            @if($relates->isNotEmpty())
                                <div class="related-projects">
                                    <h5>Dự án liên quan</h5>
                                    @foreach($relates->take(3) as $relatedProject)
                                        <div class="related-project-item">
                                            <div class="related-project-image">
                                                <img src="{{ getImage(getFilePath('project') . '/' . $relatedProject->image) }}" 
                                                     alt="{{ __($relatedProject->title) }}">
                                            </div>
                                            <div class="related-project-info">
                                                <h6>
                                                    <a href="{{ route('project.details', $relatedProject->slug) }}">
                                                        {{ __($relatedProject->title) }}
                                                    </a>
                                                </h6>
                                                <div class="related-project-meta">
                                                    <span class="roi">{{ getAmount($relatedProject->roi_percentage) }}% ROI</span>
                                                    <span class="price">{{ showAmount($relatedProject->share_amount) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    /* Modern Project Details Styles */
    .project-details-modern {
        background: #f8f9fa;
        min-height: 100vh;
    }

    /* Hero Section */
    .project-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 0 40px;
        position: relative;
        overflow: hidden;
    }

    .project-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .breadcrumb-modern {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 30px;
        font-size: 0.9rem;
    }

    .breadcrumb-link {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .breadcrumb-link:hover {
        color: white;
    }

    .breadcrumb-current {
        color: white;
        font-weight: 500;
    }

    .project-header {
        max-width: 800px;
    }

    .project-title-section {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .project-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }

    .project-status-badge {
        flex-shrink: 0;
    }

    .status-active {
        background: rgba(46, 204, 113, 0.2);
        color: #2ecc71;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-inactive {
        background: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .project-meta-header {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .meta-item i {
        font-size: 1.1rem;
    }

    /* Content Wrapper */
    .project-content-wrapper {
        margin-top: -40px;
        position: relative;
        z-index: 3;
    }

    /* Project Gallery */
    .project-gallery-modern {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }

    .main-image-container {
        position: relative;
        aspect-ratio: 16/9;
        overflow: hidden;
    }

    .main-project-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .investment-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #ff6b35, #f7931e);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .gallery-thumbnails {
        display: flex;
        gap: 12px;
        padding: 20px;
        overflow-x: auto;
    }

    .thumbnail-item {
        flex: 0 0 80px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.2s ease;
    }

    .thumbnail-item.active,
    .thumbnail-item:hover {
        opacity: 1;
    }

    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Project Stats */
    .project-stats-modern {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        transition: background-color 0.2s ease;
    }

    .stat-card:hover {
        background: #e9ecef;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        display: block;
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .stat-label {
        display: block;
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Investment Progress */
    .investment-progress-modern {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .progress-header h4 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .progress-percentage {
        font-size: 1.1rem;
        font-weight: 700;
        color: #3498db;
    }

    .progress-bar-container {
        height: 12px;
        background: #e9ecef;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3498db, #2980b9);
        border-radius: 6px;
        transition: width 0.3s ease;
    }

    .progress-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .progress-stat {
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .stat-text {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Content Tabs */
    .content-tabs-modern {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-top: 30px;
    }

    .nav-tabs-modern {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 0;
        margin: 0;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .nav-tabs-modern::-webkit-scrollbar {
        display: none;
    }

    .nav-tabs-modern .nav-item {
        flex-shrink: 0;
    }

    .nav-tabs-modern .nav-link {
        border: none;
        background: transparent;
        color: #6c757d;
        padding: 16px 24px;
        font-weight: 500;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        white-space: nowrap;
        position: relative;
    }

    .nav-tabs-modern .nav-link:hover {
        color: #495057;
        background: rgba(255, 215, 0, 0.1);
    }

    .nav-tabs-modern .nav-link.active {
        color: #000;
        background: white;
        border-bottom: 3px solid #FFD700;
    }

    .nav-tabs-modern .nav-link i {
        font-size: 1.1rem;
    }

    .tab-content-modern {
        padding: 0;
        background: white;
        min-height: 400px;
        max-height: 600px;
        overflow-y: auto;
    }

    .tab-pane {
        padding: 30px;
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    .tab-pane.fade {
        opacity: 0;
        transition: opacity 0.15s linear;
    }

    .tab-pane.fade.show {
        opacity: 1;
    }

    /* Overview Content */
    .overview-content {
        max-width: 100%;
    }

    .project-description {
        line-height: 1.8;
        color: #495057;
        margin-bottom: 30px;
    }

    .project-description p {
        margin-bottom: 1rem;
    }

    .project-description ul,
    .project-description ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }

    .project-description li {
        margin-bottom: 0.5rem;
    }

    .project-description strong {
        color: #2c3e50;
        font-weight: 600;
    }

    /* Share Section */
    .share-section {
        border-top: 1px solid #e9ecef;
        padding-top: 20px;
        margin-top: 20px;
    }

    .share-section h5 {
        margin-bottom: 15px;
        color: #2c3e50;
        font-weight: 600;
    }

    .social-share-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .share-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: white;
        font-size: 1rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .share-btn.facebook { background: #1877f2; }
    .share-btn.twitter { background: #1da1f2; }
    .share-btn.pinterest { background: #e60023; }
    .share-btn.linkedin { background: #0077b5; }
    .share-btn.copy-link { background: #6c757d; }

    .share-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        color: white;
    }

    /* FAQ Section */
    .faq-section {
        max-width: 100%;
    }

    .faq-accordion {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }

    .faq-item {
        border-bottom: 1px solid #e9ecef;
    }

    .faq-item:last-child {
        border-bottom: none;
    }

    .faq-button {
        width: 100%;
        padding: 20px;
        background: white;
        border: none;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #2c3e50;
    }

    .faq-button:hover {
        background: #f8f9fa;
    }

    .faq-button.collapsed i {
        transform: rotate(0deg);
    }

    .faq-button i {
        transition: transform 0.3s ease;
        transform: rotate(45deg);
        color: #FFD700;
    }

    .faq-body {
        padding: 20px;
        background: #f8f9fa;
        color: #495057;
        line-height: 1.6;
    }

    /* Location Section */
    .location-section h5 {
        margin-bottom: 20px;
        color: #2c3e50;
        font-weight: 600;
    }

    .map-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .map-container iframe {
        width: 100%;
        height: 400px;
        border: none;
    }

    /* Comments Section */
    .comments-section {
        max-width: 100%;
    }

    .comment-form-modern {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .user-avatar {
        flex-shrink: 0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .comment-form {
        flex: 1;
        display: flex;
        gap: 10px;
    }

    .comment-input {
        flex: 1;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        resize: vertical;
        min-height: 50px;
    }

    .comment-submit {
        width: 50px;
        height: 50px;
        border: none;
        background: #FFD700;
        color: #000;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .comment-submit:hover {
        background: #FFC800;
        transform: translateY(-2px);
    }

    .comments-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .comment-item {
        display: flex;
        gap: 15px;
        padding: 20px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .comment-item:last-child {
        border-bottom: none;
    }

    .comment-avatar {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
    }

    .comment-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .comment-content {
        flex: 1;
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .comment-author {
        font-weight: 600;
        color: #2c3e50;
    }

    .comment-date {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .comment-text {
        color: #495057;
        line-height: 1.6;
    }

    /* Sidebar */
    .project-sidebar {
        position: sticky;
        top: 24px;
    }

    .investment-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }

    .investment-header {
        text-align: center;
        margin-bottom: 24px;
    }

    .investment-header h4 {
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .investment-header p {
        color: #6c757d;
        margin: 0;
    }

    .investment-form .form-group {
        margin-bottom: 20px;
    }

    .investment-form label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
    }

    .investment-form .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 1rem;
    }

    .investment-form .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .investment-form .input-group-text {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-left: none;
        color: #6c757d;
    }

    .investment-form .form-text {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .investment-summary {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .summary-item:last-child {
        margin-bottom: 0;
    }

    .summary-item span:first-child {
        color: #6c757d;
    }

    .summary-item span:last-child {
        font-weight: 600;
        color: #2c3e50;
    }

    .btn--primary-modern {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    .btn--primary-modern:hover {
        background: linear-gradient(135deg, #2980b9, #1f5f8b);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        color: white;
        text-decoration: none;
    }

    .project-closed-card {
        background: white;
        border-radius: 16px;
        padding: 40px 24px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }

    .closed-icon {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 16px;
    }

    .project-closed-card h4 {
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .project-closed-card p {
        color: #6c757d;
        margin: 0;
    }

    /* Related Projects */
    .related-projects {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .related-projects h5 {
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .related-project-item {
        display: flex;
        gap: 12px;
        padding: 16px;
        border-radius: 12px;
        transition: background-color 0.2s ease;
        margin-bottom: 16px;
    }

    .related-project-item:hover {
        background: #f8f9fa;
    }

    .related-project-item:last-child {
        margin-bottom: 0;
    }

    .related-project-image {
        flex-shrink: 0;
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
    }

    .related-project-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .related-project-info {
        flex: 1;
    }

    .related-project-info h6 {
        margin: 0 0 8px 0;
        font-size: 0.95rem;
    }

    .related-project-info h6 a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .related-project-info h6 a:hover {
        color: #3498db;
    }

    .related-project-meta {
        display: flex;
        gap: 12px;
        font-size: 0.85rem;
    }

    .related-project-meta .roi {
        color: #27ae60;
        font-weight: 600;
    }

    .related-project-meta .price {
        color: #6c757d;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .project-title {
            font-size: 2rem;
        }
        
        .project-meta-header {
            gap: 20px;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .progress-stats {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        
        .nav-tabs-modern {
            padding: 0 16px;
        }
        
        .tab-content-modern {
            padding: 24px;
        }
    }

    @media (max-width: 768px) {
        .project-hero {
            padding: 40px 0 30px;
        }
        
        .project-title {
            font-size: 1.8rem;
        }
        
        .project-title-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        
        .project-meta-header {
            flex-direction: column;
            gap: 12px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .nav-tabs-modern {
            flex-wrap: wrap;
        }
        
        .nav-tabs-modern .nav-link {
            padding: 12px 16px;
            font-size: 0.9rem;
        }
        
        .comment-form-modern {
            flex-direction: column;
            align-items: center;
        }
        
        .comment-form {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .project-title {
            font-size: 1.5rem;
        }
        
        .gallery-thumbnails {
            padding: 16px;
        }
        
        .thumbnail-item {
            flex: 0 0 60px;
            height: 45px;
        }
        
        .investment-card,
        .related-projects {
            padding: 20px;
        }
    }
    </style>

    <script>
    // Gallery functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mainImage = document.getElementById('mainProjectImage');
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const imageSrc = this.getAttribute('data-image');
                mainImage.src = imageSrc;
                
                // Update active state
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Investment form calculations
        const amountInput = document.querySelector('#investment_amount_input');
        const termSelect = document.getElementById('term_months');
        const roiDisplay = document.getElementById('roiDisplay');
        const shareAmount = {{ $project->share_amount }};
        const roiPercentage = {{ $project->roi_percentage }};

        // Hàm format số tiền có dấu chấm
        function formatCurrencyInput(value) {
            value = value.replace(/\D/g, ''); // chỉ lấy số
            if (!value) return '';
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Hàm lấy số thực từ input đã format
        function parseCurrencyInput(value) {
            return parseFloat(value.replace(/\./g, '')) || 0;
        }

        function calculateRoi() {
            const amount = parseCurrencyInput(amountInput.value);
            const months = parseInt(termSelect.value) || 1;
            // Calculate ROI based on annual percentage, term in months, and investment amount
            const roi = (amount * roiPercentage / 100) * (months / 12);
            roiDisplay.textContent = roi.toLocaleString('vi-VN') + ' VNĐ';
        }

        if (amountInput && termSelect) {
            amountInput.addEventListener('input', function(e) {
                // Lưu vị trí con trỏ và số dấu chấm trước khi format
                let selectionStart = this.selectionStart;
                const oldValue = this.value;
                const oldDotCount = (oldValue.slice(0, selectionStart).match(/\./g) || []).length;
                // Format lại giá trị
                const formatted = formatCurrencyInput(this.value);
                this.value = formatted;
                // Đếm lại số dấu chấm mới
                const newDotCount = (formatted.slice(0, selectionStart).match(/\./g) || []).length;
                // Điều chỉnh vị trí con trỏ dựa trên số dấu chấm thay đổi
                selectionStart += (newDotCount - oldDotCount);
                this.setSelectionRange(selectionStart, selectionStart);
                calculateRoi();
                // Sync modal if open
                if (typeof window.updateModalValues === 'function' && document.getElementById('bitModal')?.classList.contains('show')) {
                    window.updateModalValues(parseCurrencyInput(this.value), termSelect.value);
                }
            });
            termSelect.addEventListener('change', function() {
                calculateRoi();
                // Sync modal if open
                if (typeof window.updateModalValues === 'function' && document.getElementById('bitModal')?.classList.contains('show')) {
                    window.updateModalValues(parseCurrencyInput(amountInput.value), this.value);
                }
            });
            // Khởi tạo tính toán ban đầu
            calculateRoi();
        }
        
        // Copy link functionality
        const copyLinkBtn = document.querySelector('.copy-link');
        if (copyLinkBtn) {
            copyLinkBtn.addEventListener('click', function() {
                const link = this.getAttribute('data-link');
                navigator.clipboard.writeText(link).then(() => {
                    // Show success message
                    this.innerHTML = '<i class="las la-check"></i>';
                    setTimeout(() => {
                        this.innerHTML = '<i class="las la-copy"></i>';
                    }, 2000);
                });
            });
        }
        
        // Modal functionality
        const bitModal = document.getElementById('bitModal');
        if (bitModal) {
            bitModal.addEventListener('show.bs.modal', function() {
                // Get the amount from the input field using the correct parsing function
                const amount = parseCurrencyInput(document.getElementById('investment_amount_input').value) || shareAmount;
                // Get the selected term
                const months = parseInt(document.getElementById('term_months').value) || 1;
                
                // Initialize modal values when modal opens
                if (typeof window.updateModalValues === 'function') {
                    // Pass the amount and term to updateModalValues
                    window.updateModalValues(amount, months);
                }
            });
        }
    });
    </script>
    
    @include('templates.basic.projects.buy-modal')
@endsection


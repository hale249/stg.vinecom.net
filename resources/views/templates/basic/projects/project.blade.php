<div class="row gy-4">
    @forelse ($projects as $project)
        <div class="col-sm-6 col-xl-4 single-project">
            <article class="card card--offer-modern">
                <div class="card-header-modern">
                    <div class="card-image-wrapper">
                        <a class="card-thumb-modern" href="{{ route('project.details', $project->slug) }}">
                            <img src="{{ getImage(getFilePath('project') . '/' . $project->image) }}" alt="@lang('Project Image')">
                            <div class="card-overlay">
                                <div class="overlay-content">
                                    <i class="las la-eye"></i>
                                    <span>Xem chi tiết</span>
                                </div>
                            </div>
                        </a>
                        <div class="card-badge">
                            <span class="roi-badge">
                                <i class="las la-chart-line"></i>
                                {{ getAmount($project->roi_percentage) }}%
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body-modern">
                    <div class="card-header-info">
                        <h6 class="card-title-modern">
                            <a href="{{ route('project.details', $project->slug) }}">{{ __($project->title) }}</a>
                        </h6>
                        <div class="project-status">
                            @if($project->status == 1)
                                <span class="status-badge status-active">
                                    <i class="las la-check-circle"></i>
                                    Đang hoạt động
                                </span>
                            @else
                                <span class="status-badge status-inactive">
                                    <i class="las la-pause-circle"></i>
                                    Tạm dừng
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-stats">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="las la-coins"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Giá mỗi cổ phần</span>
                                <span class="stat-value">{{ __(showAmount($project->share_amount)) }}</span>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="las la-boxes"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Còn lại</span>
                                <span class="stat-value">{{ __($project->available_share) }} đơn vị</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-progress">
                        @php
                            $progressPercentage = (($project->share_count - $project->available_share) / $project->share_count) * 100;
                        @endphp
                        <div class="progress-info">
                            <span class="progress-label">Tiến độ đầu tư</span>
                            <span class="progress-percentage">{{ round($progressPercentage, 1) }}%</span>
                        </div>
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>

                    <div class="card-footer-modern">
                        <div class="time-remaining">
                            <i class="las la-clock"></i>
                            <span>{{ __(diffForHumans($project->end_date)) }}</span>
                        </div>
                        <a href="{{ route('project.details', $project->slug) }}" class="btn btn--primary-modern">
                            <i class="las la-arrow-right"></i>
                            Đầu tư ngay
                        </a>
                    </div>
                </div>
            </article>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="las la-folder-open"></i>
                </div>
                <h4>Không tìm thấy dự án</h4>
                <p>Hiện tại không có dự án nào khả dụng. Vui lòng quay lại sau.</p>
            </div>
        </div>
    @endforelse
</div>

@if ($projects->hasPages())
    <div class="pagination-wrapper">
        {{ paginateLinks($projects) }}
    </div>
@endif

<style>
/* Modern Project Card Styles */
.card--offer-modern {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.card--offer-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.card-header-modern {
    position: relative;
    overflow: hidden;
}

.card-image-wrapper {
    position: relative;
    overflow: hidden;
}

.card-thumb-modern {
    display: block;
    position: relative;
    overflow: hidden;
    aspect-ratio: 16/10;
}

.card-thumb-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.card--offer-modern:hover .card-thumb-modern img {
    transform: scale(1.05);
}

.card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card--offer-modern:hover .card-overlay {
    opacity: 1;
}

.overlay-content {
    text-align: center;
    color: white;
}

.overlay-content i {
    font-size: 2rem;
    margin-bottom: 8px;
    display: block;
}

.overlay-content span {
    font-size: 0.9rem;
    font-weight: 500;
}

.card-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 2;
}

.roi-badge {
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

.roi-badge i {
    font-size: 0.9rem;
}

.card-body-modern {
    padding: 24px;
}

.card-header-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.card-title-modern {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.4;
    flex: 1;
}

.card-title-modern a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.2s ease;
}

.card-title-modern a:hover {
    color: #3498db;
}

.project-status {
    margin-left: 12px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.status-active {
    background: rgba(46, 204, 113, 0.1);
    color: #27ae60;
}

.status-inactive {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.card-stats {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 20px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 12px;
    transition: background-color 0.2s ease;
}

.stat-item:hover {
    background: #e9ecef;
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.stat-content {
    flex: 1;
}

.stat-label {
    display: block;
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 2px;
}

.stat-value {
    display: block;
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
}

.card-progress {
    margin-bottom: 20px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.progress-label {
    font-size: 0.85rem;
    color: #6c757d;
}

.progress-percentage {
    font-size: 0.85rem;
    font-weight: 600;
    color: #3498db;
}

.progress-bar-wrapper {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #3498db, #2980b9);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.card-footer-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid #e9ecef;
}

.time-remaining {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #6c757d;
}

.time-remaining i {
    color: #f39c12;
}

.btn--primary-modern {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
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

.btn--primary-modern i {
    font-size: 0.8rem;
    transition: transform 0.2s ease;
}

.btn--primary-modern:hover i {
    transform: translateX(2px);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 16px;
    border: 2px dashed #dee2e6;
}

.empty-icon {
    font-size: 4rem;
    color: #adb5bd;
    margin-bottom: 20px;
}

.empty-state h4 {
    color: #6c757d;
    margin-bottom: 12px;
}

.empty-state p {
    color: #adb5bd;
    margin: 0;
}

/* Pagination Wrapper */
.pagination-wrapper {
    margin-top: 40px;
    display: flex;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card-body-modern {
        padding: 20px;
    }
    
    .card-header-info {
        flex-direction: column;
        gap: 12px;
    }
    
    .project-status {
        margin-left: 0;
    }
    
    .card-footer-modern {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .btn--primary-modern {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .card-stats {
        gap: 12px;
    }
    
    .stat-item {
        padding: 10px;
    }
    
    .stat-icon {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }
}
</style>

<div class="row gy-4">
    @forelse ($projects as $project)
        <div class="col-sm-12">
            <article class="card card--offer-list-modern">
                <div class="card-left-modern">
                    <div class="image-container">
                        <a class="card-thumb-modern" href="{{ route('project.details', $project->slug) }}">
                            <img src="{{ getImage(getFilePath('project') . '/' . $project->image) }}" alt="@lang('Project Image')">
                            <div class="card-overlay">
                                <div class="overlay-content">
                                    <i class="las la-eye"></i>
                                    <span>Xem chi tiết</span>
                                </div>
                            </div>
                        </a>
                        <div class="roi-badge-modern">
                            <i class="las la-chart-line"></i>
                            {{ getAmount($project->roi_percentage) }}%
                        </div>
                    </div>
                </div>

                <div class="card-right-modern">
                    <div class="card-header-modern">
                        <div class="project-info">
                            <h5 class="project-title">
                                <a href="{{ route('project.details', $project->slug) }}">{{ __($project->title) }}</a>
                            </h5>
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
                        <div class="project-actions">
                            <a href="{{ route('project.details', $project->slug) }}" class="btn btn--primary-modern">
                                <i class="las la-arrow-right"></i>
                                Đầu tư ngay
                            </a>
                        </div>
                    </div>

                    <div class="card-stats-modern">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="las la-coins"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-label">Giá mỗi cổ phần</span>
                                    <span class="stat-value">{{ __(showAmount($project->share_amount)) }}</span>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="las la-boxes"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-label">Còn lại</span>
                                    <span class="stat-value">{{ __($project->available_share) }} đơn vị</span>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="las la-clock"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-label">Thời gian còn lại</span>
                                    <span class="stat-value">{{ __(diffForHumans($project->end_date)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-progress-modern">
                        <div class="progress-header">
                            <span class="progress-label">Tiến độ đầu tư</span>
                            <span class="progress-percentage">{{ $project->investment_progress }}%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: {{ $project->investment_progress }}%"></div>
                        </div>
                    </div>

                    <div class="card-footer-modern">
                        <div class="project-meta">
                            <div class="meta-item">
                                <i class="las la-calendar"></i>
                                <span>Kết thúc: {{ $project->end_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="las la-chart-bar"></i>
                                <span>ROI: {{ getAmount($project->roi_percentage) }}%</span>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('project.details', $project->slug) }}" class="btn btn--outline-modern">
                                <i class="las la-info-circle"></i>
                                Chi tiết
                            </a>
                            <a href="{{ route('project.details', $project->slug) }}" class="btn btn--primary-modern">
                                <i class="las la-arrow-right"></i>
                                Đầu tư ngay
                            </a>
                        </div>
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
/* Modern List View Project Card Styles */
.card--offer-list-modern {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    min-height: 200px;
}

.card--offer-list-modern:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.card-left-modern {
    flex: 0 0 280px;
    position: relative;
    overflow: hidden;
}

.image-container {
    position: relative;
    height: 100%;
    min-height: 200px;
}

.card-thumb-modern {
    display: block;
    width: 100%;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.card-thumb-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.card--offer-list-modern:hover .card-thumb-modern img {
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

.card--offer-list-modern:hover .card-overlay {
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

.roi-badge-modern {
    position: absolute;
    top: 16px;
    right: 16px;
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
    z-index: 2;
}

.roi-badge-modern i {
    font-size: 0.9rem;
}

.card-right-modern {
    flex: 1;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-header-modern {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.project-info {
    flex: 1;
}

.project-title {
    margin: 0 0 12px 0;
    font-size: 1.3rem;
    font-weight: 600;
    line-height: 1.4;
}

.project-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.2s ease;
}

.project-title a:hover {
    color: #3498db;
}

.project-status {
    margin-top: 8px;
}

.status-badge {
    padding: 6px 16px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-active {
    background: rgba(46, 204, 113, 0.1);
    color: #27ae60;
}

.status-inactive {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.project-actions {
    margin-left: 20px;
}

.card-stats-modern {
    margin-bottom: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    transition: background-color 0.2s ease;
}

.stat-card:hover {
    background: #e9ecef;
}

.stat-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.stat-content {
    flex: 1;
}

.stat-label {
    display: block;
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 4px;
}

.stat-value {
    display: block;
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
}

.card-progress-modern {
    margin-bottom: 20px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.progress-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.progress-percentage {
    font-size: 0.9rem;
    font-weight: 600;
    color: #3498db;
}

.progress-bar-container {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #3498db, #2980b9);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.card-footer-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.project-meta {
    display: flex;
    gap: 24px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #6c757d;
}

.meta-item i {
    color: #f39c12;
    font-size: 1rem;
}

.action-buttons {
    display: flex;
    gap: 12px;
}

.btn--outline-modern {
    background: transparent;
    color: #3498db;
    border: 2px solid #3498db;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn--outline-modern:hover {
    background: #3498db;
    color: white;
    text-decoration: none;
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
@media (max-width: 992px) {
    .card--offer-list-modern {
        flex-direction: column;
    }
    
    .card-left-modern {
        flex: none;
        height: 200px;
    }
    
    .card-right-modern {
        padding: 20px;
    }
    
    .card-header-modern {
        flex-direction: column;
        gap: 16px;
    }
    
    .project-actions {
        margin-left: 0;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
    }
    
    .card-footer-modern {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .project-meta {
        justify-content: center;
    }
    
    .action-buttons {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .project-meta {
        flex-direction: column;
        gap: 12px;
        align-items: center;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn--outline-modern,
    .btn--primary-modern {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .card-right-modern {
        padding: 16px;
    }
    
    .project-title {
        font-size: 1.1rem;
    }
    
    .stat-card {
        padding: 12px;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
    }
}
</style>

<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::controller('CronController')->group(function () {
    Route::get('cron', 'cron')->name('cron');
    Route::get('interest', 'interest')->name('interest');
});

Route::controller('ProjectController')->group(function () {
    Route::get('projects', 'projects')->name('projects');
    Route::post('check-quantity', 'checkQuantity')->name('check.quantity');
    Route::get('project/{slug}', 'projectDetails')->name('project.details');
    Route::get('project-filter', 'filter')->name('project.filter');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog', 'blogs')->name('blogs');
    Route::get('blog/category/{category}', 'blogs')->name('blogs.category');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('/', function() {
        return redirect()->route('projects');
    })->name('home');
});

Route::controller('User\InvestController')->prefix('invest')->middleware('auth')->group(function () {
    Route::post('order', 'order')->name('invest.order');
    Route::get('contract/{id}', 'showContract')->name('invest.contract');
    Route::post('confirm/{id}', 'confirm')->name('invest.confirm');
    Route::get('download-contract/{id}', 'downloadContract')->name('invest.download.contract');
    Route::post('cancel/{id}', 'cancel')->name('invest.cancel');
    Route::get('profit-schedule-pdf', 'downloadProfitSchedulePdf')->name('invest.profit.schedule.pdf');
    Route::get('profit-schedule-html', 'getProfitScheduleHtml')->name('invest.profit.schedule.html');
});

// Contract Document Routes
Route::controller('User\ContractDocumentController')->prefix('invest')->middleware('auth')->group(function () {
    Route::get('{investId}/documents', 'index')->name('user.invest.documents');
    Route::post('{investId}/documents/upload', 'upload')->name('user.invest.documents.upload');
    Route::get('{investId}/documents/{documentId}/download', 'download')->name('user.invest.documents.download');
    Route::post('{investId}/documents/{documentId}/delete', 'delete')->name('user.invest.documents.delete');
});

// Project Document View Route
Route::get('project-document/{projectId}/{documentId}', 'ProjectDocumentController@view')->withoutMiddleware('maintenance')->name('project.document.view');

// Debug route for staff check
Route::middleware(['auth'])->group(function () {
    Route::get('staff-check', 'User\StaffCheckController@check')->name('staff.check');
});

// Reference Document Routes
Route::middleware(['auth', 'staff'])->group(function () {
    Route::controller('User\ReferenceDocumentController')->prefix('reference')->name('reference.')->group(function () {
        Route::get('documents', 'index')->name('documents');
        Route::get('document/view/{id}', 'view')->name('document.view');
        Route::get('document/download/{id}', 'download')->name('document.download');
        Route::get('document/stream/{id}', 'stream')->name('document.stream');
    });
});

// Staff Routes
Route::middleware(['auth'])->prefix('user/staff')->name('user.staff.staff.')->group(function () {
    Route::get('dashboard', 'User\SalesStaffController@dashboard')->name('dashboard');
    Route::get('contracts', 'User\SalesStaffController@contracts')->name('contracts');
    Route::get('contract/{id}', 'User\SalesStaffController@contractDetails')->name('contract.details');
    Route::get('create-contract', 'User\SalesStaffController@createContract')->name('create_contract');
    Route::post('store-contract', 'User\SalesStaffController@storeContract')->name('store_contract');
    Route::post('cancel-contract/{id}', 'User\SalesStaffController@cancelContract')->name('cancel_contract');
    Route::get('alerts', 'User\SalesStaffController@alerts')->name('alerts');
    Route::get('customers', 'User\SalesStaffController@customers')->name('customers');
    
    // Contract Documents Management for Staff
    Route::controller('User\Staff\ContractDocumentController')->prefix('contract')->group(function () {
        Route::get('{investId}/documents', 'index')->name('contract.documents');
        Route::post('{investId}/documents/upload', 'upload')->name('contract.documents.upload');
        Route::get('{investId}/documents/{documentId}/download', 'download')->name('contract.documents.download');
        Route::post('{investId}/documents/{documentId}/delete', 'delete')->name('contract.documents.delete');
    });
    
    // Reference Documents for Staff
    Route::controller('User\Staff\DocumentController')->group(function () {
        Route::get('documents', 'index')->name('documents');
        Route::get('documents/view/{id}', 'view')->name('documents.view');
        Route::get('documents/download/{id}', 'download')->name('documents.download');
        Route::get('documents/stream/{id}', 'stream')->name('documents.stream');
    });
});

// Manager Routes
Route::middleware(['auth'])->prefix('user/manager')->name('user.staff.manager.')->group(function () {
    Route::get('dashboard', 'User\\SalesManagerController@dashboard')->name('dashboard');
    Route::get('team-members', 'User\\SalesManagerController@teamMembers')->name('team_members');
    Route::get('contracts', 'User\\SalesManagerController@teamContracts')->name('contracts');
    Route::get('alerts', 'User\\SalesManagerController@alerts')->name('alerts');
    Route::get('approval-requests', 'User\\SalesManagerController@approvalRequests')->name('approval_requests');

    // Reference Documents for Managers
    Route::controller('User\\Staff\\DocumentController')->group(function () {
        Route::get('documents', 'index')->name('documents');
        Route::get('documents/view/{id}', 'view')->name('documents.view');
        Route::get('documents/download/{id}', 'download')->name('documents.download');
        Route::get('documents/stream/{id}', 'stream')->name('documents.stream');
    });

    // HR Routes
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('salary', 'User\\SalesManagerController@salaryDashboard')->name('salary');
        Route::get('attendance', 'User\\SalesManagerController@attendanceDashboard')->name('attendance');
        Route::get('kpi', 'User\\SalesManagerController@kpiDashboard')->name('kpi');
        Route::get('performance', 'User\\SalesManagerController@performanceDashboard')->name('performance');
    });

    // Report Routes
    Route::get('reports', 'User\\SalesManagerController@reports')->name('reports');
    Route::get('reports/transactions', 'User\\SalesManagerController@reportTransactions')->name('report.transactions');
    Route::get('reports/interests', 'User\\SalesManagerController@reportInterests')->name('report.interests');
    Route::get('reports/commissions', 'User\\SalesManagerController@reportCommissions')->name('report.commissions');
});

// This catch-all route must be the last route
Route::get('/{slug}', 'SiteController@pages')->name('pages');

Route::get('admin/kpi-level', function () {
    $pageTitle = 'Thiết lập cấp bậc & KPI';
    
    // Get KPI data from session for demo purposes
    // In a real implementation, we would retrieve from the database
    $kpis = session('kpis', collect([]));
    
    // Extract unique level names from KPIs for filtering
    $uniqueLevelNames = $kpis->pluck('level_name')->unique()->values();
    $levels = $uniqueLevelNames->map(function($levelName) {
        return (object)['id' => $levelName, 'name' => $levelName];
    });
    
    // Filter KPIs based on request parameters
    $filteredKpis = $kpis;
    
    // Filter by level_name (job title)
    if (request()->has('level_name') && !empty(request('level_name'))) {
        $filteredKpis = $filteredKpis->filter(function($kpi) {
            return $kpi->level_name == request('level_name');
        });
    }
    
    // Filter by KPI status
    if (request()->has('kpi_status') && !empty(request('kpi_status'))) {
        $filteredKpis = $filteredKpis->filter(function($kpi) {
            return $kpi->kpi_status == request('kpi_status');
        });
    }
    
    // Filter by month
    if (request()->has('month') && !empty(request('month'))) {
        $month = request('month');
        $filteredKpis = $filteredKpis->filter(function($kpi) use ($month) {
            // If the KPI has a month property, filter by it
            if (isset($kpi->month)) {
                // Check if formats match or convert if needed
                return $kpi->month == $month || 
                       (strpos($kpi->month, '/') !== false && 
                        substr($month, 0, 4) . '-' . substr($month, 5, 2) == 
                        substr($kpi->month, 3, 4) . '-' . substr($kpi->month, 0, 2));
            }
            return true;
        });
    }
    
    // Add a level property to each KPI object for display purposes
    $kpis = $filteredKpis->map(function($kpi) {
        // Set level object based on level_name
        $kpi->level = (object)['name' => $kpi->level_name ?? 'N/A'];
        return $kpi;
    });
    
    // Calculate summary statistics based on filtered KPIs
    $summary = [
        'avg_overall_kpi' => $kpis->avg('overall_kpi_percentage') ?? 0,
        'exceeded_kpi_count' => $kpis->where('kpi_status', 'exceeded')->count(),
        'achieved_kpi_count' => $kpis->where('kpi_status', 'achieved')->count(),
        'total_actual_sales' => $kpis->sum('actual_sales') ?? 0,
        'total_actual_contracts' => $kpis->sum('actual_contracts') ?? 0
    ];
    
    return view('admin.kpi_level', compact('levels', 'kpis', 'summary', 'pageTitle'));
})->name('admin.kpi.level.index');

Route::post('admin/kpi-level', function (\Illuminate\Http\Request $request) {
    // Validate request data
    $validated = $request->validate([
        'level_name' => 'required|string|max:255',
        'role_level' => 'required|string|in:staff_level,mid_manager_level,senior_manager_level,regional_director_level',
        'kpi_default' => 'required|numeric|min:0',
        'kpi_month_1' => 'required|numeric|min:0',
        'kpi_month_2' => 'required|numeric|min:0',
        'kpi_tuyen_dung' => 'nullable|string',
        'luong_bhxh' => 'required|numeric|min:0',
        'luong_co_ban' => 'required|numeric|min:0',
        'luong_kinh_doanh' => 'required|numeric|min:0',
        'thuong_kinh_doanh' => 'nullable|numeric|min:0',
        'hh_quan_ly' => 'nullable|numeric|min:0',
        'hh_quan_ly_percent' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
    ]);
    
    // Store in session for demo purposes
    // In a real implementation, we would store in the database
    $kpis = session('kpis', collect([]));
    $validated['id'] = time(); // Use timestamp as a temporary ID
    
    // Set default values for actual data
    $validated['actual_sales'] = $validated['actual_sales'] ?? 0;
    $validated['actual_contracts'] = $validated['actual_contracts'] ?? 0;
    
    // Calculate overall KPI percentage
    $targetSales = $validated['target_sales'] ?? $validated['kpi_default'] ?? 1; // Avoid division by zero
    $actualSales = $validated['actual_sales'] ?? 0;
    $validated['overall_kpi_percentage'] = $targetSales > 0 ? ($actualSales / $targetSales) * 100 : 0;
    
    // Calculate management commission based on percentage
    $validated['hh_quan_ly'] = round($validated['kpi_default'] * ($validated['hh_quan_ly_percent'] / 100));
    
    $kpis->push((object)$validated);
    session(['kpis' => $kpis]);
    
    return redirect()->route('admin.kpi.level.index')->with('success', 'KPI đã được tạo thành công!');
})->name('admin.kpi.level.store');

// Route handler for updating a KPI policy
Route::put('admin/kpi-level/{id}', function (\Illuminate\Http\Request $request, $id) {
    // Validate request data
    $validated = $request->validate([
        'level_name' => 'required|string|max:255',
        'role_level' => 'required|string|in:staff_level,mid_manager_level,senior_manager_level,regional_director_level',
        'kpi_default' => 'required|numeric|min:0',
        'kpi_month_1' => 'required|numeric|min:0',
        'kpi_month_2' => 'required|numeric|min:0',
        'kpi_tuyen_dung' => 'nullable|string',
        'luong_bhxh' => 'required|numeric|min:0',
        'luong_co_ban' => 'required|numeric|min:0',
        'luong_kinh_doanh' => 'required|numeric|min:0',
        'thuong_kinh_doanh' => 'nullable|numeric|min:0',
        'hh_quan_ly' => 'nullable|numeric|min:0',
        'hh_quan_ly_percent' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
    ]);
    
    // Update in session for demo purposes
    // In a real implementation, we would update in the database
    $kpis = session('kpis', collect([]));
    
    $index = $kpis->search(function($kpi) use ($id) {
        return $kpi->id == $id;
    });
    
    if ($index !== false) {
        $validated['id'] = $id;
        
        // Preserve actual sales and contracts data if they exist
        $existingKpi = $kpis[$index];
        $validated['actual_sales'] = $existingKpi->actual_sales ?? 0;
        $validated['actual_contracts'] = $existingKpi->actual_contracts ?? 0;
        
        // Calculate overall KPI percentage
        $targetSales = $validated['target_sales'] ?? $validated['kpi_default'] ?? 1; // Avoid division by zero
        $actualSales = $validated['actual_sales'] ?? 0;
        $validated['overall_kpi_percentage'] = $targetSales > 0 ? ($actualSales / $targetSales) * 100 : 0;
        
        // Calculate management commission based on percentage
        $validated['hh_quan_ly'] = round($validated['kpi_default'] * ($validated['hh_quan_ly_percent'] / 100));
        
        $kpis[$index] = (object)$validated;
        session(['kpis' => $kpis]);
        return redirect()->route('admin.kpi.level.index')->with('success', 'KPI đã được cập nhật thành công!');
    }
    
    return redirect()->route('admin.kpi.level.index')->with('error', 'Không tìm thấy KPI!');
})->name('admin.kpi.level.update');

// Route handler for updating KPI details
Route::put('admin/kpi-detail/{id}', function (\Illuminate\Http\Request $request, $id) {
    // Validate request data
    $validated = $request->validate([
        'staff_name' => 'required|string|max:255',
        'month' => 'required|string',
        'target_contracts' => 'required|integer|min:0',
        'actual_contracts' => 'nullable|integer|min:0',
        'target_sales' => 'required|numeric|min:0',
        'actual_sales' => 'nullable|numeric|min:0',
        'contract_completion_rate' => 'nullable|numeric',
        'sales_completion_rate' => 'nullable|numeric',
        'overall_kpi_percentage' => 'nullable|numeric',
        'kpi_status' => 'required|string|in:exceeded,achieved,near_achieved,not_achieved,pending',
        'notes' => 'nullable|string',
    ]);
    
    // Format month for display
    $monthParts = explode('-', $validated['month']);
    if (count($monthParts) === 2) {
        $validated['month_display'] = $monthParts[1] . '/' . $monthParts[0];
    } else {
        $validated['month_display'] = $validated['month'];
    }
    
    // In a real implementation, we would update in the database
    // For now, we'll store in session to demonstrate functionality
    $kpis = session('kpis', collect([]));
    
    // Check if KPI detail exists
    $index = $kpis->search(function($kpi) use ($id) {
        return $kpi->id == $id;
    });
    
    if ($index !== false) {
        // Update existing KPI detail
        $validated['id'] = $id;
        $kpis[$index] = (object)array_merge((array)$kpis[$index], $validated);
    } else {
        // Create new KPI detail
        $validated['id'] = time(); // Use timestamp as a temporary ID
        $kpis->push((object)$validated);
    }
    
    session(['kpis' => $kpis]);
    
    return redirect()->route('admin.kpi.level.index')->with('success', 'Thông tin KPI theo tháng đã được cập nhật!');
})->name('admin.kpi.detail.update');

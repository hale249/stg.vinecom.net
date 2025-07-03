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

// Route for getting managers by roles (for staff creation form)
Route::get('/admin/users/list/managers', function (\Illuminate\Http\Request $request) {
    $roles = $request->input('roles', []);
    
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    
    $managers = \App\Models\User::where('is_staff', 1)
        ->whereIn('role', $roles)
        ->select('id', 'firstname', 'lastname', 'email', 'role', 'position_level')
        ->get()
        ->map(function($user) {
            $user->fullname = $user->firstname . ' ' . $user->lastname;
            return $user;
        });
    
    return response()->json([
        'success' => true,
        'managers' => $managers
    ]);
})->name('admin.users.list.managers');

// Route to get KPI levels for staff creation form
Route::get('/admin/users/list/positions', function () {
    // Get KPI data from the database
    $kpis = App\Models\KpiPolicy::all();
    
    // Group KPI levels by role_level
    $positionsByRole = [
        'sales_staff' => [],
        'sales_manager' => [],
        'sales_director' => [],
        'regional_director' => []
    ];
    
    foreach ($kpis as $kpi) {
        if (!isset($kpi->role_level) || !isset($kpi->level_name)) {
            continue;
        }
        
        $roleMapping = [
            'staff_level' => 'sales_staff',
            'mid_manager_level' => 'sales_manager',
            'senior_manager_level' => 'sales_director',
            'regional_director_level' => 'regional_director'
        ];
        
        $role = $roleMapping[$kpi->role_level] ?? null;
        if (!$role) {
            continue;
        }
        
        // Check if this position name is already added to avoid duplicates
        $positionExists = false;
        foreach ($positionsByRole[$role] as $position) {
            if ($position['value'] === $kpi->level_name) {
                $positionExists = true;
                break;
            }
        }
        
        if (!$positionExists) {
            $positionsByRole[$role][] = [
                'value' => $kpi->level_name,
                'label' => $kpi->level_name
            ];
        }
    }
    
    // Return the positions grouped by role
    return response()->json([
        'success' => true,
        'positionsByRole' => $positionsByRole
    ]);
})->name('admin.users.list.positions');

Route::get('admin/kpi-level', function () {
    $pageTitle = 'Thiết lập cấp bậc & KPI';
    
    // Get KPI data from database
    $kpis = App\Models\KpiPolicy::all();
    
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
    
    // Set default values for actual data
    $validated['actual_sales'] = $validated['actual_sales'] ?? 0;
    $validated['actual_contracts'] = $validated['actual_contracts'] ?? 0;
    
    // Calculate overall KPI percentage
    $targetSales = $validated['target_sales'] ?? $validated['kpi_default'] ?? 1; // Avoid division by zero
    $actualSales = $validated['actual_sales'] ?? 0;
    $validated['overall_kpi_percentage'] = $targetSales > 0 ? ($actualSales / $targetSales) * 100 : 0;
    
    // Calculate management commission based on percentage
    $validated['hh_quan_ly'] = round($validated['kpi_default'] * ($validated['hh_quan_ly_percent'] / 100));
    
    // Create new KPI policy in the database
    App\Models\KpiPolicy::create($validated);
    
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
    
    // Find KPI policy in database
    $kpi = App\Models\KpiPolicy::find($id);
    
    if ($kpi) {
        // Preserve actual sales and contracts data if they exist
        $validated['actual_sales'] = $kpi->actual_sales ?? 0;
        $validated['actual_contracts'] = $kpi->actual_contracts ?? 0;
        
        // Calculate overall KPI percentage
        $targetSales = $validated['target_sales'] ?? $validated['kpi_default'] ?? 1; // Avoid division by zero
        $actualSales = $validated['actual_sales'] ?? 0;
        $validated['overall_kpi_percentage'] = $targetSales > 0 ? ($actualSales / $targetSales) * 100 : 0;
        
        // Calculate management commission based on percentage
        $validated['hh_quan_ly'] = round($validated['kpi_default'] * ($validated['hh_quan_ly_percent'] / 100));
        
        // Update the KPI policy
        $kpi->update($validated);
        
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
    
    // Find KPI policy in database
    $kpi = App\Models\KpiPolicy::find($id);
    
    if ($kpi) {
        // Update the KPI detail
        $kpi->update($validated);
    } else {
        // Create new KPI detail
        App\Models\KpiPolicy::create(array_merge($validated, ['id' => $id]));
    }
    
    return redirect()->route('admin.kpi.level.index')->with('success', 'Thông tin KPI theo tháng đã được cập nhật!');
})->name('admin.kpi.detail.update');

Route::get('admin/kpi-level/populate', function () {
    // Clear existing data before populating
    App\Models\KpiPolicy::truncate();
    
    // Define KPI policies from Excel data
    $kpiData = [
        [
            'level_name' => 'QLKD 1',
            'role_level' => 'staff_level',
            'kpi_default' => 500000000,
            'kpi_month_1' => 250000000,
            'kpi_month_2' => 350000000,
            'kpi_tuyen_dung' => 'Cá nhân',
            'luong_bhxh' => 5350000,
            'luong_co_ban' => 6000000,
            'luong_kinh_doanh' => 7000000,
            'thuong_kinh_doanh' => 7500000,
            'hh_quan_ly' => 0,
            'hh_quan_ly_percent' => 0.1,
            'notes' => '2,60 / 650.000',
            'overall_kpi_percentage' => 0
        ],
        [
            'level_name' => 'QLKD 2',
            'role_level' => 'staff_level',
            'kpi_default' => 800000000,
            'kpi_month_1' => 400000000,
            'kpi_month_2' => 560000000,
            'kpi_tuyen_dung' => 'Cá nhân',
            'luong_bhxh' => 5350000,
            'luong_co_ban' => 7000000,
            'luong_kinh_doanh' => 9000000,
            'thuong_kinh_doanh' => 12000000,
            'hh_quan_ly' => 0,
            'hh_quan_ly_percent' => 0.1,
            'notes' => '2,00 / 1.650.000',
            'overall_kpi_percentage' => 0
        ],
        [
            'level_name' => 'QLKD 3',
            'role_level' => 'staff_level',
            'kpi_default' => 1000000000,
            'kpi_month_1' => 500000000,
            'kpi_month_2' => 700000000,
            'kpi_tuyen_dung' => 'Cá nhân',
            'luong_bhxh' => 5350000,
            'luong_co_ban' => 8500000,
            'luong_kinh_doanh' => 11500000,
            'thuong_kinh_doanh' => 15000000,
            'hh_quan_ly' => 0,
            'hh_quan_ly_percent' => 0.1,
            'notes' => '2,00 / 3.150.000',
            'overall_kpi_percentage' => 0
        ],
        [
            'level_name' => 'GĐKD 1',
            'role_level' => 'mid_manager_level',
            'kpi_default' => 4000000000,
            'kpi_month_1' => 2000000000,
            'kpi_month_2' => 2800000000,
            'kpi_tuyen_dung' => 'CN + TĐ 05 NS',
            'luong_bhxh' => 6000000,
            'luong_co_ban' => 10000000,
            'luong_kinh_doanh' => 20000000,
            'thuong_kinh_doanh' => 15000000,
            'hh_quan_ly' => 4000000,
            'hh_quan_ly_percent' => 0.75,
            'notes' => '0,75 / 4.000.000',
            'overall_kpi_percentage' => 0
        ],
        [
            'level_name' => 'GĐKD 2',
            'role_level' => 'mid_manager_level',
            'kpi_default' => 5000000000,
            'kpi_month_1' => 2500000000,
            'kpi_month_2' => 3500000000,
            'kpi_tuyen_dung' => 'CN + TĐ 05 NS',
            'luong_bhxh' => 7000000,
            'luong_co_ban' => 11000000,
            'luong_kinh_doanh' => 26000000,
            'thuong_kinh_doanh' => 15000000,
            'hh_quan_ly' => 5000000,
            'hh_quan_ly_percent' => 0.74,
            'notes' => '0,74 / 4.000.000',
            'overall_kpi_percentage' => 0
        ],
        [
            'level_name' => 'GĐTT 1',
            'role_level' => 'senior_manager_level',
            'kpi_default' => 8000000000,
            'kpi_month_1' => 4000000000,
            'kpi_month_2' => 5600000000,
            'kpi_tuyen_dung' => 'TĐ 10-20 NS',
            'luong_bhxh' => 9000000,
            'luong_co_ban' => 15000000,
            'luong_kinh_doanh' => 25000000,
            'thuong_kinh_doanh' => 0,
            'hh_quan_ly' => 8000000,
            'hh_quan_ly_percent' => 0.5,
            'notes' => '0,50 / 6.000.000',
            'overall_kpi_percentage' => 0
        ],
        [
            'level_name' => 'GĐTT 2',
            'role_level' => 'senior_manager_level',
            'kpi_default' => 10000000000, 
            'kpi_month_1' => 5000000000,
            'kpi_month_2' => 7000000000,
            'kpi_tuyen_dung' => 'TĐ 10-20 NS',
            'luong_bhxh' => 10000000,
            'luong_co_ban' => 17000000,
            'luong_kinh_doanh' => 32000000,
            'thuong_kinh_doanh' => 0,
            'hh_quan_ly' => 10000000,
            'hh_quan_ly_percent' => 0.49,
            'notes' => '0,49 / 7.000.000',
            'overall_kpi_percentage' => 0
        ],
        [
            'level_name' => 'GĐ Vùng 1',
            'role_level' => 'regional_director_level',
            'kpi_default' => 15000000000,
            'kpi_month_1' => 7500000000,
            'kpi_month_2' => 10500000000,
            'kpi_tuyen_dung' => 'TĐ 20-30 NS',
            'luong_bhxh' => 12000000,
            'luong_co_ban' => 20000000,
            'luong_kinh_doanh' => 30000000,
            'thuong_kinh_doanh' => 0,
            'hh_quan_ly' => 15000000,
            'hh_quan_ly_percent' => 0.33,
            'notes' => '0,33 / 8.000.000',
            'overall_kpi_percentage' => 0
        ],
        [
            'level_name' => 'GĐ Vùng 2',
            'role_level' => 'regional_director_level',
            'kpi_default' => 20000000000,
            'kpi_month_1' => 10000000000,
            'kpi_month_2' => 14000000000,
            'kpi_tuyen_dung' => 'TĐ 20-30 NS',
            'luong_bhxh' => 15000000,
            'luong_co_ban' => 22000000,
            'luong_kinh_doanh' => 43000000,
            'thuong_kinh_doanh' => 0,
            'hh_quan_ly' => 20000000,
            'hh_quan_ly_percent' => 0.33,
            'notes' => '0,33 / 7.000.000',
            'overall_kpi_percentage' => 0
        ],
    ];

    // Insert data into database
    foreach ($kpiData as $data) {
        App\Models\KpiPolicy::create($data);
    }

    return redirect()->route('admin.kpi.level.index')->with('success', 'KPI data has been populated successfully!');
})->name('admin.kpi.level.populate');

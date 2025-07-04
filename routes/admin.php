<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login');
            Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });

        // Admin Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('reset');
            Route::post('reset', 'sendResetCodeEmail');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        Route::controller('ResetPasswordController')->group(function () {
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
            Route::post('password/reset/change', 'reset')->name('password.change');
        });
    });
});

Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('chart/deposit-withdraw', 'depositAndWithdrawReport')->name('chart.deposit.withdraw');
        Route::get('chart/transaction', 'transactionReport')->name('chart.transaction');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all');
        Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all');
        Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single');


        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
        Route::get('chart/invest-status', 'investStatusChart')->name('chart.invest.status');
        Route::get('chart/user-count', 'userCountChart')->name('chart.user.count');
        Route::get('chart/revenue', 'revenueChart')->name('chart.revenue');
    });
    // Manage Time
    Route::controller('ManageTimeController')->name('time.')->prefix('time')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id?}', 'status')->name('status');
    });

    // Manage Invest Controller
    Route::controller('ManageInvestController')->name('invest.')->prefix('invest')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('review', 'review')->name('review');
        Route::get('review/contract/{id}', 'viewContract')->name('review.contract');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('status/{id}', 'investStatus')->name('status');
        Route::post('approve/{id}', 'approve')->name('approve');
        Route::post('reject/{id}', 'reject')->name('reject');
        Route::get('running', 'running')->name('running');
        Route::get('completed', 'completed')->name('completed');
        Route::post('stop-returns/{id}', 'stopReturns')->name('stop.returns');
        Route::post('start-returns/{id}', 'startReturns')->name('start.returns');
        Route::get('process-roi', 'processROI')->name('process.roi');
        Route::get('fix-roi-transactions', 'fixROITransactions')->name('fix.roi.transactions');
        Route::get('recalculate-roi-transactions', 'recalculateROITransactions')->name('recalculate.roi.transactions');
        Route::get('fix-user-balances', 'fixUserBalances')->name('fix.user.balances');
    });

    Route::controller('CategoryController')->name('category.')->prefix('category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id?}', 'status')->name('status');
    });

    Route::controller('CommentController')->name('comment.')->prefix('comment')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('details/{id}', 'detail')->name('details');
        Route::post('store/{id}/{comment_id}', 'store')->name('store');
        Route::post('status/{id?}', 'status')->name('status');
    });

    // Manage Project
    Route::controller('ManageProjectController')->name('project.')->prefix('project')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('show/{id}', 'show')->name('show');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::get('project/check-slug/{id?}', 'checkSlug')->name('check.slug');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('end/{id}', 'end')->name('end');
        Route::get('invest/history/{id}', 'investHistory')->name('investHistory');
        Route::get('seo/{id}', 'frontendSEO')->name('seo');
        Route::post('update/seo/{id}', 'updateSEO')->name('update.seo');
        Route::get('closed', 'closed')->name('closed');
        Route::get('lifetime', 'lifetime')->name('lifetime');
        Route::get('lifetime-return', 'lifetime')->name('lifetime');
        Route::get('repeat-return', 'repeat')->name('repeat');
    });
    
    // Project Fake Investment
    Route::controller('ProjectFakeController')->name('project.fake.')->prefix('project/fake')->group(function () {
        Route::post('investment/{id}', 'fakeInvestment')->name('investment');
        Route::post('reset/{id}', 'resetInvestment')->name('reset');
    });

    // Project Documents Management
    Route::controller('ProjectDocumentController')->name('project.documents.')->prefix('project/{projectId}/documents')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{documentId}', 'edit')->name('edit');
        Route::post('update/{documentId}', 'update')->name('update');
        Route::post('delete/{documentId}', 'destroy')->name('delete');
        Route::get('download/{documentId}', 'download')->name('download');
    });
    
    // Reference Documents Management
    Route::controller('DocumentCategoryController')->name('document.categories.')->prefix('document/categories')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('destroy/{id}', 'destroy')->name('destroy');
        Route::post('status/{id}', 'status')->name('status');
    });
    
    Route::controller('ReferenceDocumentController')->name('documents.')->prefix('documents')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('destroy/{id}', 'destroy')->name('destroy');
        Route::post('status/{id}', 'status')->name('status');
        Route::get('download/{id}', 'download')->name('download');
    });
    
    // Contract Documents Management
    Route::controller('ContractDocumentController')->name('invest.documents.')->prefix('invest/{investId}/documents')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('upload', 'upload')->name('upload');
        Route::get('download/{documentId}', 'download')->name('download');
        Route::post('delete/{documentId}', 'delete')->name('delete');
    });

    // Invest Report
    Route::controller('InvestReportController')->name('invest.report.')->prefix('invest/report')->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('invest-statistics', 'investStatistics')->name('statistics');
        Route::get('invest-statistics-by-project', 'investStatisticsByProject')->name('statistics.project');
        Route::get('invest-interest-statistics', 'investInterestStatistics')->name('interest');
        Route::get('invest-interest-chart', 'investInterestChart')->name('interest.chart');
    });
    // Manage FAQ
    Route::controller('ManageFaqController')->name('project.faq.')->prefix('project/faq')->group(function () {
        Route::get('add/{id}', 'addFaq')->name('add');
        Route::post('store/{id?}', 'storeFaq')->name('store');
        Route::post('status/{id}', 'faqStatus')->name('status');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with-balance', 'usersWithBalance')->name('with.balance');

        Route::post("/create-staff", "createStaff")->name("staff.create");

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('list', 'list')->name('list');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    });

    // Honor Management
    Route::controller('HonorController')->name('honors.')->prefix('honors')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('destroy/{id}', 'destroy')->name('destroy');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('reorder-images', 'reorderImages')->name('reorder.images');
    });

    // Deposit Gateway
    Route::name('gateway.')->prefix('gateway')->group(function () {
        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
        });


        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // DEPOSIT SYSTEM
    Route::controller('DepositController')->prefix('deposit')->name('deposit.')->group(function () {
        Route::get('all/{user_id?}', 'deposit')->name('list');
        Route::get('pending/{user_id?}', 'pending')->name('pending');
        Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
        Route::get('approved/{user_id?}', 'approved')->name('approved');
        Route::get('successful/{user_id?}', 'successful')->name('successful');
        Route::get('initiated/{user_id?}', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
    });

    // WITHDRAW SYSTEM
    Route::name('withdraw.')->prefix('withdraw')->group(function () {

        Route::controller('WithdrawalController')->name('data.')->group(function () {
            Route::get('pending/{user_id?}', 'pending')->name('pending');
            Route::get('approved/{user_id?}', 'approved')->name('approved');
            Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
            Route::get('all/{user_id?}', 'all')->name('all');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('approve', 'approve')->name('approve');
            Route::post('reject', 'reject')->name('reject');
        });


        // Withdraw Method
        Route::controller('WithdrawMethodController')->prefix('method')->name('method.')->group(function () {
            Route::get('/', 'methods')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('edit/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('transaction/{user_id?}', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
        Route::get('invest/history', 'investHistory')->name('invest.history');
        Route::get('contract/revenue', 'contractRevenue')->name('contract.revenue');
    });

    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });

    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });


    Route::controller('GeneralSettingController')->group(function () {

        Route::get('system-setting', 'systemSetting')->name('setting.system');

        // General Setting
        Route::get('general-setting', 'general')->name('setting.general');
        Route::post('general-setting', 'generalUpdate');

        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');

        Route::get('sitemap', 'sitemap')->name('setting.sitemap');
        Route::post('sitemap', 'sitemapSubmit');

        Route::get('robot', 'robot')->name('setting.robot');
        Route::post('robot', 'robotSubmit');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        //maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit');
    });


    Route::controller('CronConfigurationController')->name('cron.')->prefix('cron')->group(function () {
        Route::get('index', 'cronJobs')->name('index');
        Route::post('store', 'cronJobStore')->name('store');
        Route::post('update', 'cronJobUpdate')->name('update');
        Route::post('delete/{id}', 'cronJobDelete')->name('delete');
        Route::get('schedule', 'schedule')->name('schedule');
        Route::post('schedule/store', 'scheduleStore')->name('schedule.store');
        Route::post('schedule/status/{id}', 'scheduleStatus')->name('schedule.status');
        Route::get('schedule/pause/{id}', 'schedulePause')->name('schedule.pause');
        Route::get('schedule/logs/{id}', 'scheduleLogs')->name('schedule.logs');
        Route::post('schedule/log/resolved/{id}', 'scheduleLogResolved')->name('schedule.log.resolved');
        Route::post('schedule/log/flush/{id}', 'logFlush')->name('log.flush');
    });

    //KYC setting
    Route::controller('KycController')->group(function () {
        Route::get('kyc-setting', 'setting')->name('kyc.setting');
        Route::post('kyc-setting', 'settingUpdate');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global/email', 'globalEmail')->name('global.email');
        Route::post('global/email/update', 'globalEmailUpdate')->name('global.email.update');

        Route::get('global/sms', 'globalSms')->name('global.sms');
        Route::post('global/sms/update', 'globalSmsUpdate')->name('global.sms.update');

        Route::get('global/push', 'globalPush')->name('global.push');
        Route::post('global/push/update', 'globalPushUpdate')->name('global.push.update');

        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{type}/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });

    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');

    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key?}', 'frontendSections')->name('sections');
            Route::get('frontend-sections/blog/{category}', 'frontendSections')->name('sections.blog.category');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::get('frontend-slug-check/{key}/{id?}', 'frontendElementSlugCheck')->name('sections.element.slug.check');
            Route::get('frontend-element-seo/{key}/{id}', 'frontendSeo')->name('sections.element.seo');
            Route::post('frontend-element-seo/{key}/{id}', 'frontendSeoUpdate');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::get('manage-pages/check-slug/{id?}', 'checkSlug')->name('manage.pages.check.slug');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');

            Route::get('manage-seo/{id}', 'manageSeo')->name('manage.pages.seo');
            Route::post('manage-seo/{id}', 'manageSeoStore');
        });
    });

    // Alert Dashboard
    Route::get('alert-dashboard', 'AlertDashboardController@index')->name('alert.dashboard');
    Route::post('alert-settings', 'AlertDashboardController@saveSettings')->name('alert.settings');
    
    // Quản lý phó tổng giám đốc (chỉ admin mới có quyền)
    Route::controller('DeputyController')->name('deputy.')->prefix('deputy')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('admin.action');
        Route::get('create', 'create')->name('create')->middleware('admin.action');
        Route::post('store', 'store')->name('store')->middleware('admin.action');
        Route::post('delete/{id}', 'delete')->name('delete')->middleware('admin.action');
    });
});

// Các route POST, PUT, DELETE cần quyền thực hiện hành động
Route::middleware(['admin.action'])->group(function() {
    // Các route cho AdminController
    Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
    Route::post('password', 'AdminController@passwordUpdate')->name('password.update');
    Route::post('notifications/delete-all', 'AdminController@deleteAllNotification')->name('notifications.delete.all');
    Route::post('notifications/delete-single/{id}', 'AdminController@deleteSingleNotification')->name('notifications.delete.single');
    
    // Manage Time
    Route::post('time/store/{id?}', 'ManageTimeController@store')->name('time.store');
    Route::post('time/status/{id?}', 'ManageTimeController@status')->name('time.status');
    
    // Manage Invest
    Route::post('invest/status/{id}', 'ManageInvestController@investStatus')->name('invest.status');
    Route::post('invest/approve/{id}', 'ManageInvestController@approve')->name('invest.approve');
    Route::post('invest/reject/{id}', 'ManageInvestController@reject')->name('invest.reject');
    Route::post('invest/stop-returns/{id}', 'ManageInvestController@stopReturns')->name('invest.stop.returns');
    Route::post('invest/start-returns/{id}', 'ManageInvestController@startReturns')->name('invest.start.returns');
    
    // Category
    Route::post('category/store/{id?}', 'CategoryController@store')->name('category.store');
    Route::post('category/status/{id?}', 'CategoryController@status')->name('category.status');
    
    // Comment
    Route::post('comment/store/{id}/{comment_id}', 'CommentController@store')->name('comment.store');
    Route::post('comment/status/{id?}', 'CommentController@status')->name('comment.status');
    
    // Project
    Route::post('project/store/{id?}', 'ManageProjectController@store')->name('project.store');
    Route::post('project/status/{id}', 'ManageProjectController@status')->name('project.status');
    Route::post('project/end/{id}', 'ManageProjectController@end')->name('project.end');
    Route::post('project/update/seo/{id}', 'ManageProjectController@updateSEO')->name('project.update.seo');
    
    // Project Fake
    Route::post('project/fake/investment/{id}', 'ProjectFakeController@fakeInvestment')->name('project.fake.investment');
    Route::post('project/fake/reset/{id}', 'ProjectFakeController@resetInvestment')->name('project.fake.reset');
    
    // Project Documents
    Route::post('project/{projectId}/documents/store', 'ProjectDocumentController@store')->name('project.documents.store');
    Route::post('project/{projectId}/documents/update/{documentId}', 'ProjectDocumentController@update')->name('project.documents.update');
    Route::post('project/{projectId}/documents/delete/{documentId}', 'ProjectDocumentController@destroy')->name('project.documents.delete');
    
    // Document Categories
    Route::post('document/categories/store', 'DocumentCategoryController@store')->name('document.categories.store');
    Route::post('document/categories/update/{id}', 'DocumentCategoryController@update')->name('document.categories.update');
    Route::post('document/categories/destroy/{id}', 'DocumentCategoryController@destroy')->name('document.categories.destroy');
    Route::post('document/categories/status/{id}', 'DocumentCategoryController@status')->name('document.categories.status');
    
    // Reference Documents
    Route::post('documents/store', 'ReferenceDocumentController@store')->name('documents.store');
    Route::post('documents/update/{id}', 'ReferenceDocumentController@update')->name('documents.update');
    Route::post('documents/destroy/{id}', 'ReferenceDocumentController@destroy')->name('documents.destroy');
    Route::post('documents/status/{id}', 'ReferenceDocumentController@status')->name('documents.status');
    
    // Contract Documents
    Route::post('invest/{investId}/documents/upload', 'ContractDocumentController@upload')->name('invest.documents.upload');
    Route::post('invest/{investId}/documents/delete/{documentId}', 'ContractDocumentController@delete')->name('invest.documents.delete');
    
    // Project FAQ
    Route::post('project/faq/store/{id?}', 'ManageFaqController@storeFaq')->name('project.faq.store');
    Route::post('project/faq/status/{id}', 'ManageFaqController@faqStatus')->name('project.faq.status');
    
    // Users Manager
    Route::post('users/update/{id}', 'ManageUsersController@update')->name('users.update');
    Route::post('users/add-sub-balance/{id}', 'ManageUsersController@addSubBalance')->name('users.add.sub.balance');
    Route::post('users/send-notification/{id}', 'ManageUsersController@sendNotificationSingle')->name('users.notification.single');
    Route::post('users/status/{id}', 'ManageUsersController@status')->name('users.status');
    Route::post('users/send-notification', 'ManageUsersController@sendNotificationAll')->name('users.notification.all.send');
    Route::post('users/create-staff', 'ManageUsersController@createStaff')->name('users.staff.create');
    
    // KYC Manager
    Route::post('users/kyc-approve/{id}', 'ManageUsersController@kycApprove')->name('users.kyc.approve');
    Route::post('users/kyc-reject/{id}', 'ManageUsersController@kycReject')->name('users.kyc.reject');
    
    // Reports & Logs
    Route::post('report/notification/read-all', 'ReportController@notificationReadAll')->name('report.notification.readAll');
    Route::post('report/login/delete', 'ReportController@loginDelete')->name('report.login.delete');
    
    // Deposit System
    Route::post('deposit/approve/{id}', 'DepositController@approve')->name('deposit.approve');
    Route::post('deposit/reject/{id}', 'DepositController@reject')->name('deposit.reject');
    
    // Withdraw System
    Route::post('withdraw/approve/{id}', 'WithdrawController@approve')->name('withdraw.approve');
    Route::post('withdraw/reject/{id}', 'WithdrawController@reject')->name('withdraw.reject');
    
    // Setting & Configuration
    Route::post('setting/update', 'GeneralSettingController@update')->name('setting.update');
    Route::post('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo.icon');
    Route::post('extensions/update/{id}', 'ExtensionController@update')->name('extensions.update');
    Route::post('extensions/status/{id}', 'ExtensionController@status')->name('extensions.status');
    
    // Frontend & Pages
    Route::post('frontend/templates/activate', 'FrontendController@activeTemplate')->name('frontend.templates.activate');
    Route::post('frontend/content/{id}/{key}', 'FrontendController@frontendContent')->name('frontend.content');
    Route::post('frontend/element/{id}/{key}', 'FrontendController@frontendElement')->name('frontend.element');
    Route::post('frontend/remove/{id}', 'FrontendController@remove')->name('frontend.remove');
    Route::post('manage-language/update/{id}', 'LanguageController@langUpdate')->name('language.manage.update');
    Route::post('manage-language/delete/{id}', 'LanguageController@langDelete')->name('language.manage.delete');
    Route::post('manage-language/add', 'LanguageController@langAdd')->name('language.manage.add');
    Route::post('manage-language/store', 'LanguageController@langStore')->name('language.manage.store');
    
    // Mail & SMS Configuration
    Route::post('email-template/update/{id}', 'EmailTemplateController@update')->name('email.template.update');
    Route::post('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
    Route::post('email-template/status/{id}', 'EmailTemplateController@emailStatus')->name('email.template.status');
    Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
    Route::post('sms-template/status/{id}', 'SmsTemplateController@smsStatus')->name('sms.template.status');
    
    // Honors Manager
    Route::post('honors/store', 'HonorController@store')->name('honors.store');
    Route::post('honors/update/{id}', 'HonorController@update')->name('honors.update');
    Route::post('honors/status/{id}', 'HonorController@status')->name('honors.status');
    Route::post('honors/delete/{id}', 'HonorController@destroy')->name('honors.delete');
    Route::post('honors/{honorId}/images/upload', 'HonorController@uploadImages')->name('honors.images.upload');
    Route::post('honors/{honorId}/images/{imageId}/delete', 'HonorController@deleteImage')->name('honors.images.delete');
    Route::post('honors/{honorId}/images/{imageId}/set-featured', 'HonorController@setFeaturedImage')->name('honors.images.featured');
    Route::post('honors/{honorId}/images/update-captions', 'HonorController@updateCaptions')->name('honors.images.captions');
});

// Nhóm các route liên quan đến cài đặt hệ thống, chỉ cho phép admin thực hiện
Route::middleware(['admin', 'admin.action'])->group(function () {
    // General Setting
    Route::controller('GeneralSettingController')->group(function () {
        Route::get('system-setting', 'systemSetting')->name('setting.system');
        Route::get('general-setting', 'general')->name('setting.general');
        Route::post('general-setting', 'generalUpdate');
        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::get('sitemap', 'sitemap')->name('setting.sitemap');
        Route::get('robot', 'robot')->name('setting.robot');
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('setting/update', 'update')->name('setting.update');
    });
    // KYC Setting
    Route::get('kyc-setting', 'GeneralSettingController@setting')->name('kyc.setting');
    Route::post('kyc-setting', 'GeneralSettingController@settingUpdate');
    // Email Setting
    Route::get('email/setting', 'GeneralSettingController@emailSetting')->name('email');
    Route::post('email/setting', 'GeneralSettingController@emailSettingUpdate');
    // SMS Setting
    Route::get('sms/setting', 'GeneralSettingController@smsSetting')->name('sms');
    Route::post('sms/setting', 'GeneralSettingController@smsSettingUpdate');
    // Push Notification Setting
    Route::get('notification/push/setting', 'GeneralSettingController@pushSetting')->name('push');
    Route::post('notification/push/setting', 'GeneralSettingController@pushSettingUpdate');
    Route::post('notification/push/setting/upload', 'GeneralSettingController@pushSettingUpload')->name('push.upload');
    Route::get('notification/push/setting/download', 'GeneralSettingController@pushSettingDownload')->name('push.download');
    // Extension
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });
    // Email Template
    Route::post('email-template/update/{id}', 'EmailTemplateController@update')->name('email.template.update');
    Route::post('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
    Route::post('email-template/status/{id}', 'EmailTemplateController@emailStatus')->name('email.template.status');
    // SMS Template
    Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
    Route::post('sms-template/status/{id}', 'SmsTemplateController@smsStatus')->name('sms.template.status');
});

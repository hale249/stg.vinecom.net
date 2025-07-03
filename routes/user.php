<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->name('user.')->group(function () {

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');
    Route::post('check-referral-code', 'User\UserController@checkReferralCode')->name('checkReferralCode');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    // Sales Staff Role-Based Routes - Outside of registration.complete middleware
    Route::middleware(['check.status', 'staff.role:sales_manager'])->name('staff.')->group(function () {
        Route::controller('User\SalesManagerController')->prefix('manager')->name('manager.')->group(function () {
            Route::get('/', 'dashboard')->name('dashboard');
            Route::get('team-members', 'teamMembers')->name('team_members');
            Route::post('create-staff', 'createStaffMember')->name('create_staff');
            Route::get('contracts', 'teamContracts')->name('contracts');
            Route::get('contract/details/ajax', 'getContractDetailsAjax')->name('contract.details.ajax');
            Route::get('contract/{id}', 'viewContract')->name('contract');
            Route::get('approval-requests', 'approvalRequests')->name('approval_requests');
            Route::post('approve-contract/{id}', 'approveContract')->name('approve_contract');
            Route::post('reject-contract/{id}', 'rejectContract')->name('reject_contract');
            Route::get('alerts', 'alerts')->name('alerts');
            Route::get('reports', 'reports')->name('reports');
            Route::get('report/transactions', 'reportTransactions')->name('report.transactions');
            Route::get('report/interests', 'reportInterests')->name('report.interests');
            Route::get('report/commissions', 'reportCommissions')->name('report.commissions');
            Route::get('hr/salary', 'salaryDashboard')->name('hr.salary');
            Route::get('hr/kpi', 'kpiDashboard')->name('hr.kpi');
            Route::post('hr/kpi', 'storeKPI')->name('hr.kpi.store');
            Route::get('hr/kpi/export', 'exportKPI')->name('hr.kpi.export');
            Route::get('hr/kpi/{id}', 'showKPI')->name('hr.kpi.show');
            Route::get('hr/kpi/{id}/edit', 'editKPI')->name('hr.kpi.edit');
            Route::put('hr/kpi/{id}', 'updateKPI')->name('hr.kpi.update');
            Route::delete('hr/kpi/{id}', 'destroyKPI')->name('hr.kpi.destroy');
            Route::get('hr/performance', 'performanceDashboard')->name('hr.performance');
            
            // Attendance Management Routes
            Route::get('hr/attendance', 'attendanceDashboard')->name('hr.attendance');
            Route::post('hr/attendance/store', 'storeAttendance')->name('hr.attendance.store');
            Route::post('hr/attendance/delete/{id}', 'deleteAttendance')->name('hr.attendance.delete');
            Route::get('hr/attendance/export', 'exportAttendance')->name('hr.attendance.export');
            Route::post('hr/attendance/import', 'importAttendance')->name('hr.attendance.import');
        });
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');

                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');

                Route::get('projects', 'projects')->name('projects');
                Route::get('projects/transactions/{id}', 'projectsTransactions')->name('projects.transactions');
                
                // Investment Contract
                Route::get('investment/contract', 'investmentContract')->name('investment.contract');
            });

            // Support Ticket
            Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
                Route::get('/', 'supportTicket')->name('index');
                Route::get('new', 'openSupportTicket')->name('open');
                Route::post('create', 'storeSupportTicket')->name('store');
                Route::get('view/{ticket}', 'viewTicket')->name('view');
                Route::post('reply/{ticket}', 'replyTicket')->name('reply');
                Route::post('close/{ticket}', 'closeTicket')->name('close');
                Route::get('download/{ticket}', 'ticketDownload')->name('download');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });

            // Invest Controller
            Route::controller('InvestController')->prefix('invest')->name('invest.')->group(function () {
                Route::post('/store', 'order')->name('order');
                Route::get('/contract/{id}', 'showContract')->name('contract');
                Route::get('/contract/{id}/watermark', 'viewContractWithWatermark')->name('contract.watermark');
                Route::post('/confirm/{id}', 'confirm')->name('confirm');
                Route::post('/cancel/{id}', 'cancel')->name('cancel');
                Route::get('/contract/{id}/download', 'downloadContract')->name('contract.download');
                Route::get('/history', 'history')->name('history');
            });

            // Comment
            Route::controller('CommentController')->prefix('comment')->name('comment.')->group(function () {
                Route::post('store/{id}/{comment_id?}', 'comment')->name('store');
            });
        });

        // Payment
        Route::prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::post('insert/{investId?}', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            Route::any('/{investId?}', 'deposit')->name('index');
        });
        
        Route::middleware('staff.role:sales_staff')->name('staff.')->group(function () {
            Route::controller('User\SalesStaffController')->prefix('staff')->name('staff.')->group(function () {
                Route::get('/', 'dashboard')->name('dashboard');
                Route::get('contracts', 'contracts')->name('contracts');
                Route::get('contract/{id}', 'contractDetails')->name('contract.details');
                Route::get('create-contract', 'createContract')->name('create_contract');
                Route::post('store-contract', 'storeContract')->name('store_contract');
                Route::post('cancel-contract/{id}', 'cancelContract')->name('cancel_contract');
                Route::get('alerts', 'alerts')->name('alerts');
                Route::get('customers', 'customers')->name('customers');
                Route::get('salary', 'salary')->name('salary');
                Route::get('kpi', 'kpi')->name('kpi');
            });
            
            // Staff notification routes
            Route::controller('User\NotificationController')->prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('{id}', 'show')->name('show');
                Route::post('{id}/read', 'markAsRead')->name('read');
            });
        });
    });
});

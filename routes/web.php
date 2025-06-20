<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::controller('CronController')->group(function () {
    Route::get('cron', 'cron')->name('cron');
    Route::get('interest', 'interest')->name('interest');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
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
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
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

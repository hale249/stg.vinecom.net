<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'master';
        $this->redirectLink = 'user.ticket.view';
        $this->userType     = 'user';
        $this->column       = 'user_id';
        $this->user = auth()->user();
        
        // Override page titles for Vietnamese
        \View::share('support_ticket_titles', [
            'index' => 'Yêu cầu hỗ trợ',
            'create' => 'Tạo yêu cầu hỗ trợ mới',
            'view' => 'Chi tiết yêu cầu hỗ trợ'
        ]);
    }
} 
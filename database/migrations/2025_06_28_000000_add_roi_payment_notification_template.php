<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the notification template already exists
        $exists = DB::table('notification_templates')
            ->where('name', 'ROI_PAYMENT')
            ->exists();
            
        if (!$exists) {
            // Insert the ROI payment notification template
            // Temporarily commented out to allow seeder.sql import
            /*
            DB::table('notification_templates')->insert([
                'act' => 'ROI_PAYMENT',
                'name' => 'ROI Payment',
                'subject' => 'ROI Payment for Your Investment',
                'email_body' => '<div>
                    <div style="padding: 15px 30px">
                        <div style="margin-bottom: 25px">
                            <p style="margin-bottom: 10px">Hello {{fullname}},</p>
                            <p style="margin-bottom: 10px">We are pleased to inform you that your ROI payment has been credited to your account.</p>
                            <div style="margin-bottom: 10px">
                                <p style="margin-bottom: 5px"><strong>Investment ID:</strong> {{invest_id}}</p>
                                <p style="margin-bottom: 5px"><strong>Project:</strong> {{project_title}}</p>
                                <p style="margin-bottom: 5px"><strong>ROI Amount:</strong> {{amount}}</p>
                                <p style="margin-bottom: 5px"><strong>Current Balance:</strong> {{post_balance}}</p>
                            </div>
                            <p style="margin-bottom: 10px">Thank you for investing with us!</p>
                        </div>
                    </div>
                </div>',
                'sms_body' => 'Hello {{fullname}}, Your ROI payment of {{amount}} for investment {{invest_id}} has been credited to your account. Current balance: {{post_balance}}',
                'shortcodes' => '{"fullname":"Full Name", "username":"Username", "invest_id":"Investment ID", "amount":"ROI Amount", "project_title":"Project Title", "post_balance":"Current Balance"}',
                'email_status' => 1,
                'sms_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            */
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the ROI payment notification template
        DB::table('notification_templates')
            ->where('act', 'ROI_PAYMENT')
            ->delete();
    }
}; 
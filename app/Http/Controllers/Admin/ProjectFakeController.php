<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ProjectFakeController extends Controller
{
    /**
     * Fake investment data for a project with specified percentage
     */
    public function fakeInvestment(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $percentage = $request->percentage ?? 10;
        
        // Store original values in cache if not already stored
        if (!Cache::has('original_project_' . $project->id)) {
            Cache::put('original_project_' . $project->id, [
                'available_share' => $project->available_share,
                'share_count' => $project->share_count
            ], now()->addDay());
        }
        
        // Calculate shares to decrease based on percentage
        $sharesToDecrease = ceil($project->share_count * ($percentage / 100));
        
        // Make sure we don't go below 0
        $newAvailableShares = max(0, $project->available_share - $sharesToDecrease);
        
        // Calculate the new progress percentage
        $oldProgress = $project->investment_progress;
        
        // Update the project
        $project->available_share = $newAvailableShares;
        $project->save();
        
        // Get new progress percentage
        $newProgress = $project->investment_progress;
        
        // Store the fact that we're using fake data
        Session::put('using_fake_data_' . $project->id, true);
        
        $notify[] = ['success', "Tiến độ đầu tư đã tăng từ {$oldProgress}% lên {$newProgress}%"];
        return back()->withNotify($notify);
    }
    
    /**
     * Reset investment data to original values
     */
    public function resetInvestment($id)
    {
        $project = Project::findOrFail($id);
        
        // Get original values from cache
        $originalData = Cache::get('original_project_' . $project->id);
        
        if ($originalData) {
            // Get current values for display
            $currentProgress = $project->investment_progress;
            
            // Restore original values
            $project->available_share = $originalData['available_share'];
            $project->save();
            
            // Get new values after reset
            $newProgress = $project->investment_progress;
            
            // Remove the fake data flag
            Session::forget('using_fake_data_' . $project->id);
            
            $notify[] = ['success', "Đã reset tiến độ đầu tư từ {$currentProgress}% về {$newProgress}%"];
            return back()->withNotify($notify);
        }
        
        $notify[] = ['error', 'Không thể reset, không tìm thấy dữ liệu gốc'];
        return back()->withNotify($notify);
    }
} 
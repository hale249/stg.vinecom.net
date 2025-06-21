<?php

namespace Database\Seeders;

use App\Models\StaffSalary;
use App\Models\StaffKPI;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSalaryKPISeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sales managers and their staff
        $managers = User::where('is_staff', true)
            ->where('role', 'sales_manager')
            ->get();

        foreach ($managers as $manager) {
            $staffMembers = $manager->staffMembers;
            
            if ($staffMembers->count() > 0) {
                // Create sample data for the last 3 months
                for ($i = 2; $i >= 0; $i--) {
                    $monthYear = now()->subMonths($i)->format('Y-m');
                    
                    foreach ($staffMembers as $staff) {
                        // Create sample salary data
                        $this->createSampleSalary($manager, $staff, $monthYear);
                        
                        // Create sample KPI data
                        $this->createSampleKPI($manager, $staff, $monthYear);
                    }
                }
            }
        }
    }

    private function createSampleSalary($manager, $staff, $monthYear)
    {
        // Check if salary already exists for this month
        $existingSalary = StaffSalary::where('staff_id', $staff->id)
            ->where('month_year', $monthYear)
            ->first();

        if ($existingSalary) {
            return;
        }

        // Generate random but realistic data
        $baseSalary = rand(5000000, 8000000); // 5-8 million VND
        $salesAmount = rand(500000000, 3000000000); // 500M - 3B VND
        $commissionRate = rand(2, 5); // 2-5%
        $commissionAmount = ($salesAmount * $commissionRate) / 100;
        $bonusAmount = rand(0, 2000000); // 0-2M bonus
        $deductionAmount = rand(0, 500000); // 0-500K deduction
        $totalSalary = $baseSalary + $commissionAmount + $bonusAmount - $deductionAmount;
        
        // Calculate KPI percentage based on sales performance
        $kpiPercentage = rand(60, 150); // 60-150%
        
        // Determine KPI status
        if ($kpiPercentage >= 120) {
            $kpiStatus = 'exceeded';
        } elseif ($kpiPercentage >= 100) {
            $kpiStatus = 'achieved';
        } elseif ($kpiPercentage >= 80) {
            $kpiStatus = 'near_achieved';
        } else {
            $kpiStatus = 'not_achieved';
        }

        StaffSalary::create([
            'staff_id' => $staff->id,
            'manager_id' => $manager->id,
            'month_year' => $monthYear,
            'base_salary' => $baseSalary,
            'sales_amount' => $salesAmount,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'bonus_amount' => $bonusAmount,
            'deduction_amount' => $deductionAmount,
            'total_salary' => $totalSalary,
            'kpi_percentage' => $kpiPercentage,
            'kpi_status' => $kpiStatus,
            'notes' => 'Dữ liệu mẫu được tạo tự động',
            'status' => 'approved',
        ]);
    }

    private function createSampleKPI($manager, $staff, $monthYear)
    {
        // Check if KPI already exists for this month
        $existingKPI = StaffKPI::where('staff_id', $staff->id)
            ->where('month_year', $monthYear)
            ->first();

        if ($existingKPI) {
            return;
        }

        // Generate random but realistic KPI data
        $targetContracts = rand(3, 8);
        $actualContracts = rand(1, $targetContracts + 3);
        $targetSales = rand(500000000, 2000000000);
        $actualSales = rand($targetSales * 0.5, $targetSales * 1.5);
        $targetCustomers = rand(2, 6);
        $actualCustomers = rand(1, $targetCustomers + 2);

        // Calculate completion rates
        $contractCompletionRate = $targetContracts > 0 ? ($actualContracts / $targetContracts) * 100 : 0;
        $salesCompletionRate = $targetSales > 0 ? ($actualSales / $targetSales) * 100 : 0;
        $customerCompletionRate = $targetCustomers > 0 ? ($actualCustomers / $targetCustomers) * 100 : 0;
        
        // Calculate overall KPI (average of 3 rates)
        $overallKpiPercentage = ($contractCompletionRate + $salesCompletionRate + $customerCompletionRate) / 3;

        // Determine KPI status
        if ($overallKpiPercentage >= 120) {
            $kpiStatus = 'exceeded';
        } elseif ($overallKpiPercentage >= 100) {
            $kpiStatus = 'achieved';
        } elseif ($overallKpiPercentage >= 80) {
            $kpiStatus = 'near_achieved';
        } else {
            $kpiStatus = 'not_achieved';
        }

        StaffKPI::create([
            'staff_id' => $staff->id,
            'manager_id' => $manager->id,
            'month_year' => $monthYear,
            'target_contracts' => $targetContracts,
            'actual_contracts' => $actualContracts,
            'target_sales' => $targetSales,
            'actual_sales' => $actualSales,
            'target_customers' => $targetCustomers,
            'actual_customers' => $actualCustomers,
            'contract_completion_rate' => $contractCompletionRate,
            'sales_completion_rate' => $salesCompletionRate,
            'customer_completion_rate' => $customerCompletionRate,
            'overall_kpi_percentage' => $overallKpiPercentage,
            'kpi_status' => $kpiStatus,
            'notes' => 'Dữ liệu KPI mẫu được tạo tự động',
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }
}

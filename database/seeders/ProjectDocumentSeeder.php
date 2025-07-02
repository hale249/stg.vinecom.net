<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Database\Seeder;

class ProjectDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            // Tạo tài liệu pháp lý
            ProjectDocument::create([
                'project_id' => $project->id,
                'title' => 'Giấy phép đầu tư dự án ' . $project->title,
                'file_path' => 'project-documents/' . $project->id . '/investment-license.pdf',
                'file_name' => 'investment-license.pdf',
                'file_size' => '1024000', // 1MB
                'type' => 'legal',
                'description' => 'Giấy phép đầu tư chính thức được cấp bởi cơ quan có thẩm quyền',
                'is_public' => true,
                'sort_order' => 1
            ]);

            ProjectDocument::create([
                'project_id' => $project->id,
                'title' => 'Hợp đồng đầu tư mẫu',
                'file_path' => 'project-documents/' . $project->id . '/investment-contract.pdf',
                'file_name' => 'investment-contract.pdf',
                'file_size' => '2048000', // 2MB
                'type' => 'legal',
                'description' => 'Mẫu hợp đồng đầu tư chuẩn cho dự án này',
                'is_public' => true,
                'sort_order' => 2
            ]);

            // Tạo tài liệu tài chính
            ProjectDocument::create([
                'project_id' => $project->id,
                'title' => 'Báo cáo tài chính dự án',
                'file_path' => 'project-documents/' . $project->id . '/financial-report.pdf',
                'file_name' => 'financial-report.pdf',
                'file_size' => '1536000', // 1.5MB
                'type' => 'financial',
                'description' => 'Báo cáo tài chính chi tiết và dự báo lợi nhuận',
                'is_public' => true,
                'sort_order' => 3
            ]);

            ProjectDocument::create([
                'project_id' => $project->id,
                'title' => 'Kế hoạch tài chính',
                'file_path' => 'project-documents/' . $project->id . '/financial-plan.pdf',
                'file_name' => 'financial-plan.pdf',
                'file_size' => '1280000', // 1.25MB
                'type' => 'financial',
                'description' => 'Kế hoạch sử dụng vốn và phân bổ tài chính',
                'is_public' => true,
                'sort_order' => 4
            ]);

            // Tạo tài liệu kỹ thuật
            ProjectDocument::create([
                'project_id' => $project->id,
                'title' => 'Thiết kế kỹ thuật dự án',
                'file_path' => 'project-documents/' . $project->id . '/technical-design.pdf',
                'file_name' => 'technical-design.pdf',
                'file_size' => '2560000', // 2.5MB
                'type' => 'technical',
                'description' => 'Bản vẽ thiết kế kỹ thuật và quy trình thực hiện',
                'is_public' => true,
                'sort_order' => 5
            ]);

            // Tạo tài liệu khác
            ProjectDocument::create([
                'project_id' => $project->id,
                'title' => 'Hướng dẫn đầu tư',
                'file_path' => 'project-documents/' . $project->id . '/investment-guide.pdf',
                'file_name' => 'investment-guide.pdf',
                'file_size' => '768000', // 750KB
                'type' => 'other',
                'description' => 'Hướng dẫn chi tiết về cách đầu tư vào dự án',
                'is_public' => true,
                'sort_order' => 6
            ]);
        }
    }
} 
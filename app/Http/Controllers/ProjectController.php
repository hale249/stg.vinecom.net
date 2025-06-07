<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller {
    public function projects() {
        $pageTitle       = 'Projects';
        $categories      = Category::active()->get();
        $projects        = Project::active()->available()->beforeEndDate();
        $count           = $projects->count();
        $minProjectPrice = $projects->min('share_amount');
        $maxProjectPrice = $projects->max('share_amount');
        $projects        = $projects->latest()->paginate(getPaginate(12));
        return view('Template::projects.index', compact('pageTitle', 'projects', 'categories', 'count', 'minProjectPrice', 'maxProjectPrice'));
    }

    public function projectDetails(Request $request, $slug) {
        $pageTitle = 'Project Details';
        $project   = Project::active()->where('slug', $slug)->firstOrFail();
        $relates   = Project::where('slug', '!=', $slug)->where('category_id', $project->category_id)->active()->available()->beforeEndDate()->limit(10)->get();

        $seoContents  = $project->seo_content;
        $path         = 'assets/images/frontend/project/seo';
        $seoImage     = @$seoContents->image ? getImage($path . '/' . @$seoContents->image, getFileSize('seo')) : null;
        $commentsInit = Comment::with('replies')->active()->comment()->where('project_id', $project->id)->latest();
        $commentCount = $commentsInit->count();
        $comments     = $commentsInit->paginate(getPaginate(5));

        if ($request->ajax()) {
            return view('Template::partials.comment', compact('comments', 'project'))->render();
        }

        return view('Template::projects.project_details', compact('pageTitle', 'project', 'relates', 'comments', 'commentCount', 'seoContents', 'seoImage'));
    }

    public function checkQuantity(Request $request) {
        $validatedData = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'quantity'   => 'required|integer|min:1',
        ]);
        $projectId         = $validatedData['project_id'];
        $requestedQuantity = (int) $validatedData['quantity'];

        $project = Project::active()->findOrFail($projectId);

        if ($requestedQuantity > $project->available_share) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Requested quantity exceeds available shares.',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Requested quantity is available.',
        ]);
    }

    public function filter(Request $request) {
        $pageTitle  = 'Projects';
        $categories = Category::active()->get();

        $projects = Project::active()->searchable(['title'])->beforeEndDate()->available();

        if ($request->has('category') && !empty($request->category)) {
            $projects = $this->filterItem($request, $projects, 'category');
        }

        if ($request->has('return_type') && !empty($request->return_type)) {
            $projects = $this->filterItem($request, $projects, 'return_type');
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPrice = @$request->input('min_price') ?? 0;
            $maxPrice = @$request->input('max_price') ?? 0;

            $projects = $projects->whereBetween('share_amount', [$minPrice, $maxPrice]);
        }

        $minProjectPrice = $projects->min('share_amount');
        $maxProjectPrice = $projects->max('share_amount');

        $projects = $projects->latest()->paginate(getPaginate(12));

        $viewType = $request->input('viewType', 'grid');

        session()->put('viewType', $viewType);

        if ($viewType === 'list') {
            $view = view('Template::projects.list-project', compact('projects', 'categories'))->render();
        } else {
            $view = view('Template::projects.project', compact('projects', 'categories'))->render();
        }

        return response()->json([
            'view'          => $view,
            'totalProjects' => $projects->total(),
            'minPrice'      => $minProjectPrice,
            'maxPrice'      => $maxProjectPrice,
        ]);
    }

    protected function filterItem($request, $projects, $type) {
        $col = $type == 'category' ? ($type . '_id') : $type;

        if (is_array($request->$type)) {
            $projects->whereIn($col, $request->$type);
        } else {
            $projects->where($col, $request->$type);
        }

        return $projects;
    }
}

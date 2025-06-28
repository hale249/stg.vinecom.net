<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Invest;
use App\Models\Project;
use App\Models\Time;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ManageProjectController extends Controller
{

    public function index()
    {
        $pageTitle = 'All Projects';
        $projects = Project::withCount('comment')->orderByDesc('id')->searchable(['title'])->paginate(getPaginate());
        return view('admin.project.index', compact('pageTitle', 'projects'));
    }

    public function create()
    {
        $pageTitle = 'New Project';
        $times = Time::active()->get();
        $categories = Category::active()->get();
        return view('admin.project.create', compact('pageTitle', 'times', 'categories'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Project';
        $project = Project::findOrFail($id);
        $times = Time::active()->get();
        $categories = Category::active()->get();

        $galleries = [];

        foreach ($project->gallery ?? [] as $key => $gallery) {
            $img['id'] = $gallery;
            $img['src'] = getImage(getFilePath('project') . '/' . $gallery);
            $galleries[] = $img;
        }

        return view('admin.project.create', compact('pageTitle', 'project', 'times', 'galleries', 'categories'));
    }

    public function show($id)
    {
        $pageTitle = 'Project Details';
        $project = Project::with(['category', 'time'])->findOrFail($id);
        return view('admin.project.show', compact('pageTitle', 'project'));
    }

    public function store(Request $request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';
        
        // Convert formatted money inputs back to numeric values
        $request->merge([
            'target_amount' => $this->convertFormattedMoney($request->target_amount),
            'share_amount' => $this->convertFormattedMoney($request->share_amount),
            'roi_amount' => $this->convertFormattedMoney($request->roi_amount),
        ]);
        
        $request->validate([
            'title'          => 'required|string|max:40',
            'target_amount'  => 'required|numeric|gt:0',
            'description'    => 'required|string',
            'share_amount'   => 'required|numeric|gt:0',
            'share_count'    => "$isRequired|numeric|gt:0",
            'roi_amount'     => 'required|numeric|gt:0',
            'roi_percentage' => 'required|numeric|gt:0',
            'map_url'        => 'required|string|regex:/^<iframe.*src="https:\/\/www\.google\.com\/maps\/embed\?pb=.+".*><\/iframe>$/',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date',
            'image'          => [$isRequired, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'gallery'        => "$isRequired|array|min:0|max:4",
            'gallery.*'      => [$isRequired, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'category_id'    => "$isRequired|exists:categories,id",
        ]);

        // Calculate and validate the relationship between target_amount, share_count, and share_amount
        $targetAmount = $request->target_amount;
        $shareCount = $request->share_count;
        $shareAmount = $request->share_amount;
        
        // Ensure consistency: target_amount should equal share_count * share_amount
        $calculatedTarget = $shareCount * $shareAmount;
        $difference = abs($calculatedTarget - $targetAmount);
        
        // If difference is significant (more than 0.01), adjust target_amount to match calculation
        if ($difference > 0.01) {
            $targetAmount = $calculatedTarget;
        }

        if ($id) {
            $project = Project::findOrFail($id);
            $notify[] = ['success', 'Project updated successfully'];
            $imageToRemove = $request->old ? array_values(removeElement($project->gallery, $request->old)) : $project->gallery;

            if ($imageToRemove != null && count($imageToRemove)) {
                foreach ($imageToRemove as $singleImage) {
                    fileManager()->removeFile(getFilePath('project') . '/' . $singleImage);
                }

                $project->gallery = removeElement($project->gallery, $imageToRemove);
            }
            $redirect = back();
        } else {
            $project = new Project();
            $project->available_share = $shareCount;
            $project->share_count = $shareCount;
            $notify[] = ['success', 'Project created successfully'];
            $redirect = redirect()->route('admin.project.index');
        }

        if ($request->hasFile('image')) {
            try {
                $old = $project->image ?? null;
                $project->image = fileUploader($request->image, getFilePath('project'), getFileSize('project'), $old);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Could not upload your image'];
                return back()->withNotify($notify);
            }
        }

        $gallery = $id ? $project->gallery : [];

        if ($request->hasFile('gallery')) {
            foreach ($request->gallery as $singleImage) {
                try {
                    $gallery[] = fileUploader($singleImage, getFilePath('project'), getFileSize('project'));
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload your product gallery image'];
                    return back()->withNotify($notify);
                }
            }
        }

        $project->title = $request->title;
        $project->slug = slug($request->title);
        $project->goal = $targetAmount; // Store the calculated target amount
        $project->share_amount = $shareAmount;
        $project->share_count = $shareCount;
        $project->roi_percentage = $request->roi_percentage;
        $project->roi_amount = $request->roi_percentage / 100 * $shareAmount;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        
        // Set maturity_time to 0 and maturity_date to end_date since we're removing the field
        $project->maturity_time = $request->maturity_time;
        $project->maturity_date = $request->end_date;
        
        // Set default values for removed fields
        $project->time_id = 0;
        $project->repeat_times = 0;
        $project->return_type = Status::LIFETIME;
        $project->capital_back = Status::NO;

        $project->category_id = $request->category_id;
        $project->map_url = $request->map_url;
        $project->description = $request->description;
        $project->gallery = $gallery;
        $project->featured = $request->featured ? Status::YES : Status::NO;
        $project->save();

        return $redirect->withNotify($notify);
    }

    public function checkSlug()
    {
        $id = request()->id ?? 0;
        $page = Project::where('slug', request()->slug);

        if ($id) {
            $page = $page->where('id', '!=', $id);
        }

        $exist = $page->exists();

        return response()->json([
            'exists' => $exist
        ]);
    }

    public function status(Request $request, $id)
    {
        return Project::changeStatus($id);
    }

    public function investHistory($id)
    {
        $pageTitle = 'Invest History of ' . Project::findOrFail($id)->title;
        $invests = Invest::where('project_id', $id)->with('project', 'user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.project.invest_history', compact('pageTitle', 'invests'));
    }

    public function frontendSEO($id)
    {
        $key = 'Manage Project SEO';
        $data = Project::findOrFail($id);
        $pageTitle = 'SEO Configuration';
        return view('admin.project.seo', compact('pageTitle', 'key', 'data'));
    }

    public function updateSEO(Request $request, $id)
    {
        $request->validate([
            'image' => ['nullable', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);

        $data = Project::findOrFail($id);
        $image = @$data->seo_content->image;
        if ($request->hasFile('image')) {
            try {
                $path = 'assets/images/frontend/project/seo';
                $image = fileUploader($request->image, $path, getFileSize('seo'), @$data->seo_content->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }
        $data->seo_content = [
            'image' => $image,
            'description' => $request->description,
            'social_title' => $request->social_title,
            'social_description' => $request->social_description,
            'keywords' => $request->keywords,
        ];
        $data->save();

        $notify[] = ['success', 'SEO content updated successfully'];
        return back()->withNotify($notify);
    }

    public function closed()
    {
        $pageTitle = 'Closed Projects';
        $projects = $this->projectData('end');
        return view('admin.project.index', compact('pageTitle', 'projects'));
    }

    protected function projectData($scope = null)
    {
        if ($scope) {
            $projects = Project::$scope();
        } else {
            $projects = Project::query();
        }
        return $projects->searchable(['title'])->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function end(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->status = Status::PROJECT_END;
        $project->save();
        $notify[] = ['success', 'Project closed successfully'];
        return back()->withNotify($notify);
    }

    public function lifetime()
    {
        $pageTitle = 'Lifetime Return Projects';
        $projects = $this->projectData('lifetime');

        return view('admin.project.index', compact('pageTitle', 'projects'));
    }

    public function repeat()
    {
        $pageTitle = 'Repeat Return Projects';
        $projects = $this->projectData('repeat');
        return view('admin.project.index', compact('pageTitle', 'projects'));
    }

    /**
     * Convert formatted money string to numeric value
     * Example: "200.000.000" -> 200000000
     */
    private function convertFormattedMoney($value)
    {
        if (empty($value)) {
            return 0;
        }
        
        // Remove all dots and convert to numeric
        $numericValue = str_replace('.', '', $value);
        return (float) $numericValue;
    }
}

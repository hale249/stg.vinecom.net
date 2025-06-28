<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Honor;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HonorController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Honors';
        $honors = Honor::latest()->paginate(getPaginate());
        return view('admin.honors.index', compact('pageTitle', 'honors'));
    }

    public function create()
    {
        $pageTitle = 'Add New Honor';
        return view('admin.honors.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        $honor = new Honor();
        $honor->title = $request->title;
        $honor->description = $request->description;
        $honor->start_date = $request->start_date;
        $honor->end_date = $request->end_date;
        $honor->is_active = $request->is_active;

        // Upload Image
        if ($request->hasFile('image')) {
            $path = getFilePath('honors');
            try {
                $filename = fileUploader($request->image, $path);
                $honor->image = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $honor->save();
        
        $notify[] = ['success', 'Honor created successfully'];
        return redirect()->route('admin.honors.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Honor';
        $honor = Honor::findOrFail($id);
        return view('admin.honors.edit', compact('pageTitle', 'honor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        $honor = Honor::findOrFail($id);
        $honor->title = $request->title;
        $honor->description = $request->description;
        $honor->start_date = $request->start_date;
        $honor->end_date = $request->end_date;
        $honor->is_active = $request->is_active;

        // Upload Image
        if ($request->hasFile('image')) {
            $path = getFilePath('honors');
            try {
                $oldImage = $honor->image;
                $filename = fileUploader($request->image, $path);
                $honor->image = $filename;
                
                // Delete old image
                if ($oldImage && file_exists($path . '/' . $oldImage)) {
                    fileManager()->removeFile($path . '/' . $oldImage);
                }
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $honor->save();
        
        $notify[] = ['success', 'Honor updated successfully'];
        return redirect()->route('admin.honors.index')->withNotify($notify);
    }

    public function destroy($id)
    {
        $honor = Honor::findOrFail($id);
        $path = getFilePath('honors');
        
        // Remove image
        if ($honor->image && file_exists($path . '/' . $honor->image)) {
            fileManager()->removeFile($path . '/' . $honor->image);
        }
        
        $honor->delete();
        
        $notify[] = ['success', 'Honor deleted successfully'];
        return redirect()->route('admin.honors.index')->withNotify($notify);
    }

    public function status($id)
    {
        $honor = Honor::findOrFail($id);
        $honor->is_active = !$honor->is_active;
        $honor->save();
        
        $notify[] = ['success', 'Honor status updated successfully'];
        return redirect()->route('admin.honors.index')->withNotify($notify);
    }
}

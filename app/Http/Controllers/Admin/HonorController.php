<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Honor;
use App\Models\HonorImage;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HonorController extends Controller
{
    public function index()
    {
        $pageTitle = 'Quản lý Vinh Danh';
        $honors = Honor::latest()->paginate(getPaginate());
        return view('admin.honors.index', compact('pageTitle', 'honors'));
    }

    public function create()
    {
        $pageTitle = 'Thêm Vinh Danh Mới';
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
            'images.*' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'captions.*' => 'nullable|string|max:255',
        ]);

        $honor = new Honor();
        $honor->title = $request->title;
        $honor->description = $request->description;
        $honor->start_date = $request->start_date;
        $honor->end_date = $request->end_date;
        $honor->is_active = $request->is_active;

        // Upload Main Image
        if ($request->hasFile('image')) {
            $path = getFilePath('honors');
            try {
                $filename = fileUploader($request->image, $path);
                $honor->image = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Không thể tải lên hình ảnh chính.'];
                return back()->withNotify($notify);
            }
        }

        $honor->save();
        
        // Upload Additional Images
        if ($request->hasFile('images')) {
            $path = getFilePath('honor_images');
            
            foreach ($request->file('images') as $key => $image) {
                try {
                    $filename = fileUploader($image, $path);
                    
                    $honorImage = new HonorImage();
                    $honorImage->honor_id = $honor->id;
                    $honorImage->image = $filename;
                    $honorImage->caption = $request->captions[$key] ?? null;
                    $honorImage->sort_order = $key;
                    $honorImage->is_featured = $key === 0; // First image is featured
                    $honorImage->save();
                } catch (\Exception $exp) {
                    // Continue if one image fails
                    continue;
                }
            }
        }
        
        $notify[] = ['success', 'Vinh danh đã được tạo thành công'];
        return redirect()->route('admin.honors.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Chỉnh Sửa Vinh Danh';
        $honor = Honor::with('images')->findOrFail($id);
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
            'images.*' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'captions.*' => 'nullable|string|max:255',
            'existing_captions.*' => 'nullable|string|max:255',
        ]);

        $honor = Honor::findOrFail($id);
        $honor->title = $request->title;
        $honor->description = $request->description;
        $honor->start_date = $request->start_date;
        $honor->end_date = $request->end_date;
        $honor->is_active = $request->is_active;

        // Upload Main Image
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
                $notify[] = ['error', 'Không thể tải lên hình ảnh chính.'];
                return back()->withNotify($notify);
            }
        }

        $honor->save();
        
        // Update existing image captions
        if ($request->has('existing_captions') && is_array($request->existing_captions)) {
            foreach ($request->existing_captions as $imageId => $caption) {
                $honorImage = HonorImage::find($imageId);
                if ($honorImage && $honorImage->honor_id == $honor->id) {
                    $honorImage->caption = $caption;
                    $honorImage->save();
                }
            }
        }
        
        // Handle featured image selection
        if ($request->has('featured_image')) {
            $featuredImageId = $request->featured_image;
            HonorImage::where('honor_id', $honor->id)->update(['is_featured' => false]);
            
            $featuredImage = HonorImage::find($featuredImageId);
            if ($featuredImage && $featuredImage->honor_id == $honor->id) {
                $featuredImage->is_featured = true;
                $featuredImage->save();
            }
        }
        
        // Delete images
        if ($request->has('delete_images') && is_array($request->delete_images)) {
            foreach ($request->delete_images as $imageId) {
                $honorImage = HonorImage::find($imageId);
                if ($honorImage && $honorImage->honor_id == $honor->id) {
                    $path = getFilePath('honor_images');
                    if ($honorImage->image && file_exists($path . '/' . $honorImage->image)) {
                        fileManager()->removeFile($path . '/' . $honorImage->image);
                    }
                    $honorImage->delete();
                }
            }
        }
        
        // Upload Additional Images
        if ($request->hasFile('images')) {
            $path = getFilePath('honor_images');
            $existingCount = $honor->images->count();
            
            foreach ($request->file('images') as $key => $image) {
                try {
                    $filename = fileUploader($image, $path);
                    
                    $honorImage = new HonorImage();
                    $honorImage->honor_id = $honor->id;
                    $honorImage->image = $filename;
                    $honorImage->caption = $request->captions[$key] ?? null;
                    $honorImage->sort_order = $existingCount + $key;
                    $honorImage->is_featured = false; // Not featured by default
                    $honorImage->save();
                } catch (\Exception $exp) {
                    // Continue if one image fails
                    continue;
                }
            }
        }
        
        $notify[] = ['success', 'Vinh danh đã được cập nhật thành công'];
        return redirect()->route('admin.honors.index')->withNotify($notify);
    }

    public function destroy($id)
    {
        $honor = Honor::findOrFail($id);
        $path = getFilePath('honors');
        
        // Remove main image
        if ($honor->image && file_exists($path . '/' . $honor->image)) {
            fileManager()->removeFile($path . '/' . $honor->image);
        }
        
        // Remove additional images
        $imagePath = getFilePath('honor_images');
        foreach ($honor->images as $image) {
            if ($image->image && file_exists($imagePath . '/' . $image->image)) {
                fileManager()->removeFile($imagePath . '/' . $image->image);
            }
        }
        
        $honor->delete();
        
        $notify[] = ['success', 'Vinh danh đã được xóa thành công'];
        return redirect()->route('admin.honors.index')->withNotify($notify);
    }

    public function status($id)
    {
        $honor = Honor::findOrFail($id);
        $honor->is_active = !$honor->is_active;
        $honor->save();
        
        $notify[] = ['success', 'Trạng thái vinh danh đã được cập nhật thành công'];
        return redirect()->route('admin.honors.index')->withNotify($notify);
    }
    
    public function reorderImages(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'required|integer|exists:honor_images,id',
        ]);
        
        foreach ($request->orders as $index => $imageId) {
            $honorImage = HonorImage::find($imageId);
            if ($honorImage) {
                $honorImage->sort_order = $index;
                $honorImage->save();
            }
        }
        
        return response()->json(['success' => true]);
    }
}

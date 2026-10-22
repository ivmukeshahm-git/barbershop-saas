<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        return view('admin.gallery.index');
    }

    public function getTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = Gallery::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset('storage/' . $row->image) . '" width="60" height="60">';
                })
                ->addColumn('action', function ($row) {
                    $action = '';
                    if (auth()->user()->can('Gallery-Delete')) {
                    $action = '<button class="btn btn-sm btn-danger delete-gallery-btn">Delete</button>';
                    }
                    return $action;
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gallery_images' => ['required', 'array', 'min:1', 'max:20'],
            'gallery_images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        foreach ($validated['gallery_images'] as $image) {
            $path = $image->store('gallery', 'public');
            if ($path === false) {
                throw new \RuntimeException('Unable to store a gallery image.');
            }

            Gallery::create(['image' => $path]);
        }

        $count = count($validated['gallery_images']);

        return response()->json([
            'status' => 1,
            'message' => $count === 1 ? 'Image added successfully' : "{$count} images added successfully",
        ]);
    }

    public function update(Request $request, $shop, $id)
    {
        $gallery = Gallery::findOrFail($id);

        if ($request->hasFile('gallery_image')) {
            Storage::disk('public')->delete($gallery->image);
            $path = $request->file('gallery_image')->store('gallery', 'public');
            $gallery->image = $path;
        }

        $gallery->description = $request->description;
        $gallery->save();

        return response()->json(['status' => 1, 'message' => 'Image updated successfully']);
    }

    public function destroy($shop, $id)
    {
        $gallery = Gallery::findOrFail($id);
        Storage::disk('public')->delete($gallery->image);
        $gallery->delete();

        return response()->json(['status' => 1, 'message' => 'Image deleted successfully']);
    }
}

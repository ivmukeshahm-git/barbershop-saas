<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TestimonialController extends Controller
{
    public function index()
    {
        return view('admin.testimonial.index');
    }

    public function getTableData(Request $request)
    {
        if ($request->ajax()) {
            $data = Testimonial::query()->orderBy('sort_order')->orderByDesc('id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    return '<img src="' . e($row->image_url) . '" width="48" height="48" class="rounded-circle" style="object-fit:cover;">';
                })
                ->addColumn('review_short', function ($row) {
                    return e(\Illuminate\Support\Str::limit($row->review, 80));
                })
                ->addColumn('rating_stars', function ($row) {
                    $rating = max(1, min(5, (int) $row->rating));
                    return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->status == 1
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Hidden</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->can('Testimonial-Edit')) {
                        $actions .= '<button type="button" class="btn btn-sm btn-icon btn-warning edit-testimonial-btn"><span class="tf-icons bx bx-edit-alt bx-18px"></span></button> ';
                    }
                    if (auth()->user()->can('Testimonial-Delete')) {
                        $actions .= '<button type="button" class="btn btn-sm btn-icon btn-danger delete-testimonial-btn"><span class="tf-icons bx bx-trash bx-18px"></span></button>';
                    }
                    return $actions;
                })
                ->rawColumns(['image', 'status_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_role' => 'nullable|string|max:255',
            'review' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'client_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $item = new Testimonial();
        $item->created_by = Auth::id();
        $item->client_name = $validated['client_name'];
        $item->client_role = $validated['client_role'] ?? 'Regular Client';
        $item->review = $validated['review'];
        $item->rating = $validated['rating'];
        $item->sort_order = $validated['sort_order'] ?? 0;
        $item->status = $request->boolean('status') ? 1 : 0;

        if ($request->hasFile('client_image')) {
            $item->client_image = $request->file('client_image')->store('testimonials', 'public');
        }

        $item->save();

        return response()->json(['status' => 1, 'message' => 'Testimonial added successfully.']);
    }

    public function update(Request $request, $shop, $id)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_role' => 'nullable|string|max:255',
            'review' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'client_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $item = Testimonial::findOrFail($id);
        $item->client_name = $validated['client_name'];
        $item->client_role = $validated['client_role'] ?? 'Regular Client';
        $item->review = $validated['review'];
        $item->rating = $validated['rating'];
        $item->sort_order = $validated['sort_order'] ?? 0;
        $item->status = $request->boolean('status') ? 1 : 0;

        if ($request->hasFile('client_image')) {
            if (!empty($item->client_image) && !str_starts_with($item->client_image, 'frontend/') && Storage::disk('public')->exists($item->client_image)) {
                Storage::disk('public')->delete($item->client_image);
            }
            $item->client_image = $request->file('client_image')->store('testimonials', 'public');
        }

        $item->save();

        return response()->json(['status' => 1, 'message' => 'Testimonial updated successfully.']);
    }

    public function destroy($shop, $id)
    {
        $item = Testimonial::findOrFail($id);
        if (!empty($item->client_image) && !str_starts_with($item->client_image, 'frontend/') && Storage::disk('public')->exists($item->client_image)) {
            Storage::disk('public')->delete($item->client_image);
        }
        $item->delete();

        return response()->json(['status' => 1, 'message' => 'Testimonial deleted successfully.']);
    }
}

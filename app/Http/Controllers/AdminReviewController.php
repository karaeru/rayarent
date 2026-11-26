<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

/** Admin melihat semua ulasan dan bisa menyembunyikan ulasan yang tidak pantas dari halaman publik. */
class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('tampil');
        $reviews = Review::with('user', 'vehicle', 'order')
            ->when($filter === '1', fn ($q) => $q->where('is_visible', true))
            ->when($filter === '0', fn ($q) => $q->where('is_visible', false))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.reviews', [
            'reviews' => $reviews,
            'ringkas' => Review::ringkasan(),
            'total' => Review::count(),
            'tersembunyi' => Review::where('is_visible', false)->count(),
            'filter' => $filter,
        ]);
    }

    public function toggle(Review $review)
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        return back()->with('ok', $review->is_visible ? 'Ulasan ditampilkan lagi.' : 'Ulasan disembunyikan dari halaman publik.');
    }
}

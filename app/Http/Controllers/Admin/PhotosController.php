<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
    public function index(Request $request)
    {
        $query = Photo::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where('user_uid', 'like', "%$q%");
        }

        $photos = $query->latest()->paginate(20)->withQueryString();

        $providerStats = Photo::selectRaw('provider, count(*) as count')
            ->groupBy('provider')->get();

        $statusStats = Photo::selectRaw('status, count(*) as count')
            ->groupBy('status')->get();

        return view('admin.photos.index', compact('photos', 'providerStats', 'statusStats'));
    }

    public function destroy(Photo $photo)
    {
        $photo->delete();
        return back()->with('success', 'Photo record deleted.');
    }

    public function retry(Photo $photo)
    {
        $photo->update(['status' => 'pending', 'error_message' => null]);
        return back()->with('success', 'Photo queued for retry.');
    }
}

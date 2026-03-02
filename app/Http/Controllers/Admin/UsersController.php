<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = AppUser::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('uid', 'like', "%$q%");
            });
        }

        if ($request->filled('subscription')) {
            $query->where('subscription', $request->subscription);
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(AppUser $user)
    {
        $user->load('photos');
        return view('admin.users.show', compact('user'));
    }

    public function ban(AppUser $user)
    {
        $user->update(['is_banned' => !$user->is_banned]);
        $action = $user->is_banned ? 'banned' : 'unbanned';
        return back()->with('success', "User has been {$action}.");
    }

    public function updateCredits(Request $request, AppUser $user)
    {
        $request->validate(['credits' => 'required|integer|min:0']);
        $user->update(['credits' => $request->credits]);
        return back()->with('success', 'Credits updated.');
    }

    public function updateSubscription(Request $request, AppUser $user)
    {
        $request->validate(['subscription' => 'required|in:free,pro,premium']);
        $user->update(['subscription' => $request->subscription]);
        return back()->with('success', 'Subscription updated.');
    }

    public function destroy(AppUser $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}

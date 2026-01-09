<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Filiere;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Store search term in session for pagination
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            session(['user_search' => $search]);
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        } elseif ($request->has('page') && session('user_search')) {
            // If paginating, use stored search term
            $search = session('user_search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        } else {
            // Clear search session if no search
            session()->forget('user_search');
        }
        
        // Filter by type if specified
        if ($request->has('type') && $request->type) {
            session(['user_type_filter' => $request->type]);
            $query->where('type', $request->type);
        } elseif ($request->has('page') && session('user_type_filter')) {
            // If paginating, use stored type filter
            $query->where('type', session('user_type_filter'));
        } else {
            // Clear type filter session if not specified
            session()->forget('user_type_filter');
        }
        
        // Clear filters if requested
        if ($request->has('clear_filters')) {
            session()->forget(['user_search', 'user_type_filter']);
            return redirect()->route('users.index');
        }
        
        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        
        // Statistics (always calculate from all users, not filtered)
        $totalUsers = User::count();
        $studentCount = User::where('type', 'student')->count();
        $adminCount = User::where('type', 'admin')->count();
        $teacherCount = User::where('type', 'teacher')->count();
        
        // Pass search and filter values to view
        $currentSearch = session('user_search', '');
        $currentType = session('user_type_filter', '');
        
        return view('users.index', compact(
            'users', 
            'totalUsers',
            'studentCount',
            'adminCount',
            'teacherCount',
            'currentSearch',
            'currentType'
        ));
    }

    public function create()
    {
        // Get all filieres for student enrollment
        $filieres = Filiere::where('status', true)->orderBy('name')->get();
        return view('users.form', compact('filieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'type' => 'required|string|in:admin,teacher,student',
            'sex' => 'nullable|string|in:male,female',
            'profile_photo_path' => 'nullable|image|max:2048',
            'filieres' => 'nullable|array',
            'filieres.*' => 'exists:filieres,id',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo_path')) {
            $validated['profile_photo_path'] = $request->file('profile_photo_path')->store('profile-photos', 'public');
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Create user
        $user = User::create($validated);

        // Sync filieres if user is a student
        if ($request->type === 'student' && isset($validated['filieres'])) {
            $user->filieres()->sync($validated['filieres']);
        }

        return redirect()->route('users.show', $user->id)
            ->with('success', 'User created successfully!');
    }

    public function show($id)
    {
        $user = User::with(['filieres'])->findOrFail($id);
        
        // Load additional data based on user type
        if ($user->type === 'student') {
            $user->load(['filieres.students']);
        }
        
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with(['filieres'])->findOrFail($id);
        $filieres = Filiere::where('status', true)->orderBy('name')->get();
        
        return view('users.form', compact('user', 'filieres'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'type' => 'required|string|in:admin,teacher,student',
            'sex' => 'nullable|string|in:male,female',
            'profile_photo_path' => 'nullable|image|max:2048',
            'filieres' => 'nullable|array',
            'filieres.*' => 'exists:filieres,id',
        ]);

        // Handle password update if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo_path')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                \Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = $request->file('profile_photo_path')->store('profile-photos', 'public');
        }

        $user->update($validated);

        // Sync filieres if user is a student
        if ($request->type === 'student') {
            $user->filieres()->sync($request->filieres ?? []);
        } else {
            // Remove all filiere associations if not a student
            $user->filieres()->detach();
        }

        return redirect()->route('users.show', $user->id)
            ->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete profile photo if exists
        if ($user->profile_photo_path) {
            \Storage::disk('public')->delete($user->profile_photo_path);
        }
        
        // Detach from filieres
        $user->filieres()->detach();
        
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }
}
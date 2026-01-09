<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Filiere;
use App\Models\User;

class FiliereController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->type === 'student') {
            // Student view: only show enrolled filieres
            $filieres = $user->filieres()->withCount('students')->get();
            $enrolledCount = $filieres->count();
            
            return view('filieres.index', compact('filieres', 'enrolledCount'));
        } else {
            // Admin/Teacher view: show all filieres with search
            $query = Filiere::query()->withCount('students');
            
            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('students', function($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
                });
            }
            
            $filieres = $query->latest()->paginate(10);
            
            // Statistics
            $totalFilieres = Filiere::count();
            $activeFilieres = Filiere::where('status', true)->count();
            
            // Calculate total students across all filieres
            $totalStudents = User::where('type', 'student')->count();
            
            // Calculate average students per filiere
            $averageStudents = $totalFilieres > 0 ? 
                number_format($totalStudents / $totalFilieres, 1) : '0';
            
            return view('filieres.index', compact(
                'filieres', 
                'totalFilieres',
                'activeFilieres',
                'totalStudents',
                'averageStudents'
            ));
        }
    }

    public function create()
    {
        $students = User::where('type', 'student')->orderBy('name')->get();
        return view('filieres.form', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'total_courses' => 'required|string|max:255',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $filiere = Filiere::create($validated);

        // Directly sync user IDs (no conversion needed)
        if (isset($validated['students'])) {
            $filiere->students()->sync($validated['students']);
        }

        return redirect()->route('filieres.show', $filiere->id)
            ->with('success', 'Filiere created successfully!');
    }

    public function show($id)
    {
        $filiere = Filiere::with(['students'])->findOrFail($id);
        return view('filieres.show', compact('filiere'));
    }

    public function edit($id)
    {
        $filiere = Filiere::with(['students'])->findOrFail($id);
        $students = User::where('type', 'student')->orderBy('name')->get();
        
        return view('filieres.form', compact('filiere', 'students'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'total_courses' => 'required|string|max:255',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $filiere = Filiere::findOrFail($id);
        $filiere->update($validated);

        // Directly sync user IDs
        $filiere->students()->sync($validated['students'] ?? []);

        return redirect()->route('filieres.show', $filiere->id)
            ->with('success', 'Filiere updated successfully!');
    }

    public function destroy($id)
    {
        $filiere = Filiere::findOrFail($id);
        $filiere->students()->detach();
        $filiere->delete();

        return redirect()->route('filieres.index')
            ->with('success', 'Filiere deleted successfully!');
    }
}
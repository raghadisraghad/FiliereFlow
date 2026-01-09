@extends('layouts.app')

@section('content')
<div class="filieres-index-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-book me-2"></i>
            @if(auth()->user()->type === 'student')
                My Academic Programs
            @else
                Academic Programs
            @endif
        </h1>
        
        @if(auth()->user()->type === 'student')
            <p class="page-subtitle">Programs you are enrolled in</p>
        @else
            <p class="page-subtitle">Manage all academic programs and their details</p>
        @endif
    </div>

    <!-- Search Bar (Admin/Teacher only) -->
    @if(auth()->user()->type !== 'student')
    <div class="search-section">
        <form action="{{ route('filieres.index') }}" method="GET" class="search-form">
            <div class="search-input-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Search filieres by name or enrolled students count..." 
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('filieres.index') }}" class="clear-search">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
            <button type="submit" class="search-btn">
                Search
            </button>
        </form>
        
        @if(request('search'))
            <div class="search-results-info">
                <p>Search results for: "<strong>{{ request('search') }}</strong>"</p>
                <a href="{{ route('filieres.index') }}" class="clear-all-search">
                    <i class="fas fa-times"></i> Clear search
                </a>
            </div>
        @endif
    </div>
    @endif

    <!-- Stats Overview (Admin/Teacher only) -->
    @if(auth()->user()->type !== 'student' && $filieres->isNotEmpty())
    <div class="stats-overview">
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-content">
                <h4>Total Filieres</h4>
                <p class="stat-number">{{ $totalFilieres }}</p>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <h4>Active Programs</h4>
                <p class="stat-number">{{ $activeFilieres }}</p>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h4>Total Students</h4>
                <p class="stat-number">{{ $totalStudents }}</p>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h4>Avg. Students/Filiere</h4>
                <p class="stat-number">{{ $averageStudents }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Header Actions (Admin only) -->
    @if(auth()->user()->type === 'admin')
    <div class="header-actions">
        <a href="{{ route('filieres.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Create New Filiere
        </a>
    </div>
    @endif

    <!-- Student Enrollment Info -->
    @if(auth()->user()->type === 'student')
    <div class="student-enrollment-info">
        <div class="enrollment-card">
            <div class="enrollment-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="enrollment-content">
                <h3>My Enrollment Status</h3>
                <p>You are enrolled in <strong>{{ $enrolledCount }}</strong> academic program(s).</p>
                <p class="text-sm text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Contact your administrator to enroll in additional programs.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Filieres Table/Cards -->
    @if($filieres && $filieres->isNotEmpty())
        @if(auth()->user()->type === 'student')
            <!-- Student View - Card Layout -->
            <div class="student-filieres-grid">
                @foreach($filieres as $filiere)
                <div class="filiere-card">
                    <div class="filiere-card-header">
                        <div class="filiere-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="filiere-title">
                            <h3>{{ $filiere->name }}</h3>
                            <span class="filiere-id">#{{ str_pad($filiere->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="filiere-status">
                            <span class="status-badge {{ $filiere->status ? 'active' : 'inactive' }}">
                                {{ $filiere->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="filiere-card-body">
                        <div class="filiere-info">
                            <div class="info-item">
                                <i class="fas fa-book-open"></i>
                                <span>Courses: {{ $filiere->total_courses }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-user-graduate"></i>
                                <span>Total Students: {{ $filiere->students_count }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Created: {{ $filiere->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        
                        <div class="filiere-actions">
                            <a href="{{ route('filieres.show', $filiere->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
        @else
            <!-- Admin/Teacher View - Table Layout -->
            <div class="filieres-table-container">
                <table class="filieres-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Total Courses</th>
                            <th>Enrolled Students</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filieres as $filiere)
                        <tr>
                            <td>
                                <span class="filiere-id">#{{ str_pad($filiere->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="filiere-name">
                                    <i class="fas fa-book me-2"></i>
                                    {{ $filiere->name }}
                                </div>
                            </td>
                            <td>
                                <span class="status-badge {{ $filiere->status ? 'active' : 'inactive' }}">
                                    {{ $filiere->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $filiere->total_courses ?? '0' }}</td>
                            <td>
                                <div class="students-count">
                                    <i class="fas fa-user-graduate me-1"></i>
                                    {{ $filiere->students_count ?? '0' }}
                                </div>
                            </td>
                            <td>{{ $filiere->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="table-actions">
                                    <!-- Details Button -->
                                    <a href="{{ route('filieres.show', $filiere->id) }}" class="btn-icon info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Edit & Delete Buttons (Only for admin) -->
                                    @if(auth()->user()->type === 'admin')
                                    <a href="{{ route('filieres.edit', $filiere->id) }}" class="btn-icon primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('filieres.destroy', $filiere->id) }}" method="POST" 
                                          class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this filiere?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($filieres->hasPages())
            <div class="pagination-container">
                {{ $filieres->appends(request()->query())->links() }}
            </div>
            @endif
        @endif
        
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-book fa-2x"></i>
            </div>
            <h2>
                @if(auth()->user()->type === 'student')
                    No Enrollments Found
                @elseif(request('search'))
                    No Matching Filieres Found
                @else
                    No Filieres Found
                @endif
            </h2>
            <p class="empty-message">
                @if(auth()->user()->type === 'student')
                    You are not enrolled in any academic programs yet.
                    Contact your administrator to get enrolled.
                @elseif(request('search'))
                    No filieres match your search criteria. Try a different search term.
                @else
                    There are no academic programs created yet.
                    @if(auth()->user()->type === 'admin')
                    Start by creating your first filiere.
                    @endif
                @endif
            </p>
            @if(auth()->user()->type === 'admin' && !request('search'))
            <div class="empty-actions">
                <a href="{{ route('filieres.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Create First Filiere
                </a>
            </div>
            @endif
        </div>
    @endif
</div>
@endsection
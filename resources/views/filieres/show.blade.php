@extends('layouts.app')

@section('content')
<div class="filiere-details-container">
    <!-- Header with Action Buttons -->
    <div class="detail-header">
        <div class="header-top">
            <a href="{{ route('filieres.index') }}" class="back-button">
                <i class="fas fa-arrow-left me-2"></i>
                Back to All Filieres
            </a>
            
            <div class="detail-actions">
                <a href="{{ route('filieres.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list me-2"></i>
                    View All Filieres
                </a>
                
                @if(auth()->user()->type === 'admin')
                <a href="{{ route('filieres.edit', $filiere->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>
                    Edit Filiere
                </a>
                
                <form action="{{ route('filieres.destroy', $filiere->id) }}" method="POST" 
                      class="delete-form" 
                      onsubmit="return confirm('Are you sure you want to delete this filiere? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Filiere
                    </button>
                </form>
                
                <a href="{{ route('filieres.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>
                    Create New Filiere
                </a>
                @endif
            </div>
        </div>
        
        @if($filiere)
            <h1 class="detail-title">Filiere Details</h1>
            <p class="detail-subtitle">Complete information about this academic program</p>
        @else
            <h1 class="detail-title">Filiere Not Found</h1>
            <p class="detail-subtitle">The requested filiere could not be found</p>
        @endif
    </div>

    @if($filiere)
        <!-- Main Details Card -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="filiere-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="filiere-title">
                    <h2>{{ $filiere->name }}</h2>
                    <span class="filiere-id">ID: #{{ str_pad($filiere->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <div class="detail-content">
                <!-- Basic Information -->
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Basic Information
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-hashtag"></i>
                                Filiere ID
                            </div>
                            <div class="info-value">#{{ str_pad($filiere->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-font"></i>
                                Name
                            </div>
                            <div class="info-value">{{ $filiere->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="info-value">
                                <span class="status-badge {{ $filiere->status ? 'active' : 'inactive' }}">
                                    {{ $filiere->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-book-open"></i>
                                Total Courses
                            </div>
                            <div class="info-value">{{ $filiere->total_courses ?? '0' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar-alt"></i>
                                Created
                            </div>
                            <div class="info-value">{{ $filiere->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-history"></i>
                                Last Updated
                            </div>
                            <div class="info-value">{{ $filiere->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Statistics
                    </h3>
                    <div class="stats-grid">
                        <!-- Students Count -->
                        @php
                            $studentsCount = $filiere->students->count();
                        @endphp
                        <div class="stat-card mini {{ $studentsCount == 0 ? 'empty' : '' }}">
                            <div class="stat-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-content">
                                <h4>Enrolled Students</h4>
                                <p class="stat-number">{{ $studentsCount }}</p>
                                @if($studentsCount == 0)
                                    <p class="stat-hint">No students enrolled yet</p>
                                @endif
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="stat-card mini">
                            <div class="stat-icon">
                                <i class="fas fa-toggle-on"></i>
                            </div>
                            <div class="stat-content">
                                <h4>Status</h4>
                                <p class="stat-number {{ $filiere->status ? 'active' : 'inactive' }}">
                                    {{ $filiere->status ? 'Active' : 'Inactive' }}
                                </p>
                            </div>
                        </div>

                        <!-- Courses Card -->
                        <div class="stat-card mini {{ ($filiere->total_courses ?? 0) == 0 ? 'empty' : '' }}">
                            <div class="stat-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="stat-content">
                                <h4>Total Courses</h4>
                                <p class="stat-number">{{ $filiere->total_courses ?? '0' }}</p>
                                @if(($filiere->total_courses ?? 0) == 0)
                                    <p class="stat-hint">No courses assigned</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrolled Students Section -->
                <div class="detail-section">
                    <div class="students-section-header">
                        <h3 class="section-title">
                            <i class="fas fa-user-graduate me-2"></i>
                            Enrolled Students
                            <span class="students-count-badge">{{ $filiere->students->count() }}</span>
                        </h3>
                        
                        @if($filiere->students->isNotEmpty())
                        <div class="students-search-container">
                            <div class="students-search-input-group">
                                <i class="fas fa-search students-search-icon"></i>
                                <input type="text" 
                                       id="studentsTableSearch" 
                                       class="students-search-input" 
                                       placeholder="Search students..." 
                                       autocomplete="off">
                                <button type="button" class="clear-table-search-btn" id="clearTableSearchBtn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($filiere->students->isNotEmpty())
                    <div class="students-table-container">
                        <div class="table-responsive-wrapper">
                            <table class="students-table" id="studentsTable">
                                <thead>
                                    <tr>
                                        <th class="avatar-column">Profile</th>
                                        <th class="id-column">ID</th>
                                        <th class="name-column">Name</th>
                                        <th class="email-column">Email</th>
                                        <th class="gender-column">Gender</th>
                                        <th class="date-column">Enrollment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($filiere->students as $student)
                                    <tr class="student-row" 
                                        data-name="{{ strtolower($student->name . ' ' . ($student->last_name ?? '')) }}"
                                        data-email="{{ strtolower($student->email) }}"
                                        data-gender="{{ strtolower($student->sex ?? '') }}">
                                        <td class="avatar-column">
                                            <div class="student-avatar">
                                                @if($student->profile_photo_path)
                                                    <img src="{{ Storage::url($student->profile_photo_path) }}" 
                                                         alt="{{ $student->name }}" 
                                                         class="avatar-img">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        {{ substr($student->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="id-column">#{{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="name-column">
                                            <div class="student-fullname">
                                                <div class="first-name">{{ $student->name }}</div>
                                                @if($student->last_name)
                                                    <div class="last-name">{{ $student->last_name }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="email-column">{{ $student->email }}</td>
                                        <td class="gender-column">
                                            @if($student->sex)
                                                <span class="gender-badge {{ $student->sex === 'male' ? 'male' : 'female' }}">
                                                    {{ ucfirst($student->sex) }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                        <td class="date-column">
                                            @if(isset($student->pivot->created_at))
                                                {{ $student->pivot->created_at->format('M d, Y') }}
                                                <div class="text-xs text-muted">
                                                    {{ $student->pivot->created_at->format('h:i A') }}
                                                </div>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Table Search Results Info -->
                        <div class="table-search-results" id="tableSearchResults" style="display: none;">
                            <p id="searchResultsCount">Showing 0 of {{ $filiere->students->count() }} students</p>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearTableSearchResultsBtn">
                                <i class="fas fa-times me-1"></i>
                                Clear Search
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="empty-state-section">
                        <i class="fas fa-user-graduate fa-2x"></i>
                        <p>No students are enrolled in this filiere yet.</p>
                        @if(auth()->user()->type === 'admin')
                        <a href="{{ route('filieres.edit', $filiere->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Enroll Students
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-book fa-3x"></i>
            </div>
            <h2>Filiere Not Found</h2>
            <p class="empty-message">
                The filiere you're looking for doesn't exist or may have been deleted.
            </p>
            <div class="empty-actions">
                <a href="{{ route('filieres.index') }}" class="btn btn-primary">
                    <i class="fas fa-list me-2"></i>
                    View All Filieres
                </a>
                @if(auth()->user()->type === 'admin')
                <a href="{{ route('filieres.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>
                    Create New Filiere
                </a>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const studentRows = document.querySelectorAll('.student-row');
    const searchInput = document.getElementById('studentsTableSearch');
    const clearSearchBtn = document.getElementById('clearTableSearchBtn');
    const clearSearchResultsBtn = document.getElementById('clearTableSearchResultsBtn');
    const tableSearchResults = document.getElementById('tableSearchResults');
    const searchResultsCount = document.getElementById('searchResultsCount');
    const studentsTable = document.getElementById('studentsTable');
    const totalStudents = {{ $filiere->students->count() ?? 0 }};
    
    // Filter table rows based on search
    function filterTableRows(searchTerm) {
        searchTerm = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        
        studentRows.forEach(row => {
            const name = row.dataset.name;
            const email = row.dataset.email;
            const gender = row.dataset.gender;
            
            if (name.includes(searchTerm) || email.includes(searchTerm) || gender.includes(searchTerm) || searchTerm === '') {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update search results info
        if (searchTerm !== '') {
            searchResultsCount.textContent = `Showing ${visibleCount} of ${totalStudents} students`;
            tableSearchResults.style.display = 'flex';
        } else {
            tableSearchResults.style.display = 'none';
        }
        
        // Show/hide table header if no results
        const thead = studentsTable.querySelector('thead');
        if (visibleCount === 0 && searchTerm !== '') {
            thead.style.display = 'none';
        } else {
            thead.style.display = '';
        }
    }
    
    // Event Listeners
    
    // Table search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTableRows(this.value);
            clearSearchBtn.style.display = this.value ? 'flex' : 'none';
        });
        
        // Clear search
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterTableRows('');
            clearSearchBtn.style.display = 'none';
            searchInput.focus();
        });
    }
    
    // Clear search results button
    if (clearSearchResultsBtn) {
        clearSearchResultsBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                filterTableRows('');
                clearSearchBtn.style.display = 'none';
                searchInput.focus();
            }
        });
    }
    
    // Keyboard shortcuts for table search
    document.addEventListener('keydown', function(e) {
        // Focus search input on Ctrl/Cmd + F
        if ((e.ctrlKey || e.metaKey) && e.key === 'f' && searchInput) {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        
        // Escape to clear search
        if (e.key === 'Escape' && searchInput && searchInput.value) {
            e.preventDefault();
            searchInput.value = '';
            filterTableRows('');
            clearSearchBtn.style.display = 'none';
        }
    });
});
</script>
@endsection
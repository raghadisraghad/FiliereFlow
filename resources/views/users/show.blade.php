@extends('layouts.app')

@section('content')
<div class="user-details-container">
    <!-- Header with Action Buttons -->
    <div class="detail-header">
        <div class="header-top">
            <a href="{{ route('users.index') }}" class="back-button">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Users
            </a>
            
            <div class="detail-actions">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list me-2"></i>
                    View All Users
                </a>
                
                @if(auth()->user()->type === 'admin')
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>
                    Edit User
                </a>
                
                @if($user->id !== auth()->id())
                <form action="{{ route('users.destroy', $user->id) }}" method="POST" 
                      class="delete-form" 
                      onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete User
                    </button>
                </form>
                @endif
                
                <a href="{{ route('users.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>
                    Create New User
                </a>
                @endif
            </div>
        </div>
        
        <h1 class="detail-title">User Details</h1>
        <p class="detail-subtitle">Complete information about this user</p>
    </div>

    <!-- Main Details Card -->
    <div class="detail-card">
        <div class="detail-card-header">
            <div class="user-profile">
                @if($user->profile_photo_path)
                    <img src="{{ Storage::url($user->profile_photo_path) }}" 
                         alt="{{ $user->name }}" 
                         class="profile-img">
                @else
                    <div class="profile-placeholder">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="user-title">
                <h2>{{ $user->name }} {{ $user->last_name }}</h2>
                <div class="user-metadata">
                    <span class="type-badge type-{{ $user->type }}">
                        {{ ucfirst($user->type) }}
                    </span>
                    <span class="user-id">ID: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
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
                            User ID
                        </div>
                        <div class="info-value">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Full Name
                        </div>
                        <div class="info-value">{{ $user->name }} {{ $user->last_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user-tag"></i>
                            User Type
                        </div>
                        <div class="info-value">
                            <span class="type-badge type-{{ $user->type }}">
                                {{ ucfirst($user->type) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-venus-mars"></i>
                            Gender
                        </div>
                        <div class="info-value">
                            @if($user->sex)
                                <span class="gender-badge {{ $user->sex === 'male' ? 'male' : 'female' }}">
                                    {{ ucfirst($user->sex) }}
                                </span>
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar-alt"></i>
                            Created
                        </div>
                        <div class="info-value">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-history"></i>
                            Last Updated
                        </div>
                        <div class="info-value">{{ $user->updated_at->format('M d, Y') }}</div>
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
                    <div class="stat-card mini">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h4>Account Age</h4>
                            <p class="stat-number">{{ $user->created_at->diffInDays(now()) }} days</p>
                        </div>
                    </div>
                    
                    <div class="stat-card mini">
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <h4>Account Status</h4>
                            <p class="stat-number active">Active</p>
                        </div>
                    </div>
                    
                    @if($user->type === 'student')
                    <div class="stat-card mini">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-content">
                            <h4>Enrolled Filieres</h4>
                            <p class="stat-number">{{ $user->filieres->count() }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Enrolled Filieres Section (for students only) -->
            @if($user->type === 'student' && $user->filieres->isNotEmpty())
            <div class="detail-section">
                <div class="students-section-header">
                    <h3 class="section-title">
                        <i class="fas fa-book me-2"></i>
                        Enrolled Academic Programs
                        <span class="students-count-badge">{{ $user->filieres->count() }}</span>
                    </h3>
                    
                    <div class="students-search-container">
                        <div class="students-search-input-group">
                            <i class="fas fa-search students-search-icon"></i>
                            <input type="text" 
                                   id="filieresTableSearch" 
                                   class="students-search-input" 
                                   placeholder="Search filieres..." 
                                   autocomplete="off">
                            <button type="button" class="clear-table-search-btn" id="clearTableSearchBtn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="students-table-container">
                    <div class="table-responsive-wrapper">
                        <table class="students-table" id="filieresTable">
                            <thead>
                                <tr>
                                    <th class="icon-column">Icon</th>
                                    <th class="id-column">ID</th>
                                    <th class="name-column">Name</th>
                                    <th class="status-column">Status</th>
                                    <th class="courses-column">Total Courses</th>
                                    <th class="students-column">Total Students</th>
                                    <th class="date-column">Enrollment Date</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->filieres as $filiere)
                                <tr class="filiere-row" 
                                    data-name="{{ strtolower($filiere->name) }}">
                                    <td class="icon-column">
                                        <div class="filiere-icon-small">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    </td>
                                    <td class="id-column">#{{ str_pad($filiere->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="name-column">{{ $filiere->name }}</td>
                                    <td class="status-column">
                                        <span class="status-badge {{ $filiere->status ? 'active' : 'inactive' }}">
                                            {{ $filiere->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="courses-column">{{ $filiere->total_courses ?? '0' }}</td>
                                    <td class="students-column">{{ $filiere->students->count() ?? '0' }}</td>
                                    <td class="date-column">
                                        @if(isset($filiere->pivot->created_at))
                                            {{ $filiere->pivot->created_at->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="actions-column">
                                        <div class="table-actions">
                                            <a href="{{ route('filieres.show', $filiere->id) }}" class="btn-icon info" title="View Filiere">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Table Search Results Info -->
                    <div class="table-search-results" id="tableSearchResults" style="display: none;">
                        <p id="searchResultsCount">Showing 0 of {{ $user->filieres->count() }} filieres</p>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearTableSearchResultsBtn">
                            <i class="fas fa-times me-1"></i>
                            Clear Search
                        </button>
                    </div>
                </div>
            </div>
            @elseif($user->type === 'student')
            <div class="detail-section">
                <h3 class="section-title">
                    <i class="fas fa-book me-2"></i>
                    Enrolled Academic Programs
                </h3>
                <div class="empty-state-section">
                    <i class="fas fa-book fa-2x"></i>
                    <p>This student is not enrolled in any academic programs yet.</p>
                    @if(auth()->user()->type === 'admin')
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        Enroll in Programs
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($user->type === 'student' && $user->filieres->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const filiereRows = document.querySelectorAll('.filiere-row');
    const searchInput = document.getElementById('filieresTableSearch');
    const clearSearchBtn = document.getElementById('clearTableSearchBtn');
    const clearSearchResultsBtn = document.getElementById('clearTableSearchResultsBtn');
    const tableSearchResults = document.getElementById('tableSearchResults');
    const searchResultsCount = document.getElementById('searchResultsCount');
    const filieresTable = document.getElementById('filieresTable');
    const totalFilieres = {{ $user->filieres->count() }};
    
    // Filter table rows based on search
    function filterTableRows(searchTerm) {
        searchTerm = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        
        filiereRows.forEach(row => {
            const name = row.dataset.name;
            
            if (name.includes(searchTerm) || searchTerm === '') {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update search results info
        if (searchTerm !== '') {
            searchResultsCount.textContent = `Showing ${visibleCount} of ${totalFilieres} filieres`;
            tableSearchResults.style.display = 'flex';
        } else {
            tableSearchResults.style.display = 'none';
        }
        
        // Show/hide table header if no results
        const thead = filieresTable.querySelector('thead');
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
});
</script>
@endif
@endsection
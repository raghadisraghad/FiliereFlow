@extends('layouts.app')

@section('content')
<div class="users-index-container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users me-2"></i>
            User Management
        </h1>
        <p class="page-subtitle">Manage all users in the system</p>
    </div>

    <!-- Search and Filter Section -->
    <div class="search-filter-section">
        <form id="searchForm" action="{{ route('users.index') }}" method="GET">
            <div class="search-container">
                <div class="search-input-group">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           name="search" 
                           id="userSearch" 
                           class="search-input" 
                           placeholder="Search users by name, email, or last name..." 
                           value="{{ $currentSearch }}"
                           autocomplete="off">
                    @if($currentSearch)
                        <a href="{{ route('users.index', ['clear_filters' => true]) }}" class="clear-search">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="filter-container">
                <select name="type" id="typeFilter" class="type-filter">
                    <option value="">All Types</option>
                    <option value="admin" {{ $currentType == 'admin' ? 'selected' : '' }}>Administrators</option>
                    <option value="student" {{ $currentType == 'student' ? 'selected' : '' }}>Students</option>
                </select>
            </div>
            
            <button type="submit" class="search-btn">
                Search
            </button>
        </form>
        
        <!-- Search Results Info -->
        @if($currentSearch || $currentType)
            <div class="search-results-info">
                <p>
                    @if($currentSearch && $currentType)
                        Search results for: "<strong>{{ $currentSearch }}</strong>" and type: "<strong>{{ ucfirst($currentType) }}</strong>"
                    @elseif($currentSearch)
                        Search results for: "<strong>{{ $currentSearch }}</strong>"
                    @elseif($currentType)
                        Showing users of type: "<strong>{{ ucfirst($currentType) }}</strong>"
                    @endif
                    (Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results)
                </p>
                <a href="{{ route('users.index', ['clear_filters' => true]) }}" class="clear-all-search">
                    <i class="fas fa-times me-1"></i> Clear Filters
                </a>
            </div>
        @endif
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h4>Total Users</h4>
                <p class="stat-number">{{ $totalUsers }}</p>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <h4>Students</h4>
                <p class="stat-number">{{ $studentCount }}</p>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
                <h4>Administrators</h4>
                <p class="stat-number">{{ $adminCount }}</p>
            </div>
        </div>
    </div>

    <!-- Header Actions -->
    @if(auth()->user()->type === 'admin')
    <div class="header-actions">
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Create New User
        </a>
    </div>
    @endif

    <!-- Users Table -->
@if($users->isNotEmpty())
<div class="users-table-container">
    <div class="table-responsive-wrapper">
        <table class="users-table" id="usersTable">
            <thead>
                <tr>
                    <th class="avatar-column">Profile</th>
                    <th class="name-column">Name</th>
                    <th class="email-column">Email</th>
                    <th class="type-column">Type</th>
                    <th class="gender-column">Gender</th>
                    <th class="status-column">Status</th>
                    <th class="date-column">Created</th>
                    <th class="actions-column">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="user-row">
                    <td class="avatar-column">
                        <div class="user-avatar">
                            @if($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     alt="{{ $user->name }}" 
                                     class="avatar-img">
                            @else
                                <div class="avatar-placeholder">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="name-column">
                        <div class="user-fullname">
                            <div class="first-name">{{ $user->name }}</div>
                            @if($user->last_name)
                                <div class="last-name">{{ $user->last_name }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="email-column">{{ $user->email }}</td>
                    <td class="type-column">
                        <span class="type-badge type-{{ $user->type }}">
                            {{ ucfirst($user->type) }}
                        </span>
                    </td>
                    <td class="gender-column">
                        @if($user->sex)
                            <span class="gender-badge {{ $user->sex === 'male' ? 'male' : 'female' }}">
                                {{ ucfirst($user->sex) }}
                            </span>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </td>
                    <td class="status-column">
                        <span class="status-badge active">
                            Active
                        </span>
                    </td>
                    <td class="date-column">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="actions-column">
                        <div class="table-actions">
                            <!-- View Button -->
                            <a href="{{ route('users.show', $user->id) }}" class="btn-icon info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <!-- Edit Button (Admin only) -->
                            @if(auth()->user()->type === 'admin')
                            <a href="{{ route('users.edit', $user->id) }}" class="btn-icon primary" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <!-- Delete Button (Admin only, can't delete self) -->
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" 
                                  class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon danger" title="Delete User"
                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Stylish Pagination -->
    @if($users->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-container">
            <div class="pagination-info">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
            
            <nav class="pagination-nav">
                <ul class="pagination-list">
                    {{-- Previous Page Link --}}
                    @if($users->onFirstPage())
                        <li class="pagination-item disabled">
                            <span class="pagination-link prev-link">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="pagination-item">
                            <a href="{{ $users->previousPageUrl() }}" class="pagination-link prev-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                        @if($page == $users->currentPage())
                            <li class="pagination-item active">
                                <span class="pagination-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if($users->hasMorePages())
                        <li class="pagination-item">
                            <a href="{{ $users->nextPageUrl() }}" class="pagination-link next-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="pagination-item disabled">
                            <span class="pagination-link next-link">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
    @endif
</div>
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-users fa-2x"></i>
        </div>
        <h2>No Users Found</h2>
        <p class="empty-message">
            @if($currentSearch || $currentType)
                No users match your search criteria. Try a different search term or clear filters.
            @else
                There are no users in the system yet.
                @if(auth()->user()->type === 'admin')
                Start by creating the first user.
                @endif
            @endif
        </p>
        @if(auth()->user()->type === 'admin')
        <div class="empty-actions">
            @if($currentSearch || $currentType)
                <a href="{{ route('users.index', ['clear_filters' => true]) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>
                    Clear Filters
                </a>
            @endif
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Create First User
            </a>
        </div>
        @endif
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('userSearch');
    const typeFilter = document.getElementById('typeFilter');
    
    // Auto-submit form on filter change
    typeFilter.addEventListener('change', function() {
        searchForm.submit();
    });
    
    // Debounced search input
    // let searchTimeout;
    // searchInput.addEventListener('input', function() {
    //     clearTimeout(searchTimeout);
    //     searchTimeout = setTimeout(() => {
    //         searchForm.submit();
    //     }, 500); // 500ms delay
    // });
    
    // Enter key to submit search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchForm.submit();
        }
    });
});
</script>
@endsection
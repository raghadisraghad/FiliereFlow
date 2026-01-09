@extends('layouts.app')
@php
    $user = auth()->user();
@endphp

@section('content')
@auth
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Welcome to FiliereFlow</h1>
        <p class="dashboard-subtitle">Student & Filiere Management System</p>
        <p class="user-role-badge">
            {{ ucfirst(auth()->user()->type) }} Dashboard
        </p>
    </div>

    <!-- User Actions -->
    <div class="dashboard-actions">
        <div class="action-buttons">
            <a href="/filieres" class="btn btn-primary">
                <i class="fas fa-book-open me-2"></i>
                Browse Filieres
            </a>
            <a href="/profile" class="btn btn-secondary">
                <i class="fas fa-user-edit me-2"></i>
                Edit Profile
            </a>
        </div>
    </div>
    <!-- Profile Summary (Visible to ALL users) -->
    <div class="user-profile-summary">
        @if($user->profile_photo_path && file_exists(public_path('storage/' . $user->profile_photo_path)))
        <img id="profilePicturePreview" 
            src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name . ($user->last_name ? ' ' . $user->last_name : '')) . '&background=4f46e5&color=fff' }}" 
            alt="Profile Picture"
            class="profile-picture">
        @else
            <div class="profile-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        @endif
        <div class="profile-info">
            <h2>{{ auth()->user()->name }}</h2>
            <p class="profile-email">{{ auth()->user()->email }}</p>
            <div class="profile-meta">
                <span class="meta-item">
                    <i class="fas fa-user-tag"></i>
                    {{ ucfirst(auth()->user()->type) }} Account
                </span>
                <span class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    Member since {{ auth()->user()->created_at->format('M Y') }}
                </span>
                <span class="meta-item">
                    <i class="fas fa-clock"></i>
                    Last active: Now
                </span>
            </div>
        </div>
        <div class="profile-actions">
            <a href="/profile" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i>
                Edit Profile
            </a>
        </div>
    </div>

    <!-- Admin Dashboard -->
        @if(auth()->user()->type === 'admin')
            <div class="admin-quick-stats">
                <div class="admin-stat-grid">
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="admin-stat-content">
                            <h3>Total Users</h3>
                            @php
                                $totalUsers = DB::table('users')->count();
                            @endphp
                            <p class="admin-stat-number">{{ $totalUsers }}</p>
                            <span class="admin-stat-change positive">
                                <i class="fas fa-arrow-up"></i> System
                            </span>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="admin-stat-content">
                            <h3>Total Students</h3>
                            @php
                                $totalStudents = DB::table('users')->where('type', 'student')->count();
                            @endphp
                            <p class="admin-stat-number">{{ $totalStudents }}</p>
                            <span class="admin-stat-change">
                                In database
                            </span>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="admin-stat-content">
                            <h3>Total Filieres</h3>
                            @php
                                $totalFilieres = DB::table('filieres')->count();
                            @endphp
                            <p class="admin-stat-number">{{ $totalFilieres }}</p>
                            <span class="admin-stat-change">
                                Academic programs
                            </span>
                        </div>
                    </div>
                    
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="admin-stat-content">
                            <h3>System Status</h3>
                            <p class="admin-stat-number">100%</p>
                            <span class="admin-stat-change positive">
                                <i class="fas fa-check-circle"></i> Operational
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Actions Section -->
            <div class="admin-actions-section">
                <h2 class="section-title">Administration Panel</h2>
                <div class="admin-actions-grid">
                    <a href="/filieres" class="admin-action-card">
                        <div class="admin-action-icon primary">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="admin-action-content">
                            <h3>Manage Filieres</h3>
                            <p>Create, edit, and manage academic programs</p>
                            <span class="admin-action-link">
                                Go to Filieres <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                    
                    <a href="/users" onclick="alert('Coming soon: User Management')" class="admin-action-card">
                        <div class="admin-action-icon secondary">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="admin-action-content">
                            <h3>User Management</h3>
                            <p>Manage user accounts and permissions</p>
                            <span class="admin-action-link">
                                View Users <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                    
                    <a href="/students" onclick="alert('Coming soon: Student Management')" class="admin-action-card">
                        <div class="admin-action-icon accent">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="admin-action-content">
                            <h3>Student Management</h3>
                            <p>Manage student records and enrollments</p>
                            <span class="admin-action-link">
                                Manage Students <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Admin Recent Activity -->
            <div class="recent-section">
                <h2>Recent System Activity</h2>
                <div class="recent-list">
                    @php
                        $recentUsers = DB::table('users')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($recentUsers->count() > 0)
                        <h3 class="section-subtitle">Recent User Registrations</h3>
                        @foreach($recentUsers as $user)
                        <div class="recent-item">
                            @if($user->profile_photo_path && file_exists(public_path('storage/' . $user->profile_photo_path)))
                                <img id="profilePicturePreview" 
                                    src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name . ($user->last_name ? ' ' . $user->last_name : '')) . '&background=4f46e5&color=fff' }}" 
                                    alt="Profile Picture"
                                    class="profile-picture">
                            @else
                                <div class="profile-avatar">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif                    
                            <div class="recent-content">
                                <h4>{{ $user->name }}</h4>
                                <p>{{ $user->email }}</p>
                                <span class="recent-time">
                                    {{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }} • {{ $user->type }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Regular User Dashboard -->
        @else
            <!-- User Stats -->
            <div class="dashboard-stats">
                @php
                    $availableFilieres = DB::table('filieres')->count();
                @endphp
                
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <h3>Available Filieres</h3>
                    <p class="stat-number">{{ $availableFilieres }}</p>
                    <span class="stat-subtitle">Browse academic programs</span>
                    <a href="/filieres" class="stat-link">Explore →</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">👤</div>
                    <h3>My Profile</h3>
                    <p>Complete your profile</p>
                    <span class="stat-subtitle">Update personal information</span>
                    <a href="/profile" class="stat-link">Manage →</a>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🏠</div>
                    <h3>Dashboard</h3>
                    <p>Your home page</p>
                    <span class="stat-subtitle">Return to dashboard</span>
                    <a href="/dashboard" class="stat-link">Refresh →</a>
                </div>
            </div>

            <!-- User Announcements -->
            <div class="announcements-section">
                <h2>Information</h2>
                <div class="announcement-card">
                    <div class="announcement-icon">📚</div>
                    <div class="announcement-content">
                        <h4>Available Features</h4>
                        <p>Browse filieres, manage your profile, and view your dashboard.</p>
                    </div>
                </div>
                
                <div class="announcement-card">
                    <div class="announcement-icon">🔒</div>
                    <div class="announcement-content">
                        <h4>Privacy Protected</h4>
                        <p>Your personal information is secure and private.</p>
                    </div>
                </div>
            </div>
        @endif
</div>
@endauth
@endsection
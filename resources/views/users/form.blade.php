@extends('layouts.app')

@section('content')
<div class="user-form-container">
    <!-- Form Header -->
    <div class="form-header">
        @if(isset($user))
            <a href="{{ route('users.show', $user->id) }}" class="back-button">
                <i class="fas fa-arrow-left me-2"></i>
                Back to User Details
            </a>
        @else
            <a href="{{ route('users.index') }}" class="back-button">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Users
            </a>
        @endif
        
        <h1 class="form-title">
            <i class="fas fa-user me-2"></i>
            {{ isset($user) ? 'Edit User' : 'Create New User' }}
        </h1>
        <p class="form-subtitle">
            {{ isset($user) ? 'Update user information and permissions' : 'Add a new user to the system' }}
        </p>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              id="userForm">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="form-content">
                <!-- Profile Photo -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-camera me-2"></i>
                        Profile Photo
                    </h3>
                    
                    <div class="profile-photo-upload">
                        <div class="photo-preview">
                            @if(isset($user) && $user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     alt="{{ $user->name }}" 
                                     id="profilePhotoPreview"
                                     class="profile-photo-img">
                            @else
                                <div class="photo-placeholder" id="profilePhotoPreview">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                            @endif
                        </div>
                        <div class="photo-upload-controls">
                            <label for="profile_photo_path" class="btn btn-outline-primary">
                                <i class="fas fa-upload me-2"></i>
                                Upload Photo
                            </label>
                            <input type="file" 
                                   id="profile_photo_path" 
                                   name="profile_photo_path" 
                                   class="d-none"
                                   accept="image/*"
                                   onchange="previewProfilePhoto(event)">
                            @if(isset($user) && $user->profile_photo_path)
                                <button type="button" class="btn btn-outline-danger" onclick="removeProfilePhoto()">
                                    <i class="fas fa-trash me-2"></i>
                                    Remove
                                </button>
                            @endif
                            <small class="text-muted d-block mt-2">
                                Maximum file size: 2MB. Supported formats: JPG, PNG, GIF.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Basic Information
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label required">
                                <i class="fas fa-user me-2"></i>
                                First Name
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control" 
                                   value="{{ old('name', $user->name ?? '') }}" 
                                   placeholder="Enter first name"
                                   required
                                   autofocus>
                            @error('name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name" class="form-label">
                                <i class="fas fa-user me-2"></i>
                                Last Name
                            </label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   class="form-control" 
                                   value="{{ old('last_name', $user->last_name ?? '') }}" 
                                   placeholder="Enter last name">
                            @error('last_name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label required">
                            <i class="fas fa-envelope me-2"></i>
                            Email Address
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               value="{{ old('email', $user->email ?? '') }}" 
                               placeholder="Enter email address"
                               required>
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="type" class="form-label required">
                                <i class="fas fa-user-tag me-2"></i>
                                User Type
                            </label>
                            <select id="type" name="type" class="form-control" required>
                                <option value="admin" {{ old('type', $user->type ?? '') === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="student" {{ old('type', $user->type ?? '') === 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                            @error('type')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="sex" class="form-label">
                                <i class="fas fa-venus-mars me-2"></i>
                                Gender
                            </label>
                            <select id="sex" name="sex" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('sex', $user->sex ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('sex', $user->sex ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('sex')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-lock me-2"></i>
                        Password
                    </h3>
                    
                    @if(isset($user))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Leave password fields empty if you don't want to change the password.
                        </div>
                    @endif
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="form-label {{ !isset($user) ? 'required' : '' }}">
                                <i class="fas fa-key me-2"></i>
                                Password
                            </label>
                            <div class="password-input-group">
                                <input type="password" 
                                    id="password" 
                                    name="password" 
                                    class="form-control password-input" 
                                    placeholder="Enter password"
                                    {{ !isset($user) ? 'required' : '' }}
                                    autocomplete="new-password">
                                <button type="button" class="password-toggle" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label {{ !isset($user) ? 'required' : '' }}">
                                <i class="fas fa-key me-2"></i>
                                Confirm Password
                            </label>
                            <div class="password-input-group">
                                <input type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    class="form-control password-input" 
                                    placeholder="Confirm password"
                                    {{ !isset($user) ? 'required' : '' }}
                                    autocomplete="new-password">
                                <button type="button" class="password-toggle" data-target="password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filiere Enrollment (for students only) -->
                <div class="form-section" id="filiereSection" style="{{ old('type', $user->type ?? '') === 'student' ? '' : 'display: none;' }}">
                    <h3 class="section-title">
                        <i class="fas fa-book me-2"></i>
                        Filiere Enrollment
                    </h3>
                    
                    @php
                        // Get currently enrolled filiere IDs for edit mode
                        $enrolledFiliereIds = isset($user) ? $user->filieres->pluck('id')->toArray() : [];
                    @endphp
                    
                    @if($filieres->isNotEmpty())
                    <div class="form-group">
                        <div class="enrollment-controls">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllFilieresBtn">
                                <i class="fas fa-check-square me-1"></i>
                                Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllFilieresBtn">
                                <i class="fas fa-square me-1"></i>
                                Deselect All
                            </button>
                            <span class="text-muted ms-2">
                                <span id="selectedFilieresCount">0</span> of {{ $filieres->count() }} selected
                            </span>
                        </div>
                        
                        <div class="filieres-checkbox-grid">
                            @foreach($filieres as $filiere)
                            <div class="filiere-checkbox-item {{ in_array($filiere->id, old('filieres', $enrolledFiliereIds)) ? 'selected' : '' }}">
                                <input type="checkbox" 
                                       class="filiere-checkbox" 
                                       id="filiere_{{ $filiere->id }}" 
                                       name="filieres[]" 
                                       value="{{ $filiere->id }}"
                                       {{ in_array($filiere->id, old('filieres', $enrolledFiliereIds)) ? 'checked' : '' }}>
                                
                                <label for="filiere_{{ $filiere->id }}" class="filiere-label">
                                    <div class="filiere-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="filiere-info">
                                        <div class="filiere-name">{{ $filiere->name }}</div>
                                        <div class="filiere-stats">
                                            <small>
                                                <i class="fas fa-user-graduate me-1"></i>
                                                {{ $filiere->students_count ?? 0 }} students
                                            </small>
                                        </div>
                                    </div>
                                    <div class="selection-checkmark">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="enrollment-stats mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Selected filieres will be assigned to the student. Deselect to remove enrollment.
                            </small>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No active filieres available for enrollment.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                @if(isset($user))
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                @else
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                @endif
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    {{ isset($user) ? 'Update User' : 'Create User' }}
                </button>
            </div>
        </form>
    </div>

    <!-- User Stats (Only for edit mode) -->
    @if(isset($user))
    <div class="user-stats">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <h4>Created</h4>
                    <p class="stat-value">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="stat-content">
                    <h4>Last Updated</h4>
                    <p class="stat-value">{{ $user->updated_at->format('M d, Y') }}</p>
                </div>
            </div>
            
            @if($user->type === 'student')
            <div class="stat-card">
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
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const filiereSection = document.getElementById('filiereSection');
    const filiereCheckboxes = document.querySelectorAll('.filiere-checkbox');
    const selectAllBtn = document.getElementById('selectAllFilieresBtn');
    const deselectAllBtn = document.getElementById('deselectAllFilieresBtn');
    const selectedCountElem = document.getElementById('selectedFilieresCount');
    
    // Toggle filiere section based on user type
    function toggleFiliereSection() {
        if (typeSelect.value === 'student') {
            filiereSection.style.display = 'block';
        } else {
            filiereSection.style.display = 'none';
        }
    }
    
    // Update selected filiere count
    function updateSelectedFiliereCount() {
        const selected = document.querySelectorAll('.filiere-checkbox:checked').length;
        selectedCountElem.textContent = selected;
        
        // Update visual selection state
        filiereCheckboxes.forEach(checkbox => {
            const item = checkbox.closest('.filiere-checkbox-item');
            if (checkbox.checked) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });
    }
    
    // Select all filieres
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            filiereCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedFiliereCount();
        });
    }
    
    // Deselect all filieres
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            filiereCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedFiliereCount();
        });
    }
    
    // Update count on checkbox change
    filiereCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedFiliereCount);
    });
    
    // Type change event
    typeSelect.addEventListener('change', toggleFiliereSection);
    
    // Profile photo preview
    window.previewProfilePhoto = function(event) {
        const input = event.target;
        const preview = document.getElementById('profilePhotoPreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Create new image element
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'profile-photo-img';
                img.alt = 'Profile Photo Preview';
                
                // Replace preview content with new image
                preview.parentNode.innerHTML = '';
                preview.parentNode.appendChild(img);
                img.id = 'profilePhotoPreview';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    };
    
    // Remove profile photo
    window.removeProfilePhoto = function() {
        const preview = document.getElementById('profilePhotoPreview');
        const input = document.getElementById('profile_photo_path');
        
        // Clear file input
        if (input) {
            input.value = '';
        }
        
        // Show placeholder
        preview.parentNode.innerHTML = `
            <div class="photo-placeholder" id="profilePhotoPreview">
                <i class="fas fa-user fa-2x"></i>
            </div>
        `;
    };
    
    // Initialize
    toggleFiliereSection();
    updateSelectedFiliereCount();
});

// Password toggle functionality
document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const passwordInput = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            this.classList.add('active');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            this.classList.remove('active');
        }
    });
});
</script>
@endsection
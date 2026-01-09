@extends('layouts.app')
@php
    $user = auth()->user();
@endphp

@section('content')

@auth
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <h1 class="profile-title">Your Profile</h1>
            <p class="profile-subtitle">Manage your account settings and preferences</p>
        </div>

        <!-- Profile Tabs -->
        <div class="profile-tabs">
            <button class="profile-tab active" data-tab="profile-info">
                <i class="fas fa-user-circle me-2"></i>
                Profile Information
            </button>
            <button class="profile-tab" data-tab="update-password">
                <i class="fas fa-key me-2"></i>
                Update Password
            </button>
            @if($user->type === 'student' && $user->student)
            <button class="profile-tab" data-tab="student-info">
                <i class="fas fa-graduation-cap me-2"></i>
                Student Information
            </button>
            @endif
            <button class="profile-tab" data-tab="delete-account">
                <i class="fas fa-trash-alt me-2"></i>
                Delete Account
            </button>
        </div>

        <!-- Profile Information Tab -->
        <div class="profile-section active" id="profile-info">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-user-circle me-2"></i>
                    Profile Information
                </h2>
                <p class="section-description">Update your personal information</p>
            </div>
            
            <div class="profile-content">
                <!-- Profile Picture Section -->
                <div class="profile-picture-section">
                    <div class="current-picture">
                        <img id="profilePicturePreview" 
                            src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name . ($user->last_name ? ' ' . $user->last_name : '')) . '&background=4f46e5&color=fff' }}" 
                            alt="Profile Picture"
                            class="profile-picture">
                        <div class="picture-actions">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('profilePictureInput').click()">
                                <i class="fas fa-camera me-2"></i>
                                Change Photo
                            </button>
                            <input type="file" id="profilePictureInput" accept="image/*" style="display: none;" onchange="previewProfilePicture(event)">
                            @if($user->profile_photo_path)
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeProfilePicture()">
                                <i class="fas fa-trash me-2"></i>
                                Remove Photo
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <form id="updateProfileForm" class="profile-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden field for profile picture -->
                        <input type="hidden" id="profilePictureData" name="profile_picture">
                        <input type="hidden" id="removePicture" name="remove_picture" value="0">
                        
                        <!-- Form Fields -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">
                                    <i class="fas fa-user me-2"></i>
                                    First Name *
                                </label>
                                <input type="text" id="name" name="name" 
                                    class="form-control" 
                                    value="{{ old('name', $user->name) }}" 
                                    required>
                                <span class="form-error" id="nameError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">
                                    <i class="fas fa-user me-2"></i>
                                    Last Name
                                </label>
                                <input type="text" id="last_name" name="last_name" 
                                    class="form-control" 
                                    value="{{ old('last_name', $user->last_name) }}">
                                <span class="form-error" id="last_nameError"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>
                                Email Address *
                            </label>
                            <input type="email" id="email" name="email" 
                                class="form-control" 
                                value="{{ old('email', $user->email) }}" 
                                required>
                            <span class="form-error" id="emailError"></span>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sex">
                                    <i class="fas fa-venus-mars me-2"></i>
                                    Gender
                                </label>
                                <select id="sex" name="sex" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ $user->sex === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $user->sex === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                <span class="form-error" id="sexError"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="type">
                                    <i class="fas fa-user-tag me-2"></i>
                                    Account Type
                                </label>
                                <select id="type" name="type" class="form-control" {{ $user->type === 'admin' ? '' : 'disabled' }}>
                                    <option value="student" {{ $user->type === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="admin" {{ $user->type === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <input type="hidden" name="type" value="{{ $user->type }}">
                                <span class="form-error" id="typeError"></span>
                                <span class="form-error" id="typeError"></span>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Save Changes
                            </button>
                            <div class="form-message" id="profileMessage"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Password Tab -->
        <div class="profile-section" id="update-password">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-key me-2"></i>
                    Update Password
                </h2>
                <p class="section-description">Ensure your account is using a strong, secure password</p>
            </div>
            
            <form id="updatePasswordForm" class="profile-form">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="current_password">
                        <i class="fas fa-lock me-2"></i>
                        Current Password *
                    </label>
                    <div class="input-with-icon">
                        <input type="password" id="current_password" name="current_password" 
                            class="form-control" required>
                        <span class="input-icon" onclick="togglePassword('current_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <span class="form-error" id="current_passwordError"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-key me-2"></i>
                        New Password *
                    </label>
                    <div class="input-with-icon">
                        <input type="password" id="password" name="password" 
                            class="form-control" required>
                        <span class="input-icon" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <span class="form-error" id="passwordError"></span>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="fas fa-key me-2"></i>
                        Confirm New Password *
                    </label>
                    <div class="input-with-icon">
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                            class="form-control" required>
                        <span class="input-icon" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <span class="form-error" id="password_confirmationError"></span>
                </div>
                
                <!-- Password Strength Indicator -->
                <div class="password-strength">
                    <div class="strength-meter">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Password strength</div>
                </div>
                
                <!-- Password Requirements -->
                <div class="password-requirements">
                    <h5>Password Requirements:</h5>
                    <ul>
                        <li id="reqLength" class="requirement"><i class="fas fa-circle"></i> At least 8 characters</li>
                        <li id="reqUpper" class="requirement"><i class="fas fa-circle"></i> One uppercase letter</li>
                        <li id="reqLower" class="requirement"><i class="fas fa-circle"></i> One lowercase letter</li>
                        <li id="reqNumber" class="requirement"><i class="fas fa-circle"></i> One number</li>
                        <li id="reqSpecial" class="requirement"><i class="fas fa-circle"></i> One special character</li>
                    </ul>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Update Password
                    </button>
                    <div class="form-message" id="passwordMessage"></div>
                </div>
            </form>
        </div>

        <!-- Student Information Tab (only for students) -->
        @if($user->type === 'student' && $user->student)
        <div class="profile-section" id="student-info">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Student Information
                </h2>
                <p class="section-description">View your student information</p>
            </div>
            
            <div class="student-info-content">
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="info-content">
                            <h4>Student ID</h4>
                            <p>#{{ str_pad($user->student->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="info-content">
                            <h4>Filiere</h4>
                            <p>{{ $user->student->filiere->name ?? 'Not assigned' }}</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="info-content">
                            <h4>Enrollment Date</h4>
                            <p>{{ $user->student->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Academic Information -->
                <div class="academic-info">
                    <h4 class="info-title">Academic Details</h4>
                    <div class="info-table">
                        <div class="info-row">
                            <span class="info-label">Student Status:</span>
                            <span class="info-value">
                                <span class="badge bg-success">Active</span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Filiere Code:</span>
                            <span class="info-value">{{ $user->student->filiere_id ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">User ID:</span>
                            <span class="info-value">#{{ $user->id }}</span>
                        </div>
                    </div>
                </div>
                
                @if($user->student->filiere)
                <div class="filiere-actions mt-4">
                    <a href="{{ route('filieres.show', $user->student->filiere_id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-2"></i>
                        View Filiere Details
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Delete Account Tab -->
        <div class="profile-section danger-section" id="delete-account">
            <div class="section-header">
                <h2 class="section-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Delete Account
                </h2>
                <p class="section-description">Permanently delete your account and all associated data</p>
            </div>
            
            <div class="delete-warning">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="warning-content">
                    <h4>Warning: This action cannot be undone</h4>
                    <p>Once you delete your account, all of your data will be permanently removed including:</p>
                    <ul>
                        <li>Your profile information</li>
                        <li>Your account settings</li>
                        @if($user->type === 'student' && $user->student)
                        <li>Your student records</li>
                        <li>Your academic information</li>
                        @endif
                        <li>All associated data</li>
                        <li>Access to the system</li>
                    </ul>
                    @if($user->type === 'student' && $user->student)
                    <p class="mt-2 text-danger"><strong>Note:</strong> As a student, deleting your account will also remove your academic records.</p>
                    @endif
                </div>
            </div>
            
            <form id="deleteAccountForm" class="profile-form">
                @csrf
                @method('DELETE')
                
                <div class="form-group">
                    <label for="delete_password">
                        <i class="fas fa-lock me-2"></i>
                        Confirm Password *
                    </label>
                    <div class="input-with-icon">
                        <input type="password" id="delete_password" name="password" 
                            class="form-control" required placeholder="Enter your password to confirm">
                        <span class="input-icon" onclick="togglePassword('delete_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <span class="form-error" id="passwordError"></span>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="confirmDelete" name="confirm_delete" 
                            class="form-check-input" required>
                        <label for="confirmDelete" class="form-check-label">
                            I understand that this action cannot be undone
                        </label>
                    </div>
                    <span class="form-error" id="confirmDeleteError"></span>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" id="deleteAccountBtn" onclick="window.confirmDelete()" disabled>
                        <i class="fas fa-trash-alt me-2"></i>
                        Delete My Account
                    </button>
                    <div class="form-message" id="deleteMessage"></div>
                </div>
            </form>
        </div>
    </div>
<script>
// Tab switching functionality
document.querySelectorAll('.profile-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Update active tab
        document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // Show corresponding section
        const tabId = this.getAttribute('data-tab');
        document.querySelectorAll('.profile-section').forEach(section => {
            section.classList.remove('active');
        });
        document.getElementById(tabId).classList.add('active');
    });
});

// Password toggle functionality
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.parentElement.querySelector('.input-icon i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Profile picture preview
function previewProfilePicture(event) {
    const input = event.target;
    const preview = document.getElementById('profilePicturePreview');
    const removeBtn = document.querySelector('[onclick="removeProfilePicture()"]');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            
            // Convert image to base64 for form submission
            const file = input.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                document.getElementById('profilePictureData').value = reader.result;
                document.getElementById('removePicture').value = '0';
            };
        }
        
        reader.readAsDataURL(input.files[0]);
        
        // Show remove button
        if (removeBtn) removeBtn.style.display = 'block';
    }
}

// Remove profile picture
function removeProfilePicture() {
    const preview = document.getElementById('profilePicturePreview');
    const name = '{{ $user->name }}';
    const lastName = '{{ $user->last_name }}';
    
    // Set default avatar
    preview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name + (lastName ? ' ' + lastName : ''))}&background=4f46e5&color=fff`;
    
    // Clear file input
    document.getElementById('profilePictureInput').value = '';
    document.getElementById('profilePictureData').value = '';
    document.getElementById('removePicture').value = '1';
    
    // Hide remove button
    const removeBtn = document.querySelector('[onclick="removeProfilePicture()"]');
    if (removeBtn) removeBtn.style.display = 'none';
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    
    // Check length
    if (password.length >= 8) strength++;
    
    // Check for uppercase
    if (/[A-Z]/.test(password)) strength++;
    
    // Check for lowercase
    if (/[a-z]/.test(password)) strength++;
    
    // Check for numbers
    if (/[0-9]/.test(password)) strength++;
    
    // Check for special characters
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

function updatePasswordRequirements(password) {
    // Update requirement icons
    document.getElementById('reqLength').classList.toggle('met', password.length >= 8);
    document.getElementById('reqUpper').classList.toggle('met', /[A-Z]/.test(password));
    document.getElementById('reqLower').classList.toggle('met', /[a-z]/.test(password));
    document.getElementById('reqNumber').classList.toggle('met', /[0-9]/.test(password));
    document.getElementById('reqSpecial').classList.toggle('met', /[^A-Za-z0-9]/.test(password));
    
    // Update strength meter
    const strength = checkPasswordStrength(password);
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    strengthBar.className = 'strength-bar';
    
    if (strength === 0) {
        strengthBar.style.width = '0%';
        strengthText.textContent = 'Password strength';
    } else if (strength <= 2) {
        strengthBar.classList.add('weak');
        strengthBar.style.width = '40%';
        strengthText.textContent = 'Weak password';
    } else if (strength <= 4) {
        strengthBar.classList.add('medium');
        strengthBar.style.width = '70%';
        strengthText.textContent = 'Medium password';
    } else {
        strengthBar.classList.add('strong');
        strengthBar.style.width = '100%';
        strengthText.textContent = 'Strong password';
    }
}

// Form submissions
document.getElementById('updateProfileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('profileMessage');
    
    // Clear previous errors
    document.querySelectorAll('#profile-info .form-error').forEach(el => el.textContent = '');
    messageDiv.className = 'form-message';
    messageDiv.textContent = '';
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    submitBtn.disabled = true;
    
    try {
        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        // Remove empty values
        Object.keys(data).forEach(key => {
            if (data[key] === '' || data[key] === null || data[key] === undefined) {
                delete data[key];
            }
        });
        
        const response = await fetch('/profile/update', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            if (result.errors) {
                for (const [field, errors] of Object.entries(result.errors)) {
                    const errorEl = document.getElementById(field + 'Error');
                    if (errorEl) {
                        errorEl.textContent = errors[0];
                    }
                }
                messageDiv.className = 'form-message error';
                messageDiv.textContent = 'Please fix the errors above.';
            } else {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = result.message || 'An error occurred.';
            }
            return;
        }
        
        // Update UI with new data
        document.getElementById('name').value = result.user.name;
        document.getElementById('last_name').value = result.user.last_name || '';
        document.getElementById('email').value = result.user.email;
        document.getElementById('sex').value = result.user.sex || '';
        document.getElementById('type').value = result.user.type;
        
        if (result.user.profile_photo_path && document.getElementById('removePicture').value !== '1') {
            document.getElementById('profilePicturePreview').src = 
                '/storage/' + result.user.profile_photo_path;
        }
        
        // Show success message
        messageDiv.className = 'form-message success';
        messageDiv.textContent = 'Profile updated successfully!';
        
        // Clear profile picture data if successful
        document.getElementById('profilePictureData').value = '';
        document.getElementById('removePicture').value = '0';
        
        // Show success modal
        showSuccessModal('Profile updated successfully!');
        
    } catch (error) {
        messageDiv.className = 'form-message error';
        messageDiv.textContent = 'An error occurred. Please try again.';
        console.error('Error:', error);
    } finally {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

document.getElementById('updatePasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('passwordMessage');
    
    // Clear previous errors
    document.querySelectorAll('#update-password .form-error').forEach(el => el.textContent = '');
    messageDiv.className = 'form-message';
    messageDiv.textContent = '';
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    submitBtn.disabled = true;
    
    try {
        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        const response = await fetch('/profile/password', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            if (result.errors) {
                for (const [field, errors] of Object.entries(result.errors)) {
                    const errorEl = document.getElementById(field + 'Error');
                    if (errorEl) {
                        errorEl.textContent = errors[0];
                    }
                }
                messageDiv.className = 'form-message error';
                messageDiv.textContent = 'Please fix the errors above.';
            } else {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = result.message || 'An error occurred.';
            }
            return;
        }
        
        // Show success message
        messageDiv.className = 'form-message success';
        messageDiv.textContent = 'Password updated successfully!';
        
        // Clear form
        this.reset();
        updatePasswordRequirements('');
        
        // Show success modal
        showSuccessModal('Password updated successfully!');
        
    } catch (error) {
        messageDiv.className = 'form-message error';
        messageDiv.textContent = 'An error occurred. Please try again.';
        console.error('Error:', error);
    } finally {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Delete account confirmation
document.getElementById('confirmDelete').addEventListener('change', function() {
    document.getElementById('deleteAccountBtn').disabled = !this.checked;
});

function confirmDelete() {
    // Show confirmation dialog
    if (confirm('Are you absolutely sure? This action cannot be undone. Your account and all associated data will be permanently deleted.')) {
        deleteAccount();
    }
}

async function deleteAccount() {
    const form = document.getElementById('deleteAccountForm');
    const formData = new FormData(form);
    const messageDiv = document.getElementById('deleteMessage');
    
    // Clear previous errors
    document.querySelectorAll('#delete-account .form-error').forEach(el => el.textContent = '');
    messageDiv.className = 'form-message';
    messageDiv.textContent = '';
    
    // Show loading state
    const deleteBtn = document.getElementById('deleteAccountBtn');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
    deleteBtn.disabled = true;
    
    try {
        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });
        
        console.log('Sending delete request:', data);
        
        const response = await fetch('/profile/delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        console.log('Delete response:', result);
        
        if (!response.ok) {
            if (result.errors) {
                for (const [field, errors] of Object.entries(result.errors)) {
                    const errorEl = document.getElementById(field + 'Error');
                    if (errorEl) {
                        errorEl.textContent = errors[0];
                    } else {
                        // For general errors
                        messageDiv.className = 'form-message error';
                        messageDiv.textContent = errors[0];
                    }
                }
            }
            return;
        }
        
        // Success - redirect to home page
        if (result.redirect) {
            window.location.href = result.redirect;
        } else {
            window.location.href = '/';
        }
        
    } catch (error) {
        console.error('Error:', error);
        messageDiv.className = 'form-message error';
        messageDiv.textContent = 'An error occurred. Please try again.';
    } finally {
        // Restore button state
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    }
}

// Update the confirmDelete function
function confirmDelete() {
    // Show confirmation dialog
    if (confirm('Are you absolutely sure? This action cannot be undone. Your account and all associated data will be permanently deleted.')) {
        deleteAccount();
    }
}

// Update the checkbox event listener
document.getElementById('confirmDelete').addEventListener('change', function() {
    document.getElementById('deleteAccountBtn').disabled = !this.checked;
});

// Real-time password strength checking
document.getElementById('password').addEventListener('input', function(e) {
    updatePasswordRequirements(e.target.value);
});

// Success modal function
function showSuccessModal(message) {
    // Create a simple alert instead of modal
    alert(message);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updatePasswordRequirements('');
    
    // Fix password input styling
    document.querySelectorAll('.input-with-icon').forEach(container => {
        const input = container.querySelector('input');
        if (input) {
            input.style.paddingRight = '40px';
        }
    });
});
</script>

@endauth
@endsection
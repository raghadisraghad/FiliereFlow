@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Join FiliereFlow to manage students and filieres</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
            @csrf

            <!-- Hidden type field -->
            <input type="hidden" name="type" value="user">

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="form-input" placeholder="Enter your full name">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="form-input" placeholder="Enter your email">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-container">
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="form-input" placeholder="Create a password">
                    <button type="button" class="password-toggle-btn" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
                
                <!-- Password Requirements -->
                <div class="password-requirements" id="passwordRequirements">
                    <div class="requirements-header">
                        <span class="requirements-title">Password Requirements</span>
                        <span class="requirements-status" id="requirementsStatus">0/5</span>
                    </div>
                    <div class="requirements-progress">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                    <ul class="requirements-list">
                        <li data-rule="length" class="requirement-item">
                            <span class="requirement-icon">○</span>
                            <span class="requirement-text">At least 8 characters</span>
                        </li>
                        <li data-rule="uppercase" class="requirement-item">
                            <span class="requirement-icon">○</span>
                            <span class="requirement-text">One uppercase letter</span>
                        </li>
                        <li data-rule="lowercase" class="requirement-item">
                            <span class="requirement-icon">○</span>
                            <span class="requirement-text">One lowercase letter</span>
                        </li>
                        <li data-rule="number" class="requirement-item">
                            <span class="requirement-icon">○</span>
                            <span class="requirement-text">One number</span>
                        </li>
                        <li data-rule="special" class="requirement-item">
                            <span class="requirement-icon">○</span>
                            <span class="requirement-text">One special character</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Password Strength Notification -->
                <div class="password-strength-notification" id="passwordNotification">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Password must meet all requirements above</span>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="password-input-container">
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-input" placeholder="Confirm your password">
                    <button type="button" class="password-toggle-btn" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-match" id="passwordMatch"></div>
            </div>

            <div class="form-actions">
                <a href="{{ route('login') }}" class="auth-link">
                    Already have an account? Sign in
                </a>
                <button type="submit" class="btn btn-primary btn-submit" id="submitBtn">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// SIMPLE toggle function - SAME AS LOGIN
function togglePassword(button) {
    const container = button.parentElement;
    const input = container.querySelector('input');
    const icon = button.querySelector('i');
    
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

// Password validation
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    const passwordMatch = document.getElementById('passwordMatch');
    const progressBar = document.getElementById('progressBar');
    const requirementsStatus = document.getElementById('requirementsStatus');
    const requirementItems = document.querySelectorAll('.requirement-item');
    const passwordRequirements = document.getElementById('passwordRequirements');
    const passwordNotification = document.getElementById('passwordNotification');
    const termsCheckbox = document.getElementById('terms');
    
    // Hide notification initially
    passwordNotification.style.display = 'none';
    
    // Password validation rules
    const rules = {
        length: (value) => value.length >= 8,
        uppercase: (value) => /[A-Z]/.test(value),
        lowercase: (value) => /[a-z]/.test(value),
        number: (value) => /[0-9]/.test(value),
        special: (value) => /[!@#$%^&*(),.?":{}|<>]/.test(value)
    };
    
    // Update password requirements
    function updatePasswordRequirements() {
        const value = passwordInput.value;
        let validCount = 0;
        
        requirementItems.forEach(item => {
            const rule = item.getAttribute('data-rule');
            const isValid = rules[rule](value);
            const icon = item.querySelector('.requirement-icon');
            
            if (isValid) {
                item.classList.add('valid');
                item.classList.remove('invalid');
                icon.textContent = '✓';
                icon.style.color = '#10b981';
                validCount++;
            } else {
                item.classList.add('invalid');
                item.classList.remove('valid');
                icon.textContent = '○';
                icon.style.color = value.length > 0 ? '#ef4444' : '#94a3b8';
            }
        });
        
        // Update progress bar and status
        const progress = (validCount / 5) * 100;
        progressBar.style.width = `${progress}%`;
        progressBar.style.backgroundColor = progress === 100 ? '#10b981' : 
                                          progress >= 60 ? '#f59e0b' : '#ef4444';
        
        requirementsStatus.textContent = `${validCount}/5`;
        requirementsStatus.style.color = progress === 100 ? '#10b981' : 
                                       progress >= 60 ? '#f59e0b' : '#ef4444';
        
        // Show/hide requirements
        if (value.length > 0 || document.activeElement === passwordInput) {
            passwordRequirements.classList.add('active');
            passwordRequirements.style.maxHeight = '300px';
            passwordRequirements.style.opacity = '1';
            passwordRequirements.style.transform = 'translateY(0)';
            
            // Show notification if password is weak
            if (value.length < 8 || validCount < 5) {
                passwordNotification.style.display = 'flex';
            } else {
                passwordNotification.style.display = 'none';
            }
        } else {
            passwordRequirements.classList.remove('active');
            passwordRequirements.style.maxHeight = '0';
            passwordRequirements.style.opacity = '0';
            passwordRequirements.style.transform = 'translateY(-10px)';
            passwordNotification.style.display = 'none';
        }
        
        return validCount === 5;
    }
    
    // Check password match
    function checkPasswordMatch() {
        if (!passwordInput.value || !confirmInput.value) {
            passwordMatch.innerHTML = '';
            passwordMatch.classList.remove('match-success', 'match-error');
            return false;
        }
        
        if (passwordInput.value === confirmInput.value) {
            passwordMatch.innerHTML = '<span class="match-icon"><i class="fas fa-check"></i></span> Passwords match';
            passwordMatch.classList.add('match-success');
            passwordMatch.classList.remove('match-error');
            return true;
        } else {
            passwordMatch.innerHTML = '<span class="match-icon"><i class="fas fa-times"></i></span> Passwords do not match';
            passwordMatch.classList.add('match-error');
            passwordMatch.classList.remove('match-success');
            return false;
        }
    }
    
    // Validate form
    function validateForm() {
        const isPasswordValid = updatePasswordRequirements();
        const passwordsMatch = checkPasswordMatch();
        const isEmailValid = document.getElementById('email').value.includes('@');
        const isNameValid = document.getElementById('name').value.trim().length > 0;
        const isTermsChecked = termsCheckbox ? termsCheckbox.checked : true;
        
        const isValid = isPasswordValid && passwordsMatch && isEmailValid && isNameValid && isTermsChecked;
        
        submitBtn.disabled = !isValid;
        submitBtn.style.opacity = isValid ? '1' : '0.6';
        submitBtn.style.cursor = isValid ? 'pointer' : 'not-allowed';
        
        return isValid;
    }
    
    // Event listeners
    if (passwordInput) {
        passwordInput.addEventListener('input', validateForm);
        passwordInput.addEventListener('focus', function() {
            if (this.value.length > 0) {
                passwordRequirements.classList.add('active');
                passwordRequirements.style.maxHeight = '300px';
                passwordRequirements.style.opacity = '1';
                passwordRequirements.style.transform = 'translateY(0)';
            }
        });
    }
    
    if (confirmInput) {
        confirmInput.addEventListener('input', validateForm);
    }
    
    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', validateForm);
    }
    
    document.getElementById('email').addEventListener('input', validateForm);
    document.getElementById('name').addEventListener('input', validateForm);
    
    // Initial validation
    validateForm();
});
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="auth-title">Set New Password</h2>
            <p class="auth-subtitle">Create a new password for your account</p>
        </div>

        @if ($errors->any())
            <div class="validation-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="auth-form" id="resetPasswordForm">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="email" class="form-input" placeholder="Enter your email">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <div class="password-input-container">
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="form-input" placeholder="Create new password">
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
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <div class="password-input-container">
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-input" placeholder="Confirm new password">
                    <button type="button" class="password-toggle-btn" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-match" id="passwordMatch"></div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-submit" id="submitBtn">
                    <i class="fas fa-key me-2"></i>
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Password toggle function
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
    
    // Hide requirements initially
    passwordRequirements.style.maxHeight = '0';
    passwordRequirements.style.opacity = '0';
    passwordRequirements.style.transform = 'translateY(-10px)';
    
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
            passwordRequirements.style.padding = '0.75rem';
            passwordRequirements.style.marginTop = '0.75rem';
        } else {
            passwordRequirements.classList.remove('active');
            passwordRequirements.style.maxHeight = '0';
            passwordRequirements.style.opacity = '0';
            passwordRequirements.style.transform = 'translateY(-10px)';
            passwordRequirements.style.padding = '0 0.75rem';
            passwordRequirements.style.marginTop = '0';
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
        
        const isValid = isPasswordValid && passwordsMatch && isEmailValid;
        
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
                passwordRequirements.style.padding = '0.75rem';
                passwordRequirements.style.marginTop = '0.75rem';
            }
        });
    }
    
    if (confirmInput) {
        confirmInput.addEventListener('input', validateForm);
    }
    
    document.getElementById('email').addEventListener('input', validateForm);
    
    // Initial validation
    validateForm();
});
</script>
@endsection
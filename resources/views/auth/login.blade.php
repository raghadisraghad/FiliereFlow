@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Sign in to your FiliereFlow account</p>
        </div>

        @if (session('status'))
            <div class="auth-status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input" placeholder="Enter your email">
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-container">
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="form-input" placeholder="Enter your password">
                    <button type="button" class="password-toggle-btn" onclick="toggleLoginPassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group remember-group">
                <label class="remember-label">
                    <input type="checkbox" name="remember" id="remember_me" class="remember-checkbox">
                    <span class="remember-text">Remember me</span>
                </label>
            </div>

            <div class="form-actions">
                <div class="auth-links">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="auth-link">
                            Forgot your password?
                        </a>
                    @endif
                    <a href="{{ route('register') }}" class="auth-link">
                        Don't have an account?
                    </a>
                </div>
                <button type="submit" class="btn btn-primary btn-submit">
                    Sign In
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// SIMPLE toggle function that definitely works
function toggleLoginPassword(button) {
    // Find the input field - it's the previous sibling of the button's parent
    const container = button.parentElement;
    const input = container.querySelector('input');
    const icon = button.querySelector('i');
    
    console.log('Toggle clicked:', input.id); // Debug
    
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
</script>
@endsection
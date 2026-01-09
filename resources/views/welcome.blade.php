@extends('layouts.app')

@section('content')
<div class="welcome-container">
    <div class="welcome-hero">
        <h1 class="welcome-title">Welcome to <span class="highlight">FiliereFlow</span></h1>
        <p class="welcome-subtitle">Smart Student & Filiere Management System</p>
        <p class="welcome-description">
            Streamline academic processes, manage student pathways, and organize filieres efficiently 
            with our intuitive platform designed for educational excellence.
        </p>
        
        <div class="welcome-actions">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    Get Started Free
                </a>
                <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">
                    Sign In
                </a>
            @endauth
        </div>
    </div>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🎓</div>
            <h3>Student Management</h3>
            <p>Organize student information, track progress, and manage academic records seamlessly.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📚</div>
            <h3>Filiere Tracking</h3>
            <p>Monitor and organize different academic pathways and specializations with ease.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Analytics Dashboard</h3>
            <p>Gain insights with visual reports on student performance and program statistics.</p>
        </div>
    </div>

    @guest
    <div class="welcome-cta">
        <h2>Ready to transform your academic management?</h2>
        <a href="{{ route('register') }}" class="btn btn-accent btn-lg">
            Create Your Account Now
        </a>
    </div>
    @endguest
</div>
@endsection
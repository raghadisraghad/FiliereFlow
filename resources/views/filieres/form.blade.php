@extends('layouts.app')

@section('content')
<div class="filiere-form-container">
    <!-- Form Header -->
    <div class="form-header">
        @if(isset($filiere))
            <a href="{{ route('filieres.show', $filiere->id) }}" class="back-button">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Filiere Details
            </a>
        @else
            <a href="{{ route('filieres.index') }}" class="back-button">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Filieres
            </a>
        @endif
        
        <h1 class="form-title">
            <i class="fas fa-book me-2"></i>
            {{ isset($filiere) ? 'Edit Filiere' : 'Create New Filiere' }}
        </h1>
        <p class="form-subtitle">
            {{ isset($filiere) ? 'Update the academic program details' : 'Add a new academic program to the system' }}
        </p>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ isset($filiere) ? route('filieres.update', $filiere->id) : route('filieres.store') }}" method="POST" id="filiereForm">
            @csrf
            @if(isset($filiere))
                @method('PUT')
            @endif

            <div class="form-content">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Basic Information
                    </h3>
                    
                    <!-- Name Field -->
                    <div class="form-group">
                        <label for="name" class="form-label required">
                            <i class="fas fa-font me-2"></i>
                            Filiere Name
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control" 
                               value="{{ old('name', $filiere->name ?? '') }}" 
                               placeholder="Enter filiere name"
                               required
                               autofocus>
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status Field -->
                    <div class="form-group">
                        <label for="status" class="form-label required">
                            <i class="fas fa-toggle-on me-2"></i>
                            Status
                        </label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="1" {{ (old('status', $filiere->status ?? '')) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (old('status', $filiere->status ?? '')) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Total Courses Field -->
                    <div class="form-group">
                        <label for="total_courses" class="form-label required">
                            <i class="fas fa-book-open me-2"></i>
                            Total Courses
                        </label>
                        <input type="number" 
                               id="total_courses" 
                               name="total_courses" 
                               class="form-control" 
                               value="{{ old('total_courses', $filiere->total_courses ?? '0') }}" 
                               placeholder="Enter number of courses"
                               required
                               min="0">
                        @error('total_courses')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Students Enrollment Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user-graduate me-2"></i>
                        Manage Student Enrollment
                    </h3>
                    
                    @php
                        // Get currently enrolled student IDs
                        $enrolledStudentIds = isset($filiere) ? $filiere->students->pluck('id')->toArray() : [];
                    @endphp
                    
                    @if($students->isNotEmpty())
                    <div class="form-group">
                        <!-- Student Selection Controls -->
                        <div class="student-selection-controls">
                            <div class="search-container">
                                <div class="search-input-group">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" 
                                           id="studentSearch" 
                                           class="search-input" 
                                           placeholder="Search students by name or email..." 
                                           autocomplete="off">
                                    <button type="button" class="clear-search-btn" id="clearSearchBtn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="selection-buttons">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">
                                    <i class="fas fa-check-square me-1"></i>
                                    Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">
                                    <i class="fas fa-square me-1"></i>
                                    Deselect All
                                </button>
                                <div class="selection-info">
                                    <span id="selectedCount">0</span> of <span id="totalCount">{{ $students->count() }}</span> selected
                                </div>
                            </div>
                        </div>
                        
                        <!-- Scrollable Students List -->
                        <div class="students-checkbox-container">
                            <div class="students-checkbox-grid" id="studentsGrid">
                                @foreach($students as $student)
                                <div class="student-checkbox-item {{ in_array($student->id, old('students', $enrolledStudentIds)) ? 'selected' : '' }}" 
                                     data-student-id="{{ $student->id }}"
                                     data-name="{{ strtolower($student->name) }}"
                                     data-email="{{ strtolower($student->email) }}">
                                    <input type="checkbox" 
                                           class="student-checkbox" 
                                           id="student_{{ $student->id }}" 
                                           name="students[]" 
                                           value="{{ $student->id }}"
                                           {{ in_array($student->id, old('students', $enrolledStudentIds)) ? 'checked' : '' }}>
                                    
                                    <label for="student_{{ $student->id }}" class="student-label">
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
                                        <div class="student-info">
                                            <div class="student-name">{{ $student->name }}</div>
                                            <div class="student-email">{{ $student->email }}</div>
                                        </div>
                                        <div class="selection-checkmark">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Empty Search State -->
                            <div class="empty-search-state" id="emptySearchState" style="display: none;">
                                <div class="empty-search-icon">
                                    <i class="fas fa-search fa-lg"></i>
                                </div>
                                <p>No students match your search.</p>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSearchResultsBtn">
                                    Clear search
                                </button>
                            </div>
                        </div>
                        
                        <!-- Enrollment Stats -->
                        <div class="enrollment-stats">
                            <div class="stats-row">
                                <div class="stat">
                                    <span class="stat-label">Total Students:</span>
                                    <span class="stat-value">{{ $students->count() }}</span>
                                </div>
                                @if(isset($filiere))
                                <div class="stat">
                                    <span class="stat-label">Currently Enrolled:</span>
                                    <span class="stat-value">{{ $filiere->students->count() }}</span>
                                </div>
                                @endif
                                <div class="stat">
                                    <span class="stat-label">Selected:</span>
                                    <span class="stat-value" id="selectedStat">0</span>
                                </div>
                            </div>
                            <div class="stats-hint">
                                <i class="fas fa-info-circle me-1"></i>
                                @if(isset($filiere))
                                    Students deselected will be removed from this filiere.
                                @else
                                    Selected students will be enrolled in this new filiere.
                                @endif
                            </div>
                        </div>
                        
                        @error('students')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        @error('students.*')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No students available for enrollment. 
                        @if(!isset($filiere))
                            You can create the filiere now and add students later.
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                @if(isset($filiere))
                    <a href="{{ route('filieres.show', $filiere->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                @else
                    <a href="{{ route('filieres.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                @endif
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    {{ isset($filiere) ? 'Update Filiere' : 'Create Filiere' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics (Only for edit mode) -->
    @if(isset($filiere))
    <div class="form-stats">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-content">
                    <h4>Enrolled Students</h4>
                    <p class="stat-number">{{ $filiere->students->count() }}</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <h4>Created</h4>
                    <p class="stat-value">{{ $filiere->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="stat-content">
                    <h4>Last Updated</h4>
                    <p class="stat-value">{{ $filiere->updated_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const studentItems = document.querySelectorAll('.student-checkbox-item');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const searchInput = document.getElementById('studentSearch');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const clearSearchResultsBtn = document.getElementById('clearSearchResultsBtn');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const studentsGrid = document.getElementById('studentsGrid');
    const emptySearchState = document.getElementById('emptySearchState');
    const selectedCountElem = document.getElementById('selectedCount');
    const totalCountElem = document.getElementById('totalCount');
    const selectedStatElem = document.getElementById('selectedStat');
    
    // Update selected count
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.student-checkbox:checked').length;
        selectedCountElem.textContent = selected;
        selectedStatElem.textContent = selected;
        
        // Update visual selection state
        studentItems.forEach(item => {
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox.checked) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });
    }
    
    // Filter students based on search
    function filterStudents(searchTerm) {
        searchTerm = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        
        studentItems.forEach(item => {
            const name = item.dataset.name;
            const email = item.dataset.email;
            
            if (name.includes(searchTerm) || email.includes(searchTerm) || searchTerm === '') {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide empty state
        if (searchTerm !== '' && visibleCount === 0) {
            studentsGrid.style.display = 'none';
            emptySearchState.style.display = 'block';
        } else {
            studentsGrid.style.display = 'grid';
            emptySearchState.style.display = 'none';
        }
    }
    
    // Select all visible students
    function selectAllVisible() {
        const visibleItems = document.querySelectorAll('.student-checkbox-item[style="display: block"], .student-checkbox-item:not([style])');
        visibleItems.forEach(item => {
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox) {
                checkbox.checked = true;
            }
        });
        updateSelectedCount();
    }
    
    // Deselect all visible students
    function deselectAllVisible() {
        const visibleItems = document.querySelectorAll('.student-checkbox-item[style="display: block"], .student-checkbox-item:not([style])');
        visibleItems.forEach(item => {
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox) {
                checkbox.checked = false;
            }
        });
        updateSelectedCount();
    }
    
    // Event Listeners
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        filterStudents(this.value);
        clearSearchBtn.style.display = this.value ? 'flex' : 'none';
    });
    
    // Clear search
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterStudents('');
        clearSearchBtn.style.display = 'none';
        searchInput.focus();
    });
    
    // Clear search results button
    clearSearchResultsBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterStudents('');
        clearSearchBtn.style.display = 'none';
        searchInput.focus();
    });
    
    // Select all visible
    selectAllBtn.addEventListener('click', selectAllVisible);
    
    // Deselect all visible
    deselectAllBtn.addEventListener('click', deselectAllVisible);
    
    // Update count on checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + A to select all visible
        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
            e.preventDefault();
            selectAllVisible();
        }
        
        // Escape to clear search
        if (e.key === 'Escape' && searchInput.value) {
            e.preventDefault();
            searchInput.value = '';
            filterStudents('');
            clearSearchBtn.style.display = 'none';
        }
    });
    
    // Initialize
    updateSelectedCount();
    totalCountElem.textContent = studentItems.length;
});
</script>
@endsection

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    alert('{{ session('success') }}');
});
</script>
@endif
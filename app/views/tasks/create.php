<?php 
require_once APP_ROOT . '/app/views/layouts/header.php'; 
?>

<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --accent-color: #1abc9c;
        --dark-color: #1a252f;
        --light-color: #ecf0f1;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --transition-speed: 0.3s;
    }

    /* Form Container */
    .form-container {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        background: linear-gradient(135deg, 
            rgba(26, 37, 47, 0.95) 0%, 
            rgba(44, 62, 80, 0.95) 100%);
    }

    /* Background Animation */
    .form-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 80%, rgba(52, 152, 219, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(26, 188, 156, 0.05) 0%, transparent 50%);
        z-index: -1;
        animation: pulse 15s ease-in-out infinite alternate;
    }

    @keyframes pulse {
        0% { opacity: 0.3; }
        100% { opacity: 0.6; }
    }

    /* Glass Card */
    .card-3d {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px) saturate(180%);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 
            0 25px 45px rgba(0, 0, 0, 0.25),
            inset 0 0 0 1px rgba(255, 255, 255, 0.1);
        transition: all var(--transition-speed) ease;
        position: relative;
        overflow: hidden;
        animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-3d::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
        z-index: 2;
    }

    .card-3d:hover {
        transform: translateY(-5px);
        box-shadow: 
            0 35px 60px rgba(0, 0, 0, 0.3),
            inset 0 0 0 1px rgba(255, 255, 255, 0.15);
    }

    /* Form Elements */
    .form-control, .form-select, .form-textarea {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        color: var(--light-color);
        padding: 14px 16px;
        transition: all var(--transition-speed) ease;
        font-weight: 400;
    }

    .form-control:focus, .form-select:focus, .form-textarea:focus {
        background: rgba(255, 255, 255, 0.12);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.2);
        color: var(--light-color);
        outline: none;
    }

    .form-control::placeholder, .form-select::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .form-label {
        font-weight: 600;
        color: var(--light-color);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Textarea */
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    /* Skills Input */
    .skills-container {
        position: relative;
    }

    .skills-input {
        position: relative;
    }

    .skill-tag {
        display: inline-flex;
        align-items: center;
        background: rgba(26, 188, 156, 0.2);
        color: var(--light-color);
        padding: 6px 12px;
        border-radius: 20px;
        margin: 4px;
        font-size: 0.9rem;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }

    .skill-tag .remove-skill {
        margin-left: 6px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity var(--transition-speed) ease;
    }

    .skill-tag .remove-skill:hover {
        opacity: 1;
    }

    /* Date Input */
    .date-picker {
        position: relative;
    }

    .date-picker::-webkit-calendar-picker-indicator {
        filter: invert(1);
        opacity: 0.7;
        cursor: pointer;
        padding: 5px;
    }

    /* Form Header */
    .form-header {
        text-align: center;
        margin-bottom: 30px;
        position: relative;
    }

    .form-header h3 {
        background: linear-gradient(90deg, var(--light-color), var(--accent-color));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .form-subtitle {
        color: rgba(255, 255, 255, 0.6);
        font-size: 1rem;
    }

    /* Form Steps */
    .form-step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
        padding: 0 20px;
    }

    .form-step-indicator::before {
        content: '';
        position: absolute;
        top: 14px;
        left: 10%;
        right: 10%;
        height: 2px;
        background: rgba(255, 255, 255, 0.1);
        z-index: 1;
    }

    .step {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.5);
        position: relative;
        z-index: 2;
        transition: all var(--transition-speed) ease;
    }

    .step.active {
        background: var(--accent-color);
        color: white;
        transform: scale(1.1);
        box-shadow: 0 0 0 4px rgba(26, 188, 156, 0.2);
    }

    .step.completed {
        background: var(--success-color);
        color: white;
    }

    .step-label {
        position: absolute;
        top: 35px;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* Form Sections */
    .form-section {
        display: none;
        animation: fadeIn 0.5s ease-out;
    }

    .form-section.active {
        display: block;
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        border: none;
        border-radius: 12px;
        padding: 14px 32px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all var(--transition-speed) ease;
        position: relative;
        overflow: hidden;
        min-width: 140px;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(26, 188, 156, 0.3);
    }

    .btn-primary:active {
        transform: translateY(-1px);
    }

    .btn-primary::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.7s;
    }

    .btn-primary:hover::after {
        left: 100%;
    }

    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 14px 32px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all var(--transition-speed) ease;
        min-width: 120px;
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
    }

    /* Form Navigation */
    .form-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Progress Bar */
    .progress-bar {
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        margin: 20px 0;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
        border-radius: 2px;
        width: 33%;
        transition: width 0.5s ease;
    }

    /* AI Suggestions */
    .ai-suggestions {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        border-left: 4px solid var(--accent-color);
    }

    .ai-suggestions h6 {
        color: var(--accent-color);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .suggestion-item {
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        margin: 5px 0;
        cursor: pointer;
        transition: background var(--transition-speed) ease;
    }

    .suggestion-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Character Counter */
    .char-counter {
        text-align: right;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
        margin-top: 5px;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 25px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-3d {
            margin: 10px;
            padding: 25px !important;
        }
        
        .form-header h3 {
            font-size: 1.5rem;
        }
        
        .form-navigation {
            flex-direction: column;
            gap: 15px;
        }
        
        .btn-primary, .btn-outline-light {
            width: 100%;
            justify-content: center;
        }
        
        .form-step-indicator::before {
            left: 5%;
            right: 5%;
        }
    }

    /* Loading Animation */
    .spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .btn-primary.loading .spinner {
        display: inline-block;
    }
</style>

<section class="form-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-8">
                <div class="card card-3d">
                    <div class="card-body p-4 p-md-5">
                        <!-- Form Header -->
                        <div class="form-header">
                            <h3>
                                <i class="bi bi-plus-circle me-2"></i>
                                Create New Internship Task
                            </h3>
                            <p class="form-subtitle">Define a clear, actionable task for your interns</p>
                        </div>

                        <!-- Progress Indicator -->
                        <div class="progress-bar">
                            <div class="progress-fill" id="formProgress"></div>
                        </div>

                        <!-- Multi-Step Form -->
                        <form action="<?php echo BASE_URL; ?>/tasks/store" method="POST" id="taskForm">
                            
                            <!-- Step 1: Basic Information -->
                            <div class="form-section active" id="step1">
                                <h5 class="fw-bold mb-4 text-white">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Task Overview
                                </h5>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-card-heading"></i> Task Title
                                    </label>
                                    <input type="text" name="title" class="form-control" required 
                                           placeholder="e.g., Build a RESTful API for User Management"
                                           oninput="updateCharacterCount(this, 'titleCounter')"
                                           maxlength="100">
                                    <div class="char-counter">
                                        <span id="titleCounter">0</span>/100 characters
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-text-paragraph"></i> Description
                                    </label>
                                    <textarea name="description" class="form-control form-textarea" rows="5" required
                                              placeholder="Provide detailed instructions and objectives for this task..."
                                              oninput="updateCharacterCount(this, 'descCounter')"
                                              maxlength="1000"></textarea>
                                    <div class="char-counter">
                                        <span id="descCounter">0</span>/1000 characters
                                    </div>
                                </div>

                                <!-- AI Suggestions -->
                                <div class="ai-suggestions">
                                    <h6>
                                        <i class="bi bi-lightbulb"></i>
                                        AI Suggestions
                                    </h6>
                                    <div class="suggestion-item" onclick="applySuggestion('Develop responsive dashboard UI')">
                                        "Develop responsive dashboard UI"
                                    </div>
                                    <div class="suggestion-item" onclick="applySuggestion('Implement user authentication system')">
                                        "Implement user authentication system"
                                    </div>
                                    <div class="suggestion-item" onclick="applySuggestion('Create database migration scripts')">
                                        "Create database migration scripts"
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Requirements -->
                            <div class="form-section" id="step2">
                                <h5 class="fw-bold mb-4 text-white">
                                    <i class="bi bi-list-check me-2"></i>
                                    Requirements & Skills
                                </h5>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-tools"></i> Required Skills
                                    </label>
                                    <div class="skills-container">
                                        <div id="skillsDisplay" class="mb-3"></div>
                                        <input type="text" id="skillsInput" class="form-control" 
                                               placeholder="Type a skill and press Enter (e.g., PHP, MySQL, Bootstrap)">
                                        <div class="form-text text-white-50 mt-2">
                                            Press Enter or comma to add skills
                                        </div>
                                        <input type="hidden" name="required_skills" id="requiredSkills">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-calendar-check"></i> Deadline
                                            </label>
                                            <input type="date" name="deadline" class="form-control date-picker" required
                                                   min="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="bi bi-clock-history"></i> Estimated Time (hours)
                                            </label>
                                            <input type="number" name="estimated_time" class="form-control" 
                                                   min="1" max="200" placeholder="e.g., 40" value="40">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-bar-chart"></i> Difficulty Level
                                    </label>
                                    <select name="difficulty" class="form-select" required>
                                        <option value="">Select Difficulty</option>
                                        <option value="beginner">Beginner</option>
                                        <option value="intermediate" selected>Intermediate</option>
                                        <option value="advanced">Advanced</option>
                                        <option value="expert">Expert</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Step 3: Additional Details -->
                            <div class="form-section" id="step3">
                                <h5 class="fw-bold mb-4 text-white">
                                    <i class="bi bi-gear me-2"></i>
                                    Additional Details
                                </h5>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-link-45deg"></i> Resources & References
                                    </label>
                                    <textarea name="resources" class="form-control form-textarea" rows="4"
                                              placeholder="Add helpful links, documentation, or reference materials..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-check2-circle"></i> Success Criteria
                                    </label>
                                    <textarea name="success_criteria" class="form-control form-textarea" rows="4"
                                              placeholder="Define clear success metrics and acceptance criteria..."
                                              required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-tags"></i> Category
                                    </label>
                                    <select name="category" class="form-select">
                                        <option value="">Select Category</option>
                                        <option value="backend">Backend Development</option>
                                        <option value="frontend">Frontend Development</option>
                                        <option value="database">Database</option>
                                        <option value="devops">DevOps</option>
                                        <option value="testing">Testing</option>
                                        <option value="documentation">Documentation</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Form Navigation -->
                            <div class="form-navigation">
                                <button type="button" class="btn btn-outline-light" id="prevBtn" onclick="prevStep()" style="display: none;">
                                    <i class="bi bi-arrow-left me-2"></i> Previous
                                </button>
                                
                                <div class="ms-auto">
                                    <button type="button" class="btn btn-outline-light me-2" onclick="window.location.href='<?php echo BASE_URL; ?>/dashboard'">
                                        Cancel
                                    </button>
                                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">
                                        Continue <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                                        <span class="spinner"></span>
                                        <i class="bi bi-check-circle me-2"></i> Create Task
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let currentStep = 1;
    const totalSteps = 3;
    const skills = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize skills input
        const skillsInput = document.getElementById('skillsInput');
        const requiredSkills = document.getElementById('requiredSkills');
        
        skillsInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const skill = this.value.trim();
                if (skill && !skills.includes(skill.toLowerCase())) {
                    skills.push(skill.toLowerCase());
                    updateSkillsDisplay();
                    this.value = '';
                }
            }
        });

        // Initialize date picker with minimum date
        const dateInput = document.querySelector('input[type="date"]');
        dateInput.min = new Date().toISOString().split('T')[0];
        
        // Set default deadline to 2 weeks from now
        const twoWeeksLater = new Date();
        twoWeeksLater.setDate(twoWeeksLater.getDate() + 14);
        dateInput.value = twoWeeksLater.toISOString().split('T')[0];

        // Update progress bar
        updateProgressBar();
        
        // Initialize character counters
        document.querySelectorAll('input, textarea').forEach(input => {
            if (input.hasAttribute('maxlength')) {
                const counterId = input.name + 'Counter';
                updateCharacterCount(input, counterId);
            }
        });
    });

    function nextStep() {
        if (!validateStep(currentStep)) return;
        
        // Hide current step
        document.getElementById(`step${currentStep}`).classList.remove('active');
        
        // Show next step
        currentStep++;
        document.getElementById(`step${currentStep}`).classList.add('active');
        
        // Update buttons
        updateNavigationButtons();
        updateProgressBar();
        
        // Smooth scroll to top
        document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
    }

    function prevStep() {
        // Hide current step
        document.getElementById(`step${currentStep}`).classList.remove('active');
        
        // Show previous step
        currentStep--;
        document.getElementById(`step${currentStep}`).classList.add('active');
        
        // Update buttons
        updateNavigationButtons();
        updateProgressBar();
    }

    function validateStep(step) {
        let isValid = true;
        const inputs = document.querySelectorAll(`#step${step} input[required], #step${step} textarea[required], #step${step} select[required]`);
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
                
                // Add shake animation
                input.style.animation = 'shake 0.5s';
                setTimeout(() => {
                    input.style.animation = '';
                }, 500);
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        // Special validation for step 2 skills
        if (step === 2 && skills.length === 0) {
            document.getElementById('skillsInput').classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        if (currentStep === 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'inline-block';
            submitBtn.style.display = 'none';
        } else if (currentStep === totalSteps) {
            prevBtn.style.display = 'inline-block';
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-block';
        } else {
            prevBtn.style.display = 'inline-block';
            nextBtn.style.display = 'inline-block';
            submitBtn.style.display = 'none';
        }
    }

    function updateProgressBar() {
        const progress = (currentStep / totalSteps) * 100;
        document.getElementById('formProgress').style.width = progress + '%';
    }

    function updateSkillsDisplay() {
        const display = document.getElementById('skillsDisplay');
        const hiddenInput = document.getElementById('requiredSkills');
        
        display.innerHTML = skills.map(skill => `
            <span class="skill-tag">
                <i class="bi bi-tag-fill me-1"></i>
                ${skill}
                <span class="remove-skill" onclick="removeSkill('${skill}')">
                    <i class="bi bi-x"></i>
                </span>
            </span>
        `).join('');
        
        hiddenInput.value = skills.join(',');
    }

    function removeSkill(skill) {
        const index = skills.indexOf(skill);
        if (index > -1) {
            skills.splice(index, 1);
            updateSkillsDisplay();
        }
    }

    function applySuggestion(suggestion) {
        const titleInput = document.querySelector('input[name="title"]');
        titleInput.value = suggestion;
        titleInput.focus();
        
        // Add visual feedback
        titleInput.style.borderColor = 'var(--accent-color)';
        titleInput.style.boxShadow = '0 0 0 3px rgba(26, 188, 156, 0.3)';
        setTimeout(() => {
            titleInput.style.borderColor = '';
            titleInput.style.boxShadow = '';
        }, 1000);
    }

    function updateCharacterCount(input, counterId) {
        const counter = document.getElementById(counterId);
        if (counter) {
            counter.textContent = input.value.length;
            
            // Change color based on usage
            const maxLength = parseInt(input.getAttribute('maxlength'));
            const usage = (input.value.length / maxLength) * 100;
            
            if (usage > 90) {
                counter.style.color = 'var(--danger-color)';
            } else if (usage > 70) {
                counter.style.color = 'var(--warning-color)';
            } else {
                counter.style.color = 'rgba(255, 255, 255, 0.5)';
            }
        }
    }

    // Form submission
    document.getElementById('taskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all steps
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                // Go to first invalid step
                while (currentStep > i) prevStep();
                return;
            }
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        // Add skills to form data
        const skillsInput = document.createElement('input');
        skillsInput.type = 'hidden';
        skillsInput.name = 'required_skills';
        skillsInput.value = skills.join(',');
        this.appendChild(skillsInput);
        
        // Submit form
        setTimeout(() => {
            this.submit();
        }, 1500);
    });

    // Add shake animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .is-invalid {
            border-color: var(--danger-color) !important;
            animation: shake 0.5s;
        }
    `;
    document.head.appendChild(style);
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
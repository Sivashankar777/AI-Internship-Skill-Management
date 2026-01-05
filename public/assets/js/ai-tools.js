// AI Tools JavaScript
class AITools {
    constructor() {
        this.apiBase = '/api/ai';
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initSkillGapAnalysis();
        this.initResumeReview();
        this.initInterviewQuestions();
        this.initTaskFeedback();
    }
    
    bindEvents() {
        // Skill gap analysis
        const skillBtn = document.getElementById('btnAnalyzeSkills');
        if (skillBtn) {
            skillBtn.addEventListener('click', () => this.analyzeSkillGap());
        }
        
        // Resume review
        const resumeBtn = document.getElementById('btnReviewResume');
        if (resumeBtn) {
            resumeBtn.addEventListener('click', () => this.reviewResume());
        }
        
        // Interview questions
        const interviewBtn = document.getElementById('btnGenerateQuestions');
        if (interviewBtn) {
            interviewBtn.addEventListener('click', () => this.generateInterviewQuestions());
        }
        
        // Task feedback
        const feedbackBtn = document.getElementById('btnGetFeedback');
        if (feedbackBtn) {
            feedbackBtn.addEventListener('click', () => this.getTaskFeedback());
        }
    }
    
    async analyzeSkillGap() {
        const userSkillsInput = document.getElementById('userSkillsInput');
        const targetSkillsInput = document.getElementById('targetSkillsInput');
        const resultDiv = document.getElementById('aiSkillResult');
        const resultContent = document.getElementById('aiSkillContent');
        const btn = document.getElementById('btnAnalyzeSkills');
        
        if (!userSkillsInput || !userSkillsInput.value.trim()) {
            this.showError('Please enter your current skills');
            return;
        }
        
        const userSkills = userSkillsInput.value.split(',').map(s => s.trim()).filter(s => s);
        const targetSkills = targetSkillsInput ? targetSkillsInput.value.split(',').map(s => s.trim()).filter(s => s) : [];
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Analyzing...';
        
        try {
            const response = await fetch(`${this.apiBase}/analyze`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    type: 'skill_gap',
                    user_skills: userSkills,
                    target_skills: targetSkills
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success' && data.data) {
                resultContent.innerHTML = this.formatAIResponse(data.data);
                resultDiv.classList.remove('d-none');
                
                // Animate result appearance
                gsap.from(resultDiv, {
                    duration: 0.5,
                    y: 20,
                    opacity: 0,
                    ease: 'power3.out'
                });
            } else {
                this.showError(data.data || 'Analysis failed. Please try again.');
            }
        } catch (error) {
            console.error('Skill gap analysis error:', error);
            this.showError('Failed to connect to AI service. Please try again later.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Analyze Skill Gap';
        }
    }
    
    async reviewResume() {
        const resumeText = document.getElementById('resumeTextInput');
        const resultDiv = document.getElementById('aiResumeResult');
        const resultContent = document.getElementById('aiResumeContent');
        const btn = document.getElementById('btnReviewResume');
        
        if (!resumeText || !resumeText.value.trim()) {
            this.showError('Please paste your resume text');
            return;
        }
        
        if (resumeText.value.trim().length < 50) {
            this.showError('Resume text is too short. Minimum 50 characters required.');
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Analyzing...';
        
        try {
            const response = await fetch(`${this.apiBase}/review-resume`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    resume_text: resumeText.value,
                    target_role: document.getElementById('targetRoleInput')?.value || ''
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success' && data.data) {
                resultContent.innerHTML = this.formatAIResponse(data.data);
                resultDiv.classList.remove('d-none');
                
                // Add metrics if available
                if (data.metrics) {
                    this.showResumeMetrics(data.metrics);
                }
                
                // Animate result appearance
                gsap.from(resultDiv, {
                    duration: 0.5,
                    y: 20,
                    opacity: 0,
                    ease: 'power3.out'
                });
            } else {
                this.showError(data.data || 'Resume review failed. Please try again.');
            }
        } catch (error) {
            console.error('Resume review error:', error);
            this.showError('Failed to connect to AI service. Please try again later.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Review Resume';
        }
    }
    
    async generateInterviewQuestions() {
        const skillsInput = document.getElementById('interviewSkillsInput');
        const difficultySelect = document.getElementById('questionDifficulty');
        const resultDiv = document.getElementById('aiInterviewResult');
        const resultContent = document.getElementById('aiInterviewContent');
        const btn = document.getElementById('btnGenerateQuestions');
        
        if (!skillsInput || !skillsInput.value.trim()) {
            this.showError('Please enter skills for interview questions');
            return;
        }
        
        const skills = skillsInput.value.split(',').map(s => s.trim()).filter(s => s);
        const difficulty = difficultySelect ? difficultySelect.value : 'intermediate';
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
        
        try {
            const response = await fetch(`${this.apiBase}/interview-questions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    skills: skills,
                    difficulty: difficulty,
                    count: 10
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success' && data.data) {
                resultContent.innerHTML = this.formatAIResponse(data.data);
                resultDiv.classList.remove('d-none');
                
                // Add copy functionality
                this.addCopyButton(resultContent);
                
                // Animate result appearance
                gsap.from(resultDiv, {
                    duration: 0.5,
                    y: 20,
                    opacity: 0,
                    ease: 'power3.out'
                });
            } else {
                this.showError(data.data || 'Failed to generate questions. Please try again.');
            }
        } catch (error) {
            console.error('Interview questions error:', error);
            this.showError('Failed to connect to AI service. Please try again later.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Generate Questions';
        }
    }
    
    async getTaskFeedback() {
        const taskInput = document.getElementById('taskDescriptionInput');
        const submissionInput = document.getElementById('taskSubmissionInput');
        const resultDiv = document.getElementById('aiFeedbackResult');
        const resultContent = document.getElementById('aiFeedbackContent');
        const btn = document.getElementById('btnGetFeedback');
        
        if (!taskInput || !taskInput.value.trim() || !submissionInput || !submissionInput.value.trim()) {
            this.showError('Please provide both task description and submission');
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Analyzing...';
        
        try {
            const response = await fetch(`${this.apiBase}/task-feedback`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    task_description: taskInput.value,
                    submission: submissionInput.value
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success' && data.data) {
                resultContent.innerHTML = this.formatAIResponse(data.data);
                resultDiv.classList.remove('d-none');
                
                // Add grade if available
                if (data.grade) {
                    this.showTaskGrade(data.grade);
                }
                
                // Animate result appearance
                gsap.from(resultDiv, {
                    duration: 0.5,
                    y: 20,
                    opacity: 0,
                    ease: 'power3.out'
                });
            } else {
                this.showError(data.data || 'Feedback generation failed. Please try again.');
            }
        } catch (error) {
            console.error('Task feedback error:', error);
            this.showError('Failed to connect to AI service. Please try again later.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Get Feedback';
        }
    }
    
    formatAIResponse(text) {
        // Convert markdown-like formatting to HTML
        let html = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code>$1</code>')
            .replace(/\n\s*\n/g, '</p><p>')
            .replace(/\n/g, '<br>');
        
        // Convert numbered lists
        html = html.replace(/^\d+\.\s+(.+)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>)/gs, '<ol>$1</ol>');
        
        // Convert bullet points
        html = html.replace(/^[-*]\s+(.+)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>');
        
        // Convert headings
        html = html.replace(/^#\s+(.+)$/gm, '<h4>$1</h4>');
        html = html.replace(/^##\s+(.+)$/gm, '<h5>$1</h5>');
        
        return `<div class="ai-response">${html}</div>`;
    }
    
    showResumeMetrics(metrics) {
        const metricsDiv = document.createElement('div');
        metricsDiv.className = 'resume-metrics mt-3';
        metricsDiv.innerHTML = `
            <div class="card glass-card">
                <div class="card-body">
                    <h6 class="mb-3">Resume Metrics</h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="metric-item">
                                <span class="metric-label">Word Count</span>
                                <span class="metric-value">${metrics.word_count}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-item">
                                <span class="metric-label">Readability</span>
                                <span class="metric-value">${metrics.readability_score}/10</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const resultDiv = document.getElementById('aiResumeResult');
        resultDiv.querySelector('.ai-response').after(metricsDiv);
    }
    
    showTaskGrade(grade) {
        const gradeDiv = document.createElement('div');
        gradeDiv.className = 'task-grade mt-3';
        gradeDiv.innerHTML = `
            <div class="card glass-card">
                <div class="card-body text-center">
                    <h6 class="mb-2">Overall Grade</h6>
                    <div class="grade-circle ${grade.toLowerCase()}">
                        ${grade}
                    </div>
                </div>
            </div>
        `;
        
        const resultDiv = document.getElementById('aiFeedbackResult');
        resultDiv.querySelector('.ai-response').after(gradeDiv);
    }
    
    addCopyButton(element) {
        const copyBtn = document.createElement('button');
        copyBtn.className = 'btn btn-sm btn-outline-light copy-btn mt-3';
        copyBtn.innerHTML = '<i class="bi bi-clipboard me-2"></i>Copy to Clipboard';
        copyBtn.addEventListener('click', () => this.copyToClipboard(element));
        
        element.parentNode.insertBefore(copyBtn, element.nextSibling);
    }
    
    async copyToClipboard(element) {
        const text = element.innerText || element.textContent;
        
        try {
            await navigator.clipboard.writeText(text);
            
            // Show success message
            const originalText = element.innerHTML;
            element.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-2"></i>Copied to clipboard!</span>';
            
            setTimeout(() => {
                element.innerHTML = originalText;
            }, 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
            this.showError('Failed to copy to clipboard');
        }
    }
    
    showError(message) {
        // Use SweetAlert2 for beautiful error messages
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }
    
    showSuccess(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    }
    
    initSkillGapAnalysis() {
        const skillInput = document.getElementById('userSkillsInput');
        if (skillInput) {
            // Add tag input functionality
            new Tagify(skillInput, {
                whitelist: ['PHP', 'JavaScript', 'Python', 'React', 'Node.js', 'MySQL', 'MongoDB', 'Docker', 'AWS'],
                dropdown: {
                    enabled: 1,
                    maxItems: 10
                }
            });
        }
    }
    
    initResumeReview() {
        const resumeText = document.getElementById('resumeTextInput');
        if (resumeText) {
            // Add character counter
            const counter = document.createElement('div');
            counter.className = 'char-counter text-end small text-muted mt-1';
            resumeText.parentNode.appendChild(counter);
            
            resumeText.addEventListener('input', function() {
                const count = this.value.length;
                counter.textContent = `${count}/5000 characters`;
                
                if (count > 4500) {
                    counter.classList.add('text-warning');
                } else {
                    counter.classList.remove('text-warning');
                }
            });
            
            // Trigger initial count
            resumeText.dispatchEvent(new Event('input'));
        }
    }
}

// Initialize AI Tools when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.AITools = new AITools();
});

// Tagify wrapper for skill inputs
class Tagify {
    constructor(input, options) {
        this.input = input;
        this.options = options || {};
        this.tags = [];
        this.init();
    }
    
    init() {
        // Create tags container
        this.container = document.createElement('div');
        this.container.className = 'tagify-container';
        this.input.parentNode.insertBefore(this.container, this.input);
        this.container.appendChild(this.input);
        
        // Style the container
        this.container.style.cssText = `
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 8px;
            background: rgba(255, 255, 255, 0.05);
            min-height: 44px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 5px;
        `;
        
        // Style the input
        this.input.style.cssText = `
            border: none;
            background: transparent;
            flex: 1;
            min-width: 100px;
            outline: none;
            color: var(--text-light);
        `;
        
        // Load existing tags
        if (this.input.value) {
            this.tags = this.input.value.split(',').map(t => t.trim()).filter(t => t);
            this.renderTags();
        }
        
        // Add event listeners
        this.input.addEventListener('keydown', this.handleKeydown.bind(this));
        this.input.addEventListener('blur', this.handleBlur.bind(this));
        
        // Create dropdown if whitelist exists
        if (this.options.whitelist) {
            this.createDropdown();
        }
    }
    
    handleKeydown(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            this.addTag(this.input.value.trim());
            this.input.value = '';
        } else if (e.key === 'Backspace' && this.input.value === '' && this.tags.length > 0) {
            this.removeTag(this.tags.length - 1);
        }
    }
    
    handleBlur() {
        if (this.input.value.trim()) {
            this.addTag(this.input.value.trim());
            this.input.value = '';
        }
    }
    
    addTag(text) {
        if (!text || this.tags.includes(text)) return;
        
        this.tags.push(text);
        this.renderTags();
        this.updateInput();
    }
    
    removeTag(index) {
        this.tags.splice(index, 1);
        this.renderTags();
        this.updateInput();
    }
    
    renderTags() {
        // Clear existing tags
        const existingTags = this.container.querySelectorAll('.tag');
        existingTags.forEach(tag => tag.remove());
        
        // Create new tags
        this.tags.forEach((tag, index) => {
            const tagElement = document.createElement('span');
            tagElement.className = 'tag';
            tagElement.innerHTML = `
                ${tag}
                <button type="button" class="tag-remove">&times;</button>
            `;
            
            tagElement.style.cssText = `
                background: var(--gradient-primary);
                color: white;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.9rem;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            `;
            
            const removeBtn = tagElement.querySelector('.tag-remove');
            removeBtn.addEventListener('click', () => this.removeTag(index));
            removeBtn.style.cssText = `
                background: transparent;
                border: none;
                color: white;
                cursor: pointer;
                font-size: 1.2rem;
                line-height: 1;
            `;
            
            this.container.insertBefore(tagElement, this.input);
        });
    }
    
    updateInput() {
        this.input.value = this.tags.join(',');
    }
    
    createDropdown() {
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'tagify-dropdown';
        this.dropdown.style.cssText = `
            position: absolute;
            background: var(--bg-light);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            max-height: 200px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
        `;
        
        this.container.appendChild(this.dropdown);
        
        this.input.addEventListener('input', this.updateDropdown.bind(this));
        this.input.addEventListener('focus', this.showDropdown.bind(this));
    }
    
    updateDropdown() {
        const search = this.input.value.toLowerCase();
        const matches = this.options.whitelist.filter(item => 
            item.toLowerCase().includes(search) && !this.tags.includes(item)
        ).slice(0, this.options.dropdown?.maxItems || 5);
        
        if (matches.length > 0) {
            this.dropdown.innerHTML = matches.map(item => `
                <div class="dropdown-item">${item}</div>
            `).join('');
            
            this.showDropdown();
        } else {
            this.hideDropdown();
        }
    }
    
    showDropdown() {
        if (this.dropdown.innerHTML) {
            this.dropdown.style.display = 'block';
        }
    }
    
    hideDropdown() {
        this.dropdown.style.display = 'none';
    }
}
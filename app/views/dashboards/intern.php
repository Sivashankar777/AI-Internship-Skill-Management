<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar / Profile Summary -->
        <div class="col-md-4">
            <div class="card card-3d p-4 text-center mb-4">
                <div class="mb-3">
                    <div class="rounded-circle bg-primary d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px; font-size: 2em;"><?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?></div>
                </div>
                <h4 class="fw-bold"><?php echo $_SESSION['username'] ?? 'Intern'; ?></h4>
                <p class="text-white-50">Full Stack Developer Intern</p>
                <hr class="border-secondary">
                <div class="d-flex justify-content-around">
                    <div>
                        <h5 class="fw-bold mb-0">85%</h5>
                        <small class="text-white-50">Readiness</small>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">12</h5>
                        <small class="text-white-50">Tasks Done</small>
                    </div>
                </div>
            </div>

            <div class="card card-3d p-4 mb-4">
                <h5 class="fw-bold mb-3">AI Learning Roadmap</h5>
                <div class="timeline border-start border-secondary ps-3 ms-2">
                    <div class="mb-4 position-relative">
                        <span class="position-absolute start-0 top-0 translate-middle-x bg-primary rounded-circle" style="width: 10px; height: 10px; left: -1em;"></span>
                        <h6 class="mb-1 text-primary">Week 1: Foundations</h6>
                        <small class="d-block text-white-50">Completed</small>
                    </div>
                    <div class="mb-4 position-relative">
                        <span class="position-absolute start-0 top-0 translate-middle-x bg-secondary rounded-circle" style="width: 10px; height: 10px; left: -1em;"></span>
                        <h6 class="mb-1 text-white">Week 2: Advanced PHP</h6>
                        <small class="d-block text-white-50">In Progress</small>
                    </div>
                    <div class="mb-0 position-relative">
                        <span class="position-absolute start-0 top-0 translate-middle-x bg-secondary rounded-circle" style="width: 10px; height: 10px; left: -1em;"></span>
                        <h6 class="mb-1 text-white-50">Week 3: System Design</h6>
                        <small class="d-block text-white-50">Locked</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Task Area -->
        <div class="col-md-8">
            <div class="card card-3d p-4 mb-4">
                <h5 class="fw-bold mb-4">Current Assignments</h5>
                <div class="card bg-transparent border border-secondary mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold">Build REST API for User Auth</h5>
                            <span class="badge bg-warning text-dark">Due Tomorrow</span>
                        </div>
                        <p class="card-text text-white-50">Create endpoints for login, register, and profile management using JWT...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span class="badge bg-secondary me-2">PHP</span>
                                <span class="badge bg-secondary">API</span>
                            </div>
                            <button class="btn btn-outline-primary rounded-pill btn-sm">Submit Work</button>
                        </div>
                    </div>
                </div>
                 <div class="card bg-transparent border border-secondary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold">Design Database Schema</h5>
                            <span class="badge bg-success">Completed</span>
                        </div>
                        <p class="card-text text-white-50">Normalized schema for users, products, and orders.</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                             <div>
                                <span class="badge bg-secondary me-2">MySQL</span>
                            </div>
                            <button class="btn btn-success rounded-pill btn-sm" disabled>Graded: 95/100</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Tools -->
            <div class="row g-3">
                <div class="col-6">
                    <div class="card card-3d p-3 text-center h-100 cursor-pointer hover-lift" data-bs-toggle="modal" data-bs-target="#skillGapModal">
                        <h3 class="mb-2">🧠</h3>
                        <h6 class="fw-bold">Skill Gap Analyzer</h6>
                        <p class="small text-white-50 mb-0">Check what you're missing</p>
                    </div>
                </div>
                 <div class="col-6">
                    <div class="card card-3d p-3 text-center h-100 cursor-pointer hover-lift" data-bs-toggle="modal" data-bs-target="#resumeModal">
                        <h3 class="mb-2">📄</h3>
                        <h6 class="fw-bold">Resume Improver</h6>
                        <p class="small text-white-50 mb-0">Get ATS-friendly feedback</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Modals -->
<div class="modal fade" id="skillGapModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal text-white" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">🧠 Skill Gap Analyzer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Analyze your current skills against the internship requirements.</p>
                <div id="skillGapContent">
                    <div class="mb-3">
                        <label class="form-label">Your Skills (comma separated)</label>
                        <input type="text" class="form-control" id="userSkillsInput" value="HTML, CSS, Basic JavaScript">
                    </div>
                    <button class="btn btn-primary w-100" id="btnAnalyzeSkills">Analyze Now</button>
                    <div id="aiSkillResult" class="mt-3 d-none">
                        <hr class="border-secondary">
                        <h6>AI Feedback:</h6>
                        <div class="p-2 border border-primary rounded bg-dark" style="font-size: 0.9em; white-space: pre-line;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="resumeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal text-white" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">📄 Resume Improver</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Paste your resume content for instant AI feedback.</p>
                <div id="resumeContent">
                    <textarea class="form-control mb-3" id="resumeTextInput" rows="5" placeholder="Paste resume text here..."></textarea>
                    <button class="btn btn-primary w-100" id="btnReviewResume">Review Resume</button>
                     <div id="aiResumeResult" class="mt-3 d-none">
                        <hr class="border-secondary">
                        <h6>AI Feedback:</h6>
                        <div class="p-2 border border-primary rounded bg-dark" style="font-size: 0.9em; white-space: pre-line;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>

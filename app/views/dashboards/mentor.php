<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">Mentor Dashboard</h2>
            <p class="text-white-50">Manage internships and review submissions.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?php echo BASE_URL; ?>/tasks/create" class="btn btn-primary rounded-pill">+ Create New Task</a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Review Pipeline -->
        <div class="col-md-12">
            <div class="card card-3d p-4">
                <h5 class="fw-bold mb-4">Submission Review Pipeline</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-hover" style="background: transparent;">
                        <thead>
                            <tr>
                                <th class="bg-transparent border-secondary">Intern</th>
                                <th class="bg-transparent border-secondary">Task</th>
                                <th class="bg-transparent border-secondary">Submitted</th>
                                <th class="bg-transparent border-secondary">Status</th>
                                <th class="bg-transparent border-secondary">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="bg-transparent border-secondary">Sarah Connor</td>
                                <td class="bg-transparent border-secondary">API Integration</td>
                                <td class="bg-transparent border-secondary">2 hrs ago</td>
                                <td class="bg-transparent border-secondary"><span class="badge bg-warning text-dark">Pending Review</span></td>
                                <td class="bg-transparent border-secondary"><button class="btn btn-sm btn-outline-light rounded-pill">Review</button></td>
                            </tr>
                            <tr>
                                <td class="bg-transparent border-secondary">John Smith</td>
                                <td class="bg-transparent border-secondary">Database Schema</td>
                                <td class="bg-transparent border-secondary">5 hrs ago</td>
                                <td class="bg-transparent border-secondary"><span class="badge bg-success">Approved</span></td>
                                <td class="bg-transparent border-secondary"><button class="btn btn-sm btn-outline-light rounded-pill">View</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Active Tasks -->
        <div class="col-md-6">
            <div class="card card-3d p-4 h-100">
                <h5 class="fw-bold mb-4">Active Tasks</h5>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action bg-transparent text-white border-secondary">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Build Login Page</h6>
                            <small>3 days left</small>
                        </div>
                        <small class="text-white-50">Frontend / PHP</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action bg-transparent text-white border-secondary">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Setup AWS Server</h6>
                            <small class="text-danger">Expire Today</small>
                        </div>
                        <small class="text-white-50">DevOps</small>
                    </a>
                </div>
            </div>
        </div>

        <!-- AI Assistant -->
        <div class="col-md-6">
            <div class="card card-3d p-4 h-100">
                <h5 class="fw-bold mb-4">AI Insight Generator</h5>
                <textarea class="form-control mb-3" rows="3" placeholder="Generate feedback for a submission..."></textarea>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-white-50">Powered by OpenAI</small>
                    <button class="btn btn-primary btn-sm rounded-pill">Generate Insight</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>

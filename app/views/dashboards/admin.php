<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Admin Dashboard</h2>
        <span class="badge bg-primary rounded-pill px-3 py-2">System Admin</span>
    </div>

    <div class="row g-4">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="card card-3d p-4 h-100">
                <h6 class="text-white-50 mb-2">Total Users</h6>
                <h2 class="fw-bold mb-0">1,240</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-3d p-4 h-100">
                <h6 class="text-white-50 mb-2">Active Internships</h6>
                <h2 class="fw-bold mb-0">45</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-3d p-4 h-100">
                <h6 class="text-white-50 mb-2">AI Queries</h6>
                <h2 class="fw-bold mb-0">8,920</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-3d p-4 h-100">
                <h6 class="text-white-50 mb-2">System Health</h6>
                <h2 class="fw-bold mb-0 text-success">Good</h2>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-8">
            <div class="card card-3d p-4 h-100">
                <h5 class="fw-bold mb-4">User Growth Analytics</h5>
                <!-- Chart Placeholder -->
                <canvas id="userGrowthChart" height="150"></canvas>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-3d p-4 h-100">
                <h5 class="fw-bold mb-4">Recent Logs</h5>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-3 pb-2 border-bottom border-secondary">User 'john_doe' registered <small class="d-block text-secondary">2 mins ago</small></li>
                    <li class="mb-3 pb-2 border-bottom border-secondary">New Task Created #45 <small class="d-block text-secondary">15 mins ago</small></li>
                    <li class="mb-3 pb-2 border-bottom border-secondary">Backup Completed <small class="d-block text-secondary">1 hr ago</small></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple Chart.js Init
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Interns',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#6366f1',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.1)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>

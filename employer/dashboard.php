<?php
session_start();
require_once '../includes/db.php';
include 'employer_layout_top.php';

// Get stats
$employer_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE employer_id = ? AND status = 'active'");
$stmt->execute([$employer_id]);
$active_jobs = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = ?");
$stmt->execute([$employer_id]);
$total_apps = $stmt->fetchColumn();

// Recent jobs
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$employer_id]);
$recent_jobs = $stmt->fetchAll();
?>

<div class="container-fluid animate-up">
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-800 text-dark display-6 mb-1">Employer Dashboard</h2>
            <p class="text-muted fw-medium">Manage your recruitment process effectively.</p>
        </div>
    </div>

<div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card premium-card border-0 p-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
                <div class="position-absolute end-0 bottom-0 opacity-10 mb-n2 me-n2">
                    <i class="fas fa-briefcase fa-8x"></i>
                </div>
                <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur shadow-sm border border-white border-opacity-10">
                        <i class="fas fa-briefcase fa-2x text-white"></i>
                    </div>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-white-50 small fw-800 text-uppercase tracking-wider mb-2">Active Jobs</h6>
                    <h2 class="display-5 fw-900 mb-0"><?php echo $active_jobs; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card premium-card border-0 p-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white;">
                <div class="position-absolute end-0 bottom-0 opacity-10 mb-n2 me-n2">
                    <i class="fas fa-users fa-8x"></i>
                </div>
                <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur shadow-sm border border-white border-opacity-10">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-white-50 small fw-800 text-uppercase tracking-wider mb-2">Total Applications</h6>
                    <h2 class="display-5 fw-900 mb-0"><?php echo $total_apps; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card premium-card p-4 border-0 h-100 d-flex flex-column justify-content-center align-items-center">
                <h5 class="fw-800 text-dark mb-3">Find Talent Faster</h5>
                <a href="post_job.php" class="btn btn-premium btn-glow w-100 py-3">
                    <i class="fas fa-plus-circle me-2"></i> Post New Job
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card premium-card border-0">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 mb-0">Recently Posted Jobs</h5>
                    <a href="manage_jobs.php" class="btn btn-premium btn-sm rounded-pill px-4 fw-800">Manage All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-800 tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Job Title</th>
                                    <th class="py-3">Category</th>
                                    <th class="py-3 text-center">Type</th>
                                    <th class="py-3 text-center">Status</th>
                                    <th class="text-end px-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php if ($recent_jobs): ?>
                                    <?php foreach ($recent_jobs as $job): ?>
                                    <tr>
                                        <td class="px-4 py-4">
                                            <span class="fw-800 text-dark fs-6"><?php echo htmlspecialchars($job['title']); ?></span>
                                            <span class="small text-muted d-block fw-bold"><i class="far fa-calendar-alt me-1 text-accent"></i>Posted <?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
                                        </td>
                                        <td class="py-4 text-dark fw-bold small">
                                            <?php echo htmlspecialchars($job['category']); ?>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-bold">
                                                <?php echo $job['job_type']; ?>
                                            </span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="badge rounded-pill px-4 py-2 fw-800 <?php echo $job['status'] == 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'; ?>">
                                                <?php echo ucfirst($job['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end px-4 py-4">
                                            <a href="view_applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-premium btn-sm px-4 fw-bold shadow-sm">Applications</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div class="bg-light bg-opacity-50 d-inline-block p-4 rounded-circle mb-4">
                                                <i class="fas fa-inbox fa-3x opacity-10"></i>
                                            </div>
                                            <h5 class="fw-800 text-dark">No jobs posted yet.</h5>
                                            <p class="mb-4">Start reaching out to top talent today.</p>
                                            <a href="post_job.php" class="btn btn-premium btn-glow px-5">Post Your First Job</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'employer_layout_bottom.php'; ?>

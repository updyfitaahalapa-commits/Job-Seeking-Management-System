<?php
session_start();
require_once '../includes/db.php';
include 'seeker_layout_top.php';

// Get some stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE seeker_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_applications = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT cv_path FROM profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cv_uploaded = !empty($stmt->fetchColumn());

// Get recent applications
$stmt = $pdo->prepare("
    SELECT a.*, j.title, j.location 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE a.seeker_id = ? 
    ORDER BY a.applied_at DESC LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recent_apps = $stmt->fetchAll();
?>

<div class="container-fluid">
    <!-- Alert for incomplete profile -->
    <?php if (!$cv_uploaded): ?>
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center rounded-4 mb-5 p-4 bg-warning bg-opacity-10 animate-up" role="alert">
        <div class="bg-warning text-white p-3 rounded-circle me-3">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <h6 class="fw-bold text-dark mb-1">Complete your profile!</h6>
            <p class="text-muted small mb-0">Upload your CV to start applying for jobs. <a href="profile.php" class="fw-bold text-warning text-decoration-none ms-2">Go to Profile <i class="fas fa-arrow-right small"></i></a></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card premium-card border-0 p-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); color: white;">
                <div class="position-absolute end-0 bottom-0 opacity-10 mb-n2 me-n2">
                    <i class="fas fa-layer-group fa-8x"></i>
                </div>
                <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur shadow-sm border border-white border-opacity-10">
                        <i class="fas fa-layer-group fa-2x text-white"></i>
                    </div>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-white-50 small fw-800 text-uppercase tracking-wider mb-2">Total Applications</h6>
                    <h2 class="display-5 fw-900 mb-0"><?php echo $total_applications; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card premium-card border-0 p-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white;">
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
                    <h2 class="display-5 fw-900 mb-0">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card premium-card p-4 border-0 h-100 d-flex flex-column justify-content-center align-items-center">
                <h5 class="fw-bold text-dark mb-3 text-center">Ready for a new role?</h5>
                <a href="find_jobs.php" class="btn btn-premium btn-glow w-100">
                    <i class="fas fa-search me-2"></i> Browse Jobs
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card premium-card border-0 h-100">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 mb-0">Recent Applications</h5>
                    <a href="my_applications.php" class="btn btn-premium btn-sm rounded-pill px-3">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-bold">
                                <tr>
                                    <th class="px-3 py-2">Job Title</th>
                                    <th class="py-2">Location</th>
                                    <th class="py-2">Status</th>
                                    <th class="text-end px-3 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php if ($recent_apps): ?>
                                    <?php foreach ($recent_apps as $app): ?>
                                    <tr>
                                        <td class="px-3 py-2">
                                            <span class="fw-bold text-dark small"><?php echo htmlspecialchars($app['title']); ?></span>
                                        </td>
                                        <td class="py-2">
                                            <span class="text-muted small" style="font-size: 0.8rem;"><i class="fas fa-map-marker-alt me-1 text-accent"></i><?php echo htmlspecialchars($app['location']); ?></span>
                                        </td>
                                        <td class="py-2">
                                            <?php
                                            $status_class = 'bg-secondary bg-opacity-10 text-secondary';
                                            if ($app['status'] == 'accepted') $status_class = 'bg-success bg-opacity-10 text-success';
                                            if ($app['status'] == 'rejected') $status_class = 'bg-danger bg-opacity-10 text-danger';
                                            if ($app['status'] == 'reviewed') $status_class = 'bg-info bg-opacity-10 text-info';
                                            ?>
                                            <span class="badge rounded-pill px-2 py-1 fw-bold <?php echo $status_class; ?>" style="font-size: 0.7rem;">
                                                <?php echo ucfirst($app['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end px-3 py-2">
                                            <a href="application_status.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-primary border-2 rounded-pill px-3 py-0 fw-bold" style="font-size: 0.75rem;">View</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-10"></i>
                                            <p class="mb-0">No applications yet. <a href="find_jobs.php" class="fw-bold text-accent">Start your search!</a></p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card premium-card border-0 h-100 text-white" style="background: var(--premium-gradient);">
                <div class="card-header-premium p-3 border-bottom border-white border-opacity-10">
                    <h6 class="fw-800 mb-0 text-white">Career Insights</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3 bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10">
                        <h6 class="fw-bold text-accent mb-1 small"><i class="fas fa-lightbulb me-2"></i> Resume Tip</h6>
                        <p class="small mb-0 opacity-75" style="font-size: 0.8rem;">Focus on impact, not just responsibilities. Use action verbs and metrics where possible.</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10">
                        <h6 class="fw-bold text-accent mb-1 small"><i class="fas fa-bolt me-2"></i> Fast Apply</h6>
                        <p class="small mb-0 opacity-75" style="font-size: 0.8rem;">Employers review 80% of applications within the first 48 hours of posting.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'seeker_layout_bottom.php'; ?>

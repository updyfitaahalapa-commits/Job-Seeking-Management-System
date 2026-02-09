<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$application_id = $_GET['id'];
$seeker_id = $_SESSION['user_id'];

// Fetch application details with job and employer info
$stmt = $pdo->prepare("
    SELECT a.*, j.title, j.description, j.category, j.job_type, j.salary, j.location, p.company_name
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN profiles p ON j.employer_id = p.user_id
    WHERE a.id = ? AND a.seeker_id = ?
");
$stmt->execute([$application_id, $seeker_id]);
$app = $stmt->fetch();

if (!$app) {
    header("Location: dashboard.php");
    exit();
}

include 'seeker_layout_top.php';
?>

<div class="row justify-content-center animate-up">
    <div class="col-lg-10">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="my_applications.php" class="text-decoration-none text-muted fw-800 small transition-all hover-primary d-inline-flex align-items-center">
                <div class="bg-white p-2 rounded-circle shadow-sm me-3"><i class="fas fa-arrow-left"></i></div>
                Back to My Applications
            </a>
        </div>

        <div class="card premium-card border-0 overflow-hidden mb-5">
            <div class="card-header-premium">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
                    <div>
                        <h2 class="fw-800 text-dark mb-1 display-6"><?php echo htmlspecialchars($app['title']); ?></h2>
                        <p class="text-muted mb-0 fw-800 fs-5"><i class="fas fa-building me-2 text-accent"></i><?php echo htmlspecialchars($app['company_name']); ?></p>
                    </div>
                    <div>
                        <?php
                        $status_class = 'bg-secondary bg-opacity-10 text-secondary';
                        if ($app['status'] == 'accepted') $status_class = 'bg-success bg-opacity-10 text-success';
                        if ($app['status'] == 'rejected') $status_class = 'bg-danger bg-opacity-10 text-danger';
                        if ($app['status'] == 'reviewed') $status_class = 'bg-info bg-opacity-10 text-info';
                        ?>
                        <span class="badge rounded-pill px-5 py-3 fs-6 fw-800 <?php echo $status_class; ?>">
                            <?php echo ucfirst($app['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
            
                <!-- Stats Row -->
                <div class="bg-light bg-opacity-50 border-bottom border-light p-4">
                    <div class="row g-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-2 rounded-3 text-primary shadow-sm me-3"><i class="fas fa-layer-group"></i></div>
                                <div>
                                    <small class="text-muted d-block tiny fw-800 text-uppercase tracking-wider">Category</small>
                                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($app['category']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-2 rounded-3 text-primary shadow-sm me-3"><i class="fas fa-briefcase"></i></div>
                                <div>
                                    <small class="text-muted d-block tiny fw-800 text-uppercase tracking-wider">Type</small>
                                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($app['job_type']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-2 rounded-3 text-primary shadow-sm me-3"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <small class="text-muted d-block tiny fw-800 text-uppercase tracking-wider">Location</small>
                                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($app['location']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-2 rounded-3 text-success shadow-sm me-3"><i class="fas fa-money-bill-wave"></i></div>
                                <div>
                                    <small class="text-muted d-block tiny fw-800 text-uppercase tracking-wider">Salary</small>
                                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($app['salary'] ?: 'Negotiable'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-8 border-end border-light">
                            <div class="p-4 p-md-5">
                                <h5 class="fw-800 text-dark mb-4 d-flex align-items-center tracking-tight">
                                    <span class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary"><i class="fas fa-align-left"></i></span>
                                    Job Description
                                </h5>
                                <div class="text-muted fw-medium fs-6 lh-lg mb-5">
                                    <?php echo nl2br(htmlspecialchars($app['description'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 bg-light bg-opacity-25">
                            <div class="p-4 p-md-5">
                                <h5 class="fw-800 text-dark mb-4 d-flex align-items-center tracking-tight">
                                    <span class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary"><i class="fas fa-history"></i></span>
                                    Application Timeline
                                </h5>
                                <div class="timeline ps-3 position-relative">
                                    <div class="timeline-item border-start border-2 border-light position-relative pb-5 ps-4">
                                        <div class="position-absolute translate-middle-x start-0 bg-primary rounded-circle shadow-sm border border-4 border-white" style="width: 20px; height: 20px; top: 0;"></div>
                                        <h6 class="fw-800 text-dark mb-1">Application Submitted</h6>
                                        <p class="small text-muted fw-bold mb-0"><i class="far fa-clock me-1"></i> <?php echo date('M d, Y h:i A', strtotime($app['applied_at'])); ?></p>
                                    </div>
                                    <div class="timeline-item border-start border-2 border-light position-relative pb-2 ps-4">
                                        <div class="position-absolute translate-middle-x start-0 <?php echo $app['status'] != 'pending' ? 'bg-primary' : 'bg-secondary'; ?> rounded-circle shadow-sm border border-4 border-white" style="width: 20px; height: 20px; top: 0;"></div>
                                        <h6 class="fw-800 <?php echo $app['status'] != 'pending' ? 'text-dark' : 'text-muted'; ?> mb-1">Currently: <?php echo ucfirst($app['status']); ?></h6>
                                        <p class="small text-muted fw-bold mb-0">The employer is actively reviewing your profile and information.</p>
                                    </div>
                                </div>
                                
                                <div class="mt-5 pt-5 border-top border-light text-center">
                                    <p class="small text-muted fw-bold mb-3">Keep your profile updated for better visibility.</p>
                                    <a href="profile.php" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">Update Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-wider { letter-spacing: 0.1em; }
    .hover-primary:hover { color: var(--primary) !important; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
</style>

<?php include 'seeker_layout_bottom.php'; ?>

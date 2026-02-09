<?php
session_start();
require_once '../includes/db.php';
include 'employer_layout_top.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ? AND employer_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    header("Location: manage_jobs.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$jobs = $stmt->fetchAll();
?>

<div class="animate-up">
    <div class="mb-5">
        <h2 class="fw-800 text-dark display-6 mb-1">Job Postings</h2>
        <p class="text-muted fw-medium">Manage and track your active and inactive job listings.</p>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card premium-card border-0">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 mb-0">My Published Jobs</h5>
                    <a href="post_job.php" class="btn btn-premium btn-glow btn-sm rounded-pill px-4 fw-800"><i class="fas fa-plus me-2"></i> Post New Job</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-800 tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Job Details</th>
                                    <th class="py-3">Category</th>
                                    <th class="py-3 text-center">Status</th>
                                    <th class="text-end px-4 py-3">Management</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php if ($jobs): ?>
                                    <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td class="px-4 py-4">
                                            <span class="fw-800 d-block text-dark fs-6"><?php echo htmlspecialchars($job['title']); ?></span>
                                            <span class="small text-muted fw-bold"><i class="far fa-calendar-alt me-1 text-accent"></i>Posted: <?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
                                        </td>
                                        <td class="py-4">
                                            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-bold">
                                                <?php echo htmlspecialchars($job['category']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="badge rounded-pill px-4 py-2 fw-800 <?php echo $job['status'] == 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'; ?>">
                                                <?php echo ucfirst($job['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end px-4 py-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="view_applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-premium btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                                    <i class="fas fa-users me-2"></i> Applicants
                                                </a>
                                                <a href="?delete=<?php echo $job['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold border-2" onclick="return confirm('Are you sure you want to delete this job?')">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <div class="bg-light bg-opacity-50 d-inline-block p-4 rounded-circle mb-4">
                                                <i class="fas fa-inbox fa-3x opacity-10"></i>
                                            </div>
                                            <h5 class="fw-800 text-dark">No jobs posted yet.</h5>
                                            <p class="mb-4">Create your first job listing to find the right talent.</p>
                                            <a href="post_job.php" class="btn btn-premium btn-glow px-5">Post a Job Now</a>
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
</div>

<?php include 'employer_layout_bottom.php'; ?>

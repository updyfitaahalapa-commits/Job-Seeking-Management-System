<?php
session_start();
require_once '../includes/db.php';
include 'admin_layout_top.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: jobs.php");
    exit();
}

$jobs = $pdo->query("
    SELECT j.*, u.username as employer_name 
    FROM jobs j 
    JOIN users u ON j.employer_id = u.id 
    ORDER BY j.created_at DESC
")->fetchAll();
?>

<div class="animate-up">
    <div class="mb-5">
        <h2 class="fw-800 text-dark display-6 mb-1">Platform Job Listings</h2>
        <p class="text-muted fw-medium">Monitor and moderate all active and inactive job postings on the platform.</p>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card premium-card border-0">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 mb-0">Active Job Postings</h5>
                    <div class="bg-success bg-opacity-10 text-success px-4 py-1 rounded-pill fw-800 small border border-success border-opacity-10">Total: <?php echo count($jobs); ?></div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-800 tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Opportunity Details</th>
                                    <th class="py-3">Posted By</th>
                                    <th class="py-3 text-center">Category</th>
                                    <th class="py-3 text-center">Visibility</th>
                                    <th class="text-end px-4 py-3">Management</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php if ($jobs): ?>
                                    <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td class="px-4 py-4">
                                            <span class="fw-800 d-block text-dark fs-6"><?php echo htmlspecialchars($job['title']); ?></span>
                                            <span class="small text-muted fw-bold"><i class="far fa-calendar-alt me-1 text-accent"></i><?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
                                        </td>
                                        <td class="py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info bg-opacity-5 text-info rounded-circle p-2 me-2 small fw-800 border border-info border-opacity-10" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-building small"></i>
                                                </div>
                                                <span class="text-info small fw-800">@<?php echo htmlspecialchars($job['employer_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="badge bg-light text-dark border-0 rounded-pill px-3 py-2 fw-800 small"><?php echo htmlspecialchars($job['category']); ?></span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <?php
                                            $status_class = $job['status'] == 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                                            ?>
                                            <span class="badge rounded-pill px-4 py-2 fw-800 <?php echo $status_class; ?>">
                                                <?php echo ucfirst($job['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end px-4 py-4">
                                            <a href="?delete=<?php echo $job['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold border-2" onclick="return confirm('Moderate Content! Are you sure you want to delete this job posting?')">
                                                <i class="fas fa-trash-alt me-2"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div class="bg-light bg-opacity-50 d-inline-block p-4 rounded-circle mb-4">
                                                <i class="fas fa-inbox fa-3x opacity-10"></i>
                                            </div>
                                            <h5 class="fw-800 text-dark">No live listings found.</h5>
                                            <p class="mb-0 fw-medium">Job postings will appear here once employers create them.</p>
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

<?php include 'admin_layout_bottom.php'; ?>

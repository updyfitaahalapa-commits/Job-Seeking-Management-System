<?php
session_start();
require_once '../includes/db.php';
include 'seeker_layout_top.php';

$stmt = $pdo->prepare("
    SELECT a.*, j.title, j.location, p.company_name, j.category
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    JOIN profiles p ON j.employer_id = p.user_id
    WHERE a.seeker_id = ? 
    ORDER BY a.applied_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();
?>

<div class="animate-up">
    <div class="mb-5">
        <h2 class="fw-800 text-dark display-6 mb-1">My Applications</h2>
        <p class="text-muted fw-medium">Track your job application progress and status.</p>
    </div>

    <?php if (isset($_SESSION['success_msg'])): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 animate-up" style="background: rgba(16, 185, 129, 0.1); color: #047857;">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card premium-card border-0">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 mb-0">Application History</h5>
                    <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-800">
                        Total: <?php echo count($applications); ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-800 tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Applied Job</th>
                                    <th class="py-3">Company</th>
                                    <th class="py-3">Category</th>
                                    <th class="py-3 text-center">Date</th>
                                    <th class="py-3 text-center">Status</th>
                                    <th class="text-end px-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php if ($applications): ?>
                                    <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td class="px-4 py-4">
                                            <span class="fw-800 d-block text-dark fs-6"><?php echo htmlspecialchars($app['title']); ?></span>
                                            <span class="small text-muted fw-bold"><i class="fas fa-map-marker-alt me-1 text-accent"></i><?php echo htmlspecialchars($app['location']); ?></span>
                                        </td>
                                        <td class="py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-5 text-primary rounded-4 p-2 me-3 border border-primary border-opacity-10">
                                                    <i class="fas fa-building small"></i>
                                                </div>
                                                <span class="text-dark fw-bold"><?php echo htmlspecialchars($app['company_name'] ?: 'Tech Corp'); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-bold">
                                                <?php echo htmlspecialchars($app['category']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 text-center text-muted fw-bold small">
                                            <?php echo date('M d, Y', strtotime($app['applied_at'])); ?>
                                        </td>
                                        <td class="py-4 text-center">
                                            <?php
                                            $status_class = 'bg-secondary bg-opacity-10 text-secondary';
                                            if ($app['status'] == 'accepted') $status_class = 'bg-success bg-opacity-10 text-success';
                                            if ($app['status'] == 'rejected') $status_class = 'bg-danger bg-opacity-10 text-danger';
                                            if ($app['status'] == 'reviewed') $status_class = 'bg-info bg-opacity-10 text-info';
                                            ?>
                                            <span class="badge rounded-pill px-4 py-2 fw-800 <?php echo $status_class; ?>">
                                                <?php echo ucfirst($app['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end px-4 py-4">
                                            <div class="dropdown">
                                                <button class="btn btn-light rounded-pill px-3 fw-bold border" type="button" data-bs-toggle="dropdown">
                                                    Options <i class="fas fa-chevron-down ms-1 small"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                                    <li><a class="dropdown-item py-2 rounded-3 fw-bold" href="application_status.php?id=<?php echo $app['id']; ?>"><i class="fas fa-eye me-2 text-primary"></i> View Details</a></li>
                                                    <li><hr class="dropdown-divider opacity-10"></li>
                                                    <li><a class="dropdown-item py-2 rounded-3 text-danger fw-bold withdraw-btn" href="#" data-bs-toggle="modal" data-bs-target="#withdrawModal" data-href="withdraw_application.php?id=<?php echo $app['id']; ?>"><i class="fas fa-times-circle me-2"></i> Withdraw</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="bg-light bg-opacity-50 d-inline-block p-4 rounded-circle mb-4">
                                                <i class="fas fa-file-invoice fa-3x opacity-10"></i>
                                            </div>
                                            <h5 class="fw-800 text-dark">You haven't applied for any jobs yet.</h5>
                                            <p class="mb-4 fw-medium">Start your career journey today by exploring open roles.</p>
                                            <a href="find_jobs.php" class="btn btn-premium btn-glow px-5">Explore Opportunities</a>
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

<!-- Withdraw Confirmation Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-5 text-center">
                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 text-danger p-4 rounded-circle d-inline-block mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x animate-pulse"></i>
                    </div>
                    <h4 class="fw-800 text-dark mb-2">Withdraw Application?</h4>
                    <p class="text-muted fw-medium mb-0">Are you sure you want to withdraw this application? This action <span class="text-danger fw-bold">cannot be undone</span>.</p>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold border" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmWithdrawBtn" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Yes, Withdraw It</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var withdrawModal = document.getElementById('withdrawModal');
        withdrawModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var href = button.getAttribute('data-href');
            var confirmBtn = document.getElementById('confirmWithdrawBtn');
            confirmBtn.setAttribute('href', href);
        });
    });
</script>

<?php include 'seeker_layout_bottom.php'; ?>

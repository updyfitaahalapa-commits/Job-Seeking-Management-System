<?php
session_start();
require_once '../includes/db.php';
include 'admin_layout_top.php';

// Stats for reports
$status_counts = $pdo->query("SELECT status, COUNT(*) as count FROM applications GROUP BY status")->fetchAll();
$category_counts = $pdo->query("SELECT category, COUNT(*) as count FROM jobs GROUP BY category")->fetchAll();
?>

<div class="animate-up">
    <div class="mb-5">
        <h2 class="fw-800 text-dark display-6 mb-1">System Reports</h2>
        <p class="text-muted fw-medium">Analytical insights into platform activity and engagement.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card premium-card h-100 border-0">
                <div class="card-header-premium">
                    <h5 class="fw-800 mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i> Application Status</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-800 tracking-wider">
                            <tr>
                                <th class="px-3 py-2 border-0">Status</th>
                                <th class="text-end px-3 py-2 border-0">Total</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php foreach ($status_counts as $sc): ?>
                            <tr>
                                <td class="px-3 py-3 border-bottom-0">
                                    <?php
                                    $s_badge = 'bg-secondary bg-opacity-10 text-secondary';
                                    if ($sc['status'] == 'accepted') $s_badge = 'bg-success bg-opacity-10 text-success';
                                    if ($sc['status'] == 'reviewed') $s_badge = 'bg-info bg-opacity-10 text-info';
                                    if ($sc['status'] == 'rejected') $s_badge = 'bg-danger bg-opacity-10 text-danger';
                                    ?>
                                    <span class="badge rounded-pill <?php echo $s_badge; ?> px-4 py-2 fw-800 small tracking-wider"><?php echo ucfirst($sc['status']); ?></span>
                                </td>
                                <td class="text-end px-3 py-3 border-bottom-0 fw-800 fs-5 text-dark"><?php echo number_format($sc['count']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card premium-card h-100 border-0">
                <div class="card-header-premium">
                    <h5 class="fw-800 mb-0"><i class="fas fa-tags me-2 text-accent"></i> Industry Distribution</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-800 tracking-wider">
                            <tr>
                                <th class="px-3 py-2 border-0">Category</th>
                                <th class="text-end px-3 py-2 border-0">Postings</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php foreach ($category_counts as $cc): ?>
                            <tr>
                                <td class="px-3 py-3 border-bottom-0 fw-bold text-dark"><?php echo htmlspecialchars($cc['category']); ?></td>
                                <td class="text-end px-3 py-3 border-bottom-0 fw-800 fs-5 text-primary"><?php echo number_format($cc['count']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card premium-card p-4 border-0" style="background: rgba(255,255,255,0.4); backdrop-filter: blur(10px);">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-4 border border-primary border-opacity-10">
                        <i class="fas fa-info-circle fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-800 text-dark mb-1">Analytical Real-Time Intelligence</h6>
                        <p class="small text-muted mb-0 fw-medium">These reports are synchronized with the live production database. Distribution metrics reflect current market activity and candidate engagement profiles.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'admin_layout_bottom.php'; ?>

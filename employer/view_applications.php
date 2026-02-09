<?php
session_start();
require_once '../includes/db.php';
include 'employer_layout_top.php';

$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : null;

// Update status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $app_id = $_POST['app_id'];
    $status = $_POST['status'];
    try {
        $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $stmt->execute([$status, $app_id]);
        echo "<script>alert('Status updated successfully!');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error updating status: " . $e->getMessage() . "');</script>";
    }
}

// Fetch applications
$query = "
    SELECT a.*, j.title as job_title, u.username, p.full_name, p.cv_path, p.contact_number
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    JOIN users u ON a.seeker_id = u.id
    JOIN profiles p ON u.id = p.user_id
    WHERE j.employer_id = ?
";
$params = [$_SESSION['user_id']];

if ($job_id) {
    $query .= " AND a.job_id = ?";
    $params[] = $job_id;
}

$query .= " ORDER BY a.applied_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$applications = $stmt->fetchAll();
?>

<div class="animate-up">
    <div class="mb-5">
        <h2 class="fw-800 text-dark display-6 mb-1">Manage Applications</h2>
        <p class="text-muted fw-medium">Review candidates and manage their application status.</p>
    </div>

    <div class="card premium-card border-0">
        <div class="card-header-premium">
            <h5 class="fw-800 mb-0">Candidate Submissions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-800 tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Applicant Details</th>
                            <th class="py-3">Job Applied</th>
                            <th class="py-3 text-center">Resume</th>
                            <th class="py-3 text-center">Applied Date</th>
                            <th class="py-3 text-center">Current Status</th>
                            <th class="text-end px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if ($applications): ?>
                            <?php foreach ($applications as $app): ?>
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-5 text-primary p-2 rounded-circle me-3 border border-primary border-opacity-10">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark fs-6"><?php echo htmlspecialchars($app['full_name'] ?: $app['username']); ?></div>
                                            <div class="small text-muted fw-bold"><i class="fas fa-phone-alt me-1 text-accent"></i><?php echo htmlspecialchars($app['contact_number']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($app['job_title']); ?></span>
                                </td>
                                <td class="py-4 text-center">
                                    <?php if ($app['cv_path']): ?>
                                        <a href="../uploads/cvs/<?php echo htmlspecialchars($app['cv_path']); ?>" target="_blank" class="btn btn-premium btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                            <i class="fas fa-file-pdf me-2"></i> View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small fw-bold">No CV</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="text-muted fw-bold small"><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></span>
                                </td>
                                <td class="py-4 text-center">
                                    <?php
                                    $badge_class = 'bg-secondary bg-opacity-10 text-secondary';
                                    if ($app['status'] == 'accepted') $badge_class = 'bg-success bg-opacity-10 text-success';
                                    if ($app['status'] == 'rejected') $badge_class = 'bg-danger bg-opacity-10 text-danger';
                                    if ($app['status'] == 'reviewed') $badge_class = 'bg-info bg-opacity-10 text-info';
                                    ?>
                                    <span class="badge rounded-pill px-4 py-2 fw-800 <?php echo $badge_class; ?>">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end px-4 py-4">
                                    <form action="" method="POST" class="d-flex gap-2 justify-content-end align-items-center">
                                        <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                        <select name="status" class="form-select form-select-sm rounded-pill border-0 bg-light bg-opacity-50 fw-bold px-3 shadow-none" style="width: 130px;">
                                            <option value="pending" <?php echo $app['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="reviewed" <?php echo $app['status'] == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                            <option value="accepted" <?php echo $app['status'] == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                                            <option value="rejected" <?php echo $app['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-premium btn-sm btn-glow rounded-pill px-4 fw-bold">Update</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
<?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="bg-light bg-opacity-50 d-inline-block p-4 rounded-circle mb-4">
                                        <i class="fas fa-inbox fa-3x opacity-10"></i>
                                    </div>
                                    <h5 class="fw-800 text-dark">No applications received yet.</h5>
                                    <p class="mb-0 fw-medium">Applications will appear here once candidates apply.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'employer_layout_bottom.php'; ?>

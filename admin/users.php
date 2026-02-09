<?php
session_start();
require_once '../includes/db.php';
include 'admin_layout_top.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) { // Don't delete self
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: users.php");
    exit();
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="animate-up">
    <div class="mb-5">
        <h2 class="fw-800 text-dark display-6 mb-1">System Users</h2>
        <p class="text-muted fw-medium">Manage and monitor all platform accounts and permissions.</p>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card premium-card border-0">
                <div class="card-header-premium d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 mb-0">Platform Accounts</h5>
                    <div class="bg-light text-black px-3 py-1 rounded-pill fw-800 small border border-secondary border-opacity-25">Total: <?php echo count($users); ?></div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light bg-opacity-50 text-muted small text-uppercase fw-800 tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">User Profile</th>
                                    <th class="py-3">Email Address</th>
                                    <th class="py-3 text-center">User Role</th>
                                    <th class="py-3 text-center">Joined Date</th>
                                    <th class="text-end px-4 py-3">Management</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light text-black rounded-circle p-2 me-3 small fw-800 border border-secondary border-opacity-25" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <span class="fw-800 d-block text-dark fs-6"><?php echo htmlspecialchars($user['username']); ?></span>
                                                <span class="small text-muted fw-bold text-uppercase tracking-tighter">ID: #<?php echo $user['id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-muted fw-bold small"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="py-4 text-center">
                                        <?php 
                                        $role_class = 'bg-info bg-opacity-10 text-info';
                                        if ($user['role'] == 'admin') $role_class = 'bg-danger bg-opacity-10 text-danger';
                                        if ($user['role'] == 'employer') $role_class = 'bg-warning bg-opacity-10 text-warning';
                                        ?>
                                        <span class="badge rounded-pill px-4 py-2 fw-800 <?php echo $role_class; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-muted fw-bold small">
                                        <i class="far fa-calendar-alt me-1 text-accent"></i>
                                        <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td class="text-end px-4 py-4">
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold border-2" onclick="return confirm('Protect account data! Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash-alt me-2"></i> Delete
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-light text-black rounded-pill px-4 py-2 fw-800 border border-secondary border-opacity-25">Current Session</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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

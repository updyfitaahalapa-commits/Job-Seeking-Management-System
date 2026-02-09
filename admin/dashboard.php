<?php
session_start();
require_once '../includes/db.php';
include 'admin_layout_top.php';

// System-wide stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_jobs = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$total_apps = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$total_seekers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seeker'")->fetchColumn();
$total_employers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employer'")->fetchColumn();

// Recent users
$recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="row g-4 mb-5 animate-up">
        <div class="col-md-3">
            <div class="card premium-card border-0 p-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); color: white;">
                <div class="position-absolute end-0 bottom-0 opacity-10 mb-n2 me-n2">
                    <i class="fas fa-users fa-8x"></i>
                </div>
                <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-white-50 small fw-800 text-uppercase tracking-wider mb-2">Total Users</h6>
                    <h2 class="display-5 fw-900 mb-0"><?php echo number_format($total_users); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card premium-card border-0 p-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); color: white;"> <!-- Teal for Jobs -->
                <div class="position-absolute end-0 bottom-0 opacity-10 mb-n2 me-n2">
                    <i class="fas fa-briefcase fa-8x"></i>
                </div>
                <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur">
                        <i class="fas fa-briefcase fa-2x text-white"></i>
                    </div>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-white-50 small fw-800 text-uppercase tracking-wider mb-2">Active Jobs</h6>
                    <h2 class="display-5 fw-900 mb-0"><?php echo number_format($total_jobs); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card premium-card border-0 p-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white;"> <!-- Sky Blue for Apps -->
                <div class="position-absolute end-0 bottom-0 opacity-10 mb-n2 me-n2">
                    <i class="fas fa-paper-plane fa-8x"></i>
                </div>
                <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur">
                        <i class="fas fa-paper-plane fa-2x text-white"></i>
                    </div>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-white-50 small fw-800 text-uppercase tracking-wider mb-2">Applications</h6>
                    <h2 class="display-5 fw-900 mb-0"><?php echo number_format($total_apps); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card premium-card border-0 p-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;"> <!-- Amber for Employers -->
                <div class="position-absolute end-0 bottom-0 opacity-10 mb-n2 me-n2">
                    <i class="fas fa-building fa-8x"></i>
                </div>
                <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                    <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur">
                        <i class="fas fa-building fa-2x text-white"></i>
                    </div>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-white-50 small fw-800 text-uppercase tracking-wider mb-2">Employers</h6>
                    <h2 class="display-5 fw-900 mb-0"><?php echo number_format($total_employers); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row animate-up">
        <div class="col-lg-12">
            <div class="card premium-card border-0 overflow-hidden">
                <div class="card-header-premium d-flex justify-content-between align-items-center p-4 bg-white border-bottom border-light">
                    <div>
                        <h5 class="fw-800 mb-1">Recent Registrations</h5>
                        <p class="text-muted small fw-bold mb-0">Newest members joining the platform</p>
                    </div>
                    <a href="users.php" class="btn btn-light btn-sm rounded-pill px-4 fw-800 border text-muted hover-primary transition-all">
                        View All Users <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="px-4 py-3 text-muted small text-uppercase fw-800 tracking-wider font-monospace">User Details</th>
                                    <th class="py-3 text-muted small text-uppercase fw-800 tracking-wider font-monospace">Contact</th>
                                    <th class="py-3 text-center text-muted small text-uppercase fw-800 tracking-wider font-monospace">Role</th>
                                    <th class="text-end px-4 py-3 text-muted small text-uppercase fw-800 tracking-wider font-monospace">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php foreach ($recent_users as $user): ?>
                                <tr class="transition-all hover-bg-light">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle p-2 me-3 small fw-800 d-flex align-items-center justify-content-center text-white shadow-sm" 
                                                 style="width: 42px; height: 42px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <span class="fw-800 text-dark d-block"><?php echo htmlspecialchars($user['username']); ?></span>
                                                <span class="small text-muted fw-bold">ID: #<?php echo $user['id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center text-muted fw-medium">
                                            <i class="far fa-envelope me-2 opacity-50"></i>
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <?php
                                        $role_bg = 'bg-info bg-opacity-10 text-info';
                                        $role_icon = 'fa-user';
                                        if ($user['role'] == 'employer') {
                                            $role_bg = 'bg-warning bg-opacity-10 text-warning';
                                            $role_icon = 'fa-building';
                                        }
                                        if ($user['role'] == 'admin') {
                                            $role_bg = 'bg-danger bg-opacity-10 text-danger';
                                            $role_icon = 'fa-shield-alt';
                                        }
                                        ?>
                                        <span class="badge rounded-pill px-3 py-2 fw-800 <?php echo $role_bg; ?> border border-opacity-10">
                                            <i class="fas <?php echo $role_icon; ?> me-1"></i> <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end px-4 py-3 text-muted fw-bold small font-monospace">
                                        <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
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

<?php include 'admin_layout_bottom.php'; ?>

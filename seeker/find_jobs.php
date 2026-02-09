<?php
session_start();
require_once '../includes/db.php';
include 'seeker_layout_top.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build query
$query = "SELECT j.*, p.company_name FROM jobs j JOIN profiles p ON j.employer_id = p.user_id WHERE j.status = 'active'";
$params = [];

if ($search) {
    $query .= " AND (j.title LIKE ? OR j.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $query .= " AND j.category = ?";
    $params[] = $category;
}

$query .= " ORDER BY j.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

// Fetch categories for filter
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM jobs WHERE status = 'active'");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<style>
.job-card {
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.job-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.15), 0 18px 36px -18px rgba(0, 0, 0, 0.2) !important;
}
.border-start-md {
    border-left: 1px solid rgba(0,0,0,0.05);
}
@media (max-width: 767px) {
    .border-start-md { border-left: none; }
}
.nav-pills .nav-link.active {
    background: var(--premium-gradient) !important;
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.3);
}
.glass-input {
    background: #ffffff !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 12px !important;
    padding: 0.75rem 1.25rem !important;
}
.glass-input:focus {
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 4px var(--accent-glow) !important;
}
</style>

<div class="container-fluid animate-up">
    <div class="mb-5 animate-up">
        <div class="text-center mb-4">
            <h2 class="fw-800 text-dark display-6 mb-1">Available Jobs</h2>
            <p class="text-muted fw-medium">Find the perfect match for your career.</p>
        </div>
        
        <form action="find_jobs.php" method="GET" class="d-flex justify-content-center">
            <div class="input-group premium-card overflow-hidden border-0 shadow-sm w-100" style="max-width: 900px;">
                <span class="input-group-text border-0 bg-white ps-4"><i class="fas fa-search text-muted opacity-50"></i></span>
                <input type="text" name="search" class="form-control border-0 py-3 shadow-none" placeholder="Search job titles, keywords..." value="<?php echo htmlspecialchars($search); ?>">
                
                <div class="d-flex align-items-center border-start border-light ps-3 bg-white" style="min-width: 200px;">
                    <i class="fas fa-layer-group text-muted opacity-50 me-2"></i>
                    <select name="category" class="form-select border-0 shadow-none py-3 text-dark fw-medium" style="cursor: pointer;">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category == $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-premium btn-glow px-5 fw-bold" style="border-radius: 0;">Search</button>
            </div>
        </form>
    </div>

<div class="row g-4">
        <!-- Job Listings -->
        <div class="col-lg-10 mx-auto">
            <?php if ($jobs): ?>
                <div class="row g-3"> <!-- Reduced gap -->
                    <?php foreach ($jobs as $job): ?>
                    <div class="col-md-6"> <!-- Side by side -->
                        <div class="card premium-card border-0 p-3 job-card h-100 position-relative">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-light text-primary p-2 rounded-3 me-3 border border-secondary border-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-building fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-800 text-dark mb-1 text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($job['title']); ?></h5>
                                    <div class="text-muted small fw-bold text-truncate">
                                        <i class="fas fa-industry me-1 text-accent"></i> <?php echo htmlspecialchars($job['company_name'] ?: 'Tech Corp'); ?>
                                    </div>
                                </div>
                                <span class="badge bg-light text-muted border small"><?php echo htmlspecialchars($job['job_type']); ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="text-muted small mb-2 line-clamp-2" style="min-height: 2.6em;">
                                    <?php echo htmlspecialchars($job['description']); ?>
                                </p>
                                <div class="d-flex align-items-center text-muted small fw-medium">
                                    <i class="fas fa-map-marker-alt me-2 text-accent"></i> <?php echo htmlspecialchars($job['location']); ?>
                                    <span class="mx-2">•</span>
                                    <span class="text-success fw-bold"><?php echo htmlspecialchars($job['salary'] ?: 'Negotiable'); ?></span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top border-light">
                                <span class="text-muted small" style="font-size: 0.75rem;">
                                    <?php echo date('M d', strtotime($job['created_at'])); ?>
                                </span>
                                <form action="apply.php" method="POST" class="m-0">
                                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                    <button type="submit" name="apply_now" class="btn btn-premium btn-sm rounded-pill px-4 fw-bold shadow-sm">Apply</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card premium-card border-0 py-5 text-center">
                    <div class="bg-light bg-opacity-50 d-inline-block p-4 rounded-circle mb-4">
                        <i class="fas fa-search fa-3x text-muted opacity-20"></i>
                    </div>
                    <h4 class="fw-800 text-dark mb-2">No jobs found matching your criteria.</h4>
                    <p class="text-muted mb-4 px-5 fw-medium">Try adjusting your search keywords or exploring different categories to find new opportunities.</p>
                    <a href="find_jobs.php" class="btn btn-premium btn-sm px-4">Clear all filters</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'seeker_layout_bottom.php'; ?>

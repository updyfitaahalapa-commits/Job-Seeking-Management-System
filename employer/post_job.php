<?php
session_start();
require_once '../includes/db.php';
include 'employer_layout_top.php';

$success = '';
$error = '';

// Check if profile is completed (optional but recommended)
$stmt = $pdo->prepare("SELECT company_name FROM profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_job'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $job_type = $_POST['job_type'];
    $salary = trim($_POST['salary']);
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $deadline = $_POST['deadline'];

    try {
        $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, description, category, job_type, salary, location, deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $description, $category, $job_type, $salary, $location, $deadline]);
        $success = "Job posted successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="row mb-5 animate-up">
    <div class="col-12 text-center text-lg-start">
        <h2 class="fw-800 text-dark display-6 mb-1">Post Opportunity</h2>
        <p class="text-muted fw-medium">Connect with global talent by detailing your requirements.</p>
    </div>
</div>

<div class="row justify-content-center animate-up">
    <div class="col-lg-10">
        <div class="card premium-card border-0 overflow-hidden mb-5">
            <div class="card-body p-5">
                <?php if ($success): ?>
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 animate-up" style="background: rgba(16, 185, 129, 0.1); color: #047857;">
                        <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 animate-up" style="background: rgba(239, 68, 68, 0.1); color: #b91c1c;">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="post_job.php" method="POST" class="needs-validation" novalidate>
                    <div class="row g-4 mb-4">
                        <div class="col-md-8">
                            <label for="title" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Position Title</label>
                            <input type="text" name="title" class="form-control form-control-lg rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-3 px-4 fw-bold" id="title" placeholder="e.g. Senior PHP Developer" required>
                        </div>
                        <div class="col-md-4">
                            <label for="category" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Job Category</label>
                            <select name="category" class="form-select form-select-lg rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-3 px-4 fw-bold" id="category" required>
                                <option value="">Select Category</option>
                                <option value="IT & Software">IT & Software</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Finance">Finance</option>
                                <option value="Design">Design</option>
                                <option value="Sales">Sales</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Detailed Description</label>
                        <textarea name="description" class="form-control rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-3 px-4 fw-bold" id="description" rows="8" placeholder="Outline responsibilities, required skills, and what you're looking for..." required></textarea>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label for="job_type" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Employment Type</label>
                            <select name="job_type" class="form-select form-select-lg rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-3 px-4 fw-bold" id="job_type">
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="salary" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Compensation (Optional)</label>
                            <input type="text" name="salary" class="form-control form-control-lg rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-3 px-4 fw-bold" id="salary" placeholder="e.g. $2k - $4k">
                        </div>
                        <div class="col-md-4">
                            <label for="location" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Office Location</label>
                            <input type="text" name="location" class="form-control form-control-lg rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-3 px-4 fw-bold" id="location" placeholder="e.g. Remote / London" required>
                        </div>
                        <div class="col-md-4">
                            <label for="deadline" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Application Deadline</label>
                            <input type="date" name="deadline" class="form-control form-control-lg rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-3 px-4 fw-bold" id="deadline" required>
                        </div>
                    </div>

                    <div class="d-grid pt-2">
                        <button type="submit" name="post_job" class="btn btn-premium btn-glow btn-lg py-3 fs-5">
                            <i class="fas fa-paper-plane me-2"></i> Publish Opportunity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<?php include 'employer_layout_bottom.php'; ?>

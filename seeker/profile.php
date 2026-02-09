<?php
session_start();
require_once '../includes/db.php';
include 'seeker_layout_top.php';

$success = '';
$error = '';

// Fetch current profile
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $bio = trim($_POST['bio']);
    $contact = trim($_POST['contact_number']);
    
    // Handle CV upload
    $cv_path = $profile['cv_path'];
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx'];
        $filename = $_FILES['cv']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = "cv_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
            $dest = "../uploads/cvs/" . $new_name;
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $dest)) {
                // Delete old CV if exists
                if ($cv_path && file_exists("../uploads/cvs/" . $cv_path)) {
                    unlink("../uploads/cvs/" . $cv_path);
                }
                $cv_path = $new_name;
            }
        } else {
            $error = "Invalid file type for CV. Only PDF and DOCX allowed.";
        }
    }

    if (!$error) {
        try {
            $stmt = $pdo->prepare("UPDATE profiles SET full_name = ?, bio = ?, contact_number = ?, cv_path = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $bio, $contact, $cv_path, $_SESSION['user_id']]);
            $success = "Profile updated successfully!";
            
            // Refresh profile data
            $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $profile = $stmt->fetch();
        } catch (PDOException $e) {
            $error = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<div class="animate-up">
    <div class="mb-5">
        <h2 class="fw-800 text-dark display-6 mb-1">My Profile</h2>
        <p class="text-muted fw-medium">Manage your personal information and resume.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card premium-card border-0 mb-4">
                <div class="card-header-premium p-3">
                    <h5 class="fw-800 mb-0">Personal Information</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-3 animate-up py-2" style="background: rgba(16, 185, 129, 0.1); color: #047857;">
                            <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-3 animate-up py-2" style="background: rgba(239, 68, 68, 0.1); color: #b91c1c;">
                            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Full Name</label>
                                <input type="text" name="full_name" class="form-control rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-2 px-3 fw-bold" id="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-2 px-3 fw-bold" id="contact_number" value="<?php echo htmlspecialchars($profile['contact_number'] ?? ''); ?>" placeholder="+252 61XXXXXXX">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Professional Bio</label>
                            <textarea name="bio" class="form-control rounded-4 shadow-none border-0 bg-light bg-opacity-50 py-2 px-3 fw-bold" id="bio" rows="3" placeholder="Briefly describe your professional background"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="cv" class="form-label small fw-800 text-muted text-uppercase tracking-wider">Professional Resume (PDF)</label>
                            <div class="input-group premium-card overflow-hidden border-0 shadow-sm">
                                <span class="input-group-text border-0 bg-white ps-3"><i class="fas fa-file-upload text-muted"></i></span>
                                <input type="file" name="cv" class="form-control border-0 py-2 px-3" id="cv">
                            </div>
                            <?php if ($profile['cv_path']): ?>
                                <div class="mt-3 p-3 rounded-4 border border-primary border-opacity-10 d-flex align-items-center bg-primary bg-opacity-5">
                                    <div class="bg-primary text-white p-2 rounded-3 me-3 shadow-sm">
                                        <i class="fas fa-file-pdf fa-lg"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small fw-800 text-dark mb-0">Current Document</div>
                                        <div class="small text-muted fw-bold"><?php echo htmlspecialchars($profile['cv_path']); ?></div>
                                    </div>
                                    <a href="../uploads/cvs/<?php echo htmlspecialchars($profile['cv_path']); ?>" class="btn btn-premium btn-sm rounded-pill px-3 fw-bold" target="_blank">
                                        <i class="fas fa-eye me-2"></i> View
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="text-end border-top border-opacity-10 pt-3">
                            <button type="submit" name="update_profile" class="btn btn-premium btn-glow px-4 py-2 fw-bold">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card premium-card border-0 p-3 text-center sticky-top" style="top: 100px;">
                <div class="mb-3">
                    <div class="position-relative d-inline-block">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=020617&color=00d2ff&size=100&bold=true" alt="Avatar" class="rounded-circle border border-4 border-white shadow-lg">
                        <button class="btn btn-premium position-absolute bottom-0 end-0 rounded-circle p-1 shadow-sm border-white border-2" title="Change Avatar" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-camera fa-xs"></i>
                        </button>
                    </div>
                </div>
                <h5 class="fw-800 text-dark mb-0"><?php echo htmlspecialchars($profile['full_name'] ?: $_SESSION['username']); ?></h5>
                <p class="text-muted small fw-800 text-uppercase tracking-widest mb-3 text-accent" style="font-size: 0.7rem;"><?php echo htmlspecialchars($_SESSION['role']); ?></p>
                
                <div class="bg-light bg-opacity-50 rounded-4 p-2 mb-3 border border-light">
                    <div class="row g-0 text-center">
                        <div class="col border-end">
                            <h6 class="fw-800 mb-0 text-dark">0</h6>
                            <small class="text-muted tiny text-uppercase fw-800 tracking-wider">Apps</small>
                        </div>
                        <div class="col">
                            <h6 class="fw-800 mb-0 text-dark">0</h6>
                            <small class="text-muted tiny text-uppercase fw-800 tracking-wider">Active</small>
                        </div>
                    </div>
                </div>

                <div class="text-start">
                    <div class="mb-2">
                        <small class="text-muted d-block text-uppercase tiny fw-800 tracking-wider mb-2">Member Details</small>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary bg-opacity-5 text-primary p-1 rounded-3 me-2 tiny border border-primary border-opacity-10">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span class="small text-dark fw-bold" style="font-size: 0.8rem;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-5 text-primary p-1 rounded-3 me-2 tiny border border-primary border-opacity-10">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span class="small text-dark fw-bold" style="font-size: 0.8rem;">Joined Feb 2026</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
.tiny { font-size: 0.65rem; }
.border-dashed { border: 1px dashed #dee2e6; }
.tracking-widest {letter-spacing: 0.15em; }
</style>

<?php include 'seeker_layout_bottom.php'; ?>

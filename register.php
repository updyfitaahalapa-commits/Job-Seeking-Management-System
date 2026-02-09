<?php
session_start();
require_once 'includes/db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $error = "Username or Email already exists!";
            } else {
                // Insert user
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $email, $role]);
                $user_id = $pdo->lastInsertId();

                // Create empty profile
                $stmt = $pdo->prepare("INSERT INTO profiles (user_id) VALUES (?)");
                $stmt->execute([$user_id]);
                
                $pdo->commit();
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Error: " . $e->getMessage();
        }
    }
}
require_once 'includes/header.php';
?>



<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center position-relative overflow-hidden" style="min-height: 100vh; background: #020617;">
    <!-- Back to Home Button -->
    <a href="index.php" class="position-absolute top-0 start-0 m-4 btn btn-light rounded-pill px-4 shadow-sm fw-bold z-3">
        <i class="fas fa-arrow-left me-2"></i> Back to Home
    </a>
    <!-- Background Effects -->
    <div class="position-absolute w-100 h-100" style="background: radial-gradient(circle at 50% 50%, rgba(0, 78, 146, 0.15), transparent 70%);"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.3; background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    
    <!-- Animated Orbs -->
    <div class="position-absolute bg-accent rounded-circle opacity-10 blur-3xl animate-float-slow" style="width: 600px; height: 600px; top: -20%; right: -20%;"></div>

    <!-- Register Card -->
    <div class="glass-card w-100 rounded-5 p-4 p-md-5 position-relative z-2 animate-up" style="max-width: 600px; overflow: hidden;">
        <div class="text-center mb-4">
            <a href="index.php" class="d-inline-block mb-3 text-decoration-none">
                <div class="bg-white bg-opacity-10 d-inline-flex p-3 rounded-circle backdrop-blur">
                    <i class="fas fa-layer-group fa-2x text-accent"></i>
                </div>
            </a>
            <h2 class="display-6 fw-800 text-white mb-2">Join Elite</h2>
            <p class="text-white-50 small">Start your professional journey</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 border-start border-danger border-4 bg-danger bg-opacity-10 text-white animate-shake mb-4 text-start small border-red" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success border-0 border-start border-success border-4 bg-success bg-opacity-10 text-white animate-shake mb-4 text-start small" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label class="form-label small fw-bold text-uppercase text-white-50 tracking-wide mb-3">I want to...</label>
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="roleSeeker" value="seeker" checked autocomplete="off">
                    <label class="btn btn-outline-light w-100 py-3 rounded-4 role-selector text-start position-relative overflow-hidden" for="roleSeeker">
                        <div class="position-relative z-1 text-center">
                            <i class="fas fa-search mb-2 d-block fs-4 text-accent role-icon"></i>
                            <span class="d-block fw-bold text-white role-title small">Find a Job</span>
                        </div>
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="roleEmployer" value="employer" autocomplete="off">
                    <label class="btn btn-outline-light w-100 py-3 rounded-4 role-selector text-start position-relative overflow-hidden" for="roleEmployer">
                        <div class="position-relative z-1 text-center">
                            <i class="fas fa-briefcase mb-2 d-block fs-4 text-white-50 role-icon"></i>
                            <span class="d-block fw-bold text-white role-title small">Hire Talent</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label small fw-bold text-uppercase text-white-50 tracking-wide">Username</label>
                    <div class="input-group">
                        <span class="input-group-text glass-input border-end-0 text-white-50"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control glass-input border-start-0 ps-0 text-white" placeholder="johndoe" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold text-uppercase text-white-50 tracking-wide">Email</label>
                    <div class="input-group">
                        <span class="input-group-text glass-input border-end-0 text-white-50"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control glass-input border-start-0 ps-0 text-white" placeholder="john@example.com" required>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-uppercase text-white-50 tracking-wide">Password</label>
                    <div class="input-group">
                        <span class="input-group-text glass-input border-end-0 text-white-50"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control glass-input border-start-0 ps-0 text-white" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-uppercase text-white-50 tracking-wide">Confirm</label>
                    <div class="input-group">
                        <span class="input-group-text glass-input border-end-0 text-white-50"><i class="fas fa-lock"></i></span>
                        <input type="password" name="confirm_password" class="form-control glass-input border-start-0 ps-0 text-white" placeholder="••••••••" required>
                    </div>
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input bg-transparent border-secondary" type="checkbox" value="" id="flexCheckDefault" required>
                <label class="form-check-label small text-white-50" for="flexCheckDefault">
                    I agree to the <a href="#" class="text-accent text-decoration-none fw-bold">Terms</a> and <a href="#" class="text-accent text-decoration-none fw-bold">Privacy</a>
                </label>
            </div>

            <button type="submit" name="register" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg btn-hover-rise">
                Create Account <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-4 pt-4 border-top border-white border-opacity-10">
            <p class="text-white-50 mb-0">Already have an account? <a href="login.php" class="text-accent fw-bold text-decoration-none">Sign In</a></p>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    
    .glass-input {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
    }
    
    .form-control:focus {
        box-shadow: none;
        background: rgba(255, 255, 255, 0.1) !important;
        border-color: var(--accent) !important;
    }
    
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.3);
    }

    .role-selector {
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.02);
    }
    .role-selector:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.2);
    }
    .btn-check:checked + .role-selector {
        background: rgba(0, 210, 255, 0.1);
        border-color: var(--accent);
    }
    .btn-check:checked + .role-selector .role-icon {
        color: var(--accent) !important;
    }

    .btn-hover-rise {
        transition: all 0.3s ease;
    }
    .btn-hover-rise:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 210, 255, 0.3) !important;
    }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }
    .animate-float-slow { animation: float 6s ease-in-out infinite; }
    
    .tracking-wide { letter-spacing: 0.05em; }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake { animation: shake 0.4s ease-in-out; }
    
    /* Hide Navbar */
    .navbar { display: none !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

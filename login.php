<?php
session_start();
require_once 'includes/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] == 'employer') {
                header("Location: employer/dashboard.php");
            } else {
                header("Location: seeker/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
require_once 'includes/header.php';
?>


<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center position-relative overflow-hidden p-4" style="min-height: 100vh; background: #020617;">
    <!-- Back to Home Button -->
    <a href="index.php" class="position-absolute top-0 start-0 m-4 btn btn-light rounded-pill px-4 shadow-sm fw-bold z-3">
        <i class="fas fa-arrow-left me-2"></i> Back to Home
    </a>
    <!-- Background Effects -->
    <div class="position-absolute w-100 h-100" style="background: radial-gradient(circle at 50% 50%, rgba(0, 78, 146, 0.2), transparent 70%);"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.4; background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    
    <!-- Animated Orbs -->
    <div class="position-absolute bg-accent rounded-circle opacity-20 blur-3xl animate-float-slow" style="width: 500px; height: 500px; top: -10%; left: -10%;"></div>
    <div class="position-absolute bg-primary rounded-circle opacity-20 blur-3xl animate-float-delayed" style="width: 400px; height: 400px; bottom: 10%; right: 10%;"></div>

    <!-- Login Card -->
    <div class="glass-card w-100 rounded-5 p-4 p-md-5 position-relative z-2 animate-up" style="max-width: 500px;">
        <div class="text-center mb-5">
            <a href="index.php" class="d-inline-block mb-4 text-decoration-none">
                <div class="bg-white bg-opacity-10 d-inline-flex p-3 rounded-circle backdrop-blur">
                    <i class="fas fa-layer-group fa-2x text-accent"></i>
                </div>
            </a>
            <h2 class="display-6 fw-800 text-white mb-2">Welcome Back</h2>
            <p class="text-white-50 small">Access your professional dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 border-start border-danger border-4 bg-danger bg-opacity-10 text-white animate-shake mb-4 text-start" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-4 text-start">
                <label class="form-label small fw-bold text-uppercase text-white-50 tracking-wide">Username</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text glass-input border-end-0 text-white-50"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control glass-input border-start-0 ps-0 text-white" placeholder="e.g. johndoe" required>
                </div>
            </div>
            
            <div class="mb-4 text-start">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-bold text-uppercase text-white-50 tracking-wide mb-0">Password</label>
                    <a href="#" class="small text-accent text-decoration-none fw-bold">Forgot?</a>
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-text glass-input border-end-0 text-white-50"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control glass-input border-start-0 ps-0 text-white" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg btn-hover-rise">
                Sign In <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-5 pt-4 border-top border-white border-opacity-10">
            <p class="text-white-50 mb-0">Don't have an account? <a href="register.php" class="text-accent fw-bold text-decoration-none">Create free account</a></p>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
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
    .animate-float-delayed { animation: float 5s ease-in-out infinite 1s; }
    
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

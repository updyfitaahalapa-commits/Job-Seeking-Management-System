<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seeker Dashboard - Job Seeking Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0f172a;
            --primary-dark: #020617;
            --accent: #00d2ff;
            --accent-glow: rgba(0, 210, 255, 0.3);
            --premium-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --sidebar-bg: #ffffff;
            --main-bg: #f8fafc;
            --border-color: #e2e8f0;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--main-bg);
            color: var(--primary);
            overflow-x: hidden;
        }
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        .nav-link {
            padding: 0.8rem 1.5rem;
            color: #64748b !important;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 12px;
            margin: 0.2rem 1rem;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background: #f1f5f9;
            color: var(--primary) !important;
        }
        .nav-link.active {
            background: rgba(0, 210, 255, 0.1);
            color: #0072ff !important;
            box-shadow: inset 0 0 0 1px rgba(0, 114, 255, 0.1);
        }
        .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-right: 12px;
        }
        .main-content {
            margin-left: 280px;
            padding: 2.5rem;
            min-height: 100vh;
        }
        .top-navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            margin-bottom: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        /* Premium Components */
        .premium-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
        }
        .premium-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .premium-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(0, 210, 255, 0.3);
        }
        .premium-card:hover::before {
            opacity: 1;
        }
        .card-header-premium {
            background: transparent !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05) !important;
            padding: 1.5rem 2rem !important;
        }
        .card-header-premium h5 {
            font-weight: 800;
            margin-bottom: 0;
            color: var(--primary);
            letter-spacing: -0.5px;
        }
        .btn-premium {
            background: var(--primary);
            color: white !important;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-weight: 700;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.1);
        }
        .btn-premium:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
        }
        .btn-glow {
            background: var(--accent) !important;
            color: var(--primary) !important;
            box-shadow: 0 0 15px var(--accent-glow);
        }
        .btn-glow:hover {
            box-shadow: 0 0 25px var(--accent-glow);
            transform: scale(1.02);
        }
        .animate-up {
            animation: fadeInUp 0.5s forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h4 class="fw-800 text-dark mb-0 tracking-tighter"><i class="fas fa-layer-group text-accent me-2"></i>JOBSEEK</h4>
            <span class="small text-muted fw-semibold">Seeker Panel</span>
        </div>
        <div class="py-4">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="find_jobs.php"><i class="fas fa-search me-2"></i> Find Jobs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_applications.php"><i class="fas fa-file-alt me-2"></i> My Applications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php"><i class="fas fa-user-circle me-2"></i> My Profile</a>
                </li>
            </ul>
            <div class="mt-auto p-4 position-absolute bottom-0 w-100">
                <a href="../logout.php" class="btn btn-outline-danger w-100 rounded-pill fw-bold">Logout</a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="top-navbar d-flex align-items-center justify-content-between shadow-sm">
            <h5 class="fw-bold mb-0">Overview</h5>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end">
                    <p class="small fw-bold mb-0"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p class="small text-muted mb-0">Seeker Account</p>
                </div>
                <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                    <i class="fas fa-user text-primary"></i>
                </div>
            </div>
        </div>

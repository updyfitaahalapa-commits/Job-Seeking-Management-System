<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seeker') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_now'])) {
    $job_id = $_POST['job_id'];
    $seeker_id = $_SESSION['user_id'];

    // Check if CV is uploaded
    $stmt = $pdo->prepare("SELECT cv_path FROM profiles WHERE user_id = ?");
    $stmt->execute([$seeker_id]);
    $cv = $stmt->fetchColumn();

    if (!$cv) {
        $_SESSION['error'] = "You must upload your CV in your profile before applying!";
        header("Location: profile.php");
        exit();
    }

    // Check if already applied
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND seeker_id = ?");
    $stmt->execute([$job_id, $seeker_id]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "You have already applied for this job!";
        header("Location: dashboard.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO applications (job_id, seeker_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$job_id, $seeker_id]);
        $_SESSION['success'] = "Application submitted successfully!";
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error submitting application: " . $e->getMessage();
        header("Location: find_jobs.php");
        exit();
    }
} else {
    header("Location: find_jobs.php");
}
?>

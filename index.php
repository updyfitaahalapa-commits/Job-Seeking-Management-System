<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Fetch latest active jobs
$stmt = $pdo->prepare("
    SELECT j.*, p.company_name 
    FROM jobs j 
    JOIN profiles p ON j.employer_id = p.user_id 
    WHERE j.status = 'active' AND (j.deadline IS NULL OR j.deadline >= CURDATE())
    ORDER BY j.created_at DESC 
    LIMIT 6
");
$stmt->execute();
$latest_jobs = $stmt->fetchAll();
?>

<style>
    /* Hero Section Styles */
    .hero-section {
        background: #020617;
        position: relative;
        min-height: 100vh;
        overflow: hidden;
        padding-top: 80px; /* Account for fixed navbar */
    }

    /* Animated Background */
    .hero-bg-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.2;
        z-index: 0;
    }
    .orb-1 {
        width: 500px;
        height: 500px;
        background: var(--primary);
        top: -10%;
        left: -10%;
        animation: float 8s ease-in-out infinite;
    }
    .orb-2 {
        width: 400px;
        height: 400px;
        background: var(--accent);
        bottom: 10%;
        right: -5%;
        animation: float 6s ease-in-out infinite 1s;
    }

    /* Glass Elements */
    .glass-search-bar {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 100px;
        padding: 12px 12px 12px 35px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .glass-search-bar:focus-within {
        background: rgba(255, 255, 255, 0.12);
        border-color: var(--accent);
        box-shadow: 0 0 30px rgba(0, 210, 255, 0.3);
    }
    
    .glass-search-input {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.25rem;
        width: 100%;
        margin-right: 15px;
    }
    .glass-search-input:focus {
        outline: none;
        box-shadow: none;
        color: white;
    }
    .glass-search-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
        font-weight: 300;
    }
    select.glass-search-input option {
        color: #333;
        background: white;
    }
    select.glass-search-input {
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    /* Feature Cards */
    .feature-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 24px;
        padding: 2.5rem;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 210, 255, 0.1) 0%, transparent 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: -1;
    }
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: rgba(0, 210, 255, 0.3);
    }
    .feature-card:hover::before {
        opacity: 1;
    }
    
    .icon-wrapper {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        transition: all 0.4s ease;
        color: var(--primary);
    }
    .feature-card:hover .icon-wrapper {
        background: var(--primary-gradient);
        color: white;
        transform: rotate(5deg) scale(1.1);
    }

    /* Animations */
    @keyframes float {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(0, 20px); }
    }
    
    .animate-up {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
        color: white;
        transition: all 0.3s ease;
        height: 100%;
        min-width: 180px;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--accent);
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffffff 0%, #a5f3fc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: block;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    .stat-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        opacity: 0.7;
        font-weight: 600;
    }
</style>

<main>
    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <!-- Background Elements -->
        <div class="hero-bg-orb orb-1"></div>
        <div class="hero-bg-orb orb-2"></div>
        <div class="position-absolute w-100 h-100 top-0 start-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); z-index: 0;"></div>

        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">

                    <h1 class="display-1 fw-800 text-white mb-4 animate-up delay-100" style="line-height: 1.1; font-size: 4.5rem; text-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                        Find Your Next <br>
                        <span class="text-transparent bg-clip-text" style="background-image: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Career Defining</span> Moment
                    </h1>
                    <p class="lead text-white-50 mb-5 animate-up delay-200 mx-auto" style="max-width: 650px; font-size: 1.4rem;">
                        Connect with top-tier companies and unlock opportunities that match your potential. The future of work starts here.
                    </p>

                    <div class="glass-search-bar d-flex align-items-center mb-5 animate-up delay-300 mx-auto shadow-lg" style="max-width: 900px;">
                        <form action="seeker/find_jobs.php" method="GET" class="d-flex w-100 align-items-center">
                            <i class="fas fa-search text-white-50 me-3 fs-4"></i>
                            <input type="text" name="search" class="glass-search-input border-end border-white border-opacity-10 me-3 pe-3" placeholder="Job title, keywords...">
                            
                            <div class="d-flex align-items-center" style="min-width: 200px;">
                                <i class="fas fa-layer-group text-white-50 me-2"></i>
                                <select name="category" class="glass-search-input form-select shadow-none" style="background-image: none; cursor: pointer;">
                                    <option value="" class="text-dark">All Categories</option>
                                    <option value="IT & Software" class="text-dark">IT & Software</option>
                                    <option value="Marketing" class="text-dark">Marketing</option>
                                    <option value="Finance" class="text-dark">Finance</option>
                                    <option value="Design" class="text-dark">Design</option>
                                    <option value="Sales" class="text-dark">Sales</option>
                                    <option value="Other" class="text-dark">Other</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold ms-2 fs-5" style="min-width: 160px; box-shadow: 0 10px 20px rgba(0, 78, 146, 0.3);">
                                Search
                            </button>
                        </form>
                    </div>

                    <!-- Stats Row -->
                    <div class="row g-4 justify-content-center animate-up delay-300 mb-5">
                        <div class="col-md-auto">
                            <div class="d-flex align-items-center gap-4 p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                                <div class="text-start px-3 border-end border-white border-opacity-10">
                                    <span class="d-block fw-800 text-white h2 mb-0">10k+</span>
                                    <span class="text-white-50 small text-uppercase tracking-wide">Active Jobs</span>
                                </div>
                                <div class="text-start px-3 border-end border-white border-opacity-10">
                                    <span class="d-block fw-800 text-white h2 mb-0">500+</span>
                                    <span class="text-white-50 small text-uppercase tracking-wide">Companies</span>
                                </div>
                                <div class="text-start px-3">
                                    <span class="d-block fw-800 text-white h2 mb-0">95%</span>
                                    <span class="text-white-50 small text-uppercase tracking-wide">Success Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="d-flex justify-content-center gap-4 text-white-50 fw-bold animate-up delay-300" style="font-size: 1rem;">
                        <div class="d-flex align-items-center"><i class="fas fa-check-circle text-accent me-2"></i> Verified Employers</div>
                        <div class="d-flex align-items-center"><i class="fas fa-check-circle text-accent me-2"></i> Smart Matching</div>
                        <div class="d-flex align-items-center"><i class="fas fa-check-circle text-accent me-2"></i> Global Reach</div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light position-relative" style="margin-top: -50px; border-radius: 50px 50px 0 0; z-index: 3;">
        <div class="container py-5">
            <div class="text-center mb-5 animate-up">
                <h2 class="display-5 fw-800 text-dark mb-3">Why Choose JobSeek?</h2>
                <p class="text-muted lead mx-auto" style="max-width: 600px;">We're not just a job board. We're your career acceleration platform.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card animate-up delay-100">
                        <div class="icon-wrapper">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Fast Track Growth</h4>
                        <p class="text-muted mb-0">Get matched with roles that align with your long-term career goals and growth trajectory.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card animate-up delay-200">
                        <div class="icon-wrapper">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Verified Opportunities</h4>
                        <p class="text-muted mb-0">Every job posting and company is vetted to ensure a safe and high-quality experience.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card animate-up delay-300">
                        <div class="icon-wrapper">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Market Insights</h4>
                        <p class="text-muted mb-0">Access real-time salary data and skill trends to negotiate better and stay ahead.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Jobs Section -->
    <?php if ($latest_jobs): ?>
    <section class="py-5 bg-white position-relative" style="z-index: 3;">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-end mb-5 animate-up">
                <div>
                    <h2 class="display-6 fw-800 text-dark mb-2">Latest Opportunities</h2>
                    <p class="text-muted lead mb-0">Discover roles that match your expertise.</p>
                </div>
                <a href="login.php" class="btn btn-outline-primary rounded-pill px-4 fw-bold">View All Jobs</a>
            </div>

            <div class="row g-4">
                <?php foreach ($latest_jobs as $job): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 animate-up delay-100 feature-card p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light p-2 rounded-3 text-primary me-3">
                                    <i class="fas fa-building fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($job['company_name']); ?></h6>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1 text-accent"></i><?php echo htmlspecialchars($job['location']); ?></small>
                                </div>
                            </div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 fw-bold small">
                                <?php echo htmlspecialchars($job['job_type']); ?>
                            </span>
                        </div>
                        
                        <h5 class="fw-800 text-dark mb-3"><?php echo htmlspecialchars($job['title']); ?></h5>
                        
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="badge bg-light text-muted fw-medium rounded-pill px-3 py-2">
                                <i class="fas fa-money-bill-wave me-1 text-success"></i> <?php echo htmlspecialchars($job['salary'] ?: 'Negotiable'); ?>
                            </span>
                            <span class="badge bg-light text-muted fw-medium rounded-pill px-3 py-2">
                                <i class="fas fa-layer-group me-1 text-info"></i> <?php echo htmlspecialchars($job['category']); ?>
                            </span>
                        </div>

                        <div class="mt-auto pt-3 border-top border-light d-flex justify-content-between align-items-center">
                            <small class="text-muted fw-bold">
                                <?php if ($job['deadline']): ?>
                                    <i class="far fa-clock me-1 text-danger"></i> Deadline: <span class="text-dark"><?php echo date('M d, Y', strtotime($job['deadline'])); ?></span>
                                <?php else: ?>
                                    <i class="far fa-clock me-1 text-success"></i> Open until filled
                                <?php endif; ?>
                            </small>
                            <a href="login.php" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-sm">Apply Now</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<script>
    // Simple reveal animation on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-up').forEach((el) => {
        el.style.opacity = 0;
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.8s ease-out';
        observer.observe(el);
    });
</script>

<?php include 'includes/footer.php'; ?>

    <footer class="py-5" style="background: #000428; color: rgba(255,255,255,0.6);">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <h4 class="fw-800 text-white mb-3 tracking-tighter">JOBSEEK</h4>
                    <p class="small mb-0">Elevating the recruitment experience with state-of-the-art matching technology.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end gap-4 mb-3">
                        <a href="#" class="text-white-50"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-instagram"></i></a>
                    </div>
                    <span class="small">&copy; <?php echo date("Y"); ?> Job Seeking Management System. All Rights Reserved.</span>
                </div>
            </div>
        </div>
    </footer>
    <!-- Global Scripts -->
    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled', 'shadow-sm');
                    navbar.style.background = 'rgba(2, 6, 23, 0.95)';
                    navbar.style.backdropFilter = 'blur(10px)';
                } else {
                    navbar.classList.remove('scrolled', 'shadow-sm');
                    navbar.style.background = 'transparent';
                    navbar.style.backdropFilter = 'none';
                }
            }
        });
    </script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

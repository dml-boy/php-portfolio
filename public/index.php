<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alamutu Mubarak | Full-Stack Developer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="../bootstrap/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary: #f39c12;
            --secondary: #2ecc71;
            --dark: #121212;
            --darker: #1a1a1a;
            --light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            overflow-x: hidden;
            padding-top: 80px;
        }

        /* Modern Glass Navbar */
        .navbar {
            background: rgba(26, 26, 26, 0.95) !important;
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            color: var(--primary) !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        .nav-link {
            color: var(--light) !important;
            margin: 0 1.2rem;
            position: relative;
            font-weight: 400;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        /* Animated Hero Section */
        .hero-section {
            height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .hero-title {
            font-size: 4.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.8s 0.2s forwards;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.8s 0.4s forwards;
            color: rgba(255, 255, 255, 0.8);
        }

        .hero-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0.1;
        }

        /* Interactive Project Cards */
        .project-card {
            background: var(--darker);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .project-image {
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .project-card:hover .project-image {
            transform: scale(1.05);
        }

        .tech-badge {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            margin: 0.2rem;
            font-weight: 400;
        }

        /* Animated Sections */
        section {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.8rem;
            }

            .nav-link {
                margin: 0.5rem 0;
            }
        }
    </style>
</head>

<body>
    <!-- Dynamic Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">DML Dev</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#projects">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Connect</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section with Particles -->
        <section class="hero-section">
            <canvas class="hero-canvas" id="particleCanvas"></canvas>
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title">Code & Design</h1>
                    <p class="hero-subtitle">Building immersive digital experiences</p>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-4 text-center mb-4">
                        <img src="../IMG-20240718-WA0014.jpg"
                            class="rounded-circle img-fluid profile-image"
                            alt="Alamutu Mubarak"
                            loading="lazy">
                    </div>
                    <div class="col-lg-8">
                        <h2 class="display-5 mb-4">About Me</h2>
                        <p class="lead">Full-stack developer with expertise in modern web technologies. Passionate about creating performant, accessible, and visually stunning applications.</p>
                        <div class="mt-4">
                            <h5>Core Technologies</h5>
                            <div class="d-flex flex-wrap">
                                <span class="tech-badge">HTML</span>
                                <span class="tech-badge">CSS</span>
                                <span class="tech-badge">Javascriptg</span>
                                <span class="tech-badge">Node.js</span>
                                <span class="tech-badge">TypeScript</span>
                                <span class="tech-badge">MongoDB</span>
                                <span class="tech-badge">Bootstrap</span>
                                <span class="tech-badge">Angular</span>
                                <span class="tech-badge">MySQL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Project Showcase -->
        <section id="projects" class="py-5 bg-darker">
            <div class="container">
                <h2 class="display-5 text-center mb-5">Featured Projects</h2>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="project-card">
                            <div class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="images/project1-1.jpg"
                                            class="d-block w-100 project-image"
                                            alt="Project 1"
                                            loading="lazy">
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="h5">E-Commerce Platform</h3>
                                <p class="text-muted">Full-stack shopping solution with real-time inventory</p>
                                <div class="d-flex flex-wrap">
                                    <span class="tech-badge">Next.js</span>
                                    <span class="tech-badge">MySQL</span>
                                    <span class="tech-badge">Bootstrap</span>
                                    <span class="tech-badge">Node.js</span>
                                    <span class="tech-badge">React.js</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add more project cards -->
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-5">
            <div class="container text-center">
                <h2 class="display-5 mb-4">Join the Community</h2>
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div id="Connect" class="d-flex flex-column flex-md-row gap-4 justify-content-center">
                            <a href="register.php"
                                class="btn btn-lg rounded-pill px-5 py-3 text-uppercase fw-bold transition-all"
                                style="background: var(--primary); color: var(--darker);">
                                Get Started
                                <span class="ms-2">🚀</span>
                            </a>

                            <a href="login.php"
                                class="btn btn-lg rounded-pill px-5 py-3 text-uppercase fw-bold border-2 transition-all"
                                style="border-color: var(--primary); color: var(--primary);">
                                Returning User
                                <span class="ms-2">👋</span>
                            </a>
                        </div>

                        <div class="mt-5">
                            <p class="text-muted">Prefer direct contact?
                                <a href="mailto:alamutumubarak01@gmail.com" class="text-primary text-decoration-none">
                                    Email me instead
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once '../private/shared/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Intersection Observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.navbar');
            nav.style.background = window.scrollY > 50 ?
                'rgba(26, 26, 26, 0.98)' :
                'rgba(26, 26, 26, 0.95)';
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            });
        });

        // Simple Particle Animation
        const canvas = document.getElementById('particleCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        class Particle {
            constructor() {
                this.reset();
            }

            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 1;
                this.speed = Math.random() * 0.5 + 0.5;
            }

            update() {
                this.y += this.speed;
                if (this.y > canvas.height) this.reset();
            }

            draw() {
                ctx.fillStyle = `rgba(243, 156, 18, ${this.size/3})`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        const particles = Array.from({
            length: 100
        }, () => new Particle());

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(particle => {
                particle.update();
                particle.draw();
            });
            requestAnimationFrame(animate);
        }

        animate();
    </script>
</body>

</html>

</html>

</html>
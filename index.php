<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AccuBalance - Personal Finance Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .gradient-text {
            background: linear-gradient(45deg, #3B82F6, #10B981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #EFF6FF 0%, #F0FDFA 100%);
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .feature-card {
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.2) rotate(10deg);
        }

        .scroll-indicator {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .testimonial-card {
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="assets/logo.png" alt="AccuBalance Logo" class="h-10 w-10">
                    <span class="ml-2 text-xl font-bold text-gray-900">AccuBalance</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-blue-600">Features</a>
                    <a href="about.php" class="text-gray-600 hover:text-blue-600">About</a>
                    <a href="docs.php" class="text-gray-600 hover:text-blue-600">Documentation</a>
                    <?php if ($is_logged_in): ?>
                        <a href="dashboard.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-blue-600">Login</a>
                        <a href="register.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">
                        <span class="gradient-text">Simplify Finances,</span><br>
                        Amplify Success
                    </h1>
                    <p class="text-gray-600 text-lg mb-8">
                        Take control of your financial journey with AccuBalance. 
                        Smart budgeting, intuitive tracking, and AI-powered insights 
                        all in one place.
                    </p>
                    <div class="space-x-4">
                        <a href="register.php" 
                           class="bg-blue-500 text-white px-8 py-3 rounded-lg hover:bg-blue-600 transition-all transform hover:scale-105">
                            Start Free Trial
                        </a>
                        <a href="#demo" 
                           class="text-gray-600 hover:text-blue-600">
                            Watch Demo <i class="fas fa-play-circle ml-2"></i>
                        </a>
                    </div>
                </div>
                <div class="relative" data-aos="fade-left">
                    <img src="assets/dashboard.png" 
                         alt="Dashboard Preview" 
                         class="rounded-lg shadow-2xl floating">
                    <div class="absolute -bottom-10 -left-10 bg-white p-4 rounded-lg shadow-lg">
                        <div class="flex items-center space-x-2">
                            <div class="text-green-500 text-2xl">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Monthly Savings</p>
                                <p class="text-lg font-bold">+27.4%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 scroll-indicator">
                <i class="fas fa-chevron-down text-gray-400"></i>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold mb-4">Powerful Features</h2>
                <p class="text-gray-600">Everything you need to manage your finances effectively</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $features = [
                    [
                        'icon' => 'fa-chart-pie',
                        'title' => 'Smart Budgeting',
                        'description' => 'Set and track budgets with AI-powered recommendations',
                        'color' => 'blue'
                    ],
                    [
                        'icon' => 'fa-bolt',
                        'title' => 'Real-time Tracking',
                        'description' => 'Monitor your expenses and income in real-time',
                        'color' => 'green'
                    ],
                    [
                        'icon' => 'fa-brain',
                        'title' => 'AI Insights',
                        'description' => 'Get personalized financial insights and predictions',
                        'color' => 'purple'
                    ],
                    [
                        'icon' => 'fa-shield-alt',
                        'title' => 'Bank-level Security',
                        'description' => 'Your data is protected with enterprise-grade security',
                        'color' => 'red'
                    ],
                    [
                        'icon' => 'fa-mobile-alt',
                        'title' => 'Mobile Ready',
                        'description' => 'Access your finances from any device, anywhere',
                        'color' => 'yellow'
                    ],
                    [
                        'icon' => 'fa-sync',
                        'title' => 'Auto Sync',
                        'description' => 'Automatically sync with your bank accounts',
                        'color' => 'indigo'
                    ]
                ];

                foreach ($features as $index => $feature):
                ?>
                    <div class="feature-card bg-white rounded-lg p-6 shadow-lg" 
                         data-aos="fade-up" 
                         data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="feature-icon bg-<?php echo $feature['color']; ?>-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas <?php echo $feature['icon']; ?> text-<?php echo $feature['color']; ?>-500 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2"><?php echo $feature['title']; ?></h3>
                        <p class="text-gray-600"><?php echo $feature['description']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold mb-4">What Our Users Say</h2>
                <p class="text-gray-600">Join thousands of satisfied users managing their finances with AccuBalance</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $testimonials = [
                    [
                        'name' => 'Sarah Johnson',
                        'role' => 'Small Business Owner',
                        'content' => 'AccuBalance has transformed how I manage both personal and business finances. The insights are invaluable.',
                        'image' => 'assets/logo.png'
                    ],
                    [
                        'name' => 'Michael Chen',
                        'role' => 'Software Engineer',
                        'content' => 'The AI-powered features and clean interface make financial management actually enjoyable!',
                        'image' => 'assets/profileUser.png'
                    ],
                    [
                        'name' => 'Emily Rodriguez',
                        'role' => 'Freelancer',
                        'content' => 'Finally found a solution that helps me track multiple income streams and expenses effortlessly.',
                        'image' => 'assets/profileUser.png'
                    ]
                ];

                foreach ($testimonials as $index => $testimonial):
                ?>
                    <div class="testimonial-card bg-white rounded-lg p-6 shadow-lg" 
                         data-aos="fade-up" 
                         data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="flex items-center mb-4">
                            <img src="assets/<?php echo $testimonial['image']; ?>" 
                                 alt="<?php echo $testimonial['name']; ?>" 
                                 class="w-12 h-12 rounded-full">
                            <div class="ml-4">
                                <h4 class="font-bold"><?php echo $testimonial['name']; ?></h4>
                                <p class="text-gray-600 text-sm"><?php echo $testimonial['role']; ?></p>
                            </div>
                        </div>
                        <p class="text-gray-600">"<?php echo $testimonial['content']; ?>"</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div data-aos="fade-up">
                <h2 class="text-3xl font-bold text-white mb-4">
                    Ready to Take Control of Your Finances?
                </h2>
                <p class="text-blue-100 mb-8">
                    Join thousands of users who are already managing their finances smarter with AccuBalance
                </p>
                <a href="register.php" 
                   class="bg-white text-blue-600 px-8 py-3 rounded-lg hover:bg-blue-50 transition-all transform hover:scale-105">
                    Start Your Free Trial
                </a>
                <p class="text-blue-100 mt-4 text-sm">No credit card required</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="assets/logo.png" alt="AccuBalance Logo" class="h-8 w-8">
                        <span class="ml-2 text-xl font-bold text-white">AccuBalance</span>
                    </div>
                    <p class="text-gray-400">
                        Simplify Finances, Amplify Success
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="hover:text-white">Features</a></li>
                        <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
                        <li><a href="docs.php" class="hover:text-white">Documentation</a></li>
                        <li><a href="#updates" class="hover:text-white">Updates</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="about.php" class="hover:text-white">About</a></li>
                        <li><a href="#team" class="hover:text-white">Team</a></li>
                        <li><a href="#careers" class="hover:text-white">Careers</a></li>
                        <li><a href="#contact" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#privacy" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#terms" class="hover:text-white">Terms of Service</a></li>
                        <li><a href="#security" class="hover:text-white">Security</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    © <?php echo date('Y'); ?> AccuBalance. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 
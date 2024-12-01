<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About AccuBalance - Features & Capabilities</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
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

        .gradient-text {
            background: linear-gradient(45deg, #3B82F6, #10B981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .screenshot {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .screenshot:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center" data-aos="fade-up">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <span class="gradient-text">AccuBalance Features</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Discover how AccuBalance revolutionizes personal finance management with 
                    cutting-edge features and intelligent insights.
                </p>
            </div>
        </div>
    </section>

    <!-- Core Features -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-16">Core Features</h2>
            <div class="feature-grid">
                <?php
                $core_features = [
                    [
                        'icon' => 'fa-brain',
                        'title' => 'AI-Powered Insights',
                        'description' => 'Advanced machine learning algorithms analyze your spending patterns and provide personalized financial recommendations.',
                        'color' => 'blue'
                    ],
                    [
                        'icon' => 'fa-chart-line',
                        'title' => 'Real-time Analytics',
                        'description' => 'Interactive dashboards and reports with real-time updates on your financial status and trends.',
                        'color' => 'green'
                    ],
                    [
                        'icon' => 'fa-shield-alt',
                        'title' => 'Bank-Grade Security',
                        'description' => '256-bit encryption, two-factor authentication, and regular security audits to protect your data.',
                        'color' => 'red'
                    ],
                    [
                        'icon' => 'fa-sync',
                        'title' => 'Automatic Syncing',
                        'description' => 'Seamlessly sync with your bank accounts, credit cards, and investment portfolios.',
                        'color' => 'purple'
                    ],
                    [
                        'icon' => 'fa-bullseye',
                        'title' => 'Smart Goals',
                        'description' => 'Set and track financial goals with AI-assisted recommendations and progress tracking.',
                        'color' => 'yellow'
                    ],
                    [
                        'icon' => 'fa-bell',
                        'title' => 'Smart Alerts',
                        'description' => 'Customizable notifications for bills, unusual spending, and investment opportunities.',
                        'color' => 'indigo'
                    ]
                ];

                foreach ($core_features as $feature):
                ?>
                    <div class="feature-card bg-white rounded-lg p-6 shadow-lg" data-aos="fade-up">
                        <div class="feature-icon bg-<?php echo $feature['color']; ?>-100 w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas <?php echo $feature['icon']; ?> text-<?php echo $feature['color']; ?>-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-4"><?php echo $feature['title']; ?></h3>
                        <p class="text-gray-600"><?php echo $feature['description']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Advanced Capabilities -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-16">Advanced Capabilities</h2>
            
            <!-- Budget Management -->
            <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
                <div class="order-2 md:order-1" data-aos="fade-right">
                    <h3 class="text-2xl font-bold mb-4">Intelligent Budget Management</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>AI-powered budget recommendations based on spending patterns</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Dynamic category adjustments and rollover budgets</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Visual progress tracking and forecasting</span>
                        </li>
                    </ul>
                </div>
                <div class="order-1 md:order-2" data-aos="fade-left">
                    <img src="assets/budget.png" alt="Budget Management" class="rounded-lg shadow-xl screenshot">
                </div>
            </div>

            <!-- Investment Tracking -->
            <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
                <div data-aos="fade-right">
                    <img src="assets/investment.png" alt="Investment Tracking" class="rounded-lg shadow-xl screenshot">
                </div>
                <div data-aos="fade-left">
                    <h3 class="text-2xl font-bold mb-4">Comprehensive Investment Tracking</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Real-time portfolio performance monitoring</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Automated dividend and return tracking</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Investment recommendations based on risk profile</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bill Management -->
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1" data-aos="fade-right">
                    <h3 class="text-2xl font-bold mb-4">Smart Bill Management</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Automated bill detection and reminders</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Recurring payment tracking and optimization</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Bill negotiation recommendations</span>
                        </li>
                    </ul>
                </div>
                <div class="order-1 md:order-2" data-aos="fade-left">
                    <img src="assets/bill.png" alt="Bill Management" class="rounded-lg shadow-xl screenshot">
                </div>
            </div>
        </div>
    </section>

    <!-- Integration & API -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Powerful Integrations</h2>
                <p class="text-gray-600">Connect with your favorite financial services and tools</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <?php
                $integrations = [
                    ['name' => 'Major Banks', 'icon' => 'fa-university'],
                    ['name' => 'Investment Platforms', 'icon' => 'fa-chart-line'],
                    ['name' => 'Payment Services', 'icon' => 'fa-credit-card'],
                    ['name' => 'Accounting Software', 'icon' => 'fa-calculator'],
                    ['name' => 'Crypto Exchanges', 'icon' => 'fa-bitcoin'],
                    ['name' => 'Tax Software', 'icon' => 'fa-file-invoice-dollar'],
                    ['name' => 'ERP Systems', 'icon' => 'fa-network-wired'],
                    ['name' => 'Custom APIs', 'icon' => 'fa-code']
                ];

                foreach ($integrations as $integration):
                ?>
                    <div class="bg-white p-6 rounded-lg shadow-lg text-center" data-aos="fade-up">
                        <i class="fas <?php echo $integration['icon']; ?> text-4xl text-blue-500 mb-4"></i>
                        <h3 class="font-medium"><?php echo $integration['name']; ?></h3>
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
                    Ready to Experience AccuBalance?
                </h2>
                <p class="text-blue-100 mb-8">
                    Start your journey to better financial management today
                </p>
                <a href="register.php" 
                   class="bg-white text-blue-600 px-8 py-3 rounded-lg hover:bg-blue-50 transition-all transform hover:scale-105">
                    Start Free Trial
                </a>
                <p class="text-blue-100 mt-4 text-sm">No credit card required</p>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Image modal functionality
        document.querySelectorAll('.screenshot').forEach(img => {
            img.addEventListener('click', function() {
                // Implement lightbox/modal for screenshots
            });
        });
    </script>
</body>
</html> 
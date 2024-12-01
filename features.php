<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .demo-card {
            transition: all 0.3s ease;
        }

        .demo-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            transition: all 0.3s ease;
        }

        .demo-card:hover .feature-icon {
            transform: scale(1.2) rotate(10deg);
        }

        .interactive-demo {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .demo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .interactive-demo:hover .demo-overlay {
            opacity: 1;
        }

        .gradient-text {
            background: linear-gradient(45deg, #3B82F6, #10B981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .demo-animation {
            animation: demoSlide 0.5s ease-out;
        }

        @keyframes demoSlide {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/public-nav.php'; ?>

    <!-- Hero Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center" data-aos="fade-up">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <span class="gradient-text">Powerful Features</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience the full power of AccuBalance with our comprehensive suite of financial management tools.
                </p>
            </div>
        </div>
    </section>

    <!-- Interactive Demo Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Smart Budget Management -->
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Smart Budget Management</h2>
                    <p class="text-gray-600">Try our AI-powered budget recommendations</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 demo-animation">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="interactive-demo">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <!-- Interactive Budget Demo -->
                                <canvas id="budgetDemo" height="300"></canvas>
                            </div>
                            <div class="demo-overlay">
                                <button class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-all">
                                    Try Demo
                                </button>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-xl font-bold">Intelligent Budget Allocation</h3>
                            <p class="text-gray-600">
                                Our AI analyzes your spending patterns and suggests optimal budget allocations across categories.
                            </p>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Smart category detection</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Automated adjustments</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Personalized recommendations</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Analytics -->
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Real-time Analytics</h2>
                    <p class="text-gray-600">Interactive charts and insights at your fingertips</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 demo-animation">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-4 order-2 md:order-1">
                            <h3 class="text-xl font-bold">Dynamic Visualization</h3>
                            <p class="text-gray-600">
                                Explore your financial data through interactive charts and customizable dashboards.
                            </p>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Multiple chart types</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Custom date ranges</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Export capabilities</span>
                                </div>
                            </div>
                        </div>
                        <div class="interactive-demo order-1 md:order-2">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <!-- Interactive Analytics Demo -->
                                <canvas id="analyticsDemo" height="300"></canvas>
                            </div>
                            <div class="demo-overlay">
                                <button class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-all">
                                    Try Demo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Investment Tracking -->
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Investment Tracking</h2>
                    <p class="text-gray-600">Monitor and optimize your investment portfolio</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 demo-animation">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="interactive-demo">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <!-- Interactive Investment Demo -->
                                <canvas id="investmentDemo" height="300"></canvas>
                            </div>
                            <div class="demo-overlay">
                                <button class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-all">
                                    Try Demo
                                </button>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-xl font-bold">Portfolio Management</h3>
                            <p class="text-gray-600">
                                Track performance, analyze returns, and get investment recommendations.
                            </p>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Real-time market data</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Performance analytics</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span>Risk assessment</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Grid -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">More Features</h2>
                <p class="text-gray-600">Discover all the tools at your disposal</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                $features = [
                    [
                        'icon' => 'fa-bell',
                        'title' => 'Smart Notifications',
                        'description' => 'Get timely alerts for bills, unusual spending, and investment opportunities.',
                        'color' => 'blue'
                    ],
                    [
                        'icon' => 'fa-lock',
                        'title' => 'Bank-Grade Security',
                        'description' => 'Your data is protected with enterprise-level encryption and security measures.',
                        'color' => 'green'
                    ],
                    [
                        'icon' => 'fa-sync',
                        'title' => 'Auto Sync',
                        'description' => 'Automatically sync with your bank accounts and credit cards.',
                        'color' => 'purple'
                    ],
                    [
                        'icon' => 'fa-file-export',
                        'title' => 'Export Options',
                        'description' => 'Export your data in multiple formats for tax preparation and analysis.',
                        'color' => 'red'
                    ],
                    [
                        'icon' => 'fa-users',
                        'title' => 'Multi-User Access',
                        'description' => 'Share access with family members or financial advisors.',
                        'color' => 'yellow'
                    ],
                    [
                        'icon' => 'fa-mobile-alt',
                        'title' => 'Mobile App',
                        'description' => 'Access your finances on the go with our mobile application.',
                        'color' => 'indigo'
                    ]
                ];

                foreach ($features as $feature):
                ?>
                    <div class="demo-card bg-gray-50 rounded-lg p-6" data-aos="fade-up">
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

    <!-- CTA Section -->
    <section class="py-20 bg-blue-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div data-aos="fade-up">
                <h2 class="text-3xl font-bold text-white mb-4">
                    Ready to Experience These Features?
                </h2>
                <p class="text-blue-100 mb-8">
                    Start your free trial today and explore all the features AccuBalance has to offer
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

        // Initialize demo charts
        document.addEventListener('DOMContentLoaded', function() {
            // Budget Demo Chart
            const budgetCtx = document.getElementById('budgetDemo').getContext('2d');
            new Chart(budgetCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Housing', 'Food', 'Transportation', 'Entertainment', 'Savings'],
                    datasets: [{
                        data: [35, 20, 15, 10, 20],
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#F59E0B',
                            '#EF4444',
                            '#8B5CF6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Analytics Demo Chart
            const analyticsCtx = document.getElementById('analyticsDemo').getContext('2d');
            new Chart(analyticsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Income',
                        data: [3000, 3500, 3200, 3800, 3600, 4000],
                        borderColor: '#10B981',
                        tension: 0.4
                    }, {
                        label: 'Expenses',
                        data: [2500, 2800, 2600, 2900, 2700, 2800],
                        borderColor: '#EF4444',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Investment Demo Chart
            const investmentCtx = document.getElementById('investmentDemo').getContext('2d');
            new Chart(investmentCtx, {
                type: 'bar',
                data: {
                    labels: ['Stocks', 'Bonds', 'Real Estate', 'Crypto', 'Cash'],
                    datasets: [{
                        label: 'Portfolio Allocation',
                        data: [40, 25, 15, 10, 10],
                        backgroundColor: [
                            '#3B82F6',
                            '#10B981',
                            '#F59E0B',
                            '#EF4444',
                            '#8B5CF6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html> 
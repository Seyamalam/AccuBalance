<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .pricing-card {
            transition: all 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
        }

        .popular-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .feature-check {
            animation: checkmark 0.3s ease-in-out;
        }

        @keyframes checkmark {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .gradient-text {
            background: linear-gradient(45deg, #3B82F6, #10B981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/public-nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-20">
        <!-- Header -->
        <div class="text-center mb-16" data-aos="fade-up">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                <span class="gradient-text">Simple, Transparent Pricing</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Choose the perfect plan for your financial journey. All plans include our core features.
            </p>
        </div>

        <!-- Billing Toggle -->
        <div class="flex justify-center items-center space-x-4 mb-12" data-aos="fade-up" data-aos-delay="100">
            <span class="text-gray-600">Monthly</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" id="billingToggle">
                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
            <span class="text-gray-600">Annual <span class="text-green-500 text-sm">(Save 20%)</span></span>
        </div>

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <?php
            $plans = [
                [
                    'name' => 'Basic',
                    'description' => 'Perfect for personal finance tracking',
                    'monthly_price' => 9.99,
                    'annual_price' => 95.88,
                    'features' => [
                        'Unlimited transactions',
                        'Basic budgeting tools',
                        'Mobile app access',
                        'Email support',
                        'Basic reports'
                    ],
                    'color' => 'blue'
                ],
                [
                    'name' => 'Pro',
                    'description' => 'Advanced features for serious money management',
                    'monthly_price' => 19.99,
                    'annual_price' => 191.88,
                    'popular' => true,
                    'features' => [
                        'Everything in Basic',
                        'AI-powered insights',
                        'Investment tracking',
                        'Priority support',
                        'Advanced analytics',
                        'Custom categories',
                        'Data export'
                    ],
                    'color' => 'indigo'
                ],
                [
                    'name' => 'Enterprise',
                    'description' => 'Custom solutions for businesses',
                    'monthly_price' => 49.99,
                    'annual_price' => 479.88,
                    'features' => [
                        'Everything in Pro',
                        'Multiple users',
                        'API access',
                        'Custom integrations',
                        'Dedicated support',
                        'Training sessions',
                        'Custom reporting',
                        'SLA guarantee'
                    ],
                    'color' => 'purple'
                ]
            ];

            foreach ($plans as $index => $plan):
            ?>
                <div class="pricing-card bg-white rounded-2xl shadow-xl overflow-hidden" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $index * 100; ?>">
                    <?php if (isset($plan['popular'])): ?>
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-center py-2 popular-badge">
                            Most Popular
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-2"><?php echo $plan['name']; ?></h3>
                        <p class="text-gray-600 mb-6"><?php echo $plan['description']; ?></p>
                        
                        <div class="mb-6">
                            <div class="monthly-price <?php echo $index === 1 ? 'text-blue-600' : 'text-gray-900'; ?>">
                                <span class="text-4xl font-bold">$<?php echo number_format($plan['monthly_price'], 2); ?></span>
                                <span class="text-gray-600">/month</span>
                            </div>
                            <div class="annual-price hidden <?php echo $index === 1 ? 'text-blue-600' : 'text-gray-900'; ?>">
                                <span class="text-4xl font-bold">$<?php echo number_format($plan['annual_price'] / 12, 2); ?></span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-green-500 text-sm">Billed annually</div>
                            </div>
                        </div>
                        
                        <a href="register.php?plan=<?php echo strtolower($plan['name']); ?>" 
                           class="block w-full text-center py-3 rounded-lg mb-8 transition-all
                                  <?php echo $index === 1 ? 
                                    'bg-blue-500 text-white hover:bg-blue-600' : 
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            Get Started
                        </a>
                        
                        <div class="space-y-4">
                            <?php foreach ($plan['features'] as $feature): ?>
                                <div class="flex items-center space-x-3 feature-check">
                                    <i class="fas fa-check text-green-500"></i>
                                    <span class="text-gray-600"><?php echo $feature; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- FAQ Section -->
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-8" data-aos="fade-up">Frequently Asked Questions</h2>
            
            <div class="space-y-4" data-aos="fade-up" data-aos-delay="100">
                <?php
                $faqs = [
                    [
                        'question' => 'Can I change plans later?',
                        'answer' => 'Yes, you can upgrade or downgrade your plan at any time. Changes will be reflected in your next billing cycle.'
                    ],
                    [
                        'question' => 'Is there a free trial?',
                        'answer' => 'Yes, all plans come with a 14-day free trial. No credit card required.'
                    ],
                    [
                        'question' => 'What payment methods do you accept?',
                        'answer' => 'We accept all major credit cards, PayPal, and bank transfers for annual plans.'
                    ],
                    [
                        'question' => 'Can I cancel anytime?',
                        'answer' => 'Yes, you can cancel your subscription at any time. No questions asked.'
                    ]
                ];

                foreach ($faqs as $faq):
                ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold mb-2"><?php echo $faq['question']; ?></h3>
                        <p class="text-gray-600"><?php echo $faq['answer']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Handle billing toggle
        const billingToggle = document.getElementById('billingToggle');
        const monthlyPrices = document.querySelectorAll('.monthly-price');
        const annualPrices = document.querySelectorAll('.annual-price');

        billingToggle.addEventListener('change', function() {
            monthlyPrices.forEach(price => price.classList.toggle('hidden'));
            annualPrices.forEach(price => price.classList.toggle('hidden'));
        });
    </script>
</body>
</html> 
<?php
require_once 'config.php';
checkAuth();

// Fetch user preferences for widgets
$preferences_query = "SELECT dashboard_widgets FROM user_preferences WHERE user_id = " . $_SESSION['user_id'];
$preferences = $conn->query($preferences_query)->fetch_assoc();
$widgets = json_decode(isset($preferences['dashboard_widgets']) ? $preferences['dashboard_widgets'] : '{}', true);

// Add after line 6:
$user_query = "SELECT username FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

// Initialize loop index before the widgets loop
$loop_index = 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        .slide-in {
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 50;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>

    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="text-center mb-8 fade-in">
            <h1 class="text-3xl font-bold text-gray-900">Welcome back, <?php echo ucfirst($user['username']); ?>!</h1>
            <p class="text-gray-600 mt-2">Here's your financial overview</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 slide-in">
            <button onclick="location.href='transactions.php?action=new'" 
                    class="p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all transform hover:scale-105">
                <i class="fas fa-plus mb-2"></i>
                <span class="block">Add Transaction</span>
            </button>
            <button onclick="location.href='bills.php'" 
                    class="p-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all transform hover:scale-105">
                <i class="fas fa-file-invoice-dollar mb-2"></i>
                <span class="block">Pay Bills</span>
            </button>
            <button onclick="location.href='budgets.php'" 
                    class="p-4 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-all transform hover:scale-105">
                <i class="fas fa-chart-pie mb-2"></i>
                <span class="block">View Budget</span>
            </button>
            <button onclick="location.href='goals.php'" 
                    class="p-4 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-all transform hover:scale-105">
                <i class="fas fa-flag mb-2"></i>
                <span class="block">Track Goals</span>
            </button>
        </div>

        <!-- Main Dashboard Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Dynamic Widgets -->
            <?php foreach ($widgets as $widget => $enabled):
                if (!$enabled) continue;
                
                echo '<div class="card bg-white rounded-lg shadow p-6 slide-in" 
                           style="animation-delay: ' . ($loop_index * 0.1) . 's">';
                
                $widget_file = 'widgets/' . $widget . '.php';
                if (file_exists($widget_file)) {
                    include $widget_file;
                } else {
                    echo '<p class="text-gray-500">Widget not found: ' . htmlspecialchars($widget) . '</p>';
                }
                
                echo '</div>';
                $loop_index++;
            endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading overlay
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.opacity = '0';
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 500);
            }, 1000);

            // Initialize charts with animations
            const charts = document.querySelectorAll('.chart');
            charts.forEach(chart => {
                const ctx = chart.getContext('2d');
                // Chart initialization code here
            });

            // Add intersection observer for lazy loading
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            });

            document.querySelectorAll('.card').forEach(card => {
                observer.observe(card);
            });
        });

        // Add smooth scrolling
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
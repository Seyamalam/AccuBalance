<?php
require_once 'config.php';
checkAuth();

// Get date range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01', strtotime('-6 months'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Get report type
$report_type = isset($_GET['type']) ? $_GET['type'] : 'overview';

// Get user preferences
$preferences_query = "SELECT visualization_preferences FROM user_preferences WHERE user_id = " . $_SESSION['user_id'];
$preferences_result = $conn->query($preferences_query);
if ($preferences_result === false) {
    $preferences = ['visualization_preferences' => '{}'];
} else {
    $preferences = $preferences_result->fetch_assoc();
    if ($preferences === null) {
        $preferences = ['visualization_preferences' => '{}'];
    }
}
$viz_preferences = json_decode($preferences['visualization_preferences'] ?? '{}', true);

// Fetch report data based on type
function getReportData($type, $start_date, $end_date) {
    global $conn;
    
    // Sanitize inputs
    $start_date = $conn->real_escape_string($start_date);
    $end_date = $conn->real_escape_string($end_date);
    $user_id = (int)$_SESSION['user_id'];
    
    switch ($type) {
        case 'income':
            $query = "
                SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') as period,
                    category,
                    SUM(amount) as total
                FROM transactions t
                JOIN accounts a ON t.account_id = a.id
                WHERE a.user_id = $user_id
                AND t.type = 'income'
                AND transaction_date BETWEEN '$start_date' AND '$end_date'
                GROUP BY period, category
                ORDER BY period ASC";
            break;
            
        case 'expense':
            $query = "
                SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') as period,
                    category,
                    SUM(amount) as total
                FROM transactions t
                JOIN accounts a ON t.account_id = a.id
                WHERE a.user_id = $user_id
                AND t.type = 'expense'
                AND transaction_date BETWEEN '$start_date' AND '$end_date'
                GROUP BY period, category
                ORDER BY period ASC";
            break;
            
        case 'balance':
            $query = "
                SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') as period,
                    a.account_name,
                    SUM(CASE WHEN t.type = 'income' THEN amount ELSE -amount END) as net_change
                FROM transactions t
                JOIN accounts a ON t.account_id = a.id
                WHERE a.user_id = $user_id
                AND transaction_date BETWEEN '$start_date' AND '$end_date'
                GROUP BY period, a.id
                ORDER BY period ASC";
            break;
            
        default: // overview
            $query = "
                SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') as period,
                    t.type,
                    SUM(amount) as total
                FROM transactions t
                JOIN accounts a ON t.account_id = a.id
                WHERE a.user_id = $user_id
                AND transaction_date BETWEEN '$start_date' AND '$end_date'
                GROUP BY period, type
                ORDER BY period ASC";
    }
    
    $result = $conn->query($query);
    if ($result === false) {
        die("Error executing query: " . $conn->error);
    }
    return $result;
}

// Initialize report data
try {
    $report_data = getReportData($report_type, $start_date, $end_date);
} catch (Exception $e) {
    die("Error generating report: " . $e->getMessage());
}

// Get summary statistics
$summary_query = "
    SELECT 
        SUM(CASE WHEN t.type = 'income' THEN amount ELSE 0 END) as total_income,
        SUM(CASE WHEN t.type = 'expense' THEN amount ELSE 0 END) as total_expenses,
        COUNT(*) as total_transactions,
        COUNT(DISTINCT t.category) as unique_categories
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . (int)$_SESSION['user_id'] . "
    AND transaction_date BETWEEN '$start_date' AND '$end_date'";
$summary_result = $conn->query($summary_query);
if ($summary_result === false) {
    die("Error executing summary query: " . $conn->error);
}
$summary = $summary_result->fetch_assoc();
if ($summary === null) {
    $summary = [
        'total_income' => 0,
        'total_expenses' => 0,
        'total_transactions' => 0,
        'unique_categories' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Reports - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <style>
        .chart-container {
            position: relative;
            transition: all 0.3s ease;
        }

        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            animation: slideUp 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .chart-animation {
            animation: chartFadeIn 1s ease-out;
        }

        @keyframes chartFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .tab-button {
            transition: all 0.3s ease;
        }

        .tab-button.active {
            transform: translateY(-2px);
        }

        .export-button {
            transition: all 0.3s ease;
        }

        .export-button:hover {
            transform: translateY(-2px) scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Financial Reports</h1>
            <p class="text-gray-600">Analyze your financial data with interactive charts</p>
        </div>

        <!-- Report Controls -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Date Range -->
                <div>
                    <label class="block text-gray-700 mb-2">Date Range</label>
                    <div class="flex space-x-2">
                        <input type="date" name="start_date" value="<?php echo $start_date; ?>"
                               class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <input type="date" name="end_date" value="<?php echo $end_date; ?>"
                               class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Report Type -->
                <div>
                    <label class="block text-gray-700 mb-2">Report Type</label>
                    <select name="type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="overview" <?php echo $report_type === 'overview' ? 'selected' : ''; ?>>Overview</option>
                        <option value="income" <?php echo $report_type === 'income' ? 'selected' : ''; ?>>Income Analysis</option>
                        <option value="expense" <?php echo $report_type === 'expense' ? 'selected' : ''; ?>>Expense Analysis</option>
                        <option value="balance" <?php echo $report_type === 'balance' ? 'selected' : ''; ?>>Balance Trends</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-4">
                    <button onclick="generateReport()" 
                            class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all">
                        Generate Report
                    </button>
                    <button onclick="exportReport()" 
                            class="export-button bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $stats = [
                ['icon' => 'fa-money-bill-wave', 'label' => 'Total Income', 'value' => '$' . number_format($summary['total_income'], 2), 'color' => 'green'],
                ['icon' => 'fa-credit-card', 'label' => 'Total Expenses', 'value' => '$' . number_format($summary['total_expenses'], 2), 'color' => 'red'],
                ['icon' => 'fa-exchange-alt', 'label' => 'Transactions', 'value' => number_format($summary['total_transactions']), 'color' => 'blue'],
                ['icon' => 'fa-tags', 'label' => 'Categories', 'value' => number_format($summary['unique_categories']), 'color' => 'purple']
            ];
            
            foreach ($stats as $index => $stat):
            ?>
                <div class="stat-card bg-white rounded-lg shadow p-6" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm"><?php echo $stat['label']; ?></p>
                            <p class="text-2xl font-bold text-<?php echo $stat['color']; ?>-600">
                                <?php echo $stat['value']; ?>
                            </p>
                        </div>
                        <div class="bg-<?php echo $stat['color']; ?>-100 p-3 rounded-full">
                            <i class="fas <?php echo $stat['icon']; ?> text-<?php echo $stat['color']; ?>-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Main Chart -->
            <div class="bg-white rounded-lg shadow p-6 chart-container">
                <h2 class="text-lg font-bold mb-4">Financial Trends</h2>
                <canvas id="mainChart" class="chart-animation"></canvas>
            </div>

            <!-- Secondary Chart -->
            <div class="bg-white rounded-lg shadow p-6 chart-container">
                <h2 class="text-lg font-bold mb-4">Distribution Analysis</h2>
                <canvas id="distributionChart" class="chart-animation"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Initialize charts with animations
        function initializeCharts() {
            const mainCtx = document.getElementById('mainChart').getContext('2d');
            const distributionCtx = document.getElementById('distributionChart').getContext('2d');

            // Main Chart
            new Chart(mainCtx, {
                type: 'line',
                data: {
                    labels: <?php 
                        $report_data->data_seek(0);
                        echo json_encode(array_map(function($row) {
                            return date('M Y', strtotime($row['period'] . '-01'));
                        }, $report_data->fetch_all(MYSQLI_ASSOC)));
                    ?>,
                    datasets: [{
                        label: 'Income',
                        data: <?php 
                            $report_data->data_seek(0);
                            echo json_encode(array_column(array_filter(
                                $report_data->fetch_all(MYSQLI_ASSOC),
                                function($row) { return $row['type'] === 'income'; }
                            ), 'total'));
                        ?>,
                        borderColor: '#10B981',
                        tension: 0.4,
                        fill: false
                    }, {
                        label: 'Expenses',
                        data: <?php 
                            $report_data->data_seek(0);
                            echo json_encode(array_column(array_filter(
                                $report_data->fetch_all(MYSQLI_ASSOC),
                                function($row) { return $row['type'] === 'expense'; }
                            ), 'total'));
                        ?>,
                        borderColor: '#EF4444',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Distribution Chart
            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Income', 'Expenses'],
                    datasets: [{
                        data: [
                            <?php echo $summary['total_income']; ?>,
                            <?php echo $summary['total_expenses']; ?>
                        ],
                        backgroundColor: ['#10B981', '#EF4444']
                    }]
                },
                options: {
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', initializeCharts);

        // Export functionality
        function exportReport() {
            window.location.href = `export_report.php?start_date=${document.querySelector('[name="start_date"]').value}&end_date=${document.querySelector('[name="end_date"]').value}&type=${document.querySelector('[name="type"]').value}`;
        }

        // Generate new report
        function generateReport() {
            const form = document.createElement('form');
            form.method = 'GET';
            form.innerHTML = `
                <input type="hidden" name="start_date" value="${document.querySelector('[name="start_date"]').value}">
                <input type="hidden" name="end_date" value="${document.querySelector('[name="end_date"]').value}">
                <input type="hidden" name="type" value="${document.querySelector('[name="type"]').value}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html> 
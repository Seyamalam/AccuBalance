<?php
require_once 'config.php';
checkAuth();

// Get date range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-1 year'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get cash flow data
$cash_flow_query = "
    SELECT 
        DATE_FORMAT(t.transaction_date, '%Y-%m') as month,
        SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END) as income,
        SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END) as expenses,
        SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE -t.amount END) as net
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.transaction_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY month
    ORDER BY month";
$cash_flow = $conn->query($cash_flow_query);

// Get category breakdown
$category_breakdown_query = "
    SELECT 
        t.category,
        bc.color,
        bc.icon,
        COUNT(*) as transaction_count,
        SUM(t.amount) as total_amount,
        AVG(t.amount) as avg_amount,
        MIN(t.amount) as min_amount,
        MAX(t.amount) as max_amount
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    LEFT JOIN budget_categories bc ON t.category = bc.name AND bc.user_id = a.user_id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.transaction_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY t.category, bc.color, bc.icon
    ORDER BY total_amount DESC";
$category_breakdown = $conn->query($category_breakdown_query);

// Get daily spending pattern
$daily_pattern_query = "
    SELECT 
        DAYNAME(transaction_date) as day_of_week,
        COUNT(*) as transaction_count,
        AVG(amount) as avg_amount,
        SUM(amount) as total_amount
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.type = 'expense'
    AND t.transaction_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY day_of_week
    ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$daily_pattern = $conn->query($daily_pattern_query);

// Get account balances trend
$balance_trend_query = "
    SELECT 
        DATE_FORMAT(t.transaction_date, '%Y-%m') as month,
        a.account_name,
        SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE -t.amount END) as net_change
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.transaction_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY month, a.id, a.account_name
    ORDER BY month, a.account_name";
$balance_trend = $conn->query($balance_trend_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Visualizations - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Financial Insights</h1>
            <p class="text-gray-600">AccuBalance: Simplify Finances, Amplify Success</p>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="flex gap-4 items-end">
                <div>
                    <label class="block text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" 
                           class="px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" 
                           class="px-3 py-2 border rounded-lg">
                </div>
                <button type="submit" 
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Update Visualizations
                </button>
            </form>
        </div>

        <!-- Cash Flow Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Cash Flow Analysis</h2>
            <canvas id="cashFlowChart" height="100"></canvas>
        </div>

        <!-- Category Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Expense Distribution</h2>
                <canvas id="categoryPieChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Category Analysis</h2>
                <div class="space-y-4 max-h-[400px] overflow-y-auto">
                    <?php while ($category = $category_breakdown->fetch_assoc()): ?>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <?php if ($category['icon']): ?>
                                        <i class="fas <?php echo $category['icon']; ?>" 
                                           style="color: <?php echo $category['color'] ?? '#666'; ?>"></i>
                                    <?php endif; ?>
                                    <span class="font-medium"><?php echo ucfirst($category['category']); ?></span>
                                </div>
                                <span class="font-bold">$<?php echo number_format($category['total_amount'], 2); ?></span>
                            </div>
                            <div class="text-sm text-gray-600 grid grid-cols-2 gap-2">
                                <div>Transactions: <?php echo $category['transaction_count']; ?></div>
                                <div>Average: $<?php echo number_format($category['avg_amount'], 2); ?></div>
                                <div>Min: $<?php echo number_format($category['min_amount'], 2); ?></div>
                                <div>Max: $<?php echo number_format($category['max_amount'], 2); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Daily Pattern & Balance Trends -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Daily Spending Pattern</h2>
                <canvas id="dailyPatternChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Account Balance Trends</h2>
                <canvas id="balanceTrendChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        Chart.register(ChartDataLabels);

        // Cash Flow Chart
        const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
        new Chart(cashFlowCtx, {
            type: 'bar',
            data: {
                labels: <?php 
                    $cash_flow->data_seek(0);
                    echo json_encode(array_map(function($row) {
                        return date('M Y', strtotime($row['month'] . '-01'));
                    }, $cash_flow->fetch_all(MYSQLI_ASSOC)));
                ?>,
                datasets: [{
                    label: 'Income',
                    data: <?php 
                        $cash_flow->data_seek(0);
                        echo json_encode(array_column($cash_flow->fetch_all(MYSQLI_ASSOC), 'income'));
                    ?>,
                    backgroundColor: '#4BC0C0'
                }, {
                    label: 'Expenses',
                    data: <?php 
                        $cash_flow->data_seek(0);
                        echo json_encode(array_column($cash_flow->fetch_all(MYSQLI_ASSOC), 'expenses'));
                    ?>,
                    backgroundColor: '#FF6384'
                }, {
                    label: 'Net',
                    data: <?php 
                        $cash_flow->data_seek(0);
                        echo json_encode(array_column($cash_flow->fetch_all(MYSQLI_ASSOC), 'net'));
                    ?>,
                    type: 'line',
                    borderColor: '#36A2EB',
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    datalabels: {
                        display: false
                    }
                }
            }
        });

        // Category Pie Chart
        const categoryPieCtx = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(categoryPieCtx, {
            type: 'doughnut',
            data: {
                labels: <?php 
                    $category_breakdown->data_seek(0);
                    echo json_encode(array_map(function($row) {
                        return ucfirst($row['category']);
                    }, $category_breakdown->fetch_all(MYSQLI_ASSOC)));
                ?>,
                datasets: [{
                    data: <?php 
                        $category_breakdown->data_seek(0);
                        echo json_encode(array_column($category_breakdown->fetch_all(MYSQLI_ASSOC), 'total_amount'));
                    ?>,
                    backgroundColor: <?php 
                        $category_breakdown->data_seek(0);
                        echo json_encode(array_map(function($row) {
                            return $row['color'] ?? '#' . substr(md5($row['category']), 0, 6);
                        }, $category_breakdown->fetch_all(MYSQLI_ASSOC)));
                    ?>
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        color: '#fff',
                        formatter: (value, ctx) => {
                            let sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(1) + '%';
                            return percentage;
                        }
                    }
                }
            }
        });

        // Daily Pattern Chart
        const dailyPatternCtx = document.getElementById('dailyPatternChart').getContext('2d');
        new Chart(dailyPatternCtx, {
            type: 'bar',
            data: {
                labels: <?php 
                    $daily_pattern->data_seek(0);
                    echo json_encode(array_column($daily_pattern->fetch_all(MYSQLI_ASSOC), 'day_of_week'));
                ?>,
                datasets: [{
                    label: 'Average Spending',
                    data: <?php 
                        $daily_pattern->data_seek(0);
                        echo json_encode(array_column($daily_pattern->fetch_all(MYSQLI_ASSOC), 'avg_amount'));
                    ?>,
                    backgroundColor: '#9966FF'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    datalabels: {
                        display: false
                    }
                }
            }
        });

        // Balance Trend Chart
        const balanceTrendCtx = document.getElementById('balanceTrendChart').getContext('2d');
        new Chart(balanceTrendCtx, {
            type: 'line',
            data: {
                labels: <?php 
                    $balance_trend->data_seek(0);
                    $months = array_unique(array_map(function($row) {
                        return date('M Y', strtotime($row['month'] . '-01'));
                    }, $balance_trend->fetch_all(MYSQLI_ASSOC)));
                    echo json_encode(array_values($months));
                ?>,
                datasets: [
                    <?php
                    $balance_trend->data_seek(0);
                    $all_data = $balance_trend->fetch_all(MYSQLI_ASSOC);
                    $accounts = array_unique(array_column($all_data, 'account_name'));
                    
                    foreach ($accounts as $index => $account) {
                        $account_data = array_filter($all_data, function($row) use ($account) {
                            return $row['account_name'] === $account;
                        });
                        
                        echo "{
                            label: '" . $account . "',
                            data: " . json_encode(array_column($account_data, 'net_change')) . ",
                            borderColor: getColor(" . $index . "),
                            fill: false,
                            tension: 0.4
                        },";
                    }
                    ?>
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    datalabels: {
                        display: false
                    }
                }
            }
        });

        function getColor(index) {
            const colors = [
                '#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF',
                '#FF9F40', '#FF6384', '#4BC0C0', '#FFCD56', '#36A2EB'
            ];
            return colors[index % colors.length];
        }
    </script>
</body>
</html> 
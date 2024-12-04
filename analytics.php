<?php
require_once 'config.php';
checkAuth();

// Get historical spending patterns
$spending_patterns_query = "
    SELECT 
        DATE_FORMAT(transaction_date, '%Y-%m') as month,
        category,
        SUM(amount) as total
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.type = 'expense'
    AND transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
    GROUP BY month, category
    ORDER BY month ASC";
$spending_patterns = $conn->query($spending_patterns_query);

// Calculate monthly averages by category
$averages_query = "
    SELECT 
        category,
        AVG(monthly_total) as average_spend,
        STDDEV(monthly_total) as std_dev
    FROM (
        SELECT 
            DATE_FORMAT(transaction_date, '%Y-%m') as month,
            category,
            SUM(amount) as monthly_total
        FROM transactions t
        JOIN accounts a ON t.account_id = a.id
        WHERE a.user_id = " . $_SESSION['user_id'] . "
        AND t.type = 'expense'
        AND transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
        GROUP BY month, category
    ) as monthly_totals
    GROUP BY category";
$averages = $conn->query($averages_query);

// Get spending anomalies using subqueries instead of CTEs
$anomalies_query = "
    SELECT 
        m.month,
        m.category,
        m.total,
        s.avg_spend,
        ((m.total - s.avg_spend) / NULLIF(s.std_dev, 0)) as z_score
    FROM (
        SELECT 
            DATE_FORMAT(transaction_date, '%Y-%m') as month,
            category,
            SUM(amount) as total
        FROM transactions t
        JOIN accounts a ON t.account_id = a.id
        WHERE a.user_id = " . $_SESSION['user_id'] . "
        AND t.type = 'expense'
        AND transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)
        GROUP BY month, category
    ) m
    JOIN (
        SELECT 
            category,
            AVG(monthly_total) as avg_spend,
            STDDEV(monthly_total) as std_dev
        FROM (
            SELECT 
                DATE_FORMAT(transaction_date, '%Y-%m') as month,
                category,
                SUM(amount) as monthly_total
            FROM transactions t
            JOIN accounts a ON t.account_id = a.id
            WHERE a.user_id = " . $_SESSION['user_id'] . "
            AND t.type = 'expense'
            AND transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH)
            GROUP BY month, category
        ) monthly_data
        GROUP BY category
    ) s ON m.category = s.category
    HAVING ABS(z_score) > 2
    ORDER BY m.month DESC, ABS(z_score) DESC";
$anomalies = $conn->query($anomalies_query);
if ($anomalies === false) {
    die("Error executing anomalies query: " . $conn->error);
}

// Calculate predicted expenses for next month
$predictions_query = "
    SELECT 
        category,
        avg_amount,
        transaction_count,
        (avg_amount + (std_dev * 0.5)) as predicted_amount
    FROM (
        SELECT 
            category,
            AVG(amount) as avg_amount,
            COUNT(*) as transaction_count,
            STDDEV(amount) as std_dev
        FROM transactions t
        JOIN accounts a ON t.account_id = a.id
        WHERE a.user_id = " . $_SESSION['user_id'] . "
        AND t.type = 'expense'
        AND transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
        GROUP BY category
    ) as monthly_trends
    ORDER BY predicted_amount DESC";
$predictions = $conn->query($predictions_query);
if ($predictions === false) {
    die("Error executing predictions query: " . $conn->error);
}

// Get savings potential
$savings_query = "
    SELECT 
        t.category,
        COUNT(*) as transaction_count,
        AVG(t.amount) as avg_amount,
        MIN(t.amount) as min_amount,
        (AVG(t.amount) - MIN(t.amount)) * COUNT(*) / 6 as monthly_savings_potential
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.type = 'expense'
    AND transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY t.category
    HAVING COUNT(*) >= 3
    ORDER BY monthly_savings_potential DESC
    LIMIT 5";
$savings_potential = $conn->query($savings_query);

// Add null checks before using results
if ($anomalies && $anomalies->num_rows > 0) {
    $anomalies_count = $anomalies->num_rows;
} else {
    $anomalies_count = 0;
}

// Calculate performance metrics
$performance_query = "
    SELECT 
        DATE_FORMAT(t.transaction_date, '%Y-%m') as month,
        SUM(CASE WHEN t.type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN t.type = 'expense' THEN amount ELSE 0 END) as expenses,
        SUM(CASE WHEN t.type = 'income' THEN amount ELSE -amount END) as net_flow,
        COUNT(*) as transaction_count
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE a.user_id = " . $_SESSION['user_id'] . "
    AND t.transaction_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month DESC";

$performance = $conn->query($performance_query);
if ($performance === false) {
    die("Error executing performance query: " . $conn->error);
}

// Set default values if no performance data
if ($performance->num_rows === 0) {
    $performance_data = [
        'current_month_income' => 0,
        'current_month_expenses' => 0,
        'current_month_net' => 0,
        'avg_monthly_income' => 0,
        'avg_monthly_expenses' => 0,
        'trend' => 'neutral'
    ];
} else {
    $performance_data = [];
    $total_income = 0;
    $total_expenses = 0;
    $months = 0;
    
    while ($row = $performance->fetch_assoc()) {
        if ($months === 0) {
            $performance_data['current_month_income'] = $row['income'];
            $performance_data['current_month_expenses'] = $row['expenses'];
            $performance_data['current_month_net'] = $row['net_flow'];
        }
        $total_income += $row['income'];
        $total_expenses += $row['expenses'];
        $months++;
    }
    
    $performance_data['avg_monthly_income'] = $months > 0 ? $total_income / $months : 0;
    $performance_data['avg_monthly_expenses'] = $months > 0 ? $total_expenses / $months : 0;
    $performance_data['trend'] = $performance_data['current_month_net'] > 0 ? 'positive' : 
        ($performance_data['current_month_net'] < 0 ? 'negative' : 'neutral');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Analytics - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- AI Insights Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 mb-8 text-white">
            <h2 class="text-2xl font-bold mb-4">AI Financial Insights</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium mb-2">Key Observations</h3>
                    <ul class="space-y-2">
                        <?php
                        // Calculate some basic insights
                        if ($anomalies_count > 0): ?>
                            <li>⚠️ Found <?php echo $anomalies_count; ?> unusual spending patterns</li>
                        <?php endif; ?>
                        
                        <?php
                        $savings = $savings_potential->fetch_assoc();
                        if ($savings): ?>
                            <li>💡 Potential monthly savings of $<?php echo number_format($savings['monthly_savings_potential'], 2); ?> in <?php echo $savings['category']; ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="font-medium mb-2">Recommendations</h3>
                    <ul class="space-y-2">
                        <li>📊 Review your spending patterns in categories with high variability</li>
                        <li>🎯 Set up budget alerts for categories with frequent overruns</li>
                        <li>💰 Consider automating savings for consistent income patterns</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Predictions & Anomalies -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Spending Predictions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium mb-4">Next Month Predictions</h3>
                <div class="space-y-4">
                    <?php while ($prediction = $predictions->fetch_assoc()): ?>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600"><?php echo ucfirst($prediction['category']); ?></span>
                            <span class="font-medium">$<?php echo number_format($prediction['predicted_amount'], 2); ?></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: <?php echo min(($prediction['predicted_amount'] / $prediction['avg_amount']) * 100, 100); ?>%">
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Spending Anomalies -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium mb-4">Unusual Spending Patterns</h3>
                <div class="space-y-4">
                    <?php while ($anomaly = $anomalies->fetch_assoc()): ?>
                        <div class="p-4 <?php echo $anomaly['z_score'] > 0 ? 'bg-red-50' : 'bg-green-50'; ?> rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium"><?php echo ucfirst($anomaly['category']); ?></span>
                                <span class="<?php echo $anomaly['z_score'] > 0 ? 'text-red-600' : 'text-green-600'; ?> font-medium">
                                    $<?php echo number_format($anomaly['total'], 2); ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                <?php echo $anomaly['z_score'] > 0 ? 'Above' : 'Below'; ?> average by 
                                <?php echo number_format(abs($anomaly['z_score']) * 100, 1); ?>%
                            </p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Spending Patterns Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-medium mb-4">12-Month Spending Patterns</h3>
            <canvas id="spendingPatternsChart"></canvas>
        </div>

        <!-- Savings Opportunities -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium mb-4">Savings Opportunities</h3>
            <div class="space-y-6">
                <?php 
                $savings_potential->data_seek(0);
                while ($saving = $savings_potential->fetch_assoc()): 
                ?>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium"><?php echo ucfirst($saving['category']); ?></span>
                            <span class="text-green-600 font-medium">
                                Potential savings: $<?php echo number_format($saving['monthly_savings_potential'], 2); ?>/month
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 mb-2">
                            Based on <?php echo $saving['transaction_count']; ?> transactions
                            • Average: $<?php echo number_format($saving['avg_amount'], 2); ?>
                            • Best: $<?php echo number_format($saving['min_amount'], 2); ?>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" 
                                 style="width: <?php echo ($saving['min_amount'] / $saving['avg_amount']) * 100; ?>%">
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        // Prepare spending patterns data
        const spendingData = {
            labels: <?php 
                $spending_patterns->data_seek(0);
                echo json_encode(array_unique(array_map(function($row) {
                    return date('M Y', strtotime($row['month'] . '-01'));
                }, $spending_patterns->fetch_all(MYSQLI_ASSOC))));
            ?>,
            datasets: [
                <?php
                $spending_patterns->data_seek(0);
                $categories = array_unique(array_column($spending_patterns->fetch_all(MYSQLI_ASSOC), 'category'));
                $spending_patterns->data_seek(0);
                $all_data = $spending_patterns->fetch_all(MYSQLI_ASSOC);
                
                foreach ($categories as $index => $category) {
                    $category_data = array_filter($all_data, function($row) use ($category) {
                        return $row['category'] === $category;
                    });
                    
                    echo "{
                        label: '" . ucfirst($category) . "',
                        data: " . json_encode(array_column($category_data, 'total')) . ",
                        borderColor: getColor(" . $index . "),
                        fill: false,
                        tension: 0.4
                    },";
                }
                ?>
            ]
        };

        function getColor(index) {
            const colors = [
                '#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF',
                '#FF9F40', '#FF6384', '#4BC0C0', '#FFCD56', '#36A2EB'
            ];
            return colors[index % colors.length];
        }

        // Initialize spending patterns chart
        const spendingCtx = document.getElementById('spendingPatternsChart').getContext('2d');
        new Chart(spendingCtx, {
            type: 'line',
            data: spendingData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html> 
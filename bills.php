<?php
require_once 'config.php';
checkAuth();

// Handle bill operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $title = $conn->real_escape_string($_POST['title']);
            $amount = $conn->real_escape_string($_POST['amount']);
            $due_date = $conn->real_escape_string($_POST['due_date']);
            $category = $conn->real_escape_string($_POST['category']);
            $recurring = isset($_POST['recurring']) ? 1 : 0;
            $frequency = $recurring ? $conn->real_escape_string($_POST['frequency']) : NULL;
            $notification_days = $conn->real_escape_string($_POST['notification_days']);
            
            $query = "INSERT INTO bill_reminders 
                      (user_id, title, amount, due_date, category, recurring, frequency, notification_days) 
                      VALUES (" . $_SESSION['user_id'] . ", '$title', '$amount', '$due_date', 
                              '$category', $recurring, " . ($frequency ? "'$frequency'" : "NULL") . ", 
                              '$notification_days')";
            
            if ($conn->query($query)) {
                $success = "Bill reminder created successfully";
            } else {
                $error = "Failed to create bill reminder";
            }
        } elseif ($_POST['action'] === 'mark_paid' && isset($_POST['bill_id'])) {
            $bill_id = $conn->real_escape_string($_POST['bill_id']);
            $query = "UPDATE bill_reminders SET status = 'paid' 
                     WHERE id = $bill_id AND user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                // Create transaction for the paid bill
                $bill_query = "SELECT * FROM bill_reminders WHERE id = $bill_id";
                $bill = $conn->query($bill_query)->fetch_assoc();
                
                // Get default account
                $account_query = "SELECT id FROM accounts WHERE user_id = " . $_SESSION['user_id'] . " LIMIT 1";
                $account = $conn->query($account_query)->fetch_assoc();
                
                if ($account) {
                    $transaction_query = "INSERT INTO transactions 
                                        (account_id, type, category, amount, description, transaction_date) 
                                        VALUES (" . $account['id'] . ", 'expense', '" . $bill['category'] . "', 
                                                " . $bill['amount'] . ", 'Bill payment: " . $bill['title'] . "', 
                                                CURRENT_DATE)";
                    $conn->query($transaction_query);
                }
                
                $success = "Bill marked as paid";
            } else {
                $error = "Failed to update bill status";
            }
        }
    }
}

// Fetch upcoming bills
$upcoming_query = "
    SELECT * FROM bill_reminders 
    WHERE user_id = " . $_SESSION['user_id'] . "
    AND status = 'pending'
    AND due_date >= CURRENT_DATE
    ORDER BY due_date ASC";
$upcoming_bills = $conn->query($upcoming_query);

// Fetch overdue bills
$overdue_query = "
    SELECT * FROM bill_reminders 
    WHERE user_id = " . $_SESSION['user_id'] . "
    AND status = 'pending'
    AND due_date < CURRENT_DATE
    ORDER BY due_date ASC";
$overdue_bills = $conn->query($overdue_query);

// Get bill statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_bills,
        SUM(CASE WHEN status = 'pending' AND due_date >= CURRENT_DATE THEN amount ELSE 0 END) as upcoming_total,
        SUM(CASE WHEN status = 'pending' AND due_date < CURRENT_DATE THEN amount ELSE 0 END) as overdue_total,
        SUM(CASE WHEN status = 'paid' AND MONTH(due_date) = MONTH(CURRENT_DATE) THEN amount ELSE 0 END) as paid_this_month
    FROM bill_reminders 
    WHERE user_id = " . $_SESSION['user_id'];
$stats = $conn->query($stats_query)->fetch_assoc();

// Get monthly bill trends
$trends_query = "
    SELECT 
        DATE_FORMAT(due_date, '%Y-%m') as month,
        SUM(amount) as total
    FROM bill_reminders 
    WHERE user_id = " . $_SESSION['user_id'] . "
    GROUP BY month
    ORDER BY month DESC
    LIMIT 6";
$trends = $conn->query($trends_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Reminders - Finance Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Total Bills</h3>
                <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_bills']; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Upcoming Total</h3>
                <p class="text-2xl font-bold text-blue-600">$<?php echo number_format($stats['upcoming_total'], 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Overdue Total</h3>
                <p class="text-2xl font-bold text-red-600">$<?php echo number_format($stats['overdue_total'], 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Paid This Month</h3>
                <p class="text-2xl font-bold text-green-600">$<?php echo number_format($stats['paid_this_month'], 2); ?></p>
            </div>
        </div>

        <!-- Add Bill Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Bill Reminder</h2>
            
            <?php if (isset($success)): ?>
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label class="block text-gray-700 mb-2">Bill Title</label>
                    <input type="text" name="title" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Amount</label>
                    <input type="number" step="0.01" name="amount" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Due Date</label>
                    <input type="date" name="due_date" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Category</label>
                    <input type="text" name="category" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Notification Days Before</label>
                    <input type="number" name="notification_days" value="3" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="recurring" class="mr-2" 
                               onchange="document.getElementById('frequency').style.display = this.checked ? 'block' : 'none'">
                        Recurring Bill
                    </label>
                    <select name="frequency" id="frequency" class="w-full px-3 py-2 border rounded-lg hidden">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Add Bill Reminder
                    </button>
                </div>
            </form>
        </div>

        <!-- Bills Lists -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Overdue Bills -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b bg-red-50">
                    <h2 class="text-xl font-bold text-red-600">Overdue Bills</h2>
                </div>
                <div class="p-6">
                    <?php if ($overdue_bills->num_rows === 0): ?>
                        <p class="text-gray-500">No overdue bills</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php while ($bill = $overdue_bills->fetch_assoc()): ?>
                                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                                    <div>
                                        <h3 class="font-medium"><?php echo $bill['title']; ?></h3>
                                        <p class="text-sm text-gray-600">
                                            Due: <?php echo date('M d, Y', strtotime($bill['due_date'])); ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-red-600">$<?php echo number_format($bill['amount'], 2); ?></p>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="mark_paid">
                                            <input type="hidden" name="bill_id" value="<?php echo $bill['id']; ?>">
                                            <button type="submit" class="text-sm text-blue-600 hover:underline">Mark as Paid</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Bills -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-bold">Upcoming Bills</h2>
                </div>
                <div class="p-6">
                    <?php if ($upcoming_bills->num_rows === 0): ?>
                        <p class="text-gray-500">No upcoming bills</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php while ($bill = $upcoming_bills->fetch_assoc()): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h3 class="font-medium"><?php echo $bill['title']; ?></h3>
                                        <p class="text-sm text-gray-600">
                                            Due: <?php echo date('M d, Y', strtotime($bill['due_date'])); ?>
                                            <?php if ($bill['recurring']): ?>
                                                <span class="text-blue-600">(<?php echo ucfirst($bill['frequency']); ?>)</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold">$<?php echo number_format($bill['amount'], 2); ?></p>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="mark_paid">
                                            <input type="hidden" name="bill_id" value="<?php echo $bill['id']; ?>">
                                            <button type="submit" class="text-sm text-blue-600 hover:underline">Mark as Paid</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Monthly Trends Chart -->
        <div class="bg-white rounded-lg shadow mt-8">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold">Monthly Bill Trends</h2>
            </div>
            <div class="p-6">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Monthly Trends Chart
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: <?php 
                    $trends->data_seek(0);
                    echo json_encode(array_map(function($row) {
                        return date('M Y', strtotime($row['month'] . '-01'));
                    }, $trends->fetch_all(MYSQLI_ASSOC)));
                ?>,
                datasets: [{
                    label: 'Monthly Bills Total',
                    data: <?php 
                        $trends->data_seek(0);
                        echo json_encode(array_column($trends->fetch_all(MYSQLI_ASSOC), 'total'));
                    ?>,
                    borderColor: '#4BC0C0',
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html> 
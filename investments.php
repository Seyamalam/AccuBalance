<?php
require_once 'config.php';
checkAuth();

// Handle investment operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $name = $conn->real_escape_string($_POST['name']);
            $type = $conn->real_escape_string($_POST['type']);
            $purchase_price = $conn->real_escape_string($_POST['purchase_price']);
            $current_price = $conn->real_escape_string($_POST['current_price']);
            $quantity = $conn->real_escape_string($_POST['quantity']);
            $purchase_date = $conn->real_escape_string($_POST['purchase_date']);
            $notes = $conn->real_escape_string($_POST['notes']);
            
            $query = "INSERT INTO investments 
                      (user_id, name, type, purchase_price, current_price, quantity, purchase_date, notes) 
                      VALUES (" . $_SESSION['user_id'] . ", '$name', '$type', '$purchase_price', 
                              '$current_price', '$quantity', '$purchase_date', '$notes')";
            
            if ($conn->query($query)) {
                $success = "Investment added successfully";
            } else {
                $error = "Failed to add investment";
            }
        } elseif ($_POST['action'] === 'update_price') {
            $investment_id = $conn->real_escape_string($_POST['investment_id']);
            $current_price = $conn->real_escape_string($_POST['current_price']);
            
            $query = "UPDATE investments 
                      SET current_price = '$current_price'
                      WHERE id = $investment_id AND user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                $success = "Price updated successfully";
            } else {
                $error = "Failed to update price";
            }
        }
    }
}

// Fetch user's investments
$investments_query = "SELECT * FROM investments WHERE user_id = " . $_SESSION['user_id'] . " ORDER BY type, name";
$investments = $conn->query($investments_query);

// Calculate investment statistics
$stats_query = "
    SELECT 
        SUM(quantity * purchase_price) as total_invested,
        SUM(quantity * current_price) as current_value,
        SUM(quantity * (current_price - purchase_price)) as total_gain_loss
    FROM investments 
    WHERE user_id = " . $_SESSION['user_id'];
$stats = $conn->query($stats_query)->fetch_assoc();

// Get performance by type
$performance_query = "
    SELECT 
        type,
        SUM(quantity * purchase_price) as invested,
        SUM(quantity * current_price) as current_value,
        SUM(quantity * (current_price - purchase_price)) as gain_loss
    FROM investments 
    WHERE user_id = " . $_SESSION['user_id'] . "
    GROUP BY type";
$performance = $conn->query($performance_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investments - Finance Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Investment Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Total Invested</h3>
                <p class="text-2xl font-bold text-gray-900">
                    $<?php echo number_format($stats['total_invested'], 2); ?>
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Current Value</h3>
                <p class="text-2xl font-bold text-gray-900">
                    $<?php echo number_format($stats['current_value'], 2); ?>
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-500 text-sm font-medium">Total Gain/Loss</h3>
                <p class="text-2xl font-bold <?php echo $stats['total_gain_loss'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                    $<?php echo number_format($stats['total_gain_loss'], 2); ?>
                    (<?php echo number_format(($stats['total_gain_loss'] / $stats['total_invested']) * 100, 2); ?>%)
                </p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium mb-4">Portfolio Distribution</h3>
                <canvas id="portfolioChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium mb-4">Performance by Type</h3>
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Add Investment Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Investment</h2>
            
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
                    <label class="block text-gray-700 mb-2">Investment Name</label>
                    <input type="text" name="name" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Type</label>
                    <select name="type" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="stock">Stock</option>
                        <option value="crypto">Cryptocurrency</option>
                        <option value="mutual_fund">Mutual Fund</option>
                        <option value="bond">Bond</option>
                        <option value="real_estate">Real Estate</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Purchase Price</label>
                    <input type="number" step="0.01" name="purchase_price" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Current Price</label>
                    <input type="number" step="0.01" name="current_price" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Quantity</label>
                    <input type="number" step="0.0001" name="quantity" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Purchase Date</label>
                    <input type="date" name="purchase_date" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>

                <div class="md:col-span-3">
                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Add Investment
                    </button>
                </div>
            </form>
        </div>

        <!-- Investments List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold">Your Investments</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gain/Loss</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($investment = $investments->fetch_assoc()): ?>
                            <?php
                            $total_value = $investment['quantity'] * $investment['current_price'];
                            $gain_loss = ($investment['current_price'] - $investment['purchase_price']) * $investment['quantity'];
                            $gain_loss_percentage = ($investment['current_price'] - $investment['purchase_price']) / $investment['purchase_price'] * 100;
                            ?>
                            <tr>
                                <td class="px-6 py-4"><?php echo $investment['name']; ?></td>
                                <td class="px-6 py-4"><?php echo ucfirst($investment['type']); ?></td>
                                <td class="px-6 py-4">$<?php echo number_format($investment['purchase_price'], 2); ?></td>
                                <td class="px-6 py-4">
                                    <form method="POST" class="flex gap-2">
                                        <input type="hidden" name="action" value="update_price">
                                        <input type="hidden" name="investment_id" value="<?php echo $investment['id']; ?>">
                                        <input type="number" step="0.01" name="current_price" 
                                               value="<?php echo $investment['current_price']; ?>"
                                               class="w-24 px-2 py-1 border rounded">
                                        <button type="submit" class="text-blue-600 hover:text-blue-900">Update</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4"><?php echo number_format($investment['quantity'], 4); ?></td>
                                <td class="px-6 py-4">$<?php echo number_format($total_value, 2); ?></td>
                                <td class="px-6 py-4 <?php echo $gain_loss >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                    $<?php echo number_format($gain_loss, 2); ?>
                                    (<?php echo number_format($gain_loss_percentage, 2); ?>%)
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="showInvestmentDetails(<?php echo $investment['id']; ?>)"
                                            class="text-blue-600 hover:text-blue-900">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Portfolio Distribution Chart
        const portfolioCtx = document.getElementById('portfolioChart').getContext('2d');
        new Chart(portfolioCtx, {
            type: 'doughnut',
            data: {
                labels: <?php 
                    $performance->data_seek(0);
                    echo json_encode(array_map(function($row) {
                        return ucfirst($row['type']);
                    }, $performance->fetch_all(MYSQLI_ASSOC)));
                ?>,
                datasets: [{
                    data: <?php 
                        $performance->data_seek(0);
                        echo json_encode(array_column($performance->fetch_all(MYSQLI_ASSOC), 'current_value'));
                    ?>,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ]
                }]
            }
        });

        // Performance Chart
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'bar',
            data: {
                labels: <?php 
                    $performance->data_seek(0);
                    echo json_encode(array_map(function($row) {
                        return ucfirst($row['type']);
                    }, $performance->fetch_all(MYSQLI_ASSOC)));
                ?>,
                datasets: [{
                    label: 'Invested Amount',
                    data: <?php 
                        $performance->data_seek(0);
                        echo json_encode(array_column($performance->fetch_all(MYSQLI_ASSOC), 'invested'));
                    ?>,
                    backgroundColor: '#36A2EB'
                }, {
                    label: 'Current Value',
                    data: <?php 
                        $performance->data_seek(0);
                        echo json_encode(array_column($performance->fetch_all(MYSQLI_ASSOC), 'current_value'));
                    ?>,
                    backgroundColor: '#4BC0C0'
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

        function showInvestmentDetails(id) {
            // Implement investment details modal
            alert('Investment details modal to be implemented');
        }
    </script>
</body>
</html> 
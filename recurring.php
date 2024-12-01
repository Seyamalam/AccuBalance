<?php
require_once 'config.php';
checkAuth();

// Handle recurring transaction operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $account_id = $conn->real_escape_string($_POST['account_id']);
            $type = $conn->real_escape_string($_POST['type']);
            $category = $conn->real_escape_string($_POST['category']);
            $amount = $conn->real_escape_string($_POST['amount']);
            $description = $conn->real_escape_string($_POST['description']);
            $frequency = $conn->real_escape_string($_POST['frequency']);
            $start_date = $conn->real_escape_string($_POST['start_date']);
            $end_date = !empty($_POST['end_date']) ? "'" . $conn->real_escape_string($_POST['end_date']) . "'" : "NULL";
            
            $query = "INSERT INTO recurring_transactions 
                      (user_id, account_id, type, category, amount, description, frequency, start_date, end_date) 
                      VALUES (" . $_SESSION['user_id'] . ", '$account_id', '$type', '$category', '$amount', 
                              '$description', '$frequency', '$start_date', $end_date)";
            
            if ($conn->query($query)) {
                $success = "Recurring transaction created successfully";
            } else {
                $error = "Failed to create recurring transaction";
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['recurring_id'])) {
            $recurring_id = $conn->real_escape_string($_POST['recurring_id']);
            $query = "DELETE FROM recurring_transactions 
                     WHERE id = $recurring_id AND user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                $success = "Recurring transaction deleted successfully";
            } else {
                $error = "Failed to delete recurring transaction";
            }
        }
    }
}

// Fetch user's accounts
$accounts_query = "SELECT * FROM accounts WHERE user_id = " . $_SESSION['user_id'];
$accounts = $conn->query($accounts_query);

// Fetch recurring transactions
$recurring_query = "
    SELECT r.*, a.account_name 
    FROM recurring_transactions r
    JOIN accounts a ON r.account_id = a.id
    WHERE r.user_id = " . $_SESSION['user_id'] . "
    ORDER BY r.start_date ASC";
$recurring = $conn->query($recurring_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recurring Transactions - Finance Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Add Recurring Transaction Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Create Recurring Transaction</h2>
            
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

            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label class="block text-gray-700 mb-2">Account</label>
                    <select name="account_id" required class="w-full px-3 py-2 border rounded-lg">
                        <?php while ($account = $accounts->fetch_assoc()): ?>
                            <option value="<?php echo $account['id']; ?>">
                                <?php echo $account['account_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Type</label>
                    <select name="type" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Category</label>
                    <input type="text" name="category" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Amount</label>
                    <input type="number" step="0.01" name="amount" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Frequency</label>
                    <select name="frequency" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">End Date (Optional)</label>
                    <input type="date" name="end_date" 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Description</label>
                    <input type="text" name="description" 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="md:col-span-2">
                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Create Recurring Transaction
                    </button>
                </div>
            </form>
        </div>

        <!-- Recurring Transactions List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold">Recurring Transactions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frequency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Next Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($transaction = $recurring->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo $transaction['account_name']; ?></td>
                            <td class="px-6 py-4"><?php echo ucfirst($transaction['type']); ?></td>
                            <td class="px-6 py-4"><?php echo ucfirst($transaction['category']); ?></td>
                            <td class="px-6 py-4 <?php echo $transaction['type'] === 'expense' ? 'text-red-600' : 'text-green-600'; ?>">
                                $<?php echo number_format($transaction['amount'], 2); ?>
                            </td>
                            <td class="px-6 py-4"><?php echo ucfirst($transaction['frequency']); ?></td>
                            <td class="px-6 py-4">
                                <?php 
                                $next_date = $transaction['last_processed'] ?? $transaction['start_date'];
                                echo date('Y-m-d', strtotime($next_date));
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" class="inline" onsubmit="return confirm('Delete this recurring transaction?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="recurring_id" value="<?php echo $transaction['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 
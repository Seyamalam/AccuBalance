<?php
require_once 'config.php';
checkAuth();

// Handle budget operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $category = $conn->real_escape_string($_POST['category']);
            $amount = $conn->real_escape_string($_POST['amount']);
            $start_date = $conn->real_escape_string($_POST['start_date']);
            $end_date = $conn->real_escape_string($_POST['end_date']);
            
            $query = "INSERT INTO budgets (user_id, category, amount, start_date, end_date) 
                      VALUES (" . $_SESSION['user_id'] . ", '$category', '$amount', '$start_date', '$end_date')";
            
            if ($conn->query($query)) {
                $success = "Budget created successfully";
            } else {
                $error = "Failed to create budget";
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['budget_id'])) {
            $budget_id = $conn->real_escape_string($_POST['budget_id']);
            $query = "DELETE FROM budgets WHERE id = $budget_id AND user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                $success = "Budget deleted successfully";
            } else {
                $error = "Failed to delete budget";
            }
        }
    }
}

// Fetch current month's budgets and spending
$current_month_query = "
    SELECT 
        b.*, 
        COALESCE(SUM(t.amount), 0) as spent
    FROM budgets b
    LEFT JOIN transactions t ON t.category = b.category 
        AND t.transaction_date BETWEEN b.start_date AND b.end_date
        AND t.type = 'expense'
    WHERE b.user_id = " . $_SESSION['user_id'] . "
    GROUP BY b.id";
$budgets = $conn->query($current_month_query);

// Get spending categories for the dropdown
$categories_query = "
    SELECT DISTINCT category 
    FROM transactions 
    WHERE type = 'expense' 
    AND account_id IN (SELECT id FROM accounts WHERE user_id = " . $_SESSION['user_id'] . ")";
$categories = $conn->query($categories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgets - Finance Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Add Budget Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Create New Budget</h2>
            
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
                    <label class="block text-gray-700 mb-2">Category</label>
                    <select name="category" required class="w-full px-3 py-2 border rounded-lg">
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['category']; ?>">
                                <?php echo ucfirst($category['category']); ?>
                            </option>
                        <?php endwhile; ?>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Budget Amount</label>
                    <input type="number" step="0.01" name="amount" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="md:col-span-2">
                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Create Budget
                    </button>
                </div>
            </form>
        </div>

        <!-- Budget Progress -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold">Budget Progress</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php while ($budget = $budgets->fetch_assoc()): ?>
                        <?php 
                        $percentage = ($budget['amount'] > 0) ? ($budget['spent'] / $budget['amount']) * 100 : 0;
                        $status_color = $percentage >= 100 ? 'red' : ($percentage >= 80 ? 'yellow' : 'green');
                        ?>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-medium"><?php echo ucfirst($budget['category']); ?></h3>
                                <form method="POST" class="inline" onsubmit="return confirm('Delete this budget?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="budget_id" value="<?php echo $budget['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                                </form>
                            </div>
                            <div class="mb-2">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-<?php echo $status_color; ?>-600 h-2.5 rounded-full" 
                                         style="width: <?php echo min($percentage, 100); ?>%"></div>
                                </div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Spent: $<?php echo number_format($budget['spent'], 2); ?></span>
                                <span>Budget: $<?php echo number_format($budget['amount'], 2); ?></span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                <?php echo date('M d', strtotime($budget['start_date'])); ?> - 
                                <?php echo date('M d, Y', strtotime($budget['end_date'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add any additional JavaScript for interactivity here
    </script>
</body>
</html> 
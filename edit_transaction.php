<?php
require_once 'config.php';
checkAuth();

// Check if transaction ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die(json_encode(['error' => 'Transaction ID is required']));
}

$transaction_id = (int)$_GET['id'];

// Fetch transaction details with error handling
$transaction_query = "SELECT t.*, a.account_name 
                     FROM transactions t
                     JOIN accounts a ON t.account_id = a.id
                     WHERE t.id = ? AND a.user_id = ?";

$stmt = $conn->prepare($transaction_query);
if ($stmt === false) {
    die(json_encode(['error' => 'Failed to prepare query: ' . $conn->error]));
}

$stmt->bind_param('ii', $transaction_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die(json_encode(['error' => 'Failed to fetch transaction: ' . $conn->error]));
}

$transaction = $result->fetch_assoc();
if (!$transaction) {
    die(json_encode(['error' => 'Transaction not found']));
}

// Get user's accounts for the dropdown
$accounts_query = "SELECT id, account_name FROM accounts WHERE user_id = ?";
$stmt = $conn->prepare($accounts_query);
if ($stmt === false) {
    die(json_encode(['error' => 'Failed to prepare accounts query: ' . $conn->error]));
}

$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$accounts = $stmt->get_result();

if ($accounts === false) {
    die(json_encode(['error' => 'Failed to fetch accounts: ' . $conn->error]));
}

// Get categories for the dropdown
$categories_query = "SELECT DISTINCT category FROM transactions t
                    JOIN accounts a ON t.account_id = a.id
                    WHERE a.user_id = ?";
$stmt = $conn->prepare($categories_query);
if ($stmt === false) {
    die(json_encode(['error' => 'Failed to prepare categories query: ' . $conn->error]));
}

$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$categories = $stmt->get_result();

if ($categories === false) {
    die(json_encode(['error' => 'Failed to fetch categories: ' . $conn->error]));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Edit Transaction</h2>
            
            <form id="editTransactionForm" method="POST" action="update_transaction.php">
                <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction['id']); ?>">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Description</label>
                        <input type="text" name="description" 
                               value="<?php echo htmlspecialchars($transaction['description']); ?>"
                               class="w-full px-3 py-2 border rounded-lg" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Amount</label>
                        <input type="number" name="amount" step="0.01" 
                               value="<?php echo htmlspecialchars($transaction['amount']); ?>"
                               class="w-full px-3 py-2 border rounded-lg" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Type</label>
                        <select name="type" class="w-full px-3 py-2 border rounded-lg">
                            <option value="income" <?php echo $transaction['type'] === 'income' ? 'selected' : ''; ?>>Income</option>
                            <option value="expense" <?php echo $transaction['type'] === 'expense' ? 'selected' : ''; ?>>Expense</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full px-3 py-2 border rounded-lg">
                            <?php while ($category = $categories->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($category['category']); ?>"
                                        <?php echo $transaction['category'] === $category['category'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Account</label>
                        <select name="account_id" class="w-full px-3 py-2 border rounded-lg">
                            <?php while ($account = $accounts->fetch_assoc()): ?>
                                <option value="<?php echo $account['id']; ?>"
                                        <?php echo $transaction['account_id'] === $account['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($account['account_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Date</label>
                        <input type="date" name="transaction_date" 
                               value="<?php echo htmlspecialchars($transaction['transaction_date']); ?>"
                               class="w-full px-3 py-2 border rounded-lg" required>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="window.location.href='transactions.php'"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('editTransactionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Add form validation here if needed
            
            this.submit();
        });
    </script>
</body>
</html>

<?php
require_once 'config.php';
checkAuth();

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$account_id = isset($_GET['account_id']) ? $_GET['account_id'] : 'all';
$min_amount = isset($_GET['min_amount']) ? $_GET['min_amount'] : '';
$max_amount = isset($_GET['max_amount']) ? $_GET['max_amount'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query conditions
$conditions = ["a.user_id = " . $_SESSION['user_id']];
if ($type !== 'all') $conditions[] = "t.type = '" . $conn->real_escape_string($type) . "'";
if ($category !== 'all') $conditions[] = "t.category = '" . $conn->real_escape_string($category) . "'";
if ($account_id !== 'all') $conditions[] = "t.account_id = '" . (int)$account_id . "'";
if ($min_amount !== '') $conditions[] = "t.amount >= '" . (float)$min_amount . "'";
if ($max_amount !== '') $conditions[] = "t.amount <= '" . (float)$max_amount . "'";
if ($search !== '') $conditions[] = "(t.description LIKE '%" . $conn->real_escape_string($search) . "%' OR t.category LIKE '%" . $conn->real_escape_string($search) . "%')";

$where_clause = implode(' AND ', $conditions);

// Get transactions with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$transactions_query = "SELECT t.*, a.account_name 
                      FROM transactions t
                      JOIN accounts a ON t.account_id = a.id
                      WHERE " . implode(' AND ', $conditions) . "
                      ORDER BY t.transaction_date DESC 
                      LIMIT $offset, $per_page";

$transactions_result = $conn->query($transactions_query);
if ($transactions_result === false) {
    die("Error executing query: " . $conn->error);
}
$transactions = $transactions_result;

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total 
                 FROM transactions t 
                 JOIN accounts a ON t.account_id = a.id 
                 WHERE " . implode(' AND ', $conditions);

$total_records = $conn->query($count_query)->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// Get categories and accounts for filters
$categories_query = "SELECT DISTINCT t.category 
                     FROM transactions t
                     JOIN accounts a ON t.account_id = a.id
                     WHERE a.user_id = " . $_SESSION['user_id'];

$categories = $conn->query($categories_query);
if ($categories === false) {
    die("Error fetching categories: " . $conn->error);
}

$accounts_query = "SELECT id, account_name FROM accounts WHERE user_id = " . $_SESSION['user_id'];
$accounts = $conn->query($accounts_query);
if ($accounts === false) {
    die("Error fetching accounts: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .filter-slide {
            animation: filterSlide 0.3s ease-out;
        }

        @keyframes filterSlide {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .transaction-row {
            transition: all 0.3s ease;
        }

        .transaction-row:hover {
            transform: translateX(5px);
            background-color: #f8fafc;
        }

        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.4),
                transparent
            );
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>
            <button onclick="showAddTransactionModal()" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>Add Transaction
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium">Filters</h2>
                <button onclick="toggleFilters()" class="text-blue-500 hover:text-blue-600">
                    <i class="fas fa-filter mr-1"></i>Toggle Filters
                </button>
            </div>

            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4 filter-slide">
                <div>
                    <label class="block text-gray-700 mb-2">Date Range</label>
                    <div class="flex space-x-2">
                        <input type="date" name="start_date" value="<?php echo $start_date; ?>"
                               class="w-full px-3 py-2 border rounded-lg">
                        <input type="date" name="end_date" value="<?php echo $end_date; ?>"
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 border rounded-lg">
                        <option value="all" <?php echo $type === 'all' ? 'selected' : ''; ?>>All Types</option>
                        <option value="income" <?php echo $type === 'income' ? 'selected' : ''; ?>>Income</option>
                        <option value="expense" <?php echo $type === 'expense' ? 'selected' : ''; ?>>Expense</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full px-3 py-2 border rounded-lg">
                        <option value="all">All Categories</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category']; ?>" 
                                    <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo ucfirst($cat['category']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Account</label>
                    <select name="account_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="all">All Accounts</option>
                        <?php while ($acc = $accounts->fetch_assoc()): ?>
                            <option value="<?php echo $acc['id']; ?>"
                                    <?php echo $account_id === $acc['id'] ? 'selected' : ''; ?>>
                                <?php echo $acc['account_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Amount Range</label>
                    <div class="flex space-x-2">
                        <input type="number" name="min_amount" placeholder="Min" value="<?php echo $min_amount; ?>"
                               class="w-full px-3 py-2 border rounded-lg">
                        <input type="number" name="max_amount" placeholder="Max" value="<?php echo $max_amount; ?>"
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?php echo $search; ?>" 
                           placeholder="Search transactions..."
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="md:col-span-3 flex justify-end space-x-2">
                    <button type="reset" onclick="resetFilters()"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Reset
                    </button>
                    <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Transactions List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="transactionsTable">
                        <?php while ($transaction = $transactions->fetch_assoc()): ?>
                            <tr class="transaction-row fade-in">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo $transaction['description']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs 
                                          <?php echo $transaction['type'] === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo ucfirst($transaction['category']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo $transaction['account_name']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="<?php echo $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600'; ?>">
                                        <?php echo $transaction['type'] === 'income' ? '+' : '-'; ?>
                                        $<?php echo number_format($transaction['amount'], 2); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="editTransaction(<?php echo $transaction['id']; ?>)"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteTransaction(<?php echo $transaction['id']; ?>)"
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_records); ?> 
                            of <?php echo $total_records; ?> transactions
                        </div>
                        <div class="flex space-x-2">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"
                                   class="px-3 py-1 rounded-lg <?php echo $page === $i ? 
                                         'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleFilters() {
            const form = document.getElementById('filterForm');
            form.classList.toggle('hidden');
            form.classList.toggle('filter-slide');
        }

        function resetFilters() {
            const form = document.getElementById('filterForm');
            form.reset();
            form.submit();
        }

        function showAddTransactionModal() {
            // Implement add transaction modal
        }

        function editTransaction(id) {
            // Implement edit transaction functionality
        }

        function deleteTransaction(id) {
            if (confirm('Are you sure you want to delete this transaction?')) {
                // Implement delete transaction functionality
            }
        }

        // Add loading state to table when filtering
        document.getElementById('filterForm').addEventListener('submit', function() {
            document.getElementById('transactionsTable').classList.add('loading');
        });
    </script>
</body>
</html> 
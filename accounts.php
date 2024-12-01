<?php
require_once 'config.php';
checkAuth();

// Get user accounts
$accounts_query = "SELECT * FROM accounts WHERE user_id = " . $_SESSION['user_id'];
$accounts = $conn->query($accounts_query);

// Get total balance
$total_balance_query = "SELECT SUM(balance) as total FROM accounts WHERE user_id = " . $_SESSION['user_id'];
$total_balance = $conn->query($total_balance_query)->fetch_assoc()['total'];

// Get recent transactions for each account
function getRecentTransactions($account_id, $limit = 3) {
    global $conn;
    $query = "SELECT * FROM transactions 
              WHERE account_id = $account_id 
              ORDER BY transaction_date DESC, id DESC 
              LIMIT $limit";
    return $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <style>
        .account-card {
            transition: all 0.3s ease;
            transform-origin: center;
        }

        .account-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .balance-animation {
            animation: balanceCount 1.5s ease-out;
        }

        @keyframes balanceCount {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-up {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .transaction-list {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .transaction-list.expanded {
            max-height: 500px;
        }

        .progress-ring {
            transition: stroke-dashoffset 0.5s ease;
        }

        .account-icon {
            transition: all 0.3s ease;
        }

        .account-card:hover .account-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Total Balance -->
        <div class="text-center mb-8 slide-up">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Your Accounts</h1>
            <div class="text-2xl font-bold text-blue-600 balance-animation">
                Total Balance: $<span id="totalBalance">0</span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex justify-end mb-6 slide-up" style="animation-delay: 0.2s">
            <button onclick="showAddAccountModal()" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>Add Account
            </button>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            $delay = 0.3;
            while ($account = $accounts->fetch_assoc()): 
                $transactions = getRecentTransactions($account['id']);
            ?>
                <div class="account-card bg-white rounded-lg shadow-lg overflow-hidden" 
                     style="animation: slideUp 0.5s ease-out <?php echo $delay; ?>s both">
                    <!-- Account Header -->
                    <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <div class="account-icon bg-white p-3 rounded-full">
                                    <i class="fas fa-<?php echo isset($account['type']) && $account['type'] === 'savings' ? 'piggy-bank' : 'credit-card'; ?> text-blue-500 text-xl"></i>
                                </div>
                                <div class="text-white">
                                    <h3 class="font-bold text-lg"><?php echo $account['account_name']; ?></h3>
                                    <p class="text-blue-100"><?php echo ucfirst($account['type']); ?></p>
                                </div>
                            </div>
                            <div class="relative group">
                                <button class="text-white hover:text-blue-100">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block">
                                    <a href="#" onclick="editAccount(<?php echo $account['id']; ?>)" 
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </a>
                                    <a href="#" onclick="showTransactions(<?php echo $account['id']; ?>)" 
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-list mr-2"></i>View Transactions
                                    </a>
                                    <a href="#" onclick="deleteAccount(<?php echo $account['id']; ?>)" 
                                       class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-white text-2xl font-bold balance-animation">
                                $<?php echo number_format($account['balance'], 2); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="p-6">
                        <h4 class="text-gray-600 text-sm font-medium mb-4">Recent Transactions</h4>
                        <div class="space-y-3">
                            <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                <div class="flex justify-between items-center text-sm fade-in">
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo $transaction['description']; ?></p>
                                        <p class="text-gray-500"><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></p>
                                    </div>
                                    <span class="<?php echo $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600'; ?> font-medium">
                                        <?php echo $transaction['type'] === 'income' ? '+' : '-'; ?>
                                        $<?php echo number_format($transaction['amount'], 2); ?>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <button onclick="showAllTransactions(<?php echo $account['id']; ?>)"
                                class="mt-4 text-blue-500 hover:text-blue-600 text-sm font-medium">
                            View All Transactions
                        </button>
                    </div>

                    <!-- Account Stats -->
                    <div class="px-6 pb-6">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Monthly Spending</span>
                            <span>$<?php 
                                $spending_query = "SELECT SUM(amount) as total FROM transactions 
                                                 WHERE account_id = " . $account['id'] . " 
                                                 AND type = 'expense' 
                                                 AND MONTH(transaction_date) = MONTH(CURRENT_DATE)";
                                $spending = $conn->query($spending_query)->fetch_assoc()['total'] ?? 0;
                                echo number_format($spending, 2);
                            ?></span>
                        </div>
                        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <?php 
                            $percentage = min(($spending / $account['balance']) * 100, 100);
                            $color = $percentage > 75 ? 'bg-red-500' : ($percentage > 50 ? 'bg-yellow-500' : 'bg-green-500');
                            ?>
                            <div class="<?php echo $color; ?> h-full rounded-full" 
                                 style="width: <?php echo $percentage; ?>%; transition: width 1s ease-out;"
                            ></div>
                        </div>
                    </div>
                </div>
            <?php 
                $delay += 0.1;
            endwhile; 
            ?>
        </div>
    </div>

    <script>
        // Animate total balance counting
        const totalBalance = <?php echo $total_balance; ?>;
        const totalBalanceElement = document.getElementById('totalBalance');
        let currentBalance = 0;
        const duration = 1500;
        const steps = 60;
        const increment = totalBalance / steps;
        const stepDuration = duration / steps;

        const animateBalance = () => {
            currentBalance = Math.min(currentBalance + increment, totalBalance);
            totalBalanceElement.textContent = currentBalance.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            if (currentBalance < totalBalance) {
                setTimeout(animateBalance, stepDuration);
            }
        };

        setTimeout(animateBalance, 500);

        // Account card interactions
        function showAddAccountModal() {
            // Implement add account modal
        }

        function editAccount(id) {
            // Implement edit account functionality
        }

        function deleteAccount(id) {
            if (confirm('Are you sure you want to delete this account? This action cannot be undone.')) {
                // Implement delete account functionality
            }
        }

        function showAllTransactions(accountId) {
            window.location.href = `transactions.php?account_id=${accountId}`;
        }

        // Add intersection observer for animation on scroll
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            },
            { threshold: 0.1 }
        );

        document.querySelectorAll('.account-card').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html> 
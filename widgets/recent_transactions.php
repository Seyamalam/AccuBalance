<?php
$transactions_query = "SELECT t.*, a.account_name
FROM transactions t
JOIN accounts a ON t.account_id = a.id
WHERE a.user_id = " . $_SESSION['user_id'] . "
ORDER BY t.transaction_date DESC, t.id DESC
LIMIT 5";

$transactions = $conn->query($transactions_query);
if ($transactions === false) {
    echo '<p class="text-red-500">Error loading transaction data</p>';
    return;
}
?>

<h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
<div class="space-y-4">
    <?php while ($transaction = $transactions->fetch_assoc()): ?>
        <div class="flex justify-between items-center">
            <div>
                <p class="font-medium"><?php echo $transaction['description']; ?></p>
                <p class="text-sm text-gray-500">
                    <?php echo $transaction['account_name']; ?> • 
                    <?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?>
                </p>
            </div>
            <span class="<?php echo $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600'; ?> font-medium">
                <?php echo $transaction['type'] === 'income' ? '+' : '-'; ?>
                $<?php echo number_format($transaction['amount'], 2); ?>
            </span>
        </div>
    <?php endwhile; ?>
</div> 
<?php
$cash_flow_query = "SELECT 
    DATE_FORMAT(transaction_date, '%Y-%m') as month,
    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses
FROM transactions t
JOIN accounts a ON t.account_id = a.id
WHERE a.user_id = " . $_SESSION['user_id'] . "
GROUP BY month
ORDER BY month DESC
LIMIT 6";

$cash_flow = $conn->query($cash_flow_query);
if ($cash_flow === false) {
    echo '<p class="text-red-500">Error loading cash flow data</p>';
    return;
}

// Display cash flow widget content
?>
<h3 class="text-lg font-semibold mb-4">Cash Flow</h3>
<div class="space-y-4">
    <?php while ($row = $cash_flow->fetch_assoc()): ?>
        <div class="flex justify-between items-center">
            <span><?php echo date('M Y', strtotime($row['month'] . '-01')); ?></span>
            <div class="space-x-4">
                <span class="text-green-600">+$<?php echo number_format($row['income'], 2); ?></span>
                <span class="text-red-600">-$<?php echo number_format($row['expenses'], 2); ?></span>
            </div>
        </div>
    <?php endwhile; ?>
</div> 
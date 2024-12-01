<?php
$expense_query = "SELECT 
    category,
    SUM(amount) as total,
    COUNT(*) as count
FROM transactions t
JOIN accounts a ON t.account_id = a.id
WHERE a.user_id = " . $_SESSION['user_id'] . "
AND t.type = 'expense'
AND MONTH(transaction_date) = MONTH(CURRENT_DATE)
GROUP BY category
ORDER BY total DESC
LIMIT 5";

$expenses = $conn->query($expense_query);
if ($expenses === false) {
    echo '<p class="text-red-500">Error loading expense data</p>';
    return;
}

// Get total expenses for percentage calculation
$total_expenses = 0;
$expenses_data = [];
while ($row = $expenses->fetch_assoc()) {
    $total_expenses += $row['total'];
    $expenses_data[] = $row;
}
?>

<h3 class="text-lg font-semibold mb-4">Monthly Expenses</h3>
<div class="space-y-4">
    <?php foreach ($expenses_data as $expense): ?>
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span><?php echo ucfirst($expense['category']); ?></span>
                <span class="font-medium">$<?php echo number_format($expense['total'], 2); ?></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" 
                     style="width: <?php echo ($expense['total'] / $total_expenses) * 100; ?>%">
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div> 
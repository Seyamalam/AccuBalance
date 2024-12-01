<?php
$investments_query = "SELECT 
    type,
    SUM(quantity * purchase_price) as invested_amount,
    SUM(quantity * current_price) as current_value,
    SUM(quantity * (current_price - purchase_price)) as total_gain_loss
FROM investments
WHERE user_id = " . $_SESSION['user_id'] . "
GROUP BY type
ORDER BY current_value DESC
LIMIT 4";

$investments = $conn->query($investments_query);
if ($investments === false) {
    echo '<p class="text-red-500">Error loading investment data</p>';
    return;
}
?>

<h3 class="text-lg font-semibold mb-4">Investment Summary</h3>
<div class="space-y-4">
    <?php while ($investment = $investments->fetch_assoc()): 
        $gain_percentage = ($investment['total_gain_loss'] / $investment['invested_amount']) * 100;
    ?>
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span><?php echo ucfirst($investment['type']); ?></span>
                <span class="font-medium">$<?php echo number_format($investment['current_value'], 2); ?></span>
            </div>
            <div class="flex justify-between text-xs text-gray-500">
                <span>Initial: $<?php echo number_format($investment['invested_amount'], 2); ?></span>
                <span class="<?php echo $gain_percentage >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $gain_percentage >= 0 ? '+' : ''; ?><?php echo number_format($gain_percentage, 1); ?>%
                </span>
            </div>
        </div>
    <?php endwhile; ?>
</div> 
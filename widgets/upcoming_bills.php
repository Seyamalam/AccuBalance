<?php
$bills_query = "SELECT *
FROM bill_reminders
WHERE user_id = " . $_SESSION['user_id'] . "
AND status = 'pending'
AND due_date >= CURRENT_DATE
ORDER BY due_date ASC
LIMIT 5";

$bills = $conn->query($bills_query);
if ($bills === false) {
    echo '<p class="text-red-500">Error loading bills data</p>';
    return;
}

// Check if there are any bills
$has_bills = $bills->num_rows > 0;
?>

<h3 class="text-lg font-semibold mb-4">Upcoming Bills</h3>
<div class="space-y-4">
    <?php if ($has_bills): ?>
        <?php while ($bill = $bills->fetch_assoc()): ?>
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-medium"><?php echo htmlspecialchars($bill['description'] ?? 'Untitled Bill'); ?></p>
                    <p class="text-sm text-gray-500">
                        Due: <?php echo date('M d, Y', strtotime($bill['due_date'])); ?>
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-medium">$<?php echo number_format($bill['amount'], 2); ?></p>
                    <?php
                    $days_until = floor((strtotime($bill['due_date']) - time()) / (60 * 60 * 24));
                    $status_color = $days_until <= 3 ? 'text-red-600' : ($days_until <= 7 ? 'text-yellow-600' : 'text-green-600');
                    ?>
                    <p class="text-sm <?php echo $status_color; ?>">
                        <?php echo $days_until; ?> days left
                    </p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-500 text-center py-4">No upcoming bills</p>
    <?php endif; ?>
</div> 
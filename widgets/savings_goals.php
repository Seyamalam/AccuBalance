<?php
$goals_query = "SELECT g.*,
    (SELECT SUM(amount) FROM goal_contributions WHERE goal_id = g.id) as current_amount
FROM savings_goals g
WHERE g.user_id = " . $_SESSION['user_id'] . "
AND g.status = 'active'
ORDER BY g.deadline ASC
LIMIT 3";

$goals = $conn->query($goals_query);
if ($goals === false) {
    echo '<p class="text-red-500">Error loading goals data</p>';
    return;
}
?>

<h3 class="text-lg font-semibold mb-4">Savings Goals</h3>
<div class="space-y-4">
    <?php while ($goal = $goals->fetch_assoc()): 
        $progress = ($goal['current_amount'] / $goal['target_amount']) * 100;
        $days_left = floor((strtotime($goal['deadline']) - time()) / (60 * 60 * 24));
    ?>
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span><?php echo $goal['goal_name']; ?></span>
                <span class="font-medium">
                    $<?php echo number_format($goal['current_amount'], 2); ?> / 
                    $<?php echo number_format($goal['target_amount'], 2); ?>
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                <div class="bg-green-600 h-2 rounded-full" 
                     style="width: <?php echo min($progress, 100); ?>%">
                </div>
            </div>
            <p class="text-xs text-gray-500 text-right">
                <?php echo $days_left; ?> days remaining
            </p>
        </div>
    <?php endwhile; ?>
</div> 
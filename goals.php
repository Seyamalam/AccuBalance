<?php
require_once 'config.php';
checkAuth();

// Get user's goals
$goals_query = "SELECT * FROM goals WHERE user_id = " . $_SESSION['user_id'] . " ORDER BY deadline ASC";
$goals = $conn->query($goals_query);

// Get goal progress history
function getGoalProgress($goal_id) {
    global $conn;
    $query = "SELECT * FROM savings_goal_progress 
              WHERE goal_id = $goal_id 
              ORDER BY date DESC 
              LIMIT 6";
    return $conn->query($query);
}

// Calculate time remaining
function getTimeRemaining($deadline) {
    $now = new DateTime();
    $end = new DateTime($deadline);
    $interval = $now->diff($end);
    
    if ($interval->y > 0) {
        return $interval->format('%y years');
    } elseif ($interval->m > 0) {
        return $interval->format('%m months');
    } else {
        return $interval->format('%d days');
    }
}

// Handle goal operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $goal_name = $conn->real_escape_string($_POST['goal_name']);
            $target_amount = $conn->real_escape_string($_POST['target_amount']);
            $current_amount = $conn->real_escape_string($_POST['current_amount']);
            $deadline = $conn->real_escape_string($_POST['deadline']);
            
            $query = "INSERT INTO goals (user_id, goal_name, target_amount, current_amount, deadline) 
                      VALUES (" . $_SESSION['user_id'] . ", '$goal_name', '$target_amount', '$current_amount', '$deadline')";
            
            if ($conn->query($query)) {
                $goal_id = $conn->insert_id;
                // Add initial progress record
                $progress_query = "INSERT INTO savings_goal_progress (goal_id, amount, date) 
                                 VALUES ($goal_id, $current_amount, CURRENT_DATE)";
                $conn->query($progress_query);
                $success = "Goal created successfully";
            } else {
                $error = "Failed to create goal";
            }
        } elseif ($_POST['action'] === 'update_progress' && isset($_POST['goal_id'])) {
            $goal_id = $conn->real_escape_string($_POST['goal_id']);
            $new_amount = $conn->real_escape_string($_POST['amount']);
            
            // Update goal current amount
            $update_query = "UPDATE goals 
                           SET current_amount = $new_amount 
                           WHERE id = $goal_id AND user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($update_query)) {
                // Add progress record
                $progress_query = "INSERT INTO savings_goal_progress (goal_id, amount, date) 
                                 VALUES ($goal_id, $new_amount, CURRENT_DATE)";
                $conn->query($progress_query);
                $success = "Progress updated successfully";
            } else {
                $error = "Failed to update progress";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Goals - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <style>
        .progress-ring {
            transform: rotate(-90deg);
            transition: all 1s ease-out;
        }

        .progress-ring-circle {
            transition: stroke-dashoffset 1s ease-out;
        }

        .goal-card {
            transition: all 0.3s ease;
        }

        .goal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            transition: width 1s ease-out;
        }

        .milestone {
            transition: all 0.3s ease;
        }

        .milestone.achieved {
            transform: scale(1.1);
        }

        @keyframes celebrate {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .celebration {
            animation: celebrate 0.5s ease-out;
        }

        .progress-chart {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-out;
        }

        .progress-chart.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .sparkline {
            fill: none;
            stroke: #60A5FA;
            stroke-width: 2;
            transition: all 0.3s ease;
        }

        .sparkline:hover {
            stroke-width: 3;
        }

        .goal-icon {
            transition: all 0.3s ease;
        }

        .goal-card:hover .goal-icon {
            transform: scale(1.2) rotate(15deg);
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Financial Goals</h1>
            <p class="text-gray-600">Track and achieve your financial dreams</p>
        </div>

        <!-- Add Goal Button -->
        <div class="flex justify-end mb-6">
            <button onclick="showAddGoalModal()" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>Add New Goal
            </button>
        </div>

        <!-- Goals Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            $delay = 0;
            while ($goal = $goals->fetch_assoc()): 
                $progress = ($goal['current_amount'] / $goal['target_amount']) * 100;
                $progress_history = getGoalProgress($goal['id']);
            ?>
                <div class="goal-card bg-white rounded-lg shadow-lg overflow-hidden"
                     style="animation: slideIn 0.5s ease-out <?php echo $delay; ?>s forwards">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="goal-icon bg-blue-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-bullseye text-blue-500 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg"><?php echo $goal['goal_name']; ?></h3>
                                    <p class="text-gray-500">
                                        <?php echo getTimeRemaining($goal['deadline']); ?> remaining
                                    </p>
                                </div>
                            </div>
                            <div class="relative">
                                <button class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block">
                                    <button onclick="updateProgress(<?php echo $goal['id']; ?>)"
                                            class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        Update Progress
                                    </button>
                                    <button onclick="editGoal(<?php echo $goal['id']; ?>)"
                                            class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        Edit Goal
                                    </button>
                                    <button onclick="deleteGoal(<?php echo $goal['id']; ?>)"
                                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                        Delete Goal
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Circle -->
                        <div class="flex justify-center mb-4">
                            <svg class="progress-ring" width="120" height="120">
                                <circle class="progress-ring-circle" 
                                        stroke="#E5E7EB"
                                        stroke-width="8"
                                        fill="transparent"
                                        r="52"
                                        cx="60"
                                        cy="60"/>
                                <circle class="progress-ring-circle" 
                                        stroke="<?php echo $progress >= 100 ? '#059669' : '#3B82F6'; ?>"
                                        stroke-width="8"
                                        fill="transparent"
                                        r="52"
                                        cx="60"
                                        cy="60"
                                        style="stroke-dasharray: 326.73; stroke-dashoffset: <?php echo 326.73 * (1 - $progress / 100); ?>"/>
                                <text x="60" y="60" 
                                      text-anchor="middle" 
                                      dominant-baseline="middle"
                                      fill="<?php echo $progress >= 100 ? '#059669' : '#3B82F6'; ?>"
                                      font-size="20"
                                      font-weight="bold">
                                    <?php echo round($progress); ?>%
                                </text>
                            </svg>
                        </div>

                        <!-- Amount Progress -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">Current</span>
                                <span class="font-medium">
                                    $<?php echo number_format($goal['current_amount'], 2); ?>
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Target</span>
                                <span class="font-medium">
                                    $<?php echo number_format($goal['target_amount'], 2); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Progress History -->
                        <div class="progress-chart" data-goal-id="<?php echo $goal['id']; ?>">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Progress History</h4>
                            <canvas id="progressChart<?php echo $goal['id']; ?>" height="100"></canvas>
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
        // Initialize progress charts
        document.addEventListener('DOMContentLoaded', function() {
            const progressCharts = document.querySelectorAll('.progress-chart');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        const goalId = entry.target.dataset.goalId;
                        initializeChart(goalId);
                    }
                });
            });

            progressCharts.forEach(chart => observer.observe(chart));
        });

        function initializeChart(goalId) {
            const ctx = document.getElementById(`progressChart${goalId}`).getContext('2d');
            // Initialize chart with goal progress data
            // Add chart configuration here
        }

        function showAddGoalModal() {
            // Implement add goal modal
        }

        function updateProgress(goalId) {
            // Implement update progress modal
        }

        function editGoal(goalId) {
            // Implement edit goal functionality
        }

        function deleteGoal(goalId) {
            if (confirm('Are you sure you want to delete this goal?')) {
                // Implement delete goal functionality
            }
        }

        // Add celebration animation when goal is achieved
        function celebrateGoal(goalCard) {
            goalCard.classList.add('celebration');
            setTimeout(() => {
                goalCard.classList.remove('celebration');
            }, 500);
        }
    </script>
</body>
</html> 
<?php
function renderGoalProgressChart($goal_id, $chart_type = 'line') {
    global $conn;
    
    // Get goal progress history
    $progress_query = "SELECT * FROM savings_goal_progress 
                      WHERE goal_id = $goal_id 
                      ORDER BY date ASC";
    $progress = $conn->query($progress_query);
    
    // Get goal details
    $goal_query = "SELECT * FROM goals WHERE id = $goal_id";
    $goal = $conn->query($goal_query)->fetch_assoc();
    
    // Get milestones
    $milestones_query = "SELECT * FROM goal_milestones 
                        WHERE goal_id = $goal_id 
                        ORDER BY deadline ASC";
    $milestones = $conn->query($milestones_query);
    
    // Prepare data for charts
    $dates = [];
    $amounts = [];
    $target_line = [];
    $milestone_points = [];
    
    while ($row = $progress->fetch_assoc()) {
        $dates[] = date('M d', strtotime($row['date']));
        $amounts[] = $row['amount'];
        $target_line[] = $goal['target_amount'];
    }
    
    while ($milestone = $milestones->fetch_assoc()) {
        $milestone_points[] = [
            'x' => date('M d', strtotime($milestone['deadline'])),
            'y' => $milestone['target_amount'],
            'title' => $milestone['title']
        ];
    }
    
    // Calculate trend line
    $trend = calculateTrendLine($amounts);
    $projected_completion = calculateProjectedCompletion($amounts, $goal['target_amount']);
?>

<div class="goal-progress-chart">
    <div class="flex justify-between items-center mb-4">
        <div class="flex space-x-4">
            <button onclick="changeChartType('line')" 
                    class="chart-type-btn <?php echo $chart_type === 'line' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
            </button>
            <button onclick="changeChartType('bar')" 
                    class="chart-type-btn <?php echo $chart_type === 'bar' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
            </button>
            <button onclick="changeChartType('radar')" 
                    class="chart-type-btn <?php echo $chart_type === 'radar' ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i>
            </button>
        </div>
        
        <div class="text-sm text-gray-500">
            Projected completion: <?php echo $projected_completion; ?>
        </div>
    </div>

    <canvas id="goalChart<?php echo $goal_id; ?>" height="300"></canvas>

    <script>
        new Chart(document.getElementById('goalChart<?php echo $goal_id; ?>').getContext('2d'), {
            type: '<?php echo $chart_type; ?>',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Progress',
                    data: <?php echo json_encode($amounts); ?>,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Target',
                    data: <?php echo json_encode($target_line); ?>,
                    borderColor: '#10B981',
                    borderDash: [5, 5],
                    fill: false
                }, {
                    label: 'Trend',
                    data: <?php echo json_encode($trend); ?>,
                    borderColor: '#8B5CF6',
                    borderDash: [2, 2],
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    annotation: {
                        annotations: {
                            <?php foreach ($milestone_points as $index => $milestone): ?>
                            milestone<?php echo $index; ?>: {
                                type: 'point',
                                xValue: '<?php echo $milestone['x']; ?>',
                                yValue: <?php echo $milestone['y']; ?>,
                                backgroundColor: '#F59E0B',
                                radius: 6,
                                label: {
                                    content: '<?php echo $milestone['title']; ?>',
                                    enabled: true,
                                    position: 'top'
                                }
                            },
                            <?php endforeach; ?>
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + 
                                       context.raw.toLocaleString('en-US', {
                                           minimumFractionDigits: 2,
                                           maximumFractionDigits: 2
                                       });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('en-US');
                            }
                        }
                    }
                }
            }
        });
    </script>
</div>

<?php
}

function calculateTrendLine($amounts) {
    // Simple linear regression
    $n = count($amounts);
    $x = range(0, $n - 1);
    $xy = array_map(function($x, $y) { return $x * $y; }, $x, $amounts);
    $xx = array_map(function($x) { return $x * $x; }, $x);
    
    $slope = ($n * array_sum($xy) - array_sum($x) * array_sum($amounts)) /
             ($n * array_sum($xx) - array_sum($x) * array_sum($x));
             
    $intercept = (array_sum($amounts) - $slope * array_sum($x)) / $n;
    
    return array_map(function($x) use ($slope, $intercept) {
        return $slope * $x + $intercept;
    }, $x);
}

function calculateProjectedCompletion($amounts, $target) {
    $trend = calculateTrendLine($amounts);
    $slope = ($trend[count($trend) - 1] - $trend[0]) / (count($trend) - 1);
    
    if ($slope <= 0) return 'Not on track';
    
    $current = end($amounts);
    $remaining = $target - $current;
    $days_remaining = ceil($remaining / ($slope / 30)); // Assuming monthly data points
    
    $completion_date = date('M d, Y', strtotime("+$days_remaining days"));
    return $completion_date;
}
?> 
<?php
require_once 'config.php';
checkAuth();

// Get user preferences
$preferences_query = "SELECT * FROM user_preferences WHERE user_id = " . $_SESSION['user_id'];
$preferences = $conn->query($preferences_query)->fetch_assoc();

// If no preferences exist, create default ones
if (!$preferences) {
    $default_widgets = json_encode([
        'cash_flow' => true,
        'expense_breakdown' => true,
        'upcoming_bills' => true,
        'recent_transactions' => true,
        'savings_goals' => true,
        'investment_summary' => true
    ]);
    
    $query = "INSERT INTO user_preferences (user_id, default_currency, theme, dashboard_widgets) 
              VALUES (" . $_SESSION['user_id'] . ", 'USD', 'system', '$default_widgets')";
    $conn->query($query);
    $preferences = $conn->query($preferences_query)->fetch_assoc();
}

// Handle preference updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_general') {
            $currency = $conn->real_escape_string($_POST['default_currency']);
            $theme = $conn->real_escape_string($_POST['theme']);
            $notification_email = isset($_POST['notification_email']) ? 1 : 0;
            $notification_web = isset($_POST['notification_web']) ? 1 : 0;
            
            $query = "UPDATE user_preferences 
                      SET default_currency = '$currency',
                          theme = '$theme',
                          notification_email = $notification_email,
                          notification_web = $notification_web
                      WHERE user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                $success = "Preferences updated successfully";
            } else {
                $error = "Failed to update preferences";
            }
        } elseif ($_POST['action'] === 'update_widgets') {
            $widgets = json_encode($_POST['widgets']);
            
            $query = "UPDATE user_preferences 
                      SET dashboard_widgets = '$widgets'
                      WHERE user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                $success = "Dashboard layout updated successfully";
            } else {
                $error = "Failed to update dashboard layout";
            }
        } elseif ($_POST['action'] === 'update_visualization') {
            $chart_preferences = json_encode([
                'colors' => $_POST['chart_colors'],
                'animations' => isset($_POST['chart_animations']),
                'labels' => isset($_POST['chart_labels']),
                'currency_format' => $_POST['currency_format']
            ]);
            
            $query = "UPDATE user_preferences 
                      SET visualization_preferences = '$chart_preferences'
                      WHERE user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                $success = "Visualization preferences updated successfully";
            } else {
                $error = "Failed to update visualization preferences";
            }
        }
    }
    
    // Refresh preferences after update
    $preferences = $conn->query($preferences_query)->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferences - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">User Preferences</h1>
            <p class="text-gray-600">AccuBalance: Simplify Finances, Amplify Success</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- General Preferences -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">General Preferences</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_general">
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Default Currency</label>
                        <select name="default_currency" class="w-full px-3 py-2 border rounded-lg">
                            <option value="USD" <?php echo $preferences['default_currency'] === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                            <option value="EUR" <?php echo $preferences['default_currency'] === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                            <option value="GBP" <?php echo $preferences['default_currency'] === 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                            <option value="JPY" <?php echo $preferences['default_currency'] === 'JPY' ? 'selected' : ''; ?>>JPY (¥)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Theme</label>
                        <select name="theme" class="w-full px-3 py-2 border rounded-lg">
                            <option value="light" <?php echo $preferences['theme'] === 'light' ? 'selected' : ''; ?>>Light</option>
                            <option value="dark" <?php echo $preferences['theme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                            <option value="system" <?php echo $preferences['theme'] === 'system' ? 'selected' : ''; ?>>System Default</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="notification_email" 
                                   <?php echo $preferences['notification_email'] ? 'checked' : ''; ?>
                                   class="mr-2">
                            Enable Email Notifications
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="notification_web" 
                                   <?php echo $preferences['notification_web'] ? 'checked' : ''; ?>
                                   class="mr-2">
                            Enable Web Notifications
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Save General Preferences
                    </button>
                </form>
            </div>

            <!-- Dashboard Layout -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Dashboard Layout</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_widgets">
                    
                    <?php 
                    $widgets = json_decode($preferences['dashboard_widgets'], true);
                    $available_widgets = [
                        'cash_flow' => 'Cash Flow Chart',
                        'expense_breakdown' => 'Expense Breakdown',
                        'upcoming_bills' => 'Upcoming Bills',
                        'recent_transactions' => 'Recent Transactions',
                        'savings_goals' => 'Savings Goals',
                        'investment_summary' => 'Investment Summary'
                    ];
                    
                    foreach ($available_widgets as $key => $label):
                    ?>
                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span><?php echo $label; ?></span>
                            <input type="checkbox" name="widgets[<?php echo $key; ?>]" 
                                   <?php echo isset($widgets[$key]) && $widgets[$key] ? 'checked' : ''; ?>>
                        </label>
                    <?php endforeach; ?>

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Save Dashboard Layout
                    </button>
                </form>
            </div>

            <!-- Visualization Preferences -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Visualization Preferences</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_visualization">
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Chart Color Scheme</label>
                        <select name="chart_colors" class="w-full px-3 py-2 border rounded-lg">
                            <option value="default">Default Colors</option>
                            <option value="monochrome">Monochrome</option>
                            <option value="pastel">Pastel Colors</option>
                            <option value="vibrant">Vibrant Colors</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2">Currency Format</label>
                        <select name="currency_format" class="w-full px-3 py-2 border rounded-lg">
                            <option value="compact">Compact (1K, 1M)</option>
                            <option value="full">Full (1,000, 1,000,000)</option>
                            <option value="decimal">Decimal (1000.00)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="chart_animations" class="mr-2">
                            Enable Chart Animations
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="chart_labels" class="mr-2">
                            Show Data Labels on Charts
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Save Visualization Preferences
                    </button>
                </form>
            </div>

            <!-- Preview Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Preview</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium mb-2">Line Chart</h3>
                        <canvas id="previewLineChart"></canvas>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium mb-2">Bar Chart</h3>
                        <canvas id="previewBarChart"></canvas>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium mb-2">Doughnut Chart</h3>
                        <canvas id="previewDoughnutChart"></canvas>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium mb-2">Area Chart</h3>
                        <canvas id="previewAreaChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Export/Import Section -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h2 class="text-xl font-bold mb-4">Export/Import Preferences</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-medium mb-2">Export Preferences</h3>
                        <button onclick="exportPreferences()" 
                                class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">
                            Export Settings
                        </button>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium mb-2">Import Preferences</h3>
                        <input type="file" id="importFile" accept=".json" class="hidden" 
                               onchange="importPreferences(this)">
                        <button onclick="document.getElementById('importFile').click()" 
                                class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                            Import Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add preview functionality and real-time updates
        function updatePreview() {
            // Implement preview updates based on selected preferences
        }

        // Add theme switcher
        function applyTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (theme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                // System default
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        }

        // Apply initial theme
        applyTheme('<?php echo $preferences['theme']; ?>');

        function exportPreferences() {
            const preferences = {
                theme: document.querySelector('[name="theme"]').value,
                currency: document.querySelector('[name="default_currency"]').value,
                notifications: {
                    email: document.querySelector('[name="notification_email"]').checked,
                    web: document.querySelector('[name="notification_web"]').checked
                },
                widgets: Array.from(document.querySelectorAll('[name^="widgets"]'))
                    .reduce((acc, input) => ({...acc, [input.name]: input.checked}), {}),
                visualization: {
                    colors: document.querySelector('[name="chart_colors"]').value,
                    animations: document.querySelector('[name="chart_animations"]').checked,
                    labels: document.querySelector('[name="chart_labels"]').checked,
                    currencyFormat: document.querySelector('[name="currency_format"]').value
                }
            };

            const blob = new Blob([JSON.stringify(preferences, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'accubalance-preferences.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        async function importPreferences(input) {
            try {
                const file = input.files[0];
                const text = await file.text();
                const preferences = JSON.parse(text);

                // Apply imported preferences
                Object.entries(preferences).forEach(([key, value]) => {
                    if (typeof value === 'object') {
                        Object.entries(value).forEach(([subKey, subValue]) => {
                            const element = document.querySelector(`[name="${key}[${subKey}]"]`);
                            if (element) {
                                if (element.type === 'checkbox') {
                                    element.checked = subValue;
                                } else {
                                    element.value = subValue;
                                }
                            }
                        });
                    } else {
                        const element = document.querySelector(`[name="${key}"]`);
                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = value;
                            } else {
                                element.value = value;
                            }
                        }
                    }
                });

                // Update preview
                updatePreview();
                
                alert('Preferences imported successfully');
            } catch (error) {
                alert('Error importing preferences: ' + error.message);
            }
        }
    </script>
</body>
</html> 
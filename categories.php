<?php
require_once 'config.php';
checkAuth();

// Handle category operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $name = $conn->real_escape_string($_POST['name']);
            $type = $conn->real_escape_string($_POST['type']);
            $icon = $conn->real_escape_string($_POST['icon']);
            $color = $conn->real_escape_string($_POST['color']);
            
            $query = "INSERT INTO budget_categories (user_id, name, type, icon, color) 
                      VALUES (" . $_SESSION['user_id'] . ", '$name', '$type', '$icon', '$color')";
            
            if ($conn->query($query)) {
                $success = "Category created successfully";
            } else {
                $error = "Failed to create category";
            }
        } elseif ($_POST['action'] === 'update' && isset($_POST['category_id'])) {
            $category_id = $conn->real_escape_string($_POST['category_id']);
            $name = $conn->real_escape_string($_POST['name']);
            $icon = $conn->real_escape_string($_POST['icon']);
            $color = $conn->real_escape_string($_POST['color']);
            
            $query = "UPDATE budget_categories 
                      SET name = '$name', icon = '$icon', color = '$color'
                      WHERE id = $category_id AND user_id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                $success = "Category updated successfully";
            } else {
                $error = "Failed to update category";
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['category_id'])) {
            $category_id = $conn->real_escape_string($_POST['category_id']);
            
            // Check if category is in use
            $check_query = "SELECT COUNT(*) as count FROM transactions t
                           JOIN accounts a ON t.account_id = a.id
                           WHERE a.user_id = " . $_SESSION['user_id'] . "
                           AND t.category = (SELECT name FROM budget_categories WHERE id = $category_id)";
            $check_result = $conn->query($check_query)->fetch_assoc();
            
            if ($check_result['count'] > 0) {
                $error = "Cannot delete category that is in use";
            } else {
                $query = "DELETE FROM budget_categories 
                         WHERE id = $category_id AND user_id = " . $_SESSION['user_id'];
                
                if ($conn->query($query)) {
                    $success = "Category deleted successfully";
                } else {
                    $error = "Failed to delete category";
                }
            }
        }
    }
}

// Fetch categories
$categories_query = "SELECT * FROM budget_categories 
                    WHERE user_id = " . $_SESSION['user_id'] . "
                    ORDER BY type, name";
$categories = $conn->query($categories_query);

// Get category usage statistics
$usage_query = "
    SELECT 
        bc.name,
        COUNT(t.id) as transaction_count,
        SUM(t.amount) as total_amount
    FROM budget_categories bc
    LEFT JOIN transactions t ON t.category = bc.name
    LEFT JOIN accounts a ON t.account_id = a.id
    WHERE bc.user_id = " . $_SESSION['user_id'] . "
    AND (a.user_id = " . $_SESSION['user_id'] . " OR a.user_id IS NULL)
    GROUP BY bc.name";
$usage_stats = $conn->query($usage_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Category Management</h1>
            <p class="text-gray-600">AccuBalance: Simplify Finances, Amplify Success</p>
        </div>

        <!-- Add Category Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Category</h2>
            
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

            <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label class="block text-gray-700 mb-2">Category Name</label>
                    <input type="text" name="name" required 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Type</label>
                    <select name="type" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Icon</label>
                    <select name="icon" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="fa-shopping-cart">🛒 Shopping</option>
                        <option value="fa-utensils">🍽️ Food</option>
                        <option value="fa-home">🏠 Housing</option>
                        <option value="fa-car">🚗 Transport</option>
                        <option value="fa-heartbeat">❤️ Health</option>
                        <option value="fa-graduation-cap">🎓 Education</option>
                        <option value="fa-plane">✈️ Travel</option>
                        <option value="fa-money-bill">💰 Income</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Color</label>
                    <input type="color" name="color" required 
                           class="w-full h-10 px-3 py-2 border rounded-lg">
                </div>

                <div class="md:col-span-4">
                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Add Category
                    </button>
                </div>
            </form>
        </div>

        <!-- Categories List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Expense Categories -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-bold">Expense Categories</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php 
                        $categories->data_seek(0);
                        while ($category = $categories->fetch_assoc()):
                            if ($category['type'] === 'expense'):
                        ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <i class="fas <?php echo $category['icon']; ?>" 
                                       style="color: <?php echo $category['color']; ?>"></i>
                                    <span class="font-medium"><?php echo $category['name']; ?></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)"
                                            class="text-blue-600 hover:text-blue-800">
                                        Edit
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endwhile; 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Income Categories -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-xl font-bold">Income Categories</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php 
                        $categories->data_seek(0);
                        while ($category = $categories->fetch_assoc()):
                            if ($category['type'] === 'income'):
                        ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <i class="fas <?php echo $category['icon']; ?>" 
                                       style="color: <?php echo $category['color']; ?>"></i>
                                    <span class="font-medium"><?php echo $category['name']; ?></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)"
                                            class="text-blue-600 hover:text-blue-800">
                                        Edit
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endwhile; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h3 class="text-xl font-bold mb-4">Edit Category</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="category_id" id="editCategoryId">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Category Name</label>
                            <input type="text" name="name" id="editCategoryName" required 
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2">Icon</label>
                            <select name="icon" id="editCategoryIcon" required 
                                    class="w-full px-3 py-2 border rounded-lg">
                                <option value="fa-shopping-cart">🛒 Shopping</option>
                                <option value="fa-utensils">🍽️ Food</option>
                                <option value="fa-home">🏠 Housing</option>
                                <option value="fa-car">🚗 Transport</option>
                                <option value="fa-heartbeat">❤️ Health</option>
                                <option value="fa-graduation-cap">🎓 Education</option>
                                <option value="fa-plane">✈️ Travel</option>
                                <option value="fa-money-bill">💰 Income</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2">Color</label>
                            <input type="color" name="color" id="editCategoryColor" required 
                                   class="w-full h-10 px-3 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEditModal()"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editCategory(category) {
            document.getElementById('editCategoryId').value = category.id;
            document.getElementById('editCategoryName').value = category.name;
            document.getElementById('editCategoryIcon').value = category.icon;
            document.getElementById('editCategoryColor').value = category.color;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html> 
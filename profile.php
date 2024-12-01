<?php
require_once 'config.php';
checkAuth();

// Fetch user data
$user_query = "SELECT * FROM users WHERE id = " . $_SESSION['user_id'];
$user = $conn->query($user_query)->fetch_assoc();

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            $username = $conn->real_escape_string($_POST['username']);
            $email = $conn->real_escape_string($_POST['email']);
            
            $query = "UPDATE users SET 
                        username = '$username',
                        email = '$email'
                     WHERE id = " . $_SESSION['user_id'];
            
            if ($conn->query($query)) {
                $success = "Profile updated successfully";
            } else {
                $error = "Failed to update profile";
            }
        } elseif ($_POST['action'] === 'change_password') {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if (password_verify($current_password, $user['password'])) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $query = "UPDATE users SET password = '$hashed_password' WHERE id = " . $_SESSION['user_id'];
                    
                    if ($conn->query($query)) {
                        $success = "Password changed successfully";
                    } else {
                        $error = "Failed to change password";
                    }
                } else {
                    $error = "New passwords do not match";
                }
            } else {
                $error = "Current password is incorrect";
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
    <title>Profile - Finance Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
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
            <!-- Profile Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Profile Information</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" value="<?php echo $user['username']; ?>" required 
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?php echo $user['email']; ?>" required 
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Update Profile
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Change Password</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password" required 
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">New Password</label>
                        <input type="password" name="new_password" required 
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="confirm_password" required 
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 
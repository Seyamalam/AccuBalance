<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            
            // Set user preferences if not exists
            $pref_check = "SELECT id FROM user_preferences WHERE user_id = " . $user['id'];
            if ($conn->query($pref_check)->num_rows === 0) {
                $default_prefs = "INSERT INTO user_preferences (user_id) VALUES (" . $user['id'] . ")";
                $conn->query($default_prefs);
            }
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <style>
        .fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in-left {
            animation: slideInLeft 0.5s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            left: 0;
            background: linear-gradient(to right, #3b82f6 0%, #93c5fd 50%, #3b82f6 100%);
            animation: loading 2s infinite;
            transform-origin: left;
        }

        @keyframes loading {
            0% {
                transform: scaleX(0);
            }
            50% {
                transform: scaleX(1);
            }
            100% {
                transform: scaleX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-4xl w-full mx-4 grid md:grid-cols-2 gap-8">
        <!-- Left Side - Branding -->
        <div class="bg-white p-8 rounded-lg shadow-lg fade-in-up">
            <div class="text-center mb-8">
                <img src="assets/logo.png" alt="AccuBalance Logo" class="h-20 w-20 mx-auto mb-4 pulse">
                <h1 class="text-2xl font-bold text-gray-900">AccuBalance</h1>
                <p class="text-gray-600">Simplify Finances, Amplify Success</p>
            </div>

            <div class="space-y-6 slide-in-left">
                <div class="flex items-center space-x-4 text-gray-600">
                    <i class="fas fa-chart-line text-blue-500"></i>
                    <span>Track your finances with ease</span>
                </div>
                <div class="flex items-center space-x-4 text-gray-600">
                    <i class="fas fa-piggy-bank text-blue-500"></i>
                    <span>Set and achieve financial goals</span>
                </div>
                <div class="flex items-center space-x-4 text-gray-600">
                    <i class="fas fa-shield-alt text-blue-500"></i>
                    <span>Secure and private</span>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="bg-white p-8 rounded-lg shadow-lg fade-in-up" style="animation-delay: 0.2s">
            <h2 class="text-2xl font-bold mb-6 text-center">Welcome Back</h2>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 fade-in-up">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6" id="loginForm">
                <div>
                    <label class="block text-gray-700 mb-2" for="email">Email Address</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                           placeholder="Enter your email">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2" for="password">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                               placeholder="Enter your password">
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="form-checkbox text-blue-500">
                        <span class="ml-2 text-gray-600">Remember me</span>
                    </label>
                    <a href="#" class="text-blue-500 hover:text-blue-600">Forgot password?</a>
                </div>

                <button type="submit" id="loginButton"
                        class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 transition-all transform hover:scale-105">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="text-blue-500 hover:text-blue-600">Sign up</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.querySelector('.fa-eye');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            button.classList.add('loading');
            button.disabled = true;
            button.innerHTML = 'Signing in...';
        });
    </script>
</body>
</html> 
<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Validate password strength
    $password_strength = 0;
    $password_strength += preg_match('/[A-Z]/', $_POST['password']) ? 1 : 0;
    $password_strength += preg_match('/[a-z]/', $_POST['password']) ? 1 : 0;
    $password_strength += preg_match('/[0-9]/', $_POST['password']) ? 1 : 0;
    $password_strength += preg_match('/[^A-Za-z0-9]/', $_POST['password']) ? 1 : 0;
    
    if (strlen($_POST['password']) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($password_strength < 3) {
        $error = "Password must contain at least 3 of the following: uppercase, lowercase, numbers, special characters";
    } else {
        // Check if username or email already exists
        $check_query = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        $result = $conn->query($check_query);
        
        if ($result->num_rows > 0) {
            $error = "Username or email already exists";
        } else {
            $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
            
            if ($conn->query($query)) {
                $_SESSION['registration_success'] = true;
                header('Location: login.php');
                exit();
            } else {
                $error = "Registration failed";
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
    <title>Register - AccuBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <style>
        /* Include all animations from login.php */
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

        .password-strength-meter {
            height: 4px;
            background-color: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .password-strength-meter div {
            height: 100%;
            transition: all 0.3s;
        }

        .strength-weak { width: 25%; background-color: #ef4444; }
        .strength-fair { width: 50%; background-color: #f59e0b; }
        .strength-good { width: 75%; background-color: #10b981; }
        .strength-strong { width: 100%; background-color: #059669; }

        .feature-icon {
            transition: all 0.3s;
        }

        .feature:hover .feature-icon {
            transform: scale(1.2);
            color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full grid md:grid-cols-2 gap-8">
        <!-- Left Side - Features -->
        <div class="bg-white p-8 rounded-lg shadow-lg fade-in-up">
            <div class="text-center mb-8">
                <img src="assets/logo.png" alt="AccuBalance Logo" class="h-20 w-20 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Join AccuBalance</h1>
                <p class="text-gray-600">Simplify Finances, Amplify Success</p>
            </div>

            <div class="space-y-6 slide-in-left">
                <div class="feature flex items-center space-x-4 p-4 rounded-lg hover:bg-gray-50 transition-all">
                    <i class="feature-icon fas fa-shield-alt text-2xl text-blue-500"></i>
                    <div>
                        <h3 class="font-medium text-gray-900">Secure Account</h3>
                        <p class="text-sm text-gray-600">Bank-level security for your financial data</p>
                    </div>
                </div>

                <div class="feature flex items-center space-x-4 p-4 rounded-lg hover:bg-gray-50 transition-all">
                    <i class="feature-icon fas fa-chart-line text-2xl text-blue-500"></i>
                    <div>
                        <h3 class="font-medium text-gray-900">Smart Analytics</h3>
                        <p class="text-sm text-gray-600">AI-powered insights for better decisions</p>
                    </div>
                </div>

                <div class="feature flex items-center space-x-4 p-4 rounded-lg hover:bg-gray-50 transition-all">
                    <i class="feature-icon fas fa-mobile-alt text-2xl text-blue-500"></i>
                    <div>
                        <h3 class="font-medium text-gray-900">Access Anywhere</h3>
                        <p class="text-sm text-gray-600">Manage your finances on any device</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="bg-white p-8 rounded-lg shadow-lg fade-in-up" style="animation-delay: 0.2s">
            <h2 class="text-2xl font-bold mb-6 text-center">Create Your Account</h2>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 fade-in-up">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6" id="registrationForm">
                <div>
                    <label class="block text-gray-700 mb-2" for="username">Username</label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                           placeholder="Choose a username">
                </div>

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
                               placeholder="Create a strong password">
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength-meter mt-2">
                        <div id="strengthMeter"></div>
                    </div>
                    <p id="passwordHint" class="text-sm text-gray-500 mt-1">
                        Password must be at least 8 characters
                    </p>
                </div>

                <button type="submit" id="registerButton"
                        class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 transition-all transform hover:scale-105">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-blue-500 hover:text-blue-600">Sign in</a>
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

        // Password strength meter
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const meter = document.getElementById('strengthMeter');
            const hint = document.getElementById('passwordHint');
            
            let strength = 0;
            strength += password.length >= 8 ? 1 : 0;
            strength += /[A-Z]/.test(password) ? 1 : 0;
            strength += /[a-z]/.test(password) ? 1 : 0;
            strength += /[0-9]/.test(password) ? 1 : 0;
            strength += /[^A-Za-z0-9]/.test(password) ? 1 : 0;

            meter.className = '';
            if (strength >= 4) {
                meter.classList.add('strength-strong');
                hint.textContent = 'Strong password';
                hint.className = 'text-sm text-green-600 mt-1';
            } else if (strength >= 3) {
                meter.classList.add('strength-good');
                hint.textContent = 'Good password';
                hint.className = 'text-sm text-green-500 mt-1';
            } else if (strength >= 2) {
                meter.classList.add('strength-fair');
                hint.textContent = 'Fair password - add more variety';
                hint.className = 'text-sm text-yellow-500 mt-1';
            } else {
                meter.classList.add('strength-weak');
                hint.textContent = 'Weak password - try longer with mixed characters';
                hint.className = 'text-sm text-red-500 mt-1';
            }
        });

        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const button = document.getElementById('registerButton');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Account...';
            button.classList.add('bg-blue-400');
        });
    </script>
</body>
</html> 
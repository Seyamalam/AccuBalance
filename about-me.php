<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Developer - AccuBalance</title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .social-icon {
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            transform: translateY(-5px);
        }

        .profile-image {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/public-nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-20">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <!-- Profile Image -->
            <div class="text-center" data-aos="fade-right">
                <img src="assets/seyam.png" 
                     alt="Touhidul Alam Seyam" 
                     class="w-64 h-64 rounded-full mx-auto shadow-xl profile-image object-cover">
            </div>

            <!-- Developer Info -->
            <div data-aos="fade-left">
                <h1 class="text-4xl font-bold mb-4">Touhidul Alam Seyam</h1>
                <p class="text-xl text-gray-600 mb-6">Software Engineer at Hello World Communications Limited</p>
                
                <div class="space-y-4 text-gray-600">
                    <p class="flex items-center">
                        <i class="fas fa-graduation-cap w-8"></i>
                        4th Semester B.Sc Hons at BGC Trust University Bangladesh
                    </p>
                    
                    <div class="space-y-2">
                        <p class="flex items-center">
                            <i class="fas fa-envelope w-8"></i>
                            <a href="mailto:seyamalam41@gmail.com" class="hover:text-blue-600">seyamalam41@gmail.com</a>
                        </p>
                        <p class="flex items-center">
                            <i class="fas fa-envelope w-8"></i>
                            <a href="mailto:touhidulalam@bgctub.c.bd" class="hover:text-blue-600">touhidulalam@bgctub.c.bd</a>
                        </p>
                    </div>

                    <p class="flex items-center">
                        <i class="fas fa-phone w-8"></i>
                        <a href="tel:+8801311048041" class="hover:text-blue-600">+880 1311-04804</a>
                    </p>
                </div>

                <!-- Social Links -->
                <div class="flex space-x-6 mt-8">
                    <a href="https://github.com/Seyamalam" 
                       target="_blank"
                       class="social-icon text-gray-600 hover:text-gray-900">
                        <i class="fab fa-github text-3xl"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/touhidulalamseyam" 
                       target="_blank"
                       class="social-icon text-gray-600 hover:text-blue-600">
                        <i class="fab fa-linkedin text-3xl"></i>
                    </a>
                    <a href="https://web.facebook.com/touhidul.alam.5851/" 
                       target="_blank"
                       class="social-icon text-gray-600 hover:text-blue-600">
                        <i class="fab fa-facebook text-3xl"></i>
                    </a>
                    <a href="https://wa.me/8801311048041" 
                       target="_blank"
                       class="social-icon text-gray-600 hover:text-green-600">
                        <i class="fab fa-whatsapp text-3xl"></i>
                    </a>
                    <a href="https://t.me/8801311048041" 
                       target="_blank"
                       class="social-icon text-gray-600 hover:text-blue-500">
                        <i class="fab fa-telegram text-3xl"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Skills & Technologies -->
        <div class="mt-20" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-center mb-12">Skills & Technologies</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <i class="fas fa-code text-4xl text-blue-500 mb-4"></i>
                    <h3 class="font-bold mb-2">Web Development</h3>
                    <p class="text-gray-600">PHP, JavaScript, HTML/CSS</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-database text-4xl text-green-500 mb-4"></i>
                    <h3 class="font-bold mb-2">Database</h3>
                    <p class="text-gray-600">MySQL, MongoDB</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-mobile-alt text-4xl text-purple-500 mb-4"></i>
                    <h3 class="font-bold mb-2">Mobile Development</h3>
                    <p class="text-gray-600">React Native, Flutter</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-server text-4xl text-red-500 mb-4"></i>
                    <h3 class="font-bold mb-2">Backend</h3>
                    <p class="text-gray-600">Node.js, Express, Laravel</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html> 
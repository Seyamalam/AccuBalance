<nav class="fixed w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <img src="assets/logo.png" alt="AccuBalance Logo" class="h-10 w-10">
                    <span class="text-xl font-bold text-gray-900">AccuBalance</span>
                </a>
            </div>

            <!-- Main Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <div class="relative group">
                    <button class="text-gray-600 hover:text-blue-600">
                        Features <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block">
                        <a href="/features/budgeting" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Smart Budgeting</a>
                        <a href="/features/investments" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Investment Tracking</a>
                        <a href="/features/ai-insights" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">AI Insights</a>
                        <a href="/features/automation" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Automation</a>
                    </div>
                </div>
                
                <a href="/pricing" class="text-gray-600 hover:text-blue-600">Pricing</a>
                
                <div class="relative group">
                    <button class="text-gray-600 hover:text-blue-600">
                        Resources <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden group-hover:block">
                        <a href="/blog" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Blog</a>
                        <a href="/guides" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">User Guides</a>
                        <a href="/docs" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Documentation</a>
                        <a href="/api" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">API Reference</a>
                    </div>
                </div>
                
                <a href="/about" class="text-gray-600 hover:text-blue-600">About</a>
                
                <div class="flex items-center space-x-4">
                    <a href="/login" class="text-gray-600 hover:text-blue-600">Login</a>
                    <a href="/register" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button class="text-gray-600 hover:text-blue-600" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
        <div class="px-4 py-2">
            <a href="/features" class="block py-2 text-gray-600">Features</a>
            <a href="/pricing" class="block py-2 text-gray-600">Pricing</a>
            <a href="/resources" class="block py-2 text-gray-600">Resources</a>
            <a href="/about" class="block py-2 text-gray-600">About</a>
            <div class="border-t my-2"></div>
            <a href="/login" class="block py-2 text-gray-600">Login</a>
            <a href="/register" class="block py-2 text-blue-600">Get Started</a>
        </div>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }
</script> 
<footer class="bg-gray-900 text-gray-300 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
            <!-- Company Info -->
            <div class="col-span-2">
                <div class="flex items-center mb-4">
                    <img src="assets/logo.png" alt="AccuBalance Logo" class="h-10 w-10">
                    <span class="ml-2 text-xl font-bold text-white">AccuBalance</span>
                </div>
                <p class="text-gray-400 mb-4">
                    Simplify Finances, Amplify Success. AccuBalance is your all-in-one financial management platform with AI-powered insights and intelligent automation.
                </p>
                <div class="mb-4">
                    <p class="text-gray-400">Developed by: <a href="about-me.php" class="text-blue-400 hover:text-blue-300">Touhidul Alam Seyam</a></p>
                    <p class="text-gray-400">Software Engineer at Hello World Communications Limited</p>
                    <p class="text-gray-400">BGC Trust University Bangladesh</p>
                </div>
                <div class="flex space-x-4">
                    <a href="https://github.com/Seyamalam" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/touhidulalamseyam" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    <a href="https://web.facebook.com/touhidul.alam.5851/" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="mailto:seyamalam41@gmail.com" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-envelope text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Product -->
            <div>
                <h4 class="text-white font-bold mb-4">Product</h4>
                <ul class="space-y-2">
                    <li><a href="/features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                    <li><a href="/pricing" class="text-gray-400 hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="/integrations" class="text-gray-400 hover:text-white transition-colors">Integrations</a></li>
                    <li><a href="/docs" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                    <li><a href="/api" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                    <li><a href="/mobile" class="text-gray-400 hover:text-white transition-colors">Mobile App</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div>
                <h4 class="text-white font-bold mb-4">Resources</h4>
                <ul class="space-y-2">
                    <li><a href="/blog" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                    <li><a href="/guides" class="text-gray-400 hover:text-white transition-colors">User Guides</a></li>
                    <li><a href="/webinars" class="text-gray-400 hover:text-white transition-colors">Webinars</a></li>
                    <li><a href="/community" class="text-gray-400 hover:text-white transition-colors">Community</a></li>
                    <li><a href="/support" class="text-gray-400 hover:text-white transition-colors">Support Center</a></li>
                    <li><a href="/financial-tips" class="text-gray-400 hover:text-white transition-colors">Financial Tips</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="text-white font-bold mb-4">Company</h4>
                <ul class="space-y-2">
                    <li><a href="/about" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                    <li><a href="/careers" class="text-gray-400 hover:text-white transition-colors">Careers</a></li>
                    <li><a href="/press" class="text-gray-400 hover:text-white transition-colors">Press</a></li>
                    <li><a href="/security" class="text-gray-400 hover:text-white transition-colors">Security</a></li>
                    <li><a href="/terms" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="/privacy" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="border-t border-gray-800 mt-12 pt-8">
            <div class="max-w-md mx-auto text-center">
                <h5 class="text-white font-bold mb-2">Subscribe to our newsletter</h5>
                <p class="text-gray-400 mb-4">Get the latest updates, tips and product news</p>
                <form class="flex space-x-2">
                    <input type="email" placeholder="Enter your email" 
                           class="flex-1 px-4 py-2 rounded-lg bg-gray-800 text-white border border-gray-700 focus:outline-none focus:border-blue-500">
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 mt-12 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    © <?php echo date('Y'); ?> AccuBalance. All rights reserved.
                </div>
                <div class="flex space-x-6">
                    <a href="/terms" class="text-gray-400 hover:text-white text-sm transition-colors">Terms</a>
                    <a href="/privacy" class="text-gray-400 hover:text-white text-sm transition-colors">Privacy</a>
                    <a href="/cookies" class="text-gray-400 hover:text-white text-sm transition-colors">Cookies</a>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-400 text-sm">Language:</span>
                        <select class="bg-gray-800 text-gray-400 text-sm rounded border border-gray-700 focus:outline-none focus:border-blue-500">
                            <option value="en">English</option>
                            <option value="es">Español</option>
                            <option value="fr">Français</option>
                            <option value="de">Deutsch</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer> 
<div class="widget-container">
    <!-- Loading State -->
    <div class="loading-state hidden">
        <?php include 'includes/loading-skeleton.php'; ?>
    </div>

    <!-- Content State -->
    <div class="content-state">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium"><?php echo $widget_title; ?></h3>
            <div class="flex space-x-2">
                <button class="refresh-btn p-2 text-gray-500 hover:text-blue-500 transition-colors">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="settings-btn p-2 text-gray-500 hover:text-blue-500 transition-colors">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>
        
        <div class="widget-content">
            <?php echo $widget_content; ?>
        </div>
    </div>
</div>

<script>
    document.querySelector('.refresh-btn').addEventListener('click', function() {
        const container = this.closest('.widget-container');
        const loadingState = container.querySelector('.loading-state');
        const contentState = container.querySelector('.content-state');
        
        // Show loading state
        loadingState.classList.remove('hidden');
        contentState.classList.add('hidden');
        
        // Simulate data refresh
        setTimeout(() => {
            loadingState.classList.add('hidden');
            contentState.classList.remove('hidden');
            // Add fade-in animation
            contentState.classList.add('fade-in');
        }, 1000);
    });
</script> 
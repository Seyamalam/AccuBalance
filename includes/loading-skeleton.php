<div class="animate-pulse">
    <?php if ($type === 'card'): ?>
        <div class="bg-gray-200 h-32 rounded-lg"></div>
    <?php elseif ($type === 'chart'): ?>
        <div class="bg-gray-200 h-64 rounded-lg"></div>
    <?php elseif ($type === 'table-row'): ?>
        <div class="flex space-x-4">
            <div class="bg-gray-200 h-4 w-1/4 rounded"></div>
            <div class="bg-gray-200 h-4 w-1/4 rounded"></div>
            <div class="bg-gray-200 h-4 w-1/4 rounded"></div>
            <div class="bg-gray-200 h-4 w-1/4 rounded"></div>
        </div>
    <?php endif; ?>
</div> 
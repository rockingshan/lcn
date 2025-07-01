<?php 
$title = 'Activity Logs';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-5xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Activity Logs</h2>
    <div class="mb-4 flex justify-end">
        <a href="<?php echo BASE_PATH; ?>/export-logs" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center">
            <i class="fas fa-file-excel mr-2"></i>Download Excel
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-3 text-left">User</th>
                    <th class="py-2 px-3 text-left">Action</th>
                    <th class="py-2 px-3 text-left">Details</th>
                    <th class="py-2 px-3 text-left">IP</th>
                    <th class="py-2 px-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($logs as $log): ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-2 px-3"><?php echo htmlspecialchars($log['username']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($log['action']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($log['details']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($log['ip_address']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($log['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="flex justify-center mt-6 space-x-1 flex-wrap">
        <?php
        $range = 2; // How many pages to show on each side of current
        $showFirstLast = true;
        $showPrevNext = true;
        $ellipsis = false;

        if ($totalPages > 1) {
            if ($showFirstLast && $page > 1) {
                echo '<a href="' . BASE_PATH . '/logs?page=1" class="px-3 py-1 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">First</a>';
            }
            if ($showPrevNext && $page > 1) {
                echo '<a href="' . BASE_PATH . '/logs?page=' . ($page - 1) . '" class="px-3 py-1 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">Prev</a>';
            }
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)) {
                    if ($ellipsis) {
                        echo '<span class="px-3 py-1">...</span>';
                        $ellipsis = false;
                    }
                    echo '<a href="' . BASE_PATH . '/logs?page=' . $i . '" class="px-3 py-1 rounded ' . ($i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300') . '">' . $i . '</a>';
                } elseif (!$ellipsis) {
                    $ellipsis = true;
                }
            }
            if ($showPrevNext && $page < $totalPages) {
                echo '<a href="' . BASE_PATH . '/logs?page=' . ($page + 1) . '" class="px-3 py-1 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">Next</a>';
            }
            if ($showFirstLast && $page < $totalPages) {
                echo '<a href="' . BASE_PATH . '/logs?page=' . $totalPages . '" class="px-3 py-1 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">Last</a>';
            }
        }
        ?>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
<?php 
$title = 'Activity Logs';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-5xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Activity Logs</h2>
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
    <div class="flex justify-center mt-6 space-x-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?php echo BASE_PATH; ?>/logs?page=<?php echo $i; ?>" class="px-3 py-1 rounded <?php echo $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
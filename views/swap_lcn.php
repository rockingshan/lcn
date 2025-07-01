<?php 
$title = 'Swap LCN';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Swap LCN</h2>
    <form method="POST" action="<?php echo BASE_PATH; ?>/swap-lcn/<?php echo htmlspecialchars($mapping['cmap_id']); ?>">
        <div class="mb-4">
            <label class="block mb-1 font-semibold">SID</label>
            <input type="text" value="<?php echo htmlspecialchars($mapping['sid']); ?>" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Current LCN</label>
            <input type="text" value="<?php echo htmlspecialchars($mapping['lcn']); ?>" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Genre</label>
            <input type="text" value="<?php echo htmlspecialchars($mapping['genre']); ?>" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Channel Name</label>
            <input type="text" value="<?php echo htmlspecialchars($mapping['channelName']); ?>" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="target_cmap_id">Swap With</label>
            <select id="target_cmap_id" name="target_cmap_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select Channel to Swap With</option>
                <?php foreach ($others as $o): ?>
                    <option value="<?php echo $o['cmap_id']; ?>"><?php echo htmlspecialchars($o['lcn']) . ' | ' . htmlspecialchars($o['genre']) . ' | ' . htmlspecialchars($o['channelName']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="<?php echo BASE_PATH; ?>/" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Swap LCN</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
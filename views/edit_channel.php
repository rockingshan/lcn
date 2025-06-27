<?php 
$title = 'Edit Channel';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Edit Channel</h2>
    <form method="POST" action="<?php echo BASE_PATH; ?>/edit-channel/<?php echo htmlspecialchars($channel['channel_id']); ?>">
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="channelName">Channel Name</label>
            <input type="text" id="channelName" name="channelName" value="<?php echo htmlspecialchars($channel['channelName']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="broadcaster_id">Broadcaster</label>
            <select id="broadcaster_id" name="broadcaster_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select Broadcaster</option>
                <?php foreach ($broadcasters as $b): ?>
                    <option value="<?php echo $b['broadcaster_id']; ?>" <?php if ($b['broadcaster_id'] == $channel['broadcaster_id']) echo 'selected'; ?>><?php echo htmlspecialchars($b['broadcaster']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="price">Price (₹)</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($channel['price']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="<?php echo BASE_PATH; ?>/" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Channel</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
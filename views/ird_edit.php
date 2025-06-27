<?php 
$title = 'Edit IRD';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Edit IRD</h2>
    <form method="POST" action="<?php echo BASE_PATH; ?>/ird-inventory/edit/<?php echo htmlspecialchars($ird['ird_id']); ?>">
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="channel_id">Channel Name</label>
            <select id="channel_id" name="channel_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select Channel</option>
                <?php foreach ($channels as $c): ?>
                    <option value="<?php echo $c['channel_id']; ?>" <?php if ($c['channel_id'] == $ird['channel_id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['channelName']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="broadcaster_id">Broadcaster</label>
            <select id="broadcaster_id" name="broadcaster_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select Broadcaster</option>
                <?php foreach ($broadcasters as $b): ?>
                    <option value="<?php echo $b['broadcaster_id']; ?>" <?php if ($b['broadcaster_id'] == $ird['broadcaster_id']) echo 'selected'; ?>><?php echo htmlspecialchars($b['broadcaster']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="stbNum">STB Number</label>
            <input type="text" id="stbNum" name="stbNum" value="<?php echo htmlspecialchars($ird['stbNum']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="vcNum">VC Number</label>
            <input type="text" id="vcNum" name="vcNum" value="<?php echo htmlspecialchars($ird['vcNum']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="<?php echo BASE_PATH; ?>/ird-inventory" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update IRD</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
<?php 
$title = 'Add Channel Mapping';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Add Channel Mapping</h2>
    <form method="POST" action="<?php echo BASE_PATH; ?>/add-channel-mapping" autocomplete="off">
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="sid_id">SID</label>
            <select id="sid_id" name="sid_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select SID</option>
                <?php foreach ($sids as $sid): ?>
                    <option value="<?php echo htmlspecialchars($sid['sid_id']); ?>"><?php echo htmlspecialchars($sid['sid']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="lcn_id">LCN</label>
            <select id="lcn_id" name="lcn_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select LCN</option>
                <?php foreach ($lcns as $lcn): ?>
                    <option value="<?php echo htmlspecialchars($lcn['lcn_id']); ?>">
                        <?php echo htmlspecialchars($lcn['lcn']) . ' (' . htmlspecialchars($lcn['genre']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="channel_id">Channel Name (optional)</label>
            <select id="channel_id" name="channel_id" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">-- None --</option>
                <?php foreach ($channels as $ch): ?>
                    <option value="<?php echo htmlspecialchars($ch['channel_id']); ?>"><?php echo htmlspecialchars($ch['channelName']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="<?php echo BASE_PATH; ?>/" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Mapping</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
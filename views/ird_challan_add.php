<?php 
/*
 * views/ird_challan_add.php
 *
 * Form to add a new IRD Challan record.
 * Fields: Broadcaster (dropdown), Date, Details (textarea), PDF file upload (max 5MB, PDF only)
 * Uses Tailwind CSS for styling.
 */
$title = 'Add IRD Challan';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Add IRD Challan</h2>
    <form method="POST" action="<?php echo BASE_PATH; ?>/ird-challan/add" enctype="multipart/form-data" autocomplete="off">
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="broadcaster_id">Broadcaster</label>
            <select id="broadcaster_id" name="broadcaster_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select Broadcaster</option>
                <?php foreach ($broadcasters as $b): ?>
                    <option value="<?php echo $b['broadcaster_id']; ?>"><?php echo htmlspecialchars($b['broadcaster']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="challan_date">Challan Date</label>
            <input type="date" id="challan_date" name="challan_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="details">Details</label>
            <textarea id="details" name="details" rows="4" class="w-full border border-gray-300 rounded px-3 py-2" required></textarea>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="challan_file">Upload Challan (PDF only, max 5MB)</label>
            <input type="file" id="challan_file" name="challan_file" accept="application/pdf" class="w-full border border-gray-300 rounded px-3 py-2" required>
            <div class="text-xs text-gray-500 mt-1">Only PDF files allowed. Max file size: 5MB.</div>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="<?php echo BASE_PATH; ?>/ird-challan" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Challan</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
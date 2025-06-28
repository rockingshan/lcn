<?php 
/*
 * views/ird_challan_list.php
 *
 * Lists all IRD Challan records with broadcaster, date, details, and PDF file link.
 * Receives $challans (array of challan records with broadcaster name)
 * Button to add a new challan.
 * Uses Tailwind CSS for styling.
 */
$title = 'IRD Challan Details';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-5xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">IRD Challan Details</h2>
    <div class="mb-4 flex justify-between items-center">
        <a href="<?php echo BASE_PATH; ?>/ird-challan/add" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add New Challan</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-3 text-left">Broadcaster</th>
                    <th class="py-2 px-3 text-left">Challan Date</th>
                    <th class="py-2 px-3 text-left">Details</th>
                    <th class="py-2 px-3 text-left">File</th>
                    <th class="py-2 px-3 text-left">Added</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($challans as $row): ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-2 px-3"><?php echo htmlspecialchars($row['broadcaster']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($row['challan_date']); ?></td>
                    <td class="py-2 px-3"><?php echo nl2br(htmlspecialchars($row['details'])); ?></td>
                    <td class="py-2 px-3">
                        <a href="<?php echo BASE_PATH . '/' . htmlspecialchars($row['file_path']); ?>" target="_blank" class="text-blue-600 hover:underline">View PDF</a>
                    </td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
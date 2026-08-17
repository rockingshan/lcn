<?php 
/*
 * views/dashboard.php
 *
 * Dashboard view for LCN Management System.
 * - Receives $channels (array of channel mappings for the current city)
 * - Renders a searchable, sortable table of all channel mappings
 * - Action buttons for Edit Channel, Modify LCN, Swap LCN, Update/Add IRD
 * - Uses Tailwind CSS for styling
 */
$title = 'Dashboard - LCN Management';
require_once __DIR__ . '/partials/header.php'; 
?>

<div class="bg-white p-6 rounded-lg shadow-lg">
    <?php if (!empty($_SESSION['changed_frequencies'])): ?>
        <div class="mb-4 border-l-4 border-amber-500 bg-amber-50 p-4 text-amber-900">
            <strong>LCN strings pending:</strong> <?php echo count($_SESSION['changed_frequencies']); ?> frequency update<?php echo count($_SESSION['changed_frequencies']) === 1 ? '' : 's'; ?> were changed in this session.
            <?php if (\App\Access::can('generator')): ?><a class="underline font-semibold" href="<?php echo BASE_PATH; ?>/lcn-strings">Generate and copy the strings.</a><?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="mb-4 flex justify-between items-center">
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for channels..." class="p-2 border border-gray-300 rounded-md w-full md:w-1/3">
        <a href="<?php echo BASE_PATH; ?>/export-lcn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center">
            <i class="fas fa-file-excel mr-2"></i>Download Excel
        </a>
    </div>

    <div class="overflow-x-auto">
        <table id="channelTable" class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm cursor-pointer" onclick="sortTable(0)">Genre</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm cursor-pointer" onclick="sortTable(1)">LCN</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm cursor-pointer" onclick="sortTable(2)">SID</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm cursor-pointer" onclick="sortTable(3)">Freq</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Channel Name</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm cursor-pointer" onclick="sortTable(5)">Broadcaster</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">A La Carte Price (₹)</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($channels as $row): ?>
                  <tr class="hover:bg-gray-100">
                    <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['genre']); ?></td>
                    <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['lcn']); ?></td>
                    <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['sid']); ?></td>
                    <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['freq']); ?></td>
                    <td class="text-left py-3 px-4 font-semibold"><?php echo htmlspecialchars($row['channelName']); ?></td>
                    <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['broadcaster']); ?></td>
                    <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['price']); ?></td>
                    <td class="text-left py-3 px-4 space-x-2">
                        <?php if (\App\Access::can('edit_channel')): ?><a href="<?php echo BASE_PATH; ?>/edit-channel/<?php echo urlencode($row['channel_id']); ?>" class="inline-block px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs font-semibold">Edit Channel</a><?php endif; ?>
                        <?php if (\App\Access::can('modify_lcn')): ?><a href="<?php echo BASE_PATH; ?>/modify-lcn/<?php echo urlencode($row['cmap_id']); ?>" class="inline-block px-3 py-1 bg-yellow-400 text-gray-900 rounded hover:bg-yellow-500 text-xs font-semibold">Modify LCN</a><?php endif; ?>
                        <?php if (\App\Access::can('swap_lcn')): ?><a href="<?php echo BASE_PATH; ?>/swap-lcn/<?php echo urlencode($row['cmap_id']); ?>" class="inline-block px-3 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 text-xs font-semibold">Swap LCN</a><?php endif; ?>
                        <?php if (\App\Access::can('ird') && !empty($row['ird_id'])): ?>
                        <a href="<?php echo BASE_PATH; ?>/ird-inventory/edit/<?php echo urlencode($row['ird_id']); ?>" class="inline-block px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs font-semibold">Update IRD</a>
                        <?php elseif (\App\Access::can('ird')): ?>
                        <a href="<?php echo BASE_PATH; ?>/ird-inventory/add?channel_id=<?php echo urlencode($row['channel_id']); ?>" class="inline-block px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs font-semibold">Add IRD</a>
                        <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

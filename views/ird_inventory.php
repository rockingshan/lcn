<?php 
/*
 * views/ird_inventory.php
 *
 * Lists all IRD inventory records for the current city.
 * Receives $inventory (array of IRD records with channel, broadcaster, STB/VC numbers, last updated)
 * Searchable and sortable table. Action buttons for Edit/Delete.
 * Uses Tailwind CSS for styling.
 */
$title = 'Broadcaster IRD Inventory';
require_once __DIR__ . '/partials/header.php'; 
?>
<div class="max-w-5xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Broadcaster IRD Inventory</h2>
    <div class="mb-4 flex justify-between items-center">
        <input type="text" id="searchInput" onkeyup="searchIrdTable()" placeholder="Search inventory..." class="p-2 border border-gray-300 rounded-md w-full md:w-1/3">
        <div class="flex space-x-2">
            <a href="<?php echo BASE_PATH; ?>/export-ird-inventory" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center">
                <i class="fas fa-file-excel mr-2"></i>Download Excel
            </a>
            <a href="<?php echo BASE_PATH; ?>/ird-inventory/add" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add New IRD</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="irdTable" class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-3 text-left cursor-pointer" onclick="sortIrdTable(0)">Channel Name</th>
                    <th class="py-2 px-3 text-left cursor-pointer" onclick="sortIrdTable(1)">Broadcaster</th>
                    <th class="py-2 px-3 text-left cursor-pointer" onclick="sortIrdTable(2)">STB Number</th>
                    <th class="py-2 px-3 text-left cursor-pointer" onclick="sortIrdTable(3)">VC Number</th>
                    <th class="py-2 px-3 text-left cursor-pointer" onclick="sortIrdTable(4)">Last Updated</th>
                    <th class="py-2 px-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($inventory as $row): ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-2 px-3"><?php echo htmlspecialchars($row['channelName']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($row['broadcaster']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($row['stbNum']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($row['vcNum']); ?></td>
                    <td class="py-2 px-3"><?php echo htmlspecialchars($row['updated_at'] ?? 'N/A'); ?></td>
                    <td class="py-2 px-3 space-x-2">
                        <a href="<?php echo BASE_PATH; ?>/ird-inventory/edit/<?php echo urlencode($row['ird_id']); ?>" class="inline-block px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs font-semibold">Edit</a>
                        <form action="<?php echo BASE_PATH; ?>/ird-inventory/delete/<?php echo urlencode($row['ird_id']); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this IRD entry?');">
                            <button type="submit" class="inline-block px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
// Search
function searchIrdTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toUpperCase();
    const table = document.getElementById("irdTable");
    const tr = table.getElementsByTagName("tr");
    for (let i = 1; i < tr.length; i++) {
        let rowVisible = false;
        let tds = tr[i].getElementsByTagName("td");
        for (let j = 0; j < tds.length; j++) {
            if (tds[j] && tds[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                rowVisible = true;
                break;
            }
        }
        tr[i].style.display = rowVisible ? "" : "none";
    }
}
// Sort
const irdSortDirections = {};
function sortIrdTable(columnIndex) {
    const table = document.getElementById("irdTable");
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.getElementsByTagName("tr"));
    const dir = irdSortDirections[columnIndex] === 'asc' ? 'desc' : 'asc';
    irdSortDirections[columnIndex] = dir;
    rows.sort((a, b) => {
        const aText = a.cells[columnIndex].textContent.trim();
        const bText = b.cells[columnIndex].textContent.trim();
        const aVal = isNaN(Date.parse(aText)) ? (isNaN(parseFloat(aText)) ? aText : parseFloat(aText)) : Date.parse(aText);
        const bVal = isNaN(Date.parse(bText)) ? (isNaN(parseFloat(bText)) ? bText : parseFloat(bText)) : Date.parse(bText);
        if (aVal < bVal) return dir === 'asc' ? -1 : 1;
        if (aVal > bVal) return dir === 'asc' ? 1 : -1;
        return 0;
    });
    rows.forEach(row => tbody.appendChild(row));
}
</script>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
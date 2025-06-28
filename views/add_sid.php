<?php 
/*
 * views/add_sid.php
 *
 * Form to add a new SID for the current city.
 * Fields: SID (with AJAX uniqueness check), TS, Frequency, SID Hex (auto-calculated)
 * City is auto-selected from session.
 * Uses Tailwind CSS for styling.
 */
$title = 'Add SID';
require_once __DIR__ . '/partials/header.php'; 
$city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
$cityNames = [1=>'Kolkata',2=>'Berhampore',3=>'Bankura',4=>'SITI Headend'];
$city_name = $cityNames[$city_id] ?? 'Unknown';
?>
<div class="max-w-xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Add New SID</h2>
    <form method="POST" action="<?php echo BASE_PATH; ?>/add-sid" autocomplete="off" id="sidForm">
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="sid">SID</label>
            <input type="number" id="sid" name="sid" class="w-full border border-gray-300 rounded px-3 py-2" required oninput="updateSidHex(); checkSidUnique();">
            <div id="sid-duplicate-warning" class="text-red-600 text-sm mt-1 hidden">This SID already exists for <?php echo htmlspecialchars($city_name); ?>.</div>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="ts">TS</label>
            <input type="text" id="ts" name="ts" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="freq">Frequency</label>
            <input type="number" id="freq" name="freq" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="sidhex">SID Hex (auto-calculated)</label>
            <input type="text" id="sidhex" name="sidhex" maxlength="5" placeholder="e.g. 1A 2B" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly required>
        </div>
        <div class="mb-4 text-gray-600 text-sm">
            City will be set as current city: <strong><?php echo $city_id; ?> (<?php echo htmlspecialchars($city_name); ?>)</strong>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="<?php echo BASE_PATH; ?>/" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" id="sid-submit-btn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add SID</button>
        </div>
    </form>
</div>
<script>
function updateSidHex() {
    const sidInput = document.getElementById('sid');
    const hexInput = document.getElementById('sidhex');
    let sid = parseInt(sidInput.value, 10);
    if (isNaN(sid)) {
        hexInput.value = '';
        return;
    }
    let hex = sid.toString(16).toUpperCase().padStart(4, '0');
    hexInput.value = hex.slice(0,2) + ' ' + hex.slice(2,4);
}
function checkSidUnique() {
    const sidInput = document.getElementById('sid');
    const warning = document.getElementById('sid-duplicate-warning');
    const submitBtn = document.getElementById('sid-submit-btn');
    let sid = sidInput.value;
    if (!sid) {
        warning.classList.add('hidden');
        submitBtn.disabled = false;
        return;
    }
    fetch('<?php echo BASE_PATH; ?>/ajax/check-sid', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'sid=' + encodeURIComponent(sid)
    })
    .then(res => res.json())
    .then(data => {
        if (data.exists) {
            warning.classList.remove('hidden');
            submitBtn.disabled = true;
        } else {
            warning.classList.add('hidden');
            submitBtn.disabled = false;
        }
    });
}
</script>
<?php require_once __DIR__ . '/partials/footer.php'; ?> 
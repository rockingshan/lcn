<?php $title = 'LCN Strings'; require __DIR__ . '/partials/header.php'; ?>
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-bold mb-2">LCN Update Strings</h2>
  <p class="text-gray-600 mb-5">These frequencies changed during this login session.</p>
  <?php if (!$generated): ?><p>No LCN changes have been made in this session.</p><?php endif; ?>
  <?php foreach ($generated as $item): ?>
    <section class="mb-5"><h3 class="font-bold mb-2">Frequency <?php echo (int)$item['frequency']; ?></h3>
      <div class="flex gap-2"><textarea id="freq-<?php echo (int)$item['frequency']; ?>" readonly class="w-full border rounded p-3 font-mono text-sm" rows="3"><?php echo htmlspecialchars($item['string']); ?></textarea>
      <button onclick="navigator.clipboard.writeText(document.getElementById('freq-<?php echo (int)$item['frequency']; ?>').value)" class="px-4 bg-blue-600 text-white rounded">Copy</button></div>
    </section>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

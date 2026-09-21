<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Papan Antrian';
require_once __DIR__ . '/includes/header.php';
?>

<div class="monitor-wrap" id="monitorContent">
  <h2 style="text-align:center;">PAPAN ANTRIAN DIGITAL</h2>
  <div class="monitor-current">
    <div class="label" style="letter-spacing:2px; opacity:.8;">SEDANG DILAYANI</div>
    <div class="no" id="monitor-current-no">---</div>
  </div>
  <h3>Antrian Berikutnya</h3>
  <div class="monitor-list" id="monitor-list"></div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

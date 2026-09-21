<?php $cur = basename($_SERVER['SCRIPT_NAME']); ?>
<div class="admin-sidebar">
  <div class="brand"><i class="bi bi-shield-lock"></i> Admin ULT</div>
  <a href="dashboard.php" class="<?= $cur === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="riwayat.php" class="<?= $cur === 'riwayat.php' ? 'active' : '' ?>"><i class="bi bi-clock-history"></i> Riwayat Antrian</a>
  <a href="kategori.php" class="<?= $cur === 'kategori.php' ? 'active' : '' ?>"><i class="bi bi-tags"></i> Kelola Layanan</a>
  <a href="evaluasi.php" class="<?= $cur === 'evaluasi.php' ? 'active' : '' ?>"><i class="bi bi-star"></i> Evaluasi</a>
  <?php if (($_SESSION['admin_role'] ?? '') === 'super_admin'): ?>
  <a href="akun.php" class="<?= $cur === 'akun.php' ? 'active' : '' ?>"><i class="bi bi-people"></i> Kelola Akun</a>
  <?php endif; ?>
  <a href="../papan-antrian.php" target="_blank"><i class="bi bi-display"></i> Papan Antrian</a>
  <a href="logout.php" style="margin-top:20px; background:rgba(220,38,38,0.25);"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

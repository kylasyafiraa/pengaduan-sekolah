<?php

require __DIR__ . '/../bootstrap.php';
require_role('admin');

$stmt = $pdo->query("SELECT id, nama, nis, kelas, email, created_at FROM users WHERE role = 'siswa' ORDER BY created_at DESC");
$rows = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Data Siswa Pengadu</h5>
  <a class="btn btn-sm btn-outline-dark" href="<?= e(url('/admin/dashboard.php')) ?>">Kembali</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if (!$rows): ?>
      <div class="text-muted">Belum ada siswa terdaftar.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Nama</th>
              <th>NIS</th>
              <th>Kelas</th>
              <th>Email</th>
              <th>Tanggal Register</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= e((string)$r['nama']) ?></td>
                <td><?= e((string)($r['nis'] ?? '-')) ?></td>
                <td><?= e((string)($r['kelas'] ?? '-')) ?></td>
                <td><?= e((string)$r['email']) ?></td>
                <td><?= e((string)$r['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>

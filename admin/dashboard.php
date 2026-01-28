<?php

require __DIR__ . '/../bootstrap.php';
require_role('admin');

$stmt = $pdo->query(
    "SELECT p.*, u.nama, u.nis, u.kelas, u.email
     FROM pengaduan p
     JOIN users u ON u.id = p.user_id
     ORDER BY p.created_at DESC"
);
$rows = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="mb-0">Admin Panel</h5>
    <div class="text-muted">Daftar pengaduan masuk</div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>Siswa</th>
            <th>Kelas</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <?php
              $status = (string)($r['status'] ?? 'baru');
              $badge = 'secondary';
              if ($status === 'baru') $badge = 'warning';
              if ($status === 'diproses') $badge = 'info';
              if ($status === 'selesai') $badge = 'success';
            ?>
            <tr>
              <td><?= e((string)$r['nama']) ?><div class="small text-muted"><?= e((string)$r['email']) ?></div></td>
              <td><?= e((string)($r['kelas'] ?? '-')) ?></td>
              <td><?= e((string)$r['judul']) ?></td>
              <td><?= e((string)$r['kategori']) ?></td>
              <td><span class="badge text-bg-<?= e($badge) ?>"><?= e($status) ?></span></td>
              <td><?= e((string)$r['created_at']) ?></td>
              <td>
                <a class="btn btn-sm btn-outline-dark" href="<?= e(url('/admin/pengaduan_detail.php?id=' . (int)$r['id'])) ?>">Buka</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>

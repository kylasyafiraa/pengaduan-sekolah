<?php

require __DIR__ . '/../bootstrap.php';
require_role('siswa');

$user = current_user();

$stmt = $pdo->prepare(
    "SELECT p.*, (
        SELECT b.isi_balasan
        FROM balasan b
        WHERE b.pengaduan_id = p.id
        ORDER BY b.created_at DESC
        LIMIT 1
    ) AS balasan_terakhir
    FROM pengaduan p
    WHERE p.user_id = :uid
    ORDER BY p.created_at DESC"
);
$stmt->execute([':uid' => $user['id']]);
$rows = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="mb-0">Dashboard Siswa</h5>
    <div class="text-muted">Halo, <?= e((string)($user['nama'] ?? '')) ?></div>
  </div>
  <a class="btn btn-primary" href="<?= e(url('/siswa/pengaduan_tambah.php')) ?>">Buat Pengaduan</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <h6 class="mb-3">Riwayat Pengaduan</h6>

    <?php if (!$rows): ?>
      <div class="text-muted">Belum ada pengaduan.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Judul</th>
              <th>Kategori</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Balasan Terakhir</th>
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
                <td><?= e((string)$r['judul']) ?></td>
                <td><?= e((string)$r['kategori']) ?></td>
                <td><span class="badge text-bg-<?= e($badge) ?>"><?= e($status) ?></span></td>
                <td><?= e((string)$r['created_at']) ?></td>
                <td><?= e((string)($r['balasan_terakhir'] ?? '-')) ?></td>
                <td>
                  <a class="btn btn-sm btn-outline-dark" href="<?= e(url('/siswa/pengaduan_detail.php?id=' . (int)$r['id'])) ?>">Detail</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>

<?php

require __DIR__ . '/../bootstrap.php';
require_role('siswa');

$user = current_user();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM pengaduan WHERE id = :id AND user_id = :uid LIMIT 1');
$stmt->execute([':id' => $id, ':uid' => $user['id']]);
$pengaduan = $stmt->fetch();

if (!$pengaduan) {
    flash_set('msg', 'Pengaduan tidak ditemukan.', 'warning');
    redirect('/siswa/dashboard.php');
}

$stmt = $pdo->prepare(
    "SELECT b.*, u.nama AS admin_nama
     FROM balasan b
     JOIN users u ON u.id = b.admin_id
     WHERE b.pengaduan_id = :pid
     ORDER BY b.created_at ASC"
);
$stmt->execute([':pid' => $id]);
$balasan = $stmt->fetchAll();

require __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Detail Pengaduan</h5>
  <a class="btn btn-sm btn-outline-dark" href="<?= e(url('/siswa/dashboard.php')) ?>">Kembali</a>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-8">
        <div class="mb-1"><strong>Judul:</strong> <?= e((string)$pengaduan['judul']) ?></div>
        <div class="mb-1"><strong>Kategori:</strong> <?= e((string)$pengaduan['kategori']) ?></div>
        <div class="mb-1"><strong>Status:</strong> <?= e((string)$pengaduan['status']) ?></div>
        <div class="mb-3"><strong>Tanggal:</strong> <?= e((string)$pengaduan['created_at']) ?></div>
      </div>
    </div>
    <div>
      <strong>Isi Pengaduan</strong>
      <div class="border rounded p-3 bg-light mt-2"><?= nl2br(e((string)$pengaduan['isi'])) ?></div>
    </div>

    <?php if (!empty($pengaduan['gambar'])): ?>
      <div class="mt-3">
        <strong>Gambar</strong>
        <div class="mt-2">
          <a href="<?= e(url((string)$pengaduan['gambar'])) ?>" target="_blank" rel="noopener">
            <img src="<?= e(url((string)$pengaduan['gambar'])) ?>" class="img-fluid rounded border" alt="gambar pengaduan">
          </a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <h6 class="mb-3">Balasan Admin</h6>

    <?php if (!$balasan): ?>
      <div class="text-muted">Belum ada balasan.</div>
    <?php else: ?>
      <?php foreach ($balasan as $b): ?>
        <div class="border rounded p-3 mb-2">
          <div class="small text-muted mb-2"><?= e((string)($b['created_at'] ?? '')) ?> - <?= e((string)($b['admin_nama'] ?? 'Admin')) ?></div>
          <div><?= nl2br(e((string)($b['isi_balasan'] ?? ''))) ?></div>

          <?php if (!empty($b['gambar'])): ?>
            <div class="mt-2">
              <a href="<?= e(url((string)$b['gambar'])) ?>" target="_blank" rel="noopener">
                <img src="<?= e(url((string)$b['gambar'])) ?>" class="img-fluid rounded border" alt="gambar balasan">
              </a>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>

<?php

require __DIR__ . '/../bootstrap.php';
require_role('admin');

$admin = current_user();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT p.*, u.nama, u.nis, u.kelas, u.email
     FROM pengaduan p
     JOIN users u ON u.id = p.user_id
     WHERE p.id = :id
     LIMIT 1"
);
$stmt->execute([':id' => $id]);
$pengaduan = $stmt->fetch();

if (!$pengaduan) {
    flash_set('msg', 'Pengaduan tidak ditemukan.', 'warning');
    redirect('/admin/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isi_balasan = trim((string)($_POST['isi_balasan'] ?? ''));
    $status = trim((string)($_POST['status'] ?? ''));
    $gambarPath = upload_image($_FILES['gambar'] ?? [], 'balasan', $errors);

    if ($isi_balasan === '') {
        $errors[] = 'Isi balasan wajib diisi.';
    }
    if (!in_array($status, ['baru', 'diproses', 'selesai'], true)) {
        $errors[] = 'Status tidak valid.';
    }

    if (!$errors) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO balasan (pengaduan_id, admin_id, isi_balasan, gambar) VALUES (:pid, :aid, :isi, :gambar)');
            $stmt->execute([
                ':pid' => $id,
                ':aid' => $admin['id'],
                ':isi' => $isi_balasan,
                ':gambar' => $gambarPath,
            ]);

            $stmt = $pdo->prepare('UPDATE pengaduan SET status = :status, updated_at = NOW() WHERE id = :id');
            $stmt->execute([':status' => $status, ':id' => $id]);

            $pdo->commit();
            flash_set('msg', 'Balasan berhasil dikirim.', 'success');
            redirect('/admin/pengaduan_detail.php?id=' . $id);
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Gagal menyimpan balasan.';
        }
    }
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
  <a class="btn btn-sm btn-outline-dark" href="<?= e(url('/admin/dashboard.php')) ?>">Kembali</a>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-8">
        <div class="mb-1"><strong>Siswa:</strong> <?= e((string)$pengaduan['nama']) ?> (<?= e((string)($pengaduan['kelas'] ?? '-')) ?>)</div>
        <div class="mb-1"><strong>Email:</strong> <?= e((string)$pengaduan['email']) ?></div>
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

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <h6 class="mb-3">Kirim Balasan</h6>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?>
          <div><?= e($err) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Status</label>
        <?php $st = (string)($_POST['status'] ?? $pengaduan['status']); ?>
        <select class="form-select" name="status" required>
          <option value="baru" <?= $st === 'baru' ? 'selected' : '' ?>>baru</option>
          <option value="diproses" <?= $st === 'diproses' ? 'selected' : '' ?>>diproses</option>
          <option value="selesai" <?= $st === 'selesai' ? 'selected' : '' ?>>selesai</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Isi Balasan</label>
        <textarea class="form-control" name="isi_balasan" rows="5" required><?= e((string)($_POST['isi_balasan'] ?? '')) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Upload Gambar (opsional)</label>
        <input class="form-control" type="file" name="gambar" accept="image/png,image/jpeg,image/webp">
        <div class="form-text">Max 2MB. Format: JPG/PNG/WEBP.</div>
      </div>
      <button class="btn btn-dark" type="submit">Kirim</button>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <h6 class="mb-3">Riwayat Balasan</h6>

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

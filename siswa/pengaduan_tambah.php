<?php

require __DIR__ . '/../bootstrap.php';
require_role('siswa');

$user = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim((string)($_POST['judul'] ?? ''));
    $kategori = trim((string)($_POST['kategori'] ?? ''));
    $isi = trim((string)($_POST['isi'] ?? ''));
    $gambarPath = upload_image($_FILES['gambar'] ?? [], 'pengaduan', $errors);

    if ($judul === '') {
        $errors[] = 'Judul wajib diisi.';
    }
    if ($kategori === '') {
        $errors[] = 'Kategori wajib dipilih.';
    }
    if ($isi === '') {
        $errors[] = 'Isi pengaduan wajib diisi.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO pengaduan (user_id, judul, kategori, isi, gambar, status) VALUES (:uid, :judul, :kategori, :isi, :gambar, :status)');
        $stmt->execute([
            ':uid' => $user['id'],
            ':judul' => $judul,
            ':kategori' => $kategori,
            ':isi' => $isi,
            ':gambar' => $gambarPath,
            ':status' => 'baru',
        ]);

        flash_set('msg', 'Pengaduan berhasil dikirim.', 'success');
        redirect('/siswa/dashboard.php');
    }
}

require __DIR__ . '/../partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Buat Pengaduan</h5>
          <a class="btn btn-sm btn-outline-dark" href="<?= e(url('/siswa/dashboard.php')) ?>">Kembali</a>
        </div>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $err): ?>
              <div><?= e($err) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Judul</label>
            <input class="form-control" name="judul" value="<?= e((string)($_POST['judul'] ?? '')) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select class="form-select" name="kategori" required>
              <?php $k = (string)($_POST['kategori'] ?? ''); ?>
              <option value="">- Pilih -</option>
              <option value="Fasilitas" <?= $k === 'Fasilitas' ? 'selected' : '' ?>>Fasilitas</option>
              <option value="Guru" <?= $k === 'Guru' ? 'selected' : '' ?>>Guru</option>
              <option value="Teman" <?= $k === 'Teman' ? 'selected' : '' ?>>Teman</option>
              <option value="Kebersihan" <?= $k === 'Kebersihan' ? 'selected' : '' ?>>Kebersihan</option>
              <option value="Lainnya" <?= $k === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Isi Pengaduan</label>
            <textarea class="form-control" name="isi" rows="6" required><?= e((string)($_POST['isi'] ?? '')) ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Upload Gambar (opsional)</label>
            <input class="form-control" type="file" name="gambar" accept="image/png,image/jpeg,image/webp">
            <div class="form-text">Max 2MB. Format: JPG/PNG/WEBP.</div>
          </div>
          <button class="btn btn-primary" type="submit">Kirim</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>

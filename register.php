<?php

require __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim((string)($_POST['nama'] ?? ''));
    $nis = trim((string)($_POST['nis'] ?? ''));
    $kelas = trim((string)($_POST['kelas'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $password2 = (string)($_POST['password2'] ?? '');

    if ($nama === '') {
        $errors[] = 'Nama wajib diisi.';
    }
    if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Email tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($password !== $password2) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (nama, nis, kelas, email, password_hash, role) VALUES (:nama, :nis, :kelas, :email, :hash, :role)');
        $stmt->execute([
            ':nama' => $nama,
            ':nis' => $nis === '' ? null : $nis,
            ':kelas' => $kelas === '' ? null : $kelas,
            ':email' => $email,
            ':hash' => $hash,
            ':role' => 'siswa',
        ]);

        flash_set('msg', 'Register berhasil. Silakan login.', 'success');
        redirect('/login.php');
    }
}

require __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Register Siswa</h5>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $err): ?>
              <div><?= e($err) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input class="form-control" name="nama" value="<?= e((string)($_POST['nama'] ?? '')) ?>" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">NIS</label>
              <input class="form-control" name="nis" value="<?= e((string)($_POST['nis'] ?? '')) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Kelas</label>
              <input class="form-control" name="kelas" value="<?= e((string)($_POST['kelas'] ?? '')) ?>" placeholder="contoh: XI RPL 1">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" value="<?= e((string)($_POST['email'] ?? '')) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input class="form-control" type="password" name="password2" required>
          </div>
          <button class="btn btn-primary" type="submit">Register</button>
          <a class="btn btn-link" href="<?= e(url('/login.php')) ?>">Sudah punya akun?</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

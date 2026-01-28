<?php

require __DIR__ . '/bootstrap.php';

$errors = [];
$created = false;

$stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'admin'");
$hasAdmin = ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim((string)($_POST['nama'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($nama === '') {
        $errors[] = 'Nama wajib diisi.';
    }
    if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Email tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
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
        $stmt = $pdo->prepare('INSERT INTO users (nama, email, password_hash, role) VALUES (:nama, :email, :hash, :role)');
        $stmt->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':hash' => $hash,
            ':role' => 'admin',
        ]);
        $created = true;
    }
}

require __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Buat Akun Admin</h5>

        <?php if ($hasAdmin && !$created): ?>
          <div class="alert alert-warning">Admin sudah ada. Silakan login.</div>
        <?php endif; ?>

        <?php if ($created): ?>
          <div class="alert alert-success">Akun admin berhasil dibuat. Silakan login.</div>
          <a class="btn btn-primary" href="<?= e(url('/login.php')) ?>">Ke Login</a>
        <?php else: ?>
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
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input class="form-control" type="email" name="email" value="<?= e((string)($_POST['email'] ?? '')) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input class="form-control" type="password" name="password" required>
            </div>
            <button class="btn btn-dark" type="submit" <?= $hasAdmin ? 'disabled' : '' ?>>Buat Admin</button>
            <a class="btn btn-link" href="<?= e(url('/login.php')) ?>">Kembali</a>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

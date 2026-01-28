<?php

require __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    redirect('/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errors[] = 'Email dan password wajib diisi.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id, nama, email, role, password_hash FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            $errors[] = 'Login gagal. Email atau password salah.';
        } else {
            unset($user['password_hash']);
            $_SESSION['user'] = $user;

            flash_set('msg', 'Login berhasil.', 'success');
            redirect('/index.php');
        }
    }
}

require __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Login</h5>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $err): ?>
              <div><?= e($err) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" value="<?= e((string)($_POST['email'] ?? '')) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
          </div>
          <button class="btn btn-primary" type="submit">Login</button>
          <a class="btn btn-link" href="<?= e(url('/register.php')) ?>">Belum punya akun?</a>
        </form>

        <hr>
        <div>
          <a href="<?= e(url('/seed_admin.php')) ?>">Buat akun admin (sekali saja)</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

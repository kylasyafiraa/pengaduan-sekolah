<?php

require __DIR__ . '/bootstrap.php';

$user = current_user();
if ($user) {
    if (($user['role'] ?? '') === 'admin') {
        redirect('/admin/dashboard.php');
    }
    redirect('/siswa/dashboard.php');
}

require __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="mb-2">Website Pengaduan Sekolah</h4>
        <p class="mb-4">Silakan register/login untuk membuat pengaduan. Admin bisa membalas dan mengubah status pengaduan.</p>
        <div class="d-flex gap-2">
          <a class="btn btn-primary" href="<?= e(url('/register.php')) ?>">Register</a>
          <a class="btn btn-outline-primary" href="<?= e(url('/login.php')) ?>">Login</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>

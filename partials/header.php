<?php

declare(strict_types=1);

$user = current_user();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pengaduan Sekolah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(url('/assets/style.css')) ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= e(url('/index.php')) ?>">Pengaduan Sekolah</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <?php if ($user): ?>
          <?php if (($user['role'] ?? '') === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= e(url('/admin/dashboard.php')) ?>">Admin</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= e(url('/admin/siswa.php')) ?>">Data Siswa</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?= e(url('/siswa/dashboard.php')) ?>">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= e(url('/siswa/pengaduan_tambah.php')) ?>">Buat Pengaduan</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="<?= e(url('/logout.php')) ?>">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= e(url('/register.php')) ?>">Register</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= e(url('/login.php')) ?>">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">
<?php $flash = flash_get('msg'); ?>
<?php if ($flash): ?>
  <div class="alert alert-<?= e($flash['type'] ?? 'info') ?>">
    <?= e($flash['message'] ?? '') ?>
  </div>
<?php endif; ?>

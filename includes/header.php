<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page_title ?? 'Produk') ?> - Toko Bangunan Pacific Jaya</title>
  <meta name="description" content="Sistem manajemen produk toko bangunan">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a href="index.php" class="logo">
        <span class="logo-mark">PC</span>
        <div class="logo-text">
          <span class="logo-title">Pacific Jaya</span>
          <span class="logo-subtitle">Toko Bangunan</span>
        </div>
      </a>
      <nav class="main-nav">
        <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true">home</span> Beranda
        </a>
        <a href="create.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'create.php' ? 'active' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true">category</span> Tambah
        </a>
      </nav>
    </div>
  </header>

  <main class="main-content container">
    <?php $flash = get_flash(); if ($flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>" id="flash-msg">
        <span><?= e($flash['message']) ?></span>
        <button type="button" class="flash-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
      </div>
    <?php endif; ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page_title ?? 'Produk') ?> - Toko Bangunan</title>
  <meta name="description" content="Sistem manajemen produk toko bangunan">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=add,arrow_back,arrow_forward,category,close,delete,edit,filter_list,first_page,image,inventory_2,last_page,search,storefront,warning&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a href="index.php" class="logo">
        <span class="logo-mark">TB</span>
        <span class="logo-text">Toko Bangunan</span>
      </a>
      <nav class="main-nav">
        <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true">inventory_2</span>
          Produk
        </a>
        <a href="create.php" class="nav-link btn-accent <?= basename($_SERVER['PHP_SELF']) === 'create.php' ? 'active' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true">add</span>
          Tambah
        </a>
      </nav>
    </div>
  </header>

  <main class="container main-content">
    <?php $flash = get_flash(); if ($flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>" id="flash-msg">
        <span><?= e($flash['message']) ?></span>
        <button type="button" class="flash-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
      </div>
    <?php endif; ?>

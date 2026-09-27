<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

$errors = [];
$old    = ['name' => '', 'category' => '', 'price' => '', 'stock' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        flash('error', 'Token keamanan tidak valid. Coba lagi.');
        header('Location: index.php');
        exit;
        
    $old = [
        'name'     => trim($_POST['name'] ?? ''),
        'category' => $_POST['category'] ?? '',
        'price'    => $_POST['price'] ?? '',
        'stock'    => $_POST['stock'] ?? '',
    ];

    $errors = validate_product($old, $pdo);

    $image_name = null;
    if (empty($errors) && isset($_FILES['image'])) {
        try {
            $image_name = handle_image_upload($_FILES['image']);
        } catch (RuntimeException $ex) {
            $errors['image'] = $ex->getMessage();
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO products (name, category, price, stock, image) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $old['name'],
            $old['category'],
            (float) $old['price'],
            (int) $old['stock'],
            $image_name,
        ]);

        flash('success', 'Produk berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Tambah Produk';
require_once __DIR__ . '/includes/header.php';
?>

<h1 class="form-page-title">Tambah Produk</h1>

<div class="form-card">
  <form method="post" action="create.php" enctype="multipart/form-data" novalidate>

    <?= csrf_field() ?>

    <div class="form-group">
      <label for="name" class="form-label">Nama Produk</label>
      <input type="text" id="name" name="name" value="<?= e($old['name']) ?>"
             class="form-input <?= isset($errors['name']) ? 'form-error' : '' ?>"
             maxlength="100" required autofocus>
      <?php if (isset($errors['name'])): ?>
        <div class="form-error-text"><?= e($errors['name']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="category" class="form-label">Kategori</label>
      <select id="category" name="category" class="form-select <?= isset($errors['category']) ? 'form-error' : '' ?>" required>
        <option value="">Pilih kategori</option>
        <?php foreach (get_categories() as $c): ?>
          <option value="<?= e($c) ?>" <?= $old['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['category'])): ?>
        <div class="form-error-text"><?= e($errors['category']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="price" class="form-label">Harga (Rp)</label>
        <input type="number" id="price" name="price" value="<?= e($old['price']) ?>"
               class="form-input <?= isset($errors['price']) ? 'form-error' : '' ?>"
               min="1" step="any" required>
        <?php if (isset($errors['price'])): ?>
          <div class="form-error-text"><?= e($errors['price']) ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="stock" class="form-label">Stok</label>
        <input type="number" id="stock" name="stock" value="<?= e($old['stock']) ?>"
               class="form-input <?= isset($errors['stock']) ? 'form-error' : '' ?>"
               min="0" step="1" required>
        <?php if (isset($errors['stock'])): ?>
          <div class="form-error-text"><?= e($errors['stock']) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="form-group">
      <label for="image-input" class="form-label">Gambar Produk</label>
      <input type="file" id="image-input" name="image" class="form-input" accept="image/jpeg,image/png,image/webp">
      <div class="form-hint">JPEG, PNG, atau WebP. Maksimal 2MB.</div>
      <?php if (isset($errors['image'])): ?>
        <div class="form-error-text"><?= e($errors['image']) ?></div>
      <?php endif; ?>
      <img id="image-preview" class="form-image-preview" alt="Preview">
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary btn-lg">
        <span class="material-symbols-rounded" aria-hidden="true">add</span> Simpan Produk
      </button>
      <a href="index.php" class="btn btn-outline btn-lg">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

$errors = [];
$old    = [
    'name'     => $product['name'],
    'category' => $product['category'],
    'price'    => $product['price'],
    'stock'    => $product['stock'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'name'     => trim($_POST['name'] ?? ''),
        'category' => $_POST['category'] ?? '',
        'price'    => $_POST['price'] ?? '',
        'stock'    => $_POST['stock'] ?? '',
    ];

    $errors = validate_product($old, $pdo, $id);

    $new_image = null;
    if (empty($errors) && isset($_FILES['image'])) {
        try {
            $new_image = handle_image_upload($_FILES['image']);
        } catch (RuntimeException $ex) {
            $errors['image'] = $ex->getMessage();
        }
    }

    if (empty($errors)) {
        $image_val = $new_image ?? $product['image'];

        if ($new_image && $product['image']) {
            delete_product_image($product['image']);
        }

        $stmt = $pdo->prepare('UPDATE products SET name = ?, category = ?, price = ?, stock = ?, image = ? WHERE id = ?');
        $stmt->execute([
            $old['name'],
            $old['category'],
            (float) $old['price'],
            (int) $old['stock'],
            $image_val,
            $id,
        ]);

        flash('success', 'Produk berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Edit Produk';
require_once __DIR__ . '/includes/header.php';
?>

<h1 class="form-page-title">Edit Produk</h1>

<div class="form-card">
  <form method="post" action="edit.php?id=<?= $id ?>" enctype="multipart/form-data" novalidate>

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
        <input type="number" id="price" name="price" value="<?= e((string) $old['price']) ?>"
               class="form-input <?= isset($errors['price']) ? 'form-error' : '' ?>"
               min="1" step="any" required>
        <?php if (isset($errors['price'])): ?>
          <div class="form-error-text"><?= e($errors['price']) ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="stock" class="form-label">Stok</label>
        <input type="number" id="stock" name="stock" value="<?= e((string) $old['stock']) ?>"
               class="form-input <?= isset($errors['stock']) ? 'form-error' : '' ?>"
               min="0" step="1" required>
        <?php if (isset($errors['stock'])): ?>
          <div class="form-error-text"><?= e($errors['stock']) ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="form-group">
      <label for="image-input" class="form-label">Gambar Produk</label>
      <?php if ($product['image'] && is_file(__DIR__ . '/uploads/' . $product['image'])): ?>
        <img src="uploads/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="form-image-current">
        <div class="form-hint">Upload baru untuk mengganti gambar saat ini.</div>
      <?php endif; ?>
      <input type="file" id="image-input" name="image" class="form-input" accept="image/jpeg,image/png,image/webp" style="margin-top:8px">
      <div class="form-hint">JPEG, PNG, atau WebP. Maksimal 2MB.</div>
      <?php if (isset($errors['image'])): ?>
        <div class="form-error-text"><?= e($errors['image']) ?></div>
      <?php endif; ?>
      <img id="image-preview" class="form-image-preview" alt="Preview">
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary btn-lg">
        <span class="material-symbols-rounded" aria-hidden="true">edit</span> Simpan Perubahan
      </button>
      <a href="index.php" class="btn btn-outline btn-lg">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

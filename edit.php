<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Ambil data produk yang ada
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    flash('error', 'Produk tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$errors = [];
$old = [
    'name'     => $product['name'],
    'category' => $product['category'],
    'price'    => $product['price'],
    'stock'    => $product['stock']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // VERIFIKASI CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($token)) {
        flash('error', 'Token keamanan tidak valid. Coba lagi.');
        header('Location: index.php');
        exit;
    }

    $old = [
        'name'     => trim($_POST['name'] ?? ''),
        'category' => $_POST['category'] ?? '',
        'price'    => $_POST['price'] ?? '',
        'stock'    => $_POST['stock'] ?? '',
    ];

    // Validasi produk, sertakan $id agar nama tidak dianggap duplikat oleh produk itu sendiri
    $errors = validate_product($old, $pdo, $id);

    $image_name = $product['image'];
    if (empty($errors) && !empty($_FILES['image']['name'])) {
        try {
            $new_image = handle_image_upload($_FILES['image']);
            if ($new_image) {
                delete_product_image($product['image']);
                $image_name = $new_image;
            }
        } catch (RuntimeException $ex) {
            $errors['image'] = $ex->getMessage();
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE products SET name = ?, category = ?, price = ?, stock = ?, image = ? WHERE id = ?');
        $stmt->execute([
            $old['name'],
            $old['category'],
            (float) $old['price'],
            (int) $old['stock'],
            $image_name,
            $id
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
    <input type="hidden" name="id" value="<?= $id ?>">
    
  
    <!-- FIELD CSRF -->

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
      <label for="image-input" class="form-label">Gambar Produk Baru (Opsional)</label>
      <input type="file" id="image-input" name="image" class="form-input" accept="image/jpeg,image/png,image/webp">
      <div class="form-hint">JPEG, PNG, atau WebP. Maksimal 2MB. Kosongkan jika tidak ingin mengubah gambar.</div>
      <?php if (isset($errors['image'])): ?>
        <div class="form-error-text"><?= e($errors['image']) ?></div>
      <?php endif; ?>
      
      <?php if ($product['image']): ?>
        <div style="margin-top: 15px;">
          <p class="form-hint">Gambar saat ini:</p>
          <img src="uploads/<?= e($product['image']) ?>" alt="Preview" style="max-width: 150px; border-radius: 8px; margin-top: 5px;">
        </div>
      <?php endif; ?>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary btn-lg">
        <span class="material-symbols-rounded" aria-hidden="true">save</span> Simpan Perubahan
      </button>
      <a href="index.php" class="btn btn-outline btn-lg">Batal</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
session_start();

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function get_categories(): array
{
    return [
        'Semen & Beton',
        'Besi & Baja',
        'Kayu & Papan',
        'Cat & Finishing',
        'Pipa & Sanitasi',
        'Listrik & Kabel',
        'Atap & Genteng',
        'Peralatan Tangan',
        'Pasir & Batu',
        'Pintu & Jendela',
    ];
}

function format_price(float $price): string
{
    return 'Rp ' . number_format($price, 0, ',', '.');
}

function category_color(string $category): string
{
    $map = [
        'Semen & Beton'     => '#6b7280',
        'Besi & Baja'       => '#6366f1',
        'Kayu & Papan'      => '#92702a',
        'Cat & Finishing'    => '#8b5cf6',
        'Pipa & Sanitasi'   => '#0891b2',
        'Listrik & Kabel'   => '#ca8a04',
        'Atap & Genteng'    => '#dc2626',
        'Peralatan Tangan'  => '#059669',
        'Pasir & Batu'      => '#78716c',
        'Pintu & Jendela'   => '#4f46e5',
    ];
    return $map[$category] ?? '#6b7280';
}

function validate_product(array $data, PDO $pdo, ?int $exclude_id = null): array
{
    $errors = [];

    $name = trim($data['name'] ?? '');
    if ($name === '') {
        $errors['name'] = 'Nama produk wajib diisi.';
    } elseif (mb_strlen($name) < 3 || mb_strlen($name) > 100) {
        $errors['name'] = 'Nama produk harus 3-100 karakter.';
    } else {
        $sql = 'SELECT id FROM products WHERE name = ?';
        $params = [$name];
        if ($exclude_id !== null) {
            $sql .= ' AND id != ?';
            $params[] = $exclude_id;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if ($stmt->fetch()) {
            $errors['name'] = 'Nama produk sudah digunakan.';
        }
    }

    if (empty($data['category']) || !in_array($data['category'], get_categories(), true)) {
        $errors['category'] = 'Pilih kategori yang valid.';
    }

    $price = $data['price'] ?? '';
    if ($price === '' || !is_numeric($price) || (float) $price <= 0) {
        $errors['price'] = 'Harga harus angka lebih dari 0.';
    }

    $stock = $data['stock'] ?? '';
    if ($stock === '' || !ctype_digit((string) $stock) || (int) $stock < 0) {
        $errors['stock'] = 'Stok harus angka 0 atau lebih.';
    }

    return $errors;
}

function handle_image_upload(array $file): ?string
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Gagal mengunggah file.');
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Format file harus JPEG, PNG, atau WebP.');
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Ukuran file maksimal 2MB.');
    }

    $filename   = uniqid('prod_', true) . '.' . $allowed[$mime];
    $upload_dir = __DIR__ . '/../uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        throw new RuntimeException('Gagal menyimpan file.');
    }

    return $filename;
}

function delete_product_image(?string $filename): void
{
    if ($filename === null) return;
    $path = __DIR__ . '/../uploads/' . $filename;
    if (is_file($path)) {
        unlink($path);
    }
}

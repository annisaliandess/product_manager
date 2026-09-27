<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

$q        = trim($_GET['q'] ?? '');
$cat      = $_GET['category'] ?? '';
$page     = max(1, (int) ($_GET['page'] ?? 1));
$per_page = 12;

$where  = [];
$params = [];

if ($q !== '') {
    $where[]  = 'name LIKE ?';
    $params[] = "%{$q}%";
}
if ($cat !== '' && in_array($cat, get_categories(), true)) {
    $where[]  = 'category = ?';
    $params[] = $cat;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products {$where_sql}");
$stmt->execute($params);
$total       = (int) $stmt->fetchColumn();
$total_pages = max(1, (int) ceil($total / $per_page));
$page        = min($page, $total_pages);
$offset      = ($page - 1) * $per_page;

$limit_int  = (int) $per_page;
$offset_int = (int) $offset;
$stmt = $pdo->prepare("SELECT * FROM products {$where_sql} ORDER BY created_at DESC LIMIT {$limit_int} OFFSET {$offset_int}");
$stmt->execute($params);
$products = $stmt->fetchAll();

$page_title = 'Produk';

function build_qs(array $overrides): string
{
    $params = array_merge($_GET, $overrides);
    $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
    return '?' . http_build_query($params);
}

require_once __DIR__ . '/includes/header.php';
?>


<div class="toolbar">
  <form class="search-box" method="get" action="index.php">
    <?php if ($cat): ?>
      <input type="hidden" name="category" value="<?= e($cat) ?>">
    <?php endif; ?>
    <span class="material-symbols-rounded" aria-hidden="true">search</span>
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari material bangunan (semen, besi, cat)..." autocomplete="off">
  </form>

  <select class="filter-select" onchange="location.href='index.php'+buildCatQs(this.value)">
    <option value="">Semua Kategori</option>
    <?php foreach (get_categories() as $c): ?>
      <option value="<?= e($c) ?>" <?= $cat === $c ? 'selected' : '' ?>><?= e($c) ?></option>
    <?php endforeach; ?>
  </select>

  <?php if ($total > 0): ?>
    <div class="toolbar-info">
      <?php
        $from = $offset + 1;
        $to   = min($offset + $per_page, $total);
      ?>
      Menampilkan <?= $from ?>-<?= $to ?> dari <?= $total ?> produk
    </div>
  <?php endif; ?>
</div>

<?php if (empty($products)): ?>
  <div class="empty-state">
    <span class="material-symbols-rounded" aria-hidden="true">inventory_2</span>
    <p>Tidak ada produk ditemukan.</p>
    <?php if ($q || $cat): ?>
      <a href="index.php" class="btn btn-outline">Hapus Filter</a>
    <?php else: ?>
      <a href="create.php" class="btn btn-primary">Tambah Produk Pertama</a>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="product-grid">
    <?php foreach ($products as $p): ?>
      <div class="product-card">
        <div class="product-img-wrap">
          <?php if ($p['image'] && is_file(__DIR__ . '/uploads/' . $p['image'])): ?>
            <img src="uploads/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
          <?php else: ?>
            <div class="product-placeholder" style="background:<?= e(category_color($p['category'])) ?>">
              <?= e(mb_strtoupper(mb_substr($p['name'], 0, 2))) ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="product-body">
          <span class="product-category" style="background: #cbd5e1; color: #0a1120;">
            <?= e($p['category']) ?>
          </span>
          <h3 class="product-name"><?= e($p['name']) ?></h3>
          <div class="product-price"><?= format_price((float) $p['price']) ?></div>
          <div class="product-stock <?= (int) $p['stock'] > 0 ? 'stock-ok' : 'stock-empty' ?>">
            <?= (int) $p['stock'] > 0 ? 'Stok Tersedia (' . e((string) $p['stock']) . ' unit)' : 'Stok Habis' ?>
          </div>
          <div class="product-actions">
            <a href="edit.php?id=<?= (int) $p['id'] ?>" class="btn btn-primary btn-block">
              <span class="material-symbols-rounded" aria-hidden="true">edit</span> Edit
            </a>
            <form method="post" action="delete.php" class="delete-form" onsubmit="return confirm('Hapus produk ini?')">
              <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-outline" title="Hapus Produk">
                <span class="material-symbols-rounded" aria-hidden="true">delete</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if ($total_pages > 1): ?>
    <nav class="pagination" aria-label="Navigasi halaman">
      <span class="page-info">Halaman <?= $page ?> dari <?= $total_pages ?></span>
      <div class="page-links">
        <a href="index.php<?= build_qs(['page' => 1]) ?>" class="page-link <?= $page <= 1 ? 'disabled' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true">first_page</span>
        </a>
        <a href="index.php<?= build_qs(['page' => max(1, $page - 1)]) ?>" class="page-link <?= $page <= 1 ? 'disabled' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>
        </a>
        <?php
          $start = max(1, $page - 2);
          $end   = min($total_pages, $page + 2);
          for ($i = $start; $i <= $end; $i++):
        ?>
          <a href="index.php<?= build_qs(['page' => $i]) ?>" class="page-link <?= $i === $page ? 'active' : '' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
        <a href="index.php<?= build_qs(['page' => min($total_pages, $page + 1)]) ?>" class="page-link <?= $page >= $total_pages ? 'disabled' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true">arrow_forward</span>
        </a>
        <a href="index.php<?= build_qs(['page' => $total_pages]) ?>" class="page-link <?= $page >= $total_pages ? 'disabled' : '' ?>">
          <span class="material-symbols-rounded" aria-hidden="true">last_page</span>
        </a>
      </div>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<script>
function buildCatQs(val){
  var p = new URLSearchParams(location.search);
  if(val) p.set('category',val); else p.delete('category');
  p.delete('page');
  var s = p.toString();
  return s ? '?'+s : '';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

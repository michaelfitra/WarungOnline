<?php

$message = '';
$message_type = '';

$existing_categories = [];
try {
    
    $stmt_categories = $pdo->query("SELECT DISTINCT kategori FROM produk WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
    $existing_categories = $stmt_categories->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
  
    error_log("Error fetching categories in manage_products.php: " . $e->getMessage());
    $message = "Gagal memuat daftar kategori. Silakan coba lagi.";
    $message_type = "danger";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = filter_var($_POST['harga'], FILTER_VALIDATE_FLOAT);
    
    $kategori = '';
    if (isset($_POST['kategori'])) {
        if ($_POST['kategori'] === 'new_category_option' && isset($_POST['new_kategori'])) {
            $kategori = trim($_POST['new_kategori']); 
        } else {
            $kategori = trim($_POST['kategori']); 
        }
    }

    if (empty($nama) || empty($deskripsi) || $harga === false || $harga <= 0 || empty($kategori)) {
        $message = "Semua kolom harus diisi dengan benar. Harga harus angka positif.";
        $message_type = "danger";
    } else {
        $gambar_path = ''; 
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../assets/images/"; 
            
            if (!is_dir($target_dir)) {
                
                if (!mkdir($target_dir, 0755, true)) { 
                    $message = "Error: Direktori upload '" . htmlspecialchars($target_dir) . "' tidak ditemukan dan gagal dibuat. Periksa izin folder atau path.";
                    $message_type = "danger";
                    error_log("Failed to create upload directory: " . $target_dir); 
                }
            }

            if ($message_type !== "danger" && !is_writable($target_dir)) {
             
                $message = "Error: Direktori upload '" . htmlspecialchars($target_dir) . "' tidak memiliki izin tulis. Periksa izin folder.";
                $message_type = "danger";
                error_log("Upload directory is not writable: " . $target_dir); 
            }
            if ($message_type !== "danger") { 
                $file_extension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
                $new_file_name = uniqid() . '.' . $file_extension; 
                $target_file = $target_dir . $new_file_name;
                $imageFileType = strtolower($file_extension);

                $check = getimagesize($_FILES["gambar"]["tmp_name"]);
                if ($check === false) {
                    $message = "File yang diunggah bukan gambar atau rusak.";
                    $message_type = "danger";
                } else {
                   
                    if(!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                        $message = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
                        $message_type = "danger";
                    } 
                    else if ($_FILES["gambar"]["size"] > 2000000) { 
                        $message = "Maaf, ukuran file terlalu besar. Maksimal 2MB.";
                        $message_type = "danger";
                    } 
                    else {
                        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                            $gambar_path = $new_file_name; 
                        } else {
                            $last_error = error_get_last();
                            $message = "Maaf, terjadi kesalahan saat mengunggah file Anda. ";
                            if ($last_error && $last_error['type'] === E_WARNING && strpos($last_error['message'], 'move_uploaded_file') !== false) {
                                $message .= "Detail: " . $last_error['message'];
                            } else {
                                $message .= "Kode Error: " . $_FILES['gambar']['error']; 
                            }
                            $message_type = "danger";
                            error_log("Failed to move uploaded file. Temp: " . $_FILES["gambar"]["tmp_name"] . ", Target: " . $target_file . ", Error: " . ($last_error ? $last_error['message'] : 'Unknown'));
                        }
                    }
                }
            }
        } 
        
        else if ($_FILES['gambar']['error'] != UPLOAD_ERR_NO_FILE) {
             $upload_errors = [
                 UPLOAD_ERR_INI_SIZE   => "File melebihi ukuran maksimum yang diizinkan di php.ini.",
                 UPLOAD_ERR_FORM_SIZE  => "File melebihi ukuran maksimum yang ditentukan dalam form HTML.",
                 UPLOAD_ERR_PARTIAL    => "File hanya terunggah sebagian.",
                 UPLOAD_ERR_NO_TMP_DIR => "Direktori temporary tidak ditemukan.",
                 UPLOAD_ERR_CANT_WRITE => "Gagal menulis file ke disk.",
                 UPLOAD_ERR_EXTENSION  => "Ekstensi PHP menghentikan upload file."
             ];
             $message = "Kesalahan upload gambar: " . ($upload_errors[$_FILES['gambar']['error']] ?? "Kode error tak dikenal: " . $_FILES['gambar']['error']);
             $message_type = "danger";
             error_log("Upload error for file: " . $_FILES['gambar']['name'] . ", Error code: " . $_FILES['gambar']['error']);
        }


        if ($message_type !== "danger") { 
            try {
                $stmt = $pdo->prepare("INSERT INTO produk (nama, deskripsi, harga, gambar, kategori) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nama, $deskripsi, $harga, $gambar_path, $kategori]);
                $message = "Produk berhasil ditambahkan!";
                $message_type = "success";
                
                $stmt_categories = $pdo->query("SELECT DISTINCT kategori FROM produk WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
                $existing_categories = $stmt_categories->fetchAll(PDO::FETCH_COLUMN);

            } catch (PDOException $e) {
                $message = "Gagal menambahkan produk ke database: " . $e->getMessage();
                $message_type = "danger";
                error_log("Database insert failed in manage_products.php: " . $e->getMessage()); 
            }
        }
    }
}

// Logika untuk menghapus produk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);

    if ($product_id) {
        try {
            $stmt_img = $pdo->prepare("SELECT gambar FROM produk WHERE id = ?");
            $stmt_img->execute([$product_id]);
            $product_to_delete = $stmt_img->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
            if ($stmt->execute([$product_id])) {
            
                if ($product_to_delete && !empty($product_to_delete['gambar'])) {
                    $image_file_path = "../assets/images/" . $product_to_delete['gambar'];
                    if (file_exists($image_file_path)) {
                        if (!unlink($image_file_path)) {
                            error_log("Failed to delete image file: " . $image_file_path);
                        }
                    }
                }
                $message = "Produk berhasil dihapus.";
                $message_type = "success";
              
                $stmt_categories = $pdo->query("SELECT DISTINCT kategori FROM produk WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
                $existing_categories = $stmt_categories->fetchAll(PDO::FETCH_COLUMN);

            } else {
                $message = "Gagal menghapus produk dari database.";
                $message_type = "danger";
            }
        } catch (PDOException $e) {
            $message = "Gagal menghapus produk: " . $e->getMessage();
            $message_type = "danger";
            error_log("Database delete failed in manage_products.php: " . $e->getMessage()); 
        }
    } else {
        $message = "ID produk tidak valid.";
        $message_type = "danger";
    }
}
$products = [];
try {
    $stmt = $pdo->query("SELECT id, nama, deskripsi, harga, gambar, kategori FROM produk ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Gagal memuat daftar produk: " . $e->getMessage();
    $message_type = "danger";
    error_log("Error fetching products list in manage_products.php: " . $e->getMessage()); 
}
?>

<h2 class="mb-4">Manajemen Produk</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-warning text-white">
        <h5>Tambah Produk Baru</h5>
    </div>
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="add_product" value="1">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" step="0.01" min="0.01" required>
            </div>
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="">Pilih atau Tambah Kategori Baru</option>
                    <?php foreach ($existing_categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat); ?>"><?= htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                    <option value="new_category_option">-- Tambah Kategori Baru --</option>
                </select>
                <input type="text" class="form-control mt-2" id="new_kategori_input" name="new_kategori" placeholder="Masukkan kategori baru" style="display: none;">
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Produk</label>
                <input class="form-control" type="file" id="gambar" name="gambar" accept="image/*">
                <small class="form-text text-muted">Maksimal ukuran file 2MB. Hanya JPG, JPEG, PNG, GIF yang diizinkan.</small>
            </div>
            <button type="submit" class="btn btn-warning">Tambah Produk</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        <h5>Daftar Produk</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['id']) ?></td>
                                <td>
                                    <?php if (!empty($product['gambar'])): ?>
                                        <img src="../assets/images/<?= htmlspecialchars($product['gambar']) ?>" alt="<?= htmlspecialchars($product['nama']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <i class="fas fa-image text-muted" style="font-size: 30px;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($product['nama']) ?></td>
                                <td>Rp <?= number_format($product['harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($product['kategori']) ?></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm me-1" title="Edit Produk"><i class="fas fa-edit"></i></a>
                                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        <input type="hidden" name="delete_product" value="1">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Produk"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada produk.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('kategori');
    const newCategoryInput = document.getElementById('new_kategori_input');

    categorySelect.addEventListener('change', function() {
        if (this.value === 'new_category_option') {
            newCategoryInput.style.display = 'block'; 
            newCategoryInput.setAttribute('required', 'required'); 
            newCategoryInput.name = 'new_kategori'; 
            categorySelect.removeAttribute('name'); 
        } else {
            newCategoryInput.style.display = 'none'; 
            newCategoryInput.removeAttribute('required');
            newCategoryInput.name = 'new_kategori';
            categorySelect.name = 'kategori'; 
        }
        
        newCategoryInput.value = ''; 
    });

    document.querySelector('form').addEventListener('submit', function(event) {
        if (categorySelect.value === 'new_category_option') {
            if (newCategoryInput.value.trim() === '') {
                alert('Silakan masukkan nama kategori baru.');
                event.preventDefault(); 
                newCategoryInput.focus(); 
            }
        }
    });
    categorySelect.dispatchEvent(new Event('change'));
});
</script>
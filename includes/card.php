    <?php
    // includes/card.php

    // Pastikan $product ada, jika tidak, gunakan data dummy
    if (!isset($product)) {
        $product = [
            'id' => 999,
            'nama' => 'Produk Contoh',
            'harga' => 12345.67,
            'gambar' => 'default.jpg',
            'deskripsi' => 'Ini adalah deskripsi produk contoh.',
            'kategori' => 'Contoh Kategori' // Tambahkan kategori untuk konsistensi
        ];
    }
    ?>

    <div class="col">
        <div class="card h-100 shadow-sm border-0">
            <img src="<?= base_url('assets/images/' . htmlspecialchars($product['gambar'] ?? 'default.jpg')); ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($product['nama']); ?>">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title product-title">
                    <a href="<?= base_url('views/detail.php?id=' . htmlspecialchars($product['id'])); ?>" class="text-dark text-decoration-none">
                        <?= htmlspecialchars($product['nama']); ?>
                    </a>
                </h5>
                <p class="card-text text-muted mb-2"><small><?= htmlspecialchars($product['kategori'] ?? 'Umum'); ?></small></p>
                <p class="product-price mt-auto mb-2">Rp<?= number_format($product['harga'], 0, ',', '.'); ?></p>
                <button class="btn btn-warning btn-sm add-to-cart-btn w-100" data-product-id="<?= htmlspecialchars($product['id']); ?>">
                    <i class="fas fa-cart-plus me-1"></i> Tambah
                </button>
            </div>
        </div>
    </div>
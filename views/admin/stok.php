<?php
include '../../includes/db.php';
require_once '../../includes/auth_check.php';
require_admin(); 
include '../../includes/header_admin.php';

// Ambil data produk menggunakan PDO
try {
    $sql = "SELECT * FROM produk ORDER BY id ASC";
    $stmt = $pdo->query($sql);
    $produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Barang - Kedai Barokah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="d-flex">
        <?php $activePage = 'stok'; ?>
        <?php include '../../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-10">

            <div class="main-content">
                <?php if (isset($_GET['status'])): ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: '<?php
                            if ($_GET["status"] == "berhasil")
                                echo "Produk berhasil ditambahkan.";
                            elseif ($_GET["status"] == "update")
                                echo "Produk berhasil diupdate.";
                            elseif ($_GET["status"] == "hapus")
                                echo "Produk berhasil dihapus.";
                            ?>',
                                        timer: 2000,
                                    showConfirmButton: false
                                    });
                    </script>
                <?php endif; ?>

                <!--  -->
                <h5><strong>Data Barang</strong></h5>
                <button class="btn btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+
                    Tambah</button>

                <div class="table-container">
                    <div class="d-flex justify-content-end mb-2">
                        <label for="search" class="me-2">Search:</label>
                        <input type="text" id="search" class="form-control w-25">
                    </div>

                    <table class="table table-bordered text-center align-middle bg-white">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Barang</th>
                                <!-- <th>Gambar</th> -->
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <!-- <th>Satuan</th> -->
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($produk as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>BRG<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                    <td style="text-align: left;"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['kategori']) ?></td>
                                    <td><?= $row['stok'] ?></td>
                                    <td><?= number_format($row['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary btn-edit" data-id="<?= $row['id'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama']) ?>"
                                            data-kategori="<?= htmlspecialchars($row['kategori']) ?>"
                                            data-stok="<?= $row['stok'] ?>" data-harga="<?= $row['harga'] ?>"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit">
                                            Edit
                                        </button>
                                        <!-- /// -->
                                        <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama']) ?>">
                                            Hapus
                                        </button>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal Tambah Produk -->
        <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="tambah_stok.php" method="POST" enctype="multipart/form-data" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Kategori</label>
                            <input type="text" name="kategori" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Nama Produk</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Deskripsi</label>
                            <input type="text" name="deskripsi" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Gambar</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-2">
                            <label>Harga</label>
                            <input type="number" name="harga" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Edit Produk -->
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="edit_stok.php" method="POST" class="modal-content">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Nama Produk</label>
                            <input type="text" name="nama" id="edit-nama" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Kategori</label>
                            <input type="text" name="kategori" id="edit-kategori" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Stok</label>
                            <input type="number" name="stok" id="edit-stok" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Harga</label>
                            <input type="number" name="harga" id="edit-harga" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('d-none');
        });
    </script>
    <script>
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-nama').value = this.dataset.nama;
                document.getElementById('edit-kategori').value = this.dataset.kategori;
                document.getElementById('edit-stok').value = this.dataset.stok;
                document.getElementById('edit-harga').value = this.dataset.harga;
            });
        });
    </script>
    <script>
        document.querySelectorAll('.btn-hapus').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const nama = this.dataset.nama;

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: `Produk "${nama}" akan dihapus permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `hapus_stok.php?id=${id}`;
                    }
                });
            });
        });
    </script>

</body>

</html>
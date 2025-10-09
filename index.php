<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "db_inventaris");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_barang'];
    $kode = $_POST['kode_barang'];
    $jumlah = $_POST['jumlah'];
    $kondisi = $_POST['kondisi'];
    $tanggal = $_POST['tanggal_masuk'];

    // Upload foto
    $foto = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . '_' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
    }

    $conn->query("INSERT INTO barang (nama_barang, kode_barang, jumlah, kondisi, tanggal_masuk, foto)
                  VALUES ('$nama', '$kode', '$jumlah', '$kondisi', '$tanggal', '$foto')");
    header("Location: index.php");
    exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM barang WHERE id=$id");
    header("Location: index.php");
    exit;
}

// Edit data
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_barang'];
    $kode = $_POST['kode_barang'];
    $jumlah = $_POST['jumlah'];
    $kondisi = $_POST['kondisi'];
    $tanggal = $_POST['tanggal_masuk'];

    if (!empty($_FILES['foto']['name'])) {
        $foto_update = time() . '_' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_update);
        $conn->query("UPDATE barang SET nama_barang='$nama', kode_barang='$kode', jumlah='$jumlah',
                      kondisi='$kondisi', tanggal_masuk='$tanggal', foto='$foto_update' WHERE id=$id");
    } else {
        $conn->query("UPDATE barang SET nama_barang='$nama', kode_barang='$kode', jumlah='$jumlah',
                      kondisi='$kondisi', tanggal_masuk='$tanggal' WHERE id=$id");
    }
    header("Location: index.php");
    exit;
}

// 🔍 Pencarian
$cari = "";
if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
    $result = $conn->query("SELECT * FROM barang WHERE nama_barang LIKE '%$cari%' OR kode_barang LIKE '%$cari%'");
} else {
    $result = $conn->query("SELECT * FROM barang");
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventaris Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <h2 class="text-center mb-4">📦 Inventaris Barang</h2>

  <!-- Form Tambah -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah Barang</div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-md-4">
            <input type="text" name="nama_barang" class="form-control" placeholder="Nama Barang" required>
          </div>
          <div class="col-md-2">
            <input type="text" name="kode_barang" class="form-control" placeholder="Kode" required>
          </div>
          <div class="col-md-2">
            <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required>
          </div>
          <div class="col-md-2">
            <select name="kondisi" class="form-select" required>
              <option value="">Kondisi</option>
              <option value="Baik">Baik</option>
              <option value="Rusak">Rusak</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="date" name="tanggal_masuk" class="form-control" required>
          </div>
          <div class="col-md-4">
            <input type="file" name="foto" class="form-control">
          </div>
          <div class="col-md-12 text-end">
            <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- 🔍 Pencarian -->
  <form class="mb-3" method="GET">
    <div class="input-group">
      <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>" class="form-control" placeholder="Cari nama atau kode barang...">
      <button class="btn btn-outline-primary" type="submit">Cari</button>
      <a href="index.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <!-- Tabel Data -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Daftar Barang</div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-secondary">
          <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama</th>
            <th>Kode</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
            <th>Tanggal Masuk</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if ($result->num_rows == 0) {
              echo "<tr><td colspan='8' class='text-center text-muted'>Tidak ada data</td></tr>";
          } else {
              $no=1; while($row=$result->fetch_assoc()): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>
                  <?php if($row['foto']): ?>
                    <img src="uploads/<?= $row['foto'] ?>" width="60" class="rounded">
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td><?= $row['nama_barang'] ?></td>
                <td><?= $row['kode_barang'] ?></td>
                <td><?= $row['jumlah'] ?></td>
                <td><?= $row['kondisi'] ?></td>
                <td><?= $row['tanggal_masuk'] ?></td>
                <td>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id'] ?>">Edit</button>
                  <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
              </tr>

              <!-- Modal Edit -->
              <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                      <div class="modal-header bg-warning">
                        <h5 class="modal-title">Edit Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <div class="mb-2">
                          <label>Nama Barang</label>
                          <input type="text" name="nama_barang" class="form-control" value="<?= $row['nama_barang'] ?>" required>
                        </div>
                        <div class="mb-2">
                          <label>Kode Barang</label>
                          <input type="text" name="kode_barang" class="form-control" value="<?= $row['kode_barang'] ?>" required>
                        </div>
                        <div class="mb-2">
                          <label>Jumlah</label>
                          <input type="number" name="jumlah" class="form-control" value="<?= $row['jumlah'] ?>" required>
                        </div>
                        <div class="mb-2">
                          <label>Kondisi</label>
                          <select name="kondisi" class="form-select">
                            <option <?= $row['kondisi']=="Baik"?"selected":"" ?>>Baik</option>
                            <option <?= $row['kondisi']=="Rusak"?"selected":"" ?>>Rusak</option>
                          </select>
                        </div>
                        <div class="mb-2">
                          <label>Tanggal Masuk</label>
                          <input type="date" name="tanggal_masuk" class="form-control" value="<?= $row['tanggal_masuk'] ?>">
                        </div>
                        <div class="mb-2">
                          <label>Foto (opsional)</label>
                          <input type="file" name="foto" class="form-control">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
          <?php endwhile; } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

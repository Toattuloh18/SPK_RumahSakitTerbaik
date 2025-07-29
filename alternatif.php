<?php
// Di setiap halaman sistem
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}
include 'koneksi.php';

// Handle tambah data
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama']);
    if ($nama !== '') {
        $stmt = $conn->prepare("INSERT INTO alternatif (nama) VALUES (?)");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
    }
    header("Location: alternatif.php");
    exit();
}

// Handle hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // Hapus dulu nilai yang terkait dengan id_alternatif = $id
    $conn->query("DELETE FROM nilai WHERE id_alternatif = $id");

    // Baru hapus alternatif
    $conn->query("DELETE FROM alternatif WHERE id = $id");

    // Reset AUTO_INCREMENT supaya id dimulai ulang dari 1 (atau nilai berikutnya yang tersedia)
    $conn->query("ALTER TABLE alternatif AUTO_INCREMENT = 1");

    header("Location: alternatif.php");
    exit();
}



// Handle edit
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $nama = trim($_POST['nama']);
    if ($nama !== '') {
        $stmt = $conn->prepare("UPDATE alternatif SET nama=? WHERE id=?");
        $stmt->bind_param("si", $nama, $id);
        $stmt->execute();
    }
    header("Location: alternatif.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Data Alternatif Rumah Sakit - Sistem SPK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />
    <style>
    /* Reset & Base */
    body {
        margin: 0;
        background: #121212;
        color: #f0e9db;
        font-family: 'Montserrat', sans-serif;
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    h2 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 2.8rem;
        color: #c9b037;
        letter-spacing: 3px;
        text-align: center;
        margin-bottom: 2.5rem;
        text-shadow: 0 0 8px rgba(201, 176, 55, 0.75);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: transparent;
        border: 2px solid #c9b037;
        color: #c9b037;
        font-weight: 600;
        padding: 0.5rem 1.2rem;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-bottom: 3rem;
        user-select: none;
    }

    .btn-back:hover,
    .btn-back:focus {
        background-color: #c9b037;
        color: #121212;
        text-decoration: none;
        outline: none;
        box-shadow: 0 0 8px #c9b037;
    }

    /* Form Add */
    form.mb-4 {
        max-width: 600px;
        margin: 0 auto 3rem auto;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    form input[type="text"] {
        flex-grow: 1;
        padding: 0.65rem 1rem;
        font-size: 1.1rem;
        border-radius: 14px;
        border: 2px solid #c9b037;
        background-color: #1f1f1f;
        color: #f0e9db;
        font-weight: 600;
        transition: border-color 0.3s ease, background-color 0.3s ease;
    }

    form input[type="text"]:focus {
        outline: none;
        border-color: #fff;
        background-color: #2a2a2a;
        color: #fff;
    }

    form button.btn-primary {
        background-color: #c9b037;
        border: none;
        font-weight: 700;
        padding: 0.6rem 1.8rem;
        border-radius: 14px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    form button.btn-primary:hover,
    form button.btn-primary:focus {
        background-color: #a89529;
        box-shadow: 0 0 10px #a89529;
        outline: none;
    }

    /* Table Styling */
    table {
        width: 100%;
        max-width: 900px;
        margin: 0 auto 4rem auto;
        border-collapse: separate;
        border-spacing: 0 14px;
        font-size: 1rem;
        color: #f0e9db;
    }

    thead tr {
        color: #c9b037;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 1.2px;
        text-shadow: 0 0 7px rgba(201, 176, 55, 0.7);
    }

    tbody tr {
        background-color: #1f1f1f;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(201, 176, 55, 0.2);
        transition: background-color 0.25s ease;
    }

    tbody tr:hover {
        background-color: #2e2e2e;
    }

    tbody td {
        padding: 1rem 1.4rem;
        vertical-align: middle;
        font-weight: 600;
    }

    tbody td:first-child {
        text-align: center;
        width: 10%;
    }

    tbody td:nth-child(3) {
        width: 25%;
    }

    /* Buttons in Table */
    .btn-warning {
        background-color: #c9b037;
        border: none;
        color: #121212;
        font-weight: 600;
        padding: 0.35rem 1rem;
        border-radius: 14px;
        box-shadow: 0 0 5px #c9b037;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .btn-warning:hover,
    .btn-warning:focus {
        background-color: #a89529;
        box-shadow: 0 0 12px #a89529;
        outline: none;
        color: #fff;
    }

    .btn-danger {
        background-color: #b33a3a;
        border: none;
        font-weight: 600;
        padding: 0.35rem 1rem;
        border-radius: 14px;
        box-shadow: 0 0 5px #b33a3a;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        font-size: 0.9rem;
        cursor: pointer;
        color: #fff;
    }

    .btn-danger:hover,
    .btn-danger:focus {
        background-color: #822626;
        box-shadow: 0 0 12px #822626;
        outline: none;
    }

    /* Modal Styling */
    .modal-content {
        background: linear-gradient(145deg, #232323, #1a1a1a);
        color: #f0e9db;
        border-radius: 14px;
        border: 2px solid #c9b037;
        box-shadow: 0 8px 20px rgba(201, 176, 55, 0.5);
        font-family: 'Montserrat', sans-serif;
    }

    .modal-header {
        border-bottom: 2px solid #c9b037;
    }

    .modal-title {
        font-family: 'Playfair Display', serif;
        color: #c9b037;
        font-weight: 700;
        letter-spacing: 2px;
        font-size: 1.7rem;
    }

    .btn-close {
        filter: invert(95%) sepia(0%) saturate(0%) hue-rotate(91deg) brightness(100%) contrast(105%);
    }

    .modal-footer {
        border-top: 2px solid #c9b037;
        padding-top: 1rem;
    }

    .modal-footer .btn-primary {
        background-color: #c9b037;
        border: none;
        font-weight: 700;
        border-radius: 14px;
        padding: 0.6rem 1.8rem;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .modal-footer .btn-primary:hover,
    .modal-footer .btn-primary:focus {
        background-color: #a89529;
        box-shadow: 0 0 12px #a89529;
        outline: none;
    }

    .modal-footer .btn-secondary {
        background-color: transparent;
        border: 2px solid #c9b037;
        color: #c9b037;
        font-weight: 600;
        border-radius: 14px;
        padding: 0.5rem 1.6rem;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .modal-footer .btn-secondary:hover,
    .modal-footer .btn-secondary:focus {
        background-color: #c9b037;
        color: #121212;
        outline: none;
    }

    /* Responsive */
    @media (max-width: 600px) {
        form.mb-4 {
            flex-direction: column;
        }

        form input[type="text"],
        form button.btn-primary {
            width: 100%;
        }

        table {
            font-size: 0.9rem;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Data Alternatif Rumah Sakit</h2>
        <a href="index.php" class="btn-back" aria-label="Kembali ke halaman utama">
            <i class="bi bi-arrow-left-circle"></i> Kembali
        </a>

        <!-- Form Tambah -->
        <form method="POST" class="mb-4" role="form" aria-label="Form tambah data alternatif">
            <input type="text" name="nama" class="form-control" placeholder="Nama Alternatif" required
                aria-required="true" />
            <button type="submit" name="tambah" class="btn btn-primary mt-3">Tambah</button>
        </form>

        <!-- Tabel Alternatif -->
        <table class="table" aria-label="Tabel data alternatif Rumah Sakit">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Alternatif</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $conn->query("SELECT * FROM alternatif ORDER BY id ASC");
                while ($row = $res->fetch_assoc()) {
                    $id = htmlspecialchars($row['id']);
                    $nama = htmlspecialchars($row['nama']);
                    echo "<tr>
                        <td>{$id}</td>
                        <td>{$nama}</td>
                        <td>
                            <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal{$id}' aria-label='Edit {$nama}'>
                                <i class='bi bi-pencil-fill'></i> Edit
                            </button>
                            <a href='?hapus={$id}' onclick='return confirm(\"Yakin hapus {$nama}?\")' class='btn btn-danger btn-sm' aria-label='Hapus {$nama}'>
                                <i class='bi bi-trash-fill'></i> Hapus
                            </a>
                        </td>
                    </tr>";

                    // Modal Edit
                    echo "
<div class='modal fade' id='editModal{$id}' tabindex='-1' aria-labelledby='editModalLabel{$id}' aria-hidden='true'>
  <div class='modal-dialog'>
    <form method='POST'>
      <div class='modal-content'>
        <div class='modal-header'>
          <h5 class='modal-title' id='editModalLabel{$id}'>Edit Alternatif</h5>
          <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Tutup'></button>
        </div>
        <div class='modal-body'>
          <input type='hidden' name='id' value='{$id}'>
          <input type='text' name='nama' class='form-control' value='{$nama}' required />
        </div>
        <div class='modal-footer'>
          <button type='submit' name='edit' class='btn btn-primary'>Simpan</button>
          <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
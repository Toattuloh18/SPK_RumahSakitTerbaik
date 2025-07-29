<?php
// Di setiap halaman sistem
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}
include 'koneksi.php';

// Tambah kriteria
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama']);
    $bobot = floatval($_POST['bobot']);
    $tipe = $_POST['tipe'];

    if ($nama !== '' && ($tipe === 'maximize' || $tipe === 'minimize')) {
        $stmt = $conn->prepare("INSERT INTO kriteria (nama, bobot, tipe) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $nama, $bobot, $tipe);
        $stmt->execute();
    }
    header("Location: kriteria.php");
    exit;
}

// Edit kriteria
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $nama = trim($_POST['nama']);
    $bobot = floatval($_POST['bobot']);
    $tipe = $_POST['tipe'];

    if ($nama !== '' && ($tipe === 'maximize' || $tipe === 'minimize')) {
        $stmt = $conn->prepare("UPDATE kriteria SET nama=?, bobot=?, tipe=? WHERE id=?");
        $stmt->bind_param("sdsi", $nama, $bobot, $tipe, $id);
        $stmt->execute();
    }
    header("Location: kriteria.php");
    exit;
}

// Hapus kriteria
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // Hapus terlebih dahulu data nilai yang menggunakan id_kriteria ini
    $conn->query("DELETE FROM nilai WHERE id_kriteria = $id");

    // Baru hapus data kriteria
    $conn->query("DELETE FROM kriteria WHERE id = $id");

    // Reset auto increment jika perlu
    $conn->query("ALTER TABLE kriteria AUTO_INCREMENT = 1");

    header("Location: kriteria.php");
    exit();
}


// Ambil data untuk form edit
$edit_mode = false;
$edit_data = [
    'id' => '',
    'nama' => '',
    'bobot' => '',
    'tipe' => ''
];

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM kriteria WHERE id=$id");
    if ($res) {
        $edit_data = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Data Kriteria - Sistem SPK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />
    <style>
    body {
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

    form.row.g-3 {
        max-width: 720px;
        margin: 0 auto 3rem auto;
        background-color: #1f1f1f;
        padding: 1.8rem 2rem;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(201, 176, 55, 0.25);
    }

    form input[type="text"],
    form input[type="number"],
    form select {
        background-color: #2a2a2a;
        border: 2px solid #c9b037;
        color: #f0e9db;
        font-weight: 600;
        border-radius: 14px;
        padding: 0.6rem 1rem;
        font-size: 1.1rem;
        transition: border-color 0.3s ease, background-color 0.3s ease;
    }

    form input[type="text"]:focus,
    form input[type="number"]:focus,
    form select:focus {
        outline: none;
        border-color: #fff;
        background-color: #3a3a3a;
        color: #fff;
    }

    form button {
        border-radius: 14px;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 0.55rem 1.5rem;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    form button.btn-primary {
        background-color: #c9b037;
        border: none;
        color: #121212;
    }

    form button.btn-primary:hover,
    form button.btn-primary:focus {
        background-color: #a89529;
        box-shadow: 0 0 12px #a89529;
        outline: none;
    }

    form button.btn-warning {
        background-color: #c9b037;
        border: none;
        color: #121212;
    }

    form button.btn-warning:hover,
    form button.btn-warning:focus {
        background-color: #a89529;
        box-shadow: 0 0 12px #a89529;
        outline: none;
    }

    /* Table styling */
    table {
        max-width: 900px;
        margin: 0 auto;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 14px;
        font-size: 1rem;
        color: #f0e9db;
        box-shadow: 0 6px 18px rgba(201, 176, 55, 0.2);
        border-radius: 14px;
        overflow: hidden;
    }

    thead tr {
        background-color: #1f1f1f;
        color: #c9b037;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 1.2px;
        text-shadow: 0 0 7px rgba(201, 176, 55, 0.7);
    }

    thead th {
        padding: 1rem 1.5rem;
    }

    tbody tr {
        background-color: #2a2a2a;
        transition: background-color 0.25s ease;
        border-radius: 14px;
    }

    tbody tr:hover {
        background-color: #3a3a3a;
    }

    tbody td {
        padding: 1rem 1.4rem;
        vertical-align: middle;
        font-weight: 600;
    }

    tbody td:nth-child(1),
    tbody td:nth-child(5) {
        text-align: center;
        width: 8%;
    }

    tbody td:nth-child(3),
    tbody td:nth-child(4) {
        width: 15%;
        text-transform: capitalize;
    }

    /* Action buttons */
    .btn-warning {
        background-color: #c9b037;
        border: none;
        color: #121212;
        font-weight: 600;
        padding: 0.3rem 0.9rem;
        border-radius: 14px;
        box-shadow: 0 0 5px #c9b037;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
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
        padding: 0.3rem 0.9rem;
        border-radius: 14px;
        box-shadow: 0 0 5px #b33a3a;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        font-size: 0.9rem;
        cursor: pointer;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .btn-danger:hover,
    .btn-danger:focus {
        background-color: #822626;
        box-shadow: 0 0 12px #822626;
        outline: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        form.row.g-3 {
            max-width: 100%;
        }

        table {
            font-size: 0.85rem;
        }

        thead th {
            padding: 0.8rem 0.7rem;
        }

        tbody td {
            padding: 0.7rem 0.7rem;
        }
    }
    </style>
</head>

<body>
    <h2>Data Kriteria</h2>
    <a href="index.php" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>

    <form method="post" class="row g-3">
        <input type="hidden" name="id" value="<?= htmlspecialchars($edit_data['id']) ?>">
        <div class="col-md-5 col-sm-12">
            <input type="text" name="nama" class="form-control" placeholder="Nama Kriteria" required
                value="<?= htmlspecialchars($edit_data['nama']) ?>" aria-label="Nama Kriteria" />
        </div>
        <div class="col-md-3 col-sm-6">
            <input type="number" step="0.01" name="bobot" class="form-control" placeholder="Bobot" required
                value="<?= htmlspecialchars($edit_data['bobot']) ?>" aria-label="Bobot Kriteria" />
        </div>
        <div class="col-md-3 col-sm-6">
            <select name="tipe" class="form-select" required aria-label="Tipe Kriteria">
                <option value="" disabled <?= $edit_data['tipe'] == '' ? 'selected' : '' ?>>-- Pilih Tipe --</option>
                <option value="maximize" <?= ($edit_data['tipe'] == 'maximize') ? 'selected' : '' ?>>Maximize
                </option>
                <option value="minimize" <?= ($edit_data['tipe'] == 'minimize') ? 'selected' : '' ?>>Minimize
                </option>
            </select>
        </div>
        <div class="col-md-1 col-sm-12 d-grid">
            <button type="submit" name="<?= $edit_mode ? 'edit' : 'tambah' ?>"
                class="btn <?= $edit_mode ? 'btn-warning' : 'btn-primary' ?>"
                aria-label="<?= $edit_mode ? 'Update Kriteria' : 'Tambah Kriteria' ?>">
                <i class="bi bi-check-lg"></i>
            </button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Bobot</th>
                <th>Tipe</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conn->query("SELECT * FROM kriteria ORDER BY id");
            $no = 1;
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= number_format($row['bobot'], 2) ?></td>
                <td><?= ucfirst($row['tipe']) ?></td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning" title="Edit">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger"
                        onclick="return confirm('Yakin ingin hapus?')" title="Hapus">
                        <i class="bi bi-trash"></i> Hapus
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>